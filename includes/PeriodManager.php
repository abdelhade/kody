<?php

/**
 * PeriodManager — تعدد مدد العمل عبر قواعد بيانات منفصلة
 *
 * - سجل مرتبط في config/db_registry.json
 * - قفل مدة: إنشاء قاعدة جديدة + نسخ البيانات الأساسية + أرصدة افتتاحية
 * - إنشاء قاعدة فارغة من db/DB.sql
 * - تبديل القاعدة النشطة (session + .env)
 */
class PeriodManager
{
    private mysqli $conn;
    private string $dbHost;
    private string $dbUser;
    private string $dbPass;
    private string $currentDb;
    private string $registryPath;

    /** جداول حركة لا تُنسخ عند قفل المدة (هيكل فقط) */
    private const TX_TABLES = [
        'ot_head', 'fat_details', 'fats', 'myoper_det',
        'journal_entries', 'journal_heads',
        'closed_orders', 'process', 'orders', 'order_status',
        'transactions', 'pulse_logs', 'system_logs',
        'sessions', 'session_time', 'cache', 'cache_locks',
        'failed_jobs', 'job_batches', 'jobs', 'password_reset_tokens',
        'schema_migrations', 'migrations',
        'attlog', 'attdocs', 'attandance', 'payroll_calcs', 'salaries',
        'imporfplog', 'delivery_clients',
    ];

    public function __construct(mysqli $conn, string $dbHost, string $dbUser, string $dbPass, string $currentDb)
    {
        $this->conn = $conn;
        $this->dbHost = $dbHost;
        $this->dbUser = $dbUser;
        $this->dbPass = $dbPass;
        $this->currentDb = $currentDb;
        $this->registryPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'db_registry.json';
    }

    public static function fromEnv(?mysqli $conn = null): self
    {
        require_once __DIR__ . '/load_env.php';
        require_once __DIR__ . '/db_name.php';

        $dbHost = (string) env('DB_HOST', 'localhost');
        $dbUser = (string) env('DB_USER', 'root');
        $dbPass = (string) env('DB_PASS', '');
        $currentDb = function_exists('kody_preferred_dbname')
            ? kody_preferred_dbname()
            : (string) env('DB_NAME', 'kody2');

        if ($conn === null) {
            $conn = @new mysqli($dbHost, $dbUser, $dbPass);
        }

        return new self($conn, $dbHost, $dbUser, $dbPass, $currentDb);
    }

    // ─── Registry ───────────────────────────────────────────────

    public function getRegistry(): array
    {
        $dir = dirname($this->registryPath);
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        if (!is_readable($this->registryPath)) {
            $reg = $this->defaultRegistry();
            $this->saveRegistry($reg);
            return $reg;
        }

        $json = file_get_contents($this->registryPath);
        $reg = json_decode($json ?: '', true);
        if (!is_array($reg) || empty($reg['databases'])) {
            $reg = $this->defaultRegistry();
            $this->saveRegistry($reg);
        }

        // تأكد أن القاعدة الحالية مسجّلة
        $names = array_column($reg['databases'], 'name');
        if (!in_array($this->currentDb, $names, true)) {
            $reg['databases'][] = [
                'name' => $this->currentDb,
                'label' => $this->currentDb,
                'created_at' => date('Y-m-d H:i:s'),
                'closed_at' => null,
                'parent' => null,
            ];
            $this->saveRegistry($reg);
        }

        if (empty($reg['current'])) {
            $reg['current'] = $this->currentDb;
            $this->saveRegistry($reg);
        }

        return $reg;
    }

    public function saveRegistry(array $reg): void
    {
        $dir = dirname($this->registryPath);
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        file_put_contents(
            $this->registryPath,
            json_encode($reg, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
        );
    }

    public function registerDatabase(string $dbName, string $label = '', ?string $parent = null, bool $makeCurrent = true): void
    {
        $dbName = $this->sanitizeDbName($dbName);
        $label = $label !== '' ? $label : $dbName;
        $reg = $this->getRegistry();
        $names = array_column($reg['databases'], 'name');
        if (!in_array($dbName, $names, true)) {
            $reg['databases'][] = [
                'name' => $dbName,
                'label' => $label,
                'created_at' => date('Y-m-d H:i:s'),
                'closed_at' => null,
                'parent' => $parent,
            ];
        }
        if ($makeCurrent) {
            $reg['current'] = $dbName;
            if (session_status() === PHP_SESSION_ACTIVE) {
                $_SESSION['active_dbname'] = $dbName;
            }
            $this->updateEnvDbName($dbName);
        }
        $this->saveRegistry($reg);
    }

    private function defaultRegistry(): array
    {
        return [
            'group_id' => 'default',
            'current' => $this->currentDb,
            'databases' => [[
                'name' => $this->currentDb,
                'label' => $this->currentDb,
                'created_at' => date('Y-m-d H:i:s'),
                'closed_at' => null,
                'parent' => null,
            ]],
        ];
    }

    public function listPeriods(): array
    {
        $reg = $this->getRegistry();
        $active = $this->resolveActiveDb();
        foreach ($reg['databases'] as &$db) {
            $db['is_current'] = ($db['name'] === $active);
            $db['exists'] = $this->databaseExists($db['name']);
        }
        unset($db);
        return [
            'current' => $active,
            'databases' => $reg['databases'],
        ];
    }

    public function resolveActiveDb(): string
    {
        if (!empty($_SESSION['active_dbname'])) {
            return (string) $_SESSION['active_dbname'];
        }
        $reg = $this->getRegistry();
        return (string) ($reg['current'] ?? $this->currentDb);
    }

    // ─── Switch ─────────────────────────────────────────────────

    public function switchTo(string $dbName): array
    {
        $dbName = $this->sanitizeDbName($dbName);
        $reg = $this->getRegistry();
        $names = array_column($reg['databases'], 'name');
        if (!in_array($dbName, $names, true)) {
            throw new RuntimeException('قاعدة البيانات غير مسجّلة في مجموعة المدد');
        }
        if (!$this->databaseExists($dbName)) {
            throw new RuntimeException("قاعدة البيانات `$dbName` غير موجودة على السيرفر");
        }

        $_SESSION['active_dbname'] = $dbName;
        $reg['current'] = $dbName;
        $this->saveRegistry($reg);
        $this->updateEnvDbName($dbName);

        return [
            'success' => true,
            'message' => "تم التبديل إلى `$dbName` — أعد تحميل الصفحة",
            'current' => $dbName,
        ];
    }

    // ─── Create fresh ───────────────────────────────────────────

    public function createFresh(string $dbName, string $label = ''): array
    {
        require_once __DIR__ . '/MigrationRunner.php';

        $dbName = $this->sanitizeDbName($dbName);
        if ($this->databaseExists($dbName)) {
            return [
                'success' => false,
                'message' => "قاعدة البيانات `$dbName` موجودة مسبقاً. اختر اسماً آخر.",
            ];
        }

        $admin = $this->adminConnection();
        $sqlCreate = "CREATE DATABASE `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci";
        if (!$admin->query($sqlCreate)) {
            return [
                'success' => false,
                'message' => "فشل إنشاء قاعدة البيانات: " . $admin->error,
            ];
        }

        $admin->select_db($dbName);
        $admin->query('SET FOREIGN_KEY_CHECKS = 0');

        $schemaFile = $this->resolveSchemaFile();
        $import = $this->executeSqlFile($admin, $schemaFile, false);
        $admin->query('SET FOREIGN_KEY_CHECKS = 1');

        if (empty($import['success'])) {
            $admin->query("DROP DATABASE `$dbName`");
            return [
                'success' => false,
                'message' => 'فشل استيراد الهيكل الأساسي: ' . ($import['message'] ?? 'خطأ غير معروف'),
                'errors' => $import['errors'] ?? [],
            ];
        }

        $runner = new MigrationRunner($admin);
        $mig = $runner->runPending();
        $this->ensurePeriodMeta($admin, $dbName, $label ?: $dbName, null);

        $this->registerDatabase($dbName, $label, null, true);

        $pendingCount = count($mig['results'] ?? []);
        $suffix = $pendingCount > 0 ? " وتم تطبيق {$pendingCount} تحديث." : ' (لا تحديثات إضافية).';

        return [
            'success' => true,
            'message' => "تم إنشاء القاعدة `$dbName` وتفعيلها." . $suffix,
            'database' => $dbName,
            'migrations' => $mig,
            'status' => $runner->status(),
        ];
    }

    // ─── Restore backup ──────────────────────────────────────────

    public function restoreBackup(string $dbName, string $filePath, string $label = ''): array
    {
        require_once __DIR__ . '/MigrationRunner.php';

        $dbName = $this->sanitizeDbName($dbName);
        if (!file_exists($filePath)) {
            return [
                'success' => false,
                'message' => 'ملف النسخة الاحتياطية غير موجود على السيرفر',
            ];
        }

        $admin = $this->adminConnection();
        $sqlCreate = "CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci";
        if (!$admin->query($sqlCreate)) {
            return [
                'success' => false,
                'message' => "فشل إنشاء/الوصول لقاعدة `$dbName`: " . $admin->error,
            ];
        }

        $admin->select_db($dbName);
        $this->registerDatabase($dbName, $label, null, true);

        $admin->query('SET FOREIGN_KEY_CHECKS = 0');

        // 1) استيراد النسخة الاحتياطية مع تجاهل تعارضات الدامب القديم
        $import = $this->executeSqlFile($admin, $filePath, true);
        $skipped = (int) ($import['skipped'] ?? 0);
        $message = "تم استعادة النسخة إلى `$dbName` بنجاح" . ($skipped > 0 ? " (تم تجاهل {$skipped} استعلام فاشل)" : '');

        // أخطاء MySQL إن وُجدت
        $groups = $import['error_groups'] ?? [];
        if ($skipped > 0 && $groups !== []) {
            $message .= "\n\nأخطاء MySQL:";
            foreach (array_slice($groups, 0, 15) as $i => $g) {
                $n = $i + 1;
                $message .= "\n{$n}) [{$g['errno']}] {$g['error']} (×{$g['count']})\n   مثال SQL: {$g['sample_sql']}...";
            }
            if (count($groups) > 15) {
                $message .= "\n... وأنواع أخطاء أخرى (" . (count($groups) - 15) . ")";
            }
        }

        // 2) مقارنة بالهيكل الأساسي وإضافة الجداول الناقصة إن وجدت
        $schemaFile = $this->resolveSchemaFile();
        $ensure = $this->ensureMissingTablesFromSchema($admin, $schemaFile);
        $addedTables = $ensure['added'] ?? [];
        if (!empty($addedTables)) {
            $message .= "\n\n" . $ensure['message'] . ': ' . implode(', ', $addedTables);
        } else {
            $message .= "\nالهيكل الأساسي مكتمل.";
        }

        $admin->query('SET FOREIGN_KEY_CHECKS = 1');

        // 3) تطبيق التحديثات المعلقة
        $runner = new MigrationRunner($admin);
        $mig = $runner->runPending();
        $this->ensurePeriodMeta($admin, $dbName, $label ?: $dbName, null);

        $pendingCount = count($mig['results'] ?? []);
        $suffix = $pendingCount > 0 ? " وتم تطبيق {$pendingCount} تحديث." : ' (لا تحديثات إضافية).';

        return [
            'success' => true,
            'message' => $message . $suffix,
            'database' => $dbName,
            'skipped' => $skipped,
            'error_groups' => $groups,
            'added_tables' => $addedTables,
            'migrations' => $mig,
            'status' => $runner->status(),
        ];
    }

    // ─── Close period ───────────────────────────────────────────

    /**
     * يقفل المدة الحالية وينشئ قاعدة جديدة بالبيانات الأساسية + أرصدة افتتاحية.
     */
    public function closePeriod(string $newDbName, string $label = ''): array
    {
        $sourceDb = $this->resolveActiveDb();
        $newDbName = $this->sanitizeDbName($newDbName);
        $label = $label !== '' ? $label : $newDbName;

        if ($newDbName === $sourceDb) {
            throw new RuntimeException('اسم القاعدة الجديدة يجب أن يختلف عن الحالية');
        }
        if ($this->databaseExists($newDbName)) {
            throw new RuntimeException("القاعدة `$newDbName` موجودة مسبقاً");
        }

        $admin = $this->adminConnection();
        $admin->query(
            "CREATE DATABASE `$newDbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci"
        );

        // 1) نسخ الهيكل لكل الجداول
        $tables = $this->listTables($admin, $sourceDb);
        $admin->query('SET FOREIGN_KEY_CHECKS = 0');
        foreach ($tables as $table) {
            $admin->query("CREATE TABLE `$newDbName`.`$table` LIKE `$sourceDb`.`$table`");
        }

        // 2) نسخ البيانات الأساسية (بدون جداول الحركة)
        $copied = [];
        $skipped = [];
        foreach ($tables as $table) {
            if (in_array(strtolower($table), self::TX_TABLES, true)) {
                $skipped[] = $table;
                continue;
            }
            $admin->query("INSERT INTO `$newDbName`.`$table` SELECT * FROM `$sourceDb`.`$table`");
            $copied[] = $table;
        }

        // 3) أرصدة افتتاحية
        $admin->select_db($newDbName);
        $openStats = $this->applyOpeningBalances($admin, $sourceDb, $newDbName);

        // 4) meta + migrations tracker
        $this->ensurePeriodMeta(
            $admin,
            $newDbName,
            $label,
            $sourceDb,
            false
        );
        $this->markSourceClosed($admin, $sourceDb, $newDbName);

        require_once __DIR__ . '/MigrationRunner.php';
        $runner = new MigrationRunner($admin);
        $runner->markAllApplied();

        $admin->query('SET FOREIGN_KEY_CHECKS = 1');

        // 5) تحديث السجل
        $reg = $this->getRegistry();
        $now = date('Y-m-d H:i:s');
        foreach ($reg['databases'] as &$db) {
            if ($db['name'] === $sourceDb) {
                $db['closed_at'] = $now;
            }
        }
        unset($db);
        $reg['databases'][] = [
            'name' => $newDbName,
            'label' => $label,
            'created_at' => $now,
            'closed_at' => null,
            'parent' => $sourceDb,
        ];
        $reg['current'] = $newDbName;
        $this->saveRegistry($reg);

        // 6) التبديل للمدة الجديدة
        $_SESSION['active_dbname'] = $newDbName;
        $this->updateEnvDbName($newDbName);

        return [
            'success' => true,
            'message' => "تم قفل `$sourceDb` وفتح المدة `$newDbName` بالأرصدة الافتتاحية",
            'source' => $sourceDb,
            'database' => $newDbName,
            'copied_tables' => count($copied),
            'skipped_tables' => $skipped,
            'opening' => $openStats,
        ];
    }

    /**
     * ينقل أرصدة الحسابات والمخزون كأرصدة افتتاحية في القاعدة الجديدة.
     * بعد إدراج سندات الافتتاح تُعاد أرصدة acc_head من المصدر لأن triggers
     * على journal_entries / fat_details تعدّل balance تلقائياً.
     */
    private function applyOpeningBalances(mysqli $newConn, string $sourceDb, string $newDb): array
    {
        // التقاط أرصدة المصدر قبل أي إدراج يفعّل الـ triggers
        $balanceMap = [];
        $bres = $newConn->query(
            "SELECT id, balance FROM `$sourceDb`.acc_head WHERE isdeleted = 0"
        );
        if ($bres) {
            while ($row = $bres->fetch_assoc()) {
                $balanceMap[(int) $row['id']] = (float) $row['balance'];
            }
        }

        $newConn->query(
            "UPDATE `$newDb`.acc_head
             SET start_balance = balance, debit = 0, credit = 0
             WHERE isdeleted = 0"
        );

        $stockMap = [];
        $res = $newConn->query(
            "SELECT item_id,
                    COALESCE(SUM(qty_in) - SUM(qty_out), 0) AS qty,
                    COALESCE(MAX(det_store), 0) AS store_id
             FROM `$sourceDb`.fat_details
             WHERE isdeleted = 0
             GROUP BY item_id
             HAVING qty <> 0"
        );
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $stockMap[(int) $row['item_id']] = [
                    'qty' => (float) $row['qty'],
                    'store_id' => (int) $row['store_id'],
                ];
            }
        }

        foreach ($stockMap as $itemId => $info) {
            $qty = $info['qty'];
            $stmt = $newConn->prepare("UPDATE `$newDb`.myitems SET itmqty = ? WHERE id = ?");
            if ($stmt) {
                $stmt->bind_param('di', $qty, $itemId);
                $stmt->execute();
                $stmt->close();
            }
        }

        $accountsCount = 0;
        $itemsCount = 0;
        $today = date('Y-m-d');
        $userId = (int) ($_SESSION['userid'] ?? 0);

        $accRows = [];
        foreach ($balanceMap as $accId => $bal) {
            if (abs($bal) < 0.00001) {
                continue;
            }
            // تخطّي الحسابات الأساسية إن وُجدت في الخريطة بدون رصيد تشغيلي — نفلتر عبر new DB
            $accRows[] = ['id' => $accId, 'balance' => $bal];
        }

        // فلترة is_basic = 0 فقط
        $leafIds = [];
        $leafRes = $newConn->query(
            "SELECT id FROM `$newDb`.acc_head WHERE isdeleted = 0 AND is_basic = 0"
        );
        if ($leafRes) {
            while ($lr = $leafRes->fetch_assoc()) {
                $leafIds[(int) $lr['id']] = true;
            }
        }
        $accRows = array_values(array_filter(
            $accRows,
            static fn($r) => isset($leafIds[(int) $r['id']])
        ));
        $accountsCount = count($accRows);

        if ($accountsCount > 0) {
            $info = 'أرصدة افتتاحية حسابات (قفل مدة)';
            $newConn->query(
                "INSERT INTO `$newDb`.ot_head
                 (pro_id, pro_tybe, is_stock, is_journal, journal_tybe, info, pro_date, pro_value, emp_id, isdeleted)
                 VALUES (1, 15, 0, 1, 15, '" . $newConn->real_escape_string($info) . "', '$today', 0, $userId, 0)"
            );
            $opId = (int) $newConn->insert_id;

            $newConn->query(
                "INSERT INTO `$newDb`.journal_heads (journal_id, op_id, total, jdate, details, user, op2)
                 VALUES (1, $opId, 0, '$today', '" . $newConn->real_escape_string($info) . "', $userId, 0)"
            );
            $journalId = (int) $newConn->insert_id;

            foreach ($accRows as $row) {
                $accId = (int) $row['id'];
                $bal = (float) $row['balance'];
                if ($bal >= 0) {
                    $debit = $bal;
                    $credit = 0.0;
                    $tybe = 0;
                } else {
                    $debit = 0.0;
                    $credit = abs($bal);
                    $tybe = 1;
                }
                $newConn->query(
                    "INSERT INTO `$newDb`.journal_entries
                     (journal_id, account_id, debit, credit, tybe, op_id, isdeleted)
                     VALUES ($journalId, $accId, $debit, $credit, $tybe, $opId, 0)"
                );
            }
        }

        if (!empty($stockMap)) {
            $info = 'أرصدة افتتاحية مخازن (قفل مدة)';
            $defaultStore = 0;
            $storeRes = $newConn->query(
                "SELECT id FROM `$newDb`.acc_head WHERE is_stock = 1 AND isdeleted = 0 LIMIT 1"
            );
            if ($storeRes && $sr = $storeRes->fetch_assoc()) {
                $defaultStore = (int) $sr['id'];
            }

            $newConn->query(
                "INSERT INTO `$newDb`.ot_head
                 (pro_id, pro_tybe, is_stock, is_journal, journal_tybe, info, pro_date, pro_value, store_id, emp_id, isdeleted)
                 VALUES (1, 14, 1, 0, 14, '" . $newConn->real_escape_string($info) . "', '$today', 0, $defaultStore, $userId, 0)"
            );
            $stockOpId = (int) $newConn->insert_id;

            foreach ($stockMap as $itemId => $infoRow) {
                $qty = (float) $infoRow['qty'];
                $storeId = (int) ($infoRow['store_id'] ?: $defaultStore);
                if ($qty == 0.0) {
                    continue;
                }
                $itemsCount++;
                $cost = 0.0;
                $cr = $newConn->query("SELECT cost_price FROM `$newDb`.myitems WHERE id = $itemId");
                if ($cr && $crow = $cr->fetch_assoc()) {
                    $cost = (float) $crow['cost_price'];
                }
                $qtyIn = $qty > 0 ? $qty : 0;
                $qtyOut = $qty < 0 ? abs($qty) : 0;
                $newConn->query(
                    "INSERT INTO `$newDb`.fat_details
                     (pro_id, fatid, item_id, qty_in, qty_out, price, cost_price, det_store, pro_tybe, isdeleted)
                     VALUES ($stockOpId, $stockOpId, $itemId, $qtyIn, $qtyOut, $cost, $cost, $storeId, 14, 0)"
                );
            }
        }

        // إعادة ضبط الأرصدة بعد الـ triggers لتطابق إقفال المدة السابقة
        foreach ($balanceMap as $accId => $bal) {
            $stmt = $newConn->prepare(
                "UPDATE `$newDb`.acc_head
                 SET balance = ?, start_balance = ?, debit = 0, credit = 0
                 WHERE id = ?"
            );
            if ($stmt) {
                $stmt->bind_param('ddi', $bal, $bal, $accId);
                $stmt->execute();
                $stmt->close();
            }
        }

        // إعادة ضبط كميات الأصناف بعد أي أثر جانبي
        foreach ($stockMap as $itemId => $info) {
            $qty = $info['qty'];
            $stmt = $newConn->prepare("UPDATE `$newDb`.myitems SET itmqty = ? WHERE id = ?");
            if ($stmt) {
                $stmt->bind_param('di', $qty, $itemId);
                $stmt->execute();
                $stmt->close();
            }
        }

        return [
            'accounts' => $accountsCount,
            'items' => $itemsCount,
        ];
    }

    private function ensurePeriodMeta(
        mysqli $conn,
        string $dbName,
        string $label,
        ?string $parent,
        bool $isClosed = false
    ): void {
        $conn->query(
            "CREATE TABLE IF NOT EXISTS `$dbName`.period_meta (
                id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                db_name VARCHAR(64) NOT NULL,
                label VARCHAR(120) NOT NULL,
                parent_db VARCHAR(64) DEFAULT NULL,
                is_closed TINYINT(1) NOT NULL DEFAULT 0,
                closed_at DATETIME DEFAULT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY uq_db_name (db_name)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        );

        $closed = $isClosed ? 1 : 0;
        $closedAt = $isClosed ? "'" . date('Y-m-d H:i:s') . "'" : 'NULL';
        $parentSql = $parent ? "'" . $conn->real_escape_string($parent) . "'" : 'NULL';
        $labelEsc = $conn->real_escape_string($label);
        $dbEsc = $conn->real_escape_string($dbName);

        $conn->query(
            "INSERT INTO `$dbName`.period_meta (db_name, label, parent_db, is_closed, closed_at)
             VALUES ('$dbEsc', '$labelEsc', $parentSql, $closed, $closedAt)
             ON DUPLICATE KEY UPDATE label = VALUES(label), parent_db = VALUES(parent_db),
               is_closed = VALUES(is_closed), closed_at = VALUES(closed_at)"
        );
    }

    private function markSourceClosed(mysqli $admin, string $sourceDb, string $successor): void
    {
        $admin->query(
            "CREATE TABLE IF NOT EXISTS `$sourceDb`.period_meta (
                id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                db_name VARCHAR(64) NOT NULL,
                label VARCHAR(120) NOT NULL,
                parent_db VARCHAR(64) DEFAULT NULL,
                is_closed TINYINT(1) NOT NULL DEFAULT 0,
                closed_at DATETIME DEFAULT NULL,
                successor_db VARCHAR(64) DEFAULT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY uq_db_name (db_name)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        );

        // قد لا يوجد عمود successor — نضيفه بأمان
        try {
            $admin->query("ALTER TABLE `$sourceDb`.period_meta ADD COLUMN successor_db VARCHAR(64) DEFAULT NULL");
        } catch (Throwable $e) {
            // ignore duplicate
        }

        $now = date('Y-m-d H:i:s');
        $src = $admin->real_escape_string($sourceDb);
        $suc = $admin->real_escape_string($successor);
        $admin->query(
            "INSERT INTO `$sourceDb`.period_meta (db_name, label, is_closed, closed_at, successor_db)
             VALUES ('$src', '$src', 1, '$now', '$suc')
             ON DUPLICATE KEY UPDATE is_closed = 1, closed_at = '$now', successor_db = '$suc'"
        );
    }

    // ─── Helpers ────────────────────────────────────────────────

    public function sanitizeDbName(string $name): string
    {
        require_once __DIR__ . '/db_name.php';
        return kody_validate_dbname($name);
    }

    public function databaseExists(string $dbName): bool
    {
        $dbName = $this->sanitizeDbName($dbName);
        $stmt = $this->conn->prepare(
            'SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?'
        );
        if (!$stmt) {
            $admin = $this->adminConnection();
            $res = $admin->query("SHOW DATABASES LIKE '" . $admin->real_escape_string($dbName) . "'");
            return $res && $res->num_rows > 0;
        }
        $stmt->bind_param('s', $dbName);
        $stmt->execute();
        $res = $stmt->get_result();
        $ok = $res && $res->num_rows > 0;
        $stmt->close();
        return $ok;
    }

    private function adminConnection(): mysqli
    {
        $c = @new mysqli($this->dbHost, $this->dbUser, $this->dbPass);
        if ($c->connect_error) {
            throw new RuntimeException('فشل الاتصال بـ MySQL: ' . $c->connect_error);
        }
        $c->set_charset('utf8mb4');
        return $c;
    }

    /** @return list<string> */
    private function listTables(mysqli $conn, string $dbName): array
    {
        $tables = [];
        $res = $conn->query("SHOW TABLES FROM `$dbName`");
        if ($res) {
            while ($row = $res->fetch_row()) {
                $tables[] = $row[0];
            }
        }
        return $tables;
    }

    private function updateEnvDbName(string $dbName): void
    {
        $envPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . '.env';
        if (!is_writable($envPath) && !file_exists($envPath)) {
            // أنشئ .env بسيط
            @file_put_contents($envPath, "DB_HOST={$this->dbHost}\nDB_USER={$this->dbUser}\nDB_PASS={$this->dbPass}\nDB_NAME={$dbName}\n");
            $_ENV['DB_NAME'] = $dbName;
            putenv("DB_NAME=$dbName");
            return;
        }
        if (!is_readable($envPath)) {
            $_ENV['DB_NAME'] = $dbName;
            putenv("DB_NAME=$dbName");
            return;
        }

        $content = file_get_contents($envPath);
        if ($content === false) {
            return;
        }
        if (preg_match('/^DB_NAME\s*=.*$/m', $content)) {
            $content = preg_replace('/^DB_NAME\s*=.*$/m', 'DB_NAME=' . $dbName, $content, 1);
        } else {
            $content = rtrim($content) . "\nDB_NAME=" . $dbName . "\n";
        }
        @file_put_contents($envPath, $content);
        $_ENV['DB_NAME'] = $dbName;
        putenv("DB_NAME=$dbName");
    }

    private function resolveSchemaFile(): string
    {
        $candidates = [
            dirname(__DIR__) . '/db/DB.sql',
            dirname(__DIR__) . '/db/db.sql',
            dirname(__DIR__) . '/backup/DB.sql',
        ];
        foreach ($candidates as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }
        throw new RuntimeException('ملف الهيكل db/DB.sql غير موجود');
    }

    public function parseSqlStatements(string $filePath): array
    {
        if (!file_exists($filePath)) {
            return ['error' => 'ملف SQL غير موجود: ' . basename($filePath)];
        }

        $lines = file($filePath);
        if ($lines === false) {
            return ['error' => 'فشل في قراءة ملف SQL'];
        }

        $statements = [];
        $query = '';
        $delimiter = ';';
        $inMultiLineComment = false;

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '') {
                continue;
            }

            if (!$inMultiLineComment && str_starts_with($trimmed, '/*')) {
                if (!str_contains($trimmed, '*/')) {
                    $inMultiLineComment = true;
                }
                continue;
            }
            if ($inMultiLineComment) {
                if (str_contains($trimmed, '*/')) {
                    $inMultiLineComment = false;
                }
                continue;
            }

            if (str_starts_with($trimmed, '--') || str_starts_with($trimmed, '#')) {
                continue;
            }

            if (preg_match('/^DELIMITER\s+(.+)$/i', $trimmed, $matches)) {
                $delimiter = trim($matches[1]);
                continue;
            }

            $query .= $line . ' ';

            if (str_ends_with($trimmed, $delimiter)) {
                $execQuery = substr(trim($query), 0, -strlen($delimiter));
                $execQuery = trim($execQuery);
                if ($execQuery !== '') {
                    $statements[] = $execQuery;
                }
                $query = '';
            }
        }

        return $statements;
    }

    public function normalizeBackupStatement(string $sql): ?string
    {
        $sql = trim($sql);
        if ($sql === '') {
            return null;
        }

        if (preg_match('/^(USE|CREATE\s+DATABASE)\b/i', $sql)) {
            return null;
        }

        if (preg_match('/^DROP\s+TABLE\s+(?!IF\s+EXISTS)/i', $sql)) {
            $sql = preg_replace('/^DROP\s+TABLE\s+/i', 'DROP TABLE IF EXISTS ', $sql, 1);
        }

        if (preg_match('/^CREATE\s+TABLE\s+(?!IF\s+NOT\s+EXISTS)/i', $sql)) {
            $sql = preg_replace('/^CREATE\s+TABLE\s+/i', 'CREATE TABLE IF NOT EXISTS ', $sql, 1);
        }

        if (preg_match('/^CREATE\s+TABLE\b/i', $sql)) {
            $sql = preg_replace(
                '/UNIQUE\s+KEY\s+`?receipt_number`?\s*\(\s*`?receipt_number`?\s*\)/i',
                'KEY `receipt_number` (`receipt_number`)',
                $sql
            );
        }

        return $sql;
    }

    public function executeSqlFile(mysqli $conn, string $filePath, bool $continueOnError = false): array
    {
        $parsed = $this->parseSqlStatements($filePath);
        if (isset($parsed['error'])) {
            return ['success' => false, 'message' => $parsed['error']];
        }

        $skipped = 0;
        $errors = [];
        $errorGroups = [];

        foreach ($parsed as $execQuery) {
            if ($continueOnError) {
                $normalized = $this->normalizeBackupStatement($execQuery);
                if ($normalized === null) {
                    continue;
                }
                $execQuery = $normalized;
            }

            if (!$conn->query($execQuery)) {
                $errno = (int) $conn->errno;
                $error = (string) $conn->error;
                $sample = substr($execQuery, 0, 220);
                $errDetail = [
                    'errno' => $errno,
                    'error' => $error,
                    'sql'   => $sample,
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
                    'errors'  => [$errDetail],
                ];
            }
        }

        $msg = $continueOnError
            ? 'تم استعادة النسخة الاحتياطية بنجاح'
            : 'تم تهيئة قاعدة البيانات بنجاح';

        if ($continueOnError && $skipped > 0) {
            $msg .= "\n\nتم تجاهل {$skipped} استعلام فاشل.";
        }

        return [
            'success'      => true,
            'message'      => $msg,
            'skipped'      => $skipped,
            'errors'       => $errors,
            'error_groups' => array_values($errorGroups),
        ];
    }

    public function ensureMissingTablesFromSchema(mysqli $conn, string $schemaFile): array
    {
        $parsed = $this->parseSqlStatements($schemaFile);
        if (isset($parsed['error'])) {
            return ['success' => false, 'message' => $parsed['error'], 'added' => []];
        }

        $existing = [];
        $res = $conn->query('SHOW TABLES');
        if ($res) {
            while ($row = $res->fetch_row()) {
                $existing[strtolower($row[0])] = true;
            }
        }

        $added = [];
        $addedLookup = [];

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
                'added'   => [],
            ];
        }

        foreach ($parsed as $sql) {
            if (preg_match('/^ALTER\s+TABLE\s+`?([a-zA-Z0-9_]+)`?/i', $sql, $m)) {
                if (isset($addedLookup[strtolower($m[1])])) {
                    $conn->query($sql);
                }
                continue;
            }
            if (preg_match('/^INSERT\s+INTO\s+`?([a-zA-Z0-9_]+)`?/i', $sql, $m)) {
                if (isset($addedLookup[strtolower($m[1])])) {
                    $conn->query($sql);
                }
            }
        }

        return [
            'success' => true,
            'message' => 'تم إضافة ' . count($added) . ' جدول ناقص من الهيكل الأساسي',
            'added'   => $added,
        ];
    }
}
