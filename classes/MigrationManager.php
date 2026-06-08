<?php
/**
 * MigrationManager Class
 * Purpose: إدارة عمليات تهيئة وتحديث قاعدة البيانات وتتبعها
 * Date: 2026-06-09
 */

class MigrationManager {
    private $conn;
    private $migrationsDir;

    /**
     * Constructor
     * 
     * @param mysqli $conn Database connection
     * @param string $migrationsDir Path to migrations folder
     */
    public function __construct($conn, $migrationsDir = __DIR__ . '/../database/migrations') {
        $this->conn = $conn;
        $this->migrationsDir = rtrim($migrationsDir, '/\\');
        
        // Ensure migrations directory exists
        if (!is_dir($this->migrationsDir)) {
            mkdir($this->migrationsDir, 0755, true);
        }
        
        $this->initMigrationsTable();
    }

    /**
     * Initialize migrations tracking table if not exists
     */
    private function initMigrationsTable() {
        $sql = "CREATE TABLE IF NOT EXISTS `migrations` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `migration` VARCHAR(255) NOT NULL,
            `batch` INT NOT NULL,
            `applied_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
        
        if (!$this->conn->query($sql)) {
            throw new Exception("Failed to initialize migrations table: " . $this->conn->error);
        }
    }

    /**
     * Get list of applied migrations from DB
     */
    public function getAppliedMigrations() {
        $result = $this->conn->query("SELECT * FROM migrations ORDER BY id ASC");
        if (!$result) {
            return [];
        }
        $applied = [];
        while ($row = $result->fetch_assoc()) {
            $applied[$row['migration']] = $row;
        }
        return $applied;
    }

    /**
     * Scan migrations directory for .php and .sql files
     */
    public function getAllMigrationFiles() {
        $files = glob($this->migrationsDir . '/*.{php,sql}', GLOB_BRACE);
        if ($files === false) {
            return [];
        }
        $migrations = [];
        foreach ($files as $file) {
            $base = basename($file);
            // Skip rollback files (e.g. *.down.sql or *.rollback.sql)
            if (strpos($base, '.down.sql') !== false || strpos($base, '.rollback.sql') !== false) {
                continue;
            }
            $migrations[] = $base;
        }
        sort($migrations);
        return $migrations;
    }

    /**
     * Get the status of all migrations (both pending and applied)
     */
    public function getMigrationStatus() {
        $files = $this->getAllMigrationFiles();
        $applied = $this->getAppliedMigrations();
        $status = [];
        foreach ($files as $file) {
            $isApplied = isset($applied[$file]);
            $status[] = [
                'file' => $file,
                'applied' => $isApplied,
                'batch' => $isApplied ? (int)$applied[$file]['batch'] : null,
                'applied_at' => $isApplied ? $applied[$file]['applied_at'] : null
            ];
        }
        return $status;
    }

    /**
     * Get the next batch number
     */
    public function getNextBatchNumber() {
        $result = $this->conn->query("SELECT MAX(batch) as max_batch FROM migrations");
        if (!$result) {
            return 1;
        }
        $row = $result->fetch_assoc();
        return ((int)($row['max_batch'] ?? 0)) + 1;
    }

    /**
     * Execute SQL migration file statement by statement
     */
    private function executeSqlFile($filePath) {
        $sql = file_get_contents($filePath);
        if ($sql === false) {
            throw new Exception("Unable to read migration file: $filePath");
        }

        // Remove SQL comments
        $sql = preg_replace('/--.*$/m', '', $sql);
        $sql = preg_replace('/^\/\*.*\*\/$/m', '', $sql);
        
        // Split queries by semicolon (;)
        $queries = array_filter(array_map('trim', explode(';', $sql)));
        
        foreach ($queries as $query) {
            if (empty($query)) {
                continue;
            }
            
            try {
                if (!$this->conn->query($query)) {
                    $error_msg = $this->conn->error;
                    // Ignore common DDL errors that signify duplicate columns or tables if they already exist,
                    // but fail on major syntax/constraint issues.
                    if (strpos($error_msg, 'already exists') === false && 
                        strpos($error_msg, 'Duplicate column name') === false &&
                        strpos($error_msg, 'Duplicate key name') === false) {
                        throw new Exception("Query Failed: " . $error_msg . "\nQuery: " . $query);
                    }
                }
            } catch (Exception $e) {
                // If it's a duplicate column/key/table error, ignore it. Otherwise throw.
                $msg = $e->getMessage();
                if (strpos($msg, 'already exists') === false && 
                    strpos($msg, 'Duplicate column name') === false &&
                    strpos($msg, 'Duplicate key name') === false) {
                    throw $e;
                }
            }
        }
    }

    /**
     * Run all pending migrations
     */
    public function runPending() {
        $status = $this->getMigrationStatus();
        $pending = array_filter($status, function($m) { return !$m['applied']; });
        
        if (empty($pending)) {
            return [
                'success' => true,
                'count' => 0,
                'executed' => [],
                'logs' => ['No pending migrations found. Database is up to date.']
            ];
        }

        $batch = $this->getNextBatchNumber();
        $executed = [];
        $logs = [];

        foreach ($pending as $m) {
            $file = $m['file'];
            $filePath = $this->migrationsDir . '/' . $file;
            $logs[] = "Running migration: $file ...";
            
            try {
                $ext = pathinfo($file, PATHINFO_EXTENSION);
                if ($ext === 'sql') {
                    $this->executeSqlFile($filePath);
                } else if ($ext === 'php') {
                    // Load the migration anonymous class
                    $migrationObj = require $filePath;
                    if (is_object($migrationObj) && method_exists($migrationObj, 'up')) {
                        $migrationObj->up($this->conn);
                    } else {
                        throw new Exception("PHP migration file must return an object with an 'up(\$conn)' method.");
                    }
                }
                
                // Record execution
                $stmt = $this->conn->prepare("INSERT INTO migrations (migration, batch) VALUES (?, ?)");
                if (!$stmt) {
                    throw new Exception("Failed to prepare statement: " . $this->conn->error);
                }
                $stmt->bind_param('si', $file, $batch);
                if (!$stmt->execute()) {
                    throw new Exception("Failed to log migration: " . $stmt->error);
                }
                $stmt->close();
                
                $executed[] = $file;
                $logs[] = "✅ Migration $file completed successfully.";
            } catch (Exception $e) {
                $logs[] = "❌ Failed running migration ($file): " . $e->getMessage();
                return [
                    'success' => false,
                    'count' => count($executed),
                    'executed' => $executed,
                    'logs' => $logs,
                    'error' => $e->getMessage()
                ];
            }
        }

        return [
            'success' => true,
            'count' => count($executed),
            'executed' => $executed,
            'batch' => $batch,
            'logs' => $logs
        ];
    }

    /**
     * Rollback the latest batch of migrations
     */
    public function rollback() {
        // Find maximum batch
        $result = $this->conn->query("SELECT MAX(batch) as max_batch FROM migrations");
        if (!$result) {
            return ['success' => true, 'count' => 0, 'rolled_back' => [], 'logs' => ['No migrations table or batch history found.']];
        }
        $row = $result->fetch_assoc();
        $lastBatch = isset($row['max_batch']) ? (int)$row['max_batch'] : 0;
        
        if ($lastBatch === 0) {
            return [
                'success' => true,
                'count' => 0,
                'rolled_back' => [],
                'logs' => ['No migrations to rollback.']
            ];
        }
        
        // Get migrations in last batch, in reverse execution order
        $stmt = $this->conn->prepare("SELECT * FROM migrations WHERE batch = ? ORDER BY id DESC");
        if (!$stmt) {
            throw new Exception("Failed to prepare rollback query: " . $this->conn->error);
        }
        $stmt->bind_param('i', $lastBatch);
        $stmt->execute();
        $batchMigrations = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        
        if (empty($batchMigrations)) {
            return [
                'success' => true,
                'count' => 0,
                'rolled_back' => [],
                'logs' => ["Batch $lastBatch has no registered migrations."]
            ];
        }

        $rolledBack = [];
        $logs = [];
        
        foreach ($batchMigrations as $m) {
            $file = $m['migration'];
            $filePath = $this->migrationsDir . '/' . $file;
            $logs[] = "Rolling back migration: $file ...";
            
            try {
                $ext = pathinfo($file, PATHINFO_EXTENSION);
                if ($ext === 'sql') {
                    // Look for companion .down.sql or .rollback.sql
                    $downFile = str_replace('.sql', '.down.sql', $filePath);
                    if (!file_exists($downFile)) {
                        $downFile = str_replace('.sql', '.rollback.sql', $filePath);
                    }
                    
                    if (file_exists($downFile)) {
                        $logs[] = "Executing rollback SQL file: " . basename($downFile);
                        $this->executeSqlFile($downFile);
                    } else {
                        $logs[] = "⚠️ Warning: No companion .down.sql file found for $file. SQL statements will not be automatically reversed.";
                    }
                } else if ($ext === 'php') {
                    if (file_exists($filePath)) {
                        $migrationObj = require $filePath;
                        if (is_object($migrationObj) && method_exists($migrationObj, 'down')) {
                            $migrationObj->down($this->conn);
                        } else {
                            $logs[] = "⚠️ Warning: PHP migration class does not contain a 'down(\$conn)' method.";
                        }
                    } else {
                        $logs[] = "⚠️ Warning: Migration file $file not found on disk. Deleting reference anyway.";
                    }
                }
                
                // Remove record from migrations table
                $delStmt = $this->conn->prepare("DELETE FROM migrations WHERE id = ?");
                $delStmt->bind_param('i', $m['id']);
                $delStmt->execute();
                $delStmt->close();
                
                $rolledBack[] = $file;
                $logs[] = "✅ Rolled back: $file";
            } catch (Exception $e) {
                $logs[] = "❌ Failed rolling back ($file): " . $e->getMessage();
                return [
                    'success' => false,
                    'count' => count($rolledBack),
                    'rolled_back' => $rolledBack,
                    'logs' => $logs,
                    'error' => $e->getMessage()
                ];
            }
        }
        
        return [
            'success' => true,
            'count' => count($rolledBack),
            'rolled_back' => $rolledBack,
            'batch' => $lastBatch,
            'logs' => $logs
        ];
    }

    /**
     * Create a new migration file stub
     * 
     * @param string $name Short description of migration
     * @param string $type php or sql
     * @return string Filename of created migration
     */
    public function create($name, $type = 'php') {
        $type = strtolower($type) === 'sql' ? 'sql' : 'php';
        
        // Clean name
        $cleanName = preg_replace('/[^a-zA-Z0-9_]/', '_', strtolower($name));
        $cleanName = trim($cleanName, '_');
        if (empty($cleanName)) {
            $cleanName = "migration";
        }
        
        $timestamp = date('Y_m_d_His');
        $fileName = $timestamp . '_' . $cleanName . '.' . $type;
        $filePath = $this->migrationsDir . '/' . $fileName;
        
        if ($type === 'php') {
            $content = "<?php\n\nreturn new class {\n    /**\n     * Run the migrations.\n     *\n     * @param mysqli \$conn\n     */\n    public function up(\$conn) {\n        // \$conn->query(\"CREATE TABLE ...\");\n    }\n\n    /**\n     * Reverse the migrations.\n     *\n     * @param mysqli \$conn\n     */\n    public function down(\$conn) {\n        // \$conn->query(\"DROP TABLE ...\");\n    }\n};\n";
        } else {
            $content = "-- Migration: " . htmlspecialchars($name) . "\n-- Date: " . date('Y-m-d H:i:s') . "\n-- Run your migration SQL statements here. Separate queries with semicolons (;)\n\n";
            
            // Create a matching down.sql
            $downFileName = $timestamp . '_' . $cleanName . '.down.sql';
            $downFilePath = $this->migrationsDir . '/' . $downFileName;
            $downContent = "-- Rollback for migration: " . htmlspecialchars($name) . "\n-- Run your rollback SQL statements here\n\n";
            file_put_contents($downFilePath, $downContent);
        }
        
        if (file_put_contents($filePath, $content) === false) {
            throw new Exception("Unable to write migration file to path: $filePath");
        }
        
        return $fileName;
    }
}
