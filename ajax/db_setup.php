<?php
// ajax/db_setup.php - Database Setup Backend
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../includes/load_env.php';
require_once __DIR__ . '/../includes/MigrationRunner.php';

$dbhost = env('DB_HOST', 'localhost');
$dbuser = env('DB_USER', 'root');
$dbpass = env('DB_PASS', '');
$dbname = env('DB_NAME', 'kody2');

mysqli_report(MYSQLI_REPORT_OFF);

function execute_sql_file($conn, $file_path) {
    if (!file_exists($file_path)) {
        return ["success" => false, "message" => "ملف SQL غير موجود: " . basename($file_path)];
    }

    $lines = file($file_path);
    if ($lines === false) {
        return ["success" => false, "message" => "فشل في قراءة ملف SQL"];
    }

    $query = '';
    $delimiter = ';';
    $in_multi_line_comment = false;

    foreach ($lines as $line) {
        $line = trim($line);

        if ($line === '') continue;

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

        $query .= $line . " ";

        if (str_ends_with($line, $delimiter)) {
            $exec_query = substr(trim($query), 0, -strlen($delimiter));

            if ($exec_query !== '') {
                if (!$conn->query($exec_query)) {
                    return [
                        "success" => false,
                        "message" => "خطأ في تنفيذ SQL: " . $conn->error . " <br> في الاستعلام: " . substr($exec_query, 0, 150) . "..."
                    ];
                }
            }
            $query = '';
        }
    }

    return ["success" => true, "message" => "تم تهيئة قاعدة البيانات بنجاح"];
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

    $conn = @new mysqli($dbhost, $dbuser, $dbpass);
    if ($conn->connect_error) {
        echo json_encode(["success" => false, "message" => "فشل الاتصال بـ MySQL: " . $conn->connect_error]);
        exit;
    }

    $sql_create = "CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci";
    $conn->query($sql_create);
    $conn->select_db($dbname);

    $conn->query("SET FOREIGN_KEY_CHECKS = 0");
    $result = execute_sql_file($conn, $file['tmp_name']);
    $conn->query("SET FOREIGN_KEY_CHECKS = 1");

    echo json_encode(finish_with_migrations($conn, $result));
    $conn->close();

} elseif ($action === 'migrate') {
    $conn = @new mysqli($dbhost, $dbuser, $dbpass, $dbname);
    if ($conn->connect_error) {
        echo json_encode(["success" => false, "message" => "فشل الاتصال: " . $conn->connect_error]);
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
