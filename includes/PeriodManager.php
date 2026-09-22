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

    public static function fromEnv(mysqli $conn): self
    {
        return new self(
            $conn,
            (string) env('DB_HOST', 'localhost'),
            (string) env('DB_USER', 'root'),
            (string) env('DB_PASS', ''),
            (string) (env('DB_NAME', 'kody2'))
        );
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
            throw new RuntimeException("القاعدة `$dbName` موجودة مسبقاً");
        }

        $admin = $this->adminConnection();
        $admin->query(
            "CREATE DATABASE `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci"
        );
        $admin->select_db($dbName);
        $admin->query('SET FOREIGN_KEY_CHECKS = 0');

        $schemaFile = $this->resolveSchemaFile();
        $import = $this->executeSqlFile($admin, $schemaFile);
        if (!$import['success']) {
            throw new RuntimeException($import['message']);
        }

        $admin->query('SET FOREIGN_KEY_CHECKS = 1');

        $runner = new MigrationRunner($admin);
        $runner->runPending();
        $this->ensurePeriodMeta($admin, $dbName, $label ?: $dbName, null);

        $reg = $this->getRegistry();
        $reg['databases'][] = [
            'name' => $dbName,
            'label' => $label !== '' ? $label : $dbName,
            'created_at' => date('Y-m-d H:i:s'),
            'closed_at' => null,
            'parent' => null,
        ];
        $this->saveRegistry($reg);

        return [
            'success' => true,
            'message' => "تم إنشاء القاعدة `$dbName` بنجاح",
            'database' => $dbName,
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
        $name = trim($name);
        if (!preg_match('/^[A-Za-z0-9_]{2,64}$/', $name)) {
            throw new InvalidArgumentException('اسم القاعدة غير صالح (حروف/أرقام/_ فقط، 2–64)');
        }
        return $name;
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

    private function executeSqlFile(mysqli $conn, string $filePath): array
    {
        if (!file_exists($filePath)) {
            return ['success' => false, 'message' => 'ملف SQL غير موجود'];
        }
        $lines = file($filePath);
        if ($lines === false) {
            return ['success' => false, 'message' => 'فشل قراءة ملف SQL'];
        }

        $query = '';
        $delimiter = ';';
        $inBlock = false;

        foreach ($lines as $line) {
            $trim = trim($line);
            if ($trim === '') {
                continue;
            }
            if (!$inBlock && str_starts_with($trim, '/*')) {
                if (!str_contains($trim, '*/')) {
                    $inBlock = true;
                }
                continue;
            }
            if ($inBlock) {
                if (str_contains($trim, '*/')) {
                    $inBlock = false;
                }
                continue;
            }
            if (str_starts_with($trim, '--') || str_starts_with($trim, '#')) {
                continue;
            }
            if (preg_match('/^DELIMITER\s+(.+)$/i', $trim, $m)) {
                $delimiter = trim($m[1]);
                continue;
            }
            $query .= $line . ' ';
            if (str_ends_with($trim, $delimiter)) {
                $exec = substr(trim($query), 0, -strlen($delimiter));
                if ($exec !== '' && !$conn->query($exec)) {
                    return [
                        'success' => false,
                        'message' => 'خطأ SQL: ' . $conn->error,
                    ];
                }
                $query = '';
            }
        }

        return ['success' => true, 'message' => 'تم الاستيراد'];
    }
}
