<?php
// ajax/db_setup.php - Database Setup Backend
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../includes/load_env.php';
require_once __DIR__ . '/../includes/db_name.php';
require_once __DIR__ . '/../includes/MigrationRunner.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$dbhost = env('DB_HOST', 'localhost');
$dbuser = env('DB_USER', 'root');
$dbpass = env('DB_PASS', '');
$dbname = kody_preferred_dbname();

mysqli_report(MYSQLI_REPORT_OFF);

/**
 * Parse SQL file into executable statements (respects DELIMITER / comments).
 *
 * @return list<string>|array{error: string}
 */
function parse_sql_statements(string $file_path)
{
    if (!file_exists($file_path)) {
        return ['error' => 'ملف SQL غير موجود: ' . basename($file_path)];
    }

    $lines = file($file_path);
    if ($lines === false) {
        return ['error' => 'فشل في قراءة ملف SQL'];
    }

    $statements = [];
    $query = '';
    $delimiter = ';';
    $in_multi_line_comment = false;

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '') {
            continue;
        }

        if (!$in_multi_line_comment && str_starts_with($line, '/*')) {
            if (!str_contains($line, '*/')) {
                $in_multi_line_comment = true;
            }
            continue;
        }
        if ($in_multi_line_comment) {
            if (str_contains($line, '*/')) {
                $in_multi_line_comment = false;
            }
            continue;
        }

        if (str_starts_with($line, '--') || str_starts_with($line, '#')) {
            continue;
        }

        if (preg_match('/^DELIMITER\s+(.+)$/i', $line, $matches)) {
            $delimiter = trim($matches[1]);
            continue;
        }

        $query .= $line . ' ';

        if (str_ends_with($line, $delimiter)) {
            $exec_query = substr(trim($query), 0, -strlen($delimiter));
            $exec_query = trim($exec_query);
            if ($exec_query !== '') {
                $statements[] = $exec_query;
            }
            $query = '';
        }
    }

    return $statements;
}

/**
 * Soft-normalize statements from a backup dump before inject.
 */
function normalize_backup_statement(string $sql): ?string
{
    $sql = trim($sql);
    if ($sql === '') {
        return null;
    }

    // Keep using the DB we created/selected — ignore dump USE/CREATE DATABASE.
    if (preg_match('/^(USE|CREATE\s+DATABASE)\b/i', $sql)) {
        return null;
    }

    // Avoid aborting on empty DBs: DROP TABLE x → DROP TABLE IF EXISTS x
    if (preg_match('/^DROP\s+TABLE\s+(?!IF\s+EXISTS)/i', $sql)) {
        $sql = preg_replace('/^DROP\s+TABLE\s+/i', 'DROP TABLE IF EXISTS ', $sql, 1);
    }

    // Soft CREATE when DROP was skipped / table already exists
    if (preg_match('/^CREATE\s+TABLE\s+(?!IF\s+NOT\s+EXISTS)/i', $sql)) {
        $sql = preg_replace('/^CREATE\s+TABLE\s+/i', 'CREATE TABLE IF NOT EXISTS ', $sql, 1);
    }

    // الباكاب القديم يصدّر NULL كـ "" و UNIQUE على receipt_number يرفض تكرار الفاضي (1062)
    if (preg_match('/^CREATE\s+TABLE\b/i', $sql)) {
        $sql = preg_replace(
            '/UNIQUE\s+KEY\s+`?receipt_number`?\s*\(\s*`?receipt_number`?\s*\)/i',
            'KEY `receipt_number` (`receipt_number`)',
            $sql
        );
    }

    return $sql;
}

function execute_sql_file($conn, $file_path, bool $continueOnError = false)
{
    $parsed = parse_sql_statements($file_path);
    if (isset($parsed['error'])) {
        return ['success' => false, 'message' => $parsed['error']];
    }

    $skipped = 0;
    $errors = [];
    $errorGroups = []; // key => [errno, error, count, sample_sql]
    foreach ($parsed as $exec_query) {
        if ($continueOnError) {
            $normalized = normalize_backup_statement($exec_query);
            if ($normalized === null) {
                continue;
            }
            $exec_query = $normalized;
        }

        if (!$conn->query($exec_query)) {
            $errno = (int) $conn->errno;
            $error = (string) $conn->error;
            $sample = substr($exec_query, 0, 220);
            $errDetail = [
                'errno' => $errno,
                'error' => $error,
                'sql' => $sample,
            ];
            if ($continueOnError) {
                $skipped++;
                $gkey = $errno . '|' . $error;
                if (!isset($errorGroups[$gkey])) {
                    $errorGroups[$gkey] = [
                        'errno' => $errno,
                        'error' => $error,
                        'count' => 0,
                        'sample_sql' => $sample,
                    ];
                }
                $errorGroups[$gkey]['count']++;
                if (count($errors) < 25) {
                    $errors[] = $errDetail;
                }
                continue;
            }
            return [
                'success' => false,
                'message' => 'خطأ في تنفيذ SQL [' . $errno . ']: ' . $error
                    . "\nالاستعلام: " . $sample . '...',
                'errors' => [$errDetail],
            ];
        }
    }

    $msg = $continueOnError
        ? 'تم استعادة النسخة الاحتياطية بنجاح'
        : 'تم تهيئة قاعدة البيانات بنجاح';
    if ($continueOnError && $skipped > 0) {
        $msg .= "\n\nتم تجاهل {$skipped} استعلام فاشل. أخطاء MySQL:";
        $i = 0;
        foreach ($errorGroups as $g) {
            $i++;
            if ($i > 15) {
                $msg .= "\n... وأنواع أخطاء أخرى";
                break;
            }
            $msg .= "\n{$i}) [{$g['errno']}] {$g['error']} (×{$g['count']})"
                . "\n   مثال SQL: {$g['sample_sql']}...";
        }
    }

    return [
        'success' => true,
        'message' => $msg,
        'skipped' => $skipped,
        'errors' => $errors,
        'error_groups' => array_values($errorGroups),
    ];
}

/**
 * List existing table names (lowercase keys).
 *
 * @return array<string, true>
 */
function list_existing_tables(mysqli $conn): array
{
    $tables = [];
    $res = $conn->query('SHOW TABLES');
    if ($res) {
        while ($row = $res->fetch_row()) {
            $tables[strtolower($row[0])] = true;
        }
    }
    return $tables;
}

/**
 * After backup inject: create any tables missing vs base schema (db/DB.sql),
 * plus their indexes / auto_increment / seed INSERTs for those new tables only.
 */
function ensure_missing_tables_from_schema(mysqli $conn, string $schemaFile): array
{
    $parsed = parse_sql_statements($schemaFile);
    if (isset($parsed['error'])) {
        return ['success' => false, 'message' => $parsed['error'], 'added' => []];
    }

    $existing = list_existing_tables($conn);
    $added = [];
    $addedLookup = [];

    // Pass 1: CREATE TABLE for missing tables only
    foreach ($parsed as $sql) {
        if (!preg_match('/^CREATE\s+TABLE\s+(?:IF\s+NOT\s+EXISTS\s+)?`?([a-zA-Z0-9_]+)`?/i', $sql, $m)) {
            continue;
        }
        $table = $m[1];
        $key = strtolower($table);
        if (isset($existing[$key])) {
            continue;
        }

        $createSql = preg_replace('/^CREATE\s+TABLE\s+(?!IF\s+NOT\s+EXISTS)/i', 'CREATE TABLE IF NOT EXISTS ', $sql, 1);
        if ($conn->query($createSql)) {
            $added[] = $table;
            $addedLookup[$key] = true;
            $existing[$key] = true;
        }
    }

    if ($added === []) {
        return [
            'success' => true,
            'message' => 'لا جداول ناقصة في الهيكل الأساسي',
            'added' => [],
        ];
    }

    // Pass 2: ALTER / INSERT belonging only to newly added tables
    foreach ($parsed as $sql) {
        if (preg_match('/^ALTER\s+TABLE\s+`?([a-zA-Z0-9_]+)`?/i', $sql, $m)) {
            if (isset($addedLookup[strtolower($m[1])])) {
                $conn->query($sql); // ignore duplicate-key / already-exists
            }
            continue;
        }
        if (preg_match('/^INSERT\s+INTO\s+`?([a-zA-Z0-9_]+)`?/i', $sql, $m)) {
            if (isset($addedLookup[strtolower($m[1])])) {
                $conn->query($sql); // seed defaults for empty new tables only
            }
        }
    }

    return [
        'success' => true,
        'message' => 'تم إضافة ' . count($added) . ' جدول ناقص من الهيكل الأساسي',
        'added' => $added,
    ];
}

/**
 * After schema import: apply any migrations not already in the dump.
 */
function finish_with_migrations(mysqli $conn, array $result): array
{
    if (empty($result['success'])) {
        return $result;
    }

    try {
        $runner = new MigrationRunner($conn);
        $mig = $runner->runPending();
        if (!$mig['success']) {
            return [
                'success' => false,
                'message' => $result['message'] . ' — لكن فشل تطبيق التحديثات: ' . $mig['message'],
                'migrations' => $mig,
            ];
        }
        $pendingCount = count($mig['results']);
        $suffix = $pendingCount > 0
            ? " وتم تطبيق {$pendingCount} تحديث."
            : ' (لا تحديثات إضافية).';
        return [
            'success' => true,
            'message' => $result['message'] . $suffix,
            'migrations' => $mig,
            'status' => $runner->status(),
            'added_tables' => $result['added_tables'] ?? [],
        ];
    } catch (Throwable $e) {
        return [
            'success' => false,
            'message' => $result['message'] . ' — خطأ migrations: ' . $e->getMessage(),
        ];
    }
}

function resolve_schema_file(): string
{
    $candidates = [
        __DIR__ . '/../db/DB.sql',
        __DIR__ . '/../db/db.sql',
        __DIR__ . '/../backup/DB.sql',
        __DIR__ . '/../backup/db.sql',
    ];
    foreach ($candidates as $path) {
        if (file_exists($path)) {
            return $path;
        }
    }
    return $candidates[0];
}

$action = $_POST['action'] ?? '';

if ($action === 'create') {
    $conn = @new mysqli($dbhost, $dbuser, $dbpass);
    if ($conn->connect_error) {
        echo json_encode(["success" => false, "message" => "فشل الاتصال بـ MySQL: " . $conn->connect_error]);
        exit;
    }

    $sql_create = "CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci";
    if (!$conn->query($sql_create)) {
        echo json_encode(["success" => false, "message" => "فشل إنشاء قاعدة البيانات: " . $conn->error]);
        exit;
    }

    $conn->select_db($dbname);
    $_SESSION['active_dbname'] = $dbname;
    $conn->query("SET FOREIGN_KEY_CHECKS = 0");

    $result = execute_sql_file($conn, resolve_schema_file());
    $conn->query("SET FOREIGN_KEY_CHECKS = 1");

    echo json_encode(finish_with_migrations($conn, $result));
    $conn->close();

} elseif ($action === 'restore') {
    if (!isset($_FILES['backup_file'])) {
        echo json_encode(["success" => false, "message" => "لم يتم رفع أي ملف"]);
        exit;
    }

    $file = $_FILES['backup_file'];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(["success" => false, "message" => "خطأ في رفع الملف: " . $file['error']]);
        exit;
    }

    try {
        $dbname = kody_validate_dbname((string) ($_POST['db_name'] ?? ''));
    } catch (InvalidArgumentException $e) {
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
        exit;
    }

    $conn = @new mysqli($dbhost, $dbuser, $dbpass);
    if ($conn->connect_error) {
        echo json_encode(["success" => false, "message" => "فشل الاتصال بـ MySQL: " . $conn->connect_error]);
        exit;
    }

    $sql_create = "CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci";
    if (!$conn->query($sql_create)) {
        echo json_encode(["success" => false, "message" => "فشل إنشاء/الوصول لقاعدة `$dbname`: " . $conn->error]);
        exit;
    }
    $conn->select_db($dbname);
    kody_register_dbname($dbname);

    $conn->query("SET FOREIGN_KEY_CHECKS = 0");

    // 1) Inject backup without aborting on conflicts / missing drops
    $result = execute_sql_file($conn, $file['tmp_name'], true);
    $skipped = (int) ($result['skipped'] ?? 0);
    $result['message'] = "تم استعادة النسخة إلى `$dbname` بنجاح"
        . ($skipped > 0 ? " (تم تجاهل {$skipped} استعلام فاشل)" : '');

    // أظهر رسائل MySQL الفعلية مجمّعة (العدّ من كل الـ 2991 وليس عيّنة)
    $groups = $result['error_groups'] ?? [];
    if ($skipped > 0 && $groups !== []) {
        $result['message'] .= "\n\nأخطاء MySQL:";
        foreach (array_slice($groups, 0, 15) as $i => $g) {
            $n = $i + 1;
            $result['message'] .= "\n{$n}) [{$g['errno']}] {$g['error']} (×{$g['count']})"
                . "\n   مثال SQL: {$g['sample_sql']}...";
        }
        if (count($groups) > 15) {
            $result['message'] .= "\n... وأنواع أخطاء أخرى (" . (count($groups) - 15) . ")";
        }
    }

    // 2) Compare with base schema used for new DB — create any missing tables
    $schemaFile = resolve_schema_file();
    $ensure = ensure_missing_tables_from_schema($conn, $schemaFile);
    if (!empty($ensure['added'])) {
        $result['message'] .= "\n\n" . $ensure['message'] . ': ' . implode(', ', $ensure['added']);
        $result['added_tables'] = $ensure['added'];
    } else {
        $result['message'] .= "\nالهيكل الأساسي مكتمل.";
        $result['added_tables'] = [];
    }
    $result['database'] = $dbname;

    $conn->query("SET FOREIGN_KEY_CHECKS = 1");

    echo json_encode(finish_with_migrations($conn, $result));
    $conn->close();

} elseif ($action === 'migrate') {
    $conn = @new mysqli($dbhost, $dbuser, $dbpass);
    if ($conn->connect_error) {
        echo json_encode(["success" => false, "message" => "فشل الاتصال: " . $conn->connect_error]);
        exit;
    }
    $selected = kody_select_existing_db($conn);
    if ($selected === null) {
        echo json_encode(["success" => false, "message" => "قاعدة البيانات غير موجودة"]);
        exit;
    }
    $runner = new MigrationRunner($conn);
    $result = $runner->runPending();
    echo json_encode([
        'success' => $result['success'],
        'message' => $result['message'],
        'results' => $result['results'],
        'status' => $runner->status(),
    ]);
    $conn->close();

} else {
    echo json_encode(["success" => false, "message" => "إجراء غير صالح"]);
}
