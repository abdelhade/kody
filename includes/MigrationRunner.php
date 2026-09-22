<?php

/**
 * MigrationRunner — runs numbered SQL files in update/ once each.
 * Tracks applied versions in schema_migrations.
 */
class MigrationRunner
{
    private mysqli $conn;
    private string $dir;

    public function __construct(mysqli $conn, ?string $migrationsDir = null)
    {
        $this->conn = $conn;
        $this->dir = $migrationsDir ?: (dirname(__DIR__) . DIRECTORY_SEPARATOR . 'update');
    }

    public function ensureTracker(): void
    {
        $this->conn->query(
            "CREATE TABLE IF NOT EXISTS `schema_migrations` (
                `version` VARCHAR(64) NOT NULL,
                `applied_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `checksum` VARCHAR(64) DEFAULT NULL,
                PRIMARY KEY (`version`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci"
        );
    }

    /**
     * @return list<array{version:string,file:string,label:string}>
     */
    public function discover(): array
    {
        $files = glob($this->dir . DIRECTORY_SEPARATOR . '[0-9][0-9][0-9]_*.sql') ?: [];
        sort($files, SORT_STRING);
        $list = [];
        foreach ($files as $path) {
            $base = basename($path);
            if (!preg_match('/^(\d{3}_.+)\.sql$/i', $base, $m)) {
                continue;
            }
            $list[] = [
                'version' => $m[1],
                'file' => $path,
                'label' => $base,
            ];
        }
        return $list;
    }

    /**
     * @return list<string>
     */
    public function appliedVersions(): array
    {
        $this->ensureTracker();
        $versions = [];
        $res = $this->conn->query('SELECT version FROM schema_migrations ORDER BY version');
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $versions[] = $row['version'];
            }
        }
        return $versions;
    }

    /**
     * @return list<array{version:string,file:string,label:string}>
     */
    public function pending(): array
    {
        $applied = array_flip($this->appliedVersions());
        $pending = [];
        foreach ($this->discover() as $m) {
            if (!isset($applied[$m['version']])) {
                $pending[] = $m;
            }
        }
        return $pending;
    }

    public function status(): array
    {
        $all = $this->discover();
        $pending = $this->pending();
        return [
            'total' => count($all),
            'applied' => count($all) - count($pending),
            'pending' => count($pending),
            'pending_list' => array_column($pending, 'label'),
            'latest_applied' => $this->latestApplied(),
        ];
    }

    public function latestApplied(): ?string
    {
        $applied = $this->appliedVersions();
        return $applied ? end($applied) : null;
    }

    /**
     * Run all pending migrations.
     *
     * @return array{success:bool,message:string,results:list<array>}
     */
    public function runPending(): array
    {
        $this->ensureTracker();
        $pending = $this->pending();
        if (empty($pending)) {
            return [
                'success' => true,
                'message' => 'قاعدة البيانات محدّثة — لا توجد migrations ناقصة',
                'results' => [],
            ];
        }

        $results = [];
        $failed = 0;

        foreach ($pending as $m) {
            $result = $this->runFile($m['version'], $m['file']);
            $results[] = $result;
            if (!$result['ok']) {
                $failed++;
                break; // stop on first hard failure
            }
        }

        $okCount = count(array_filter($results, fn($r) => $r['ok']));
        if ($failed > 0) {
            return [
                'success' => false,
                'message' => "توقف التحديث بعد {$okCount} ناجح وفشل واحد",
                'results' => $results,
            ];
        }

        return [
            'success' => true,
            'message' => "تم تطبيق {$okCount} تحديث بنجاح",
            'results' => $results,
        ];
    }

    /**
     * Mark all discovered migrations as applied without running them
     * (useful right after importing a full DB dump that already includes changes).
     */
    public function markAllApplied(): void
    {
        $this->ensureTracker();
        foreach ($this->discover() as $m) {
            $this->markApplied($m['version'], $m['file']);
        }
    }

    private function runFile(string $version, string $path): array
    {
        if (!is_readable($path)) {
            return [
                'ok' => false,
                'version' => $version,
                'skipped' => false,
                'error' => 'الملف غير قابل للقراءة',
            ];
        }

        $sql = file_get_contents($path);
        $checksum = md5($sql);
        $statements = $this->splitStatements($sql);
        $executed = 0;
        $skippedStmts = 0;

        try {
            foreach ($statements as $statement) {
                $r = $this->execStatement($statement);
                if (!$r['ok']) {
                    return [
                        'ok' => false,
                        'version' => $version,
                        'skipped' => false,
                        'error' => $r['error'],
                        'executed' => $executed,
                    ];
                }
                if ($r['skipped']) {
                    $skippedStmts++;
                } else {
                    $executed++;
                }
            }
            $this->markApplied($version, $path, $checksum);
            return [
                'ok' => true,
                'version' => $version,
                'skipped' => ($executed === 0 && $skippedStmts > 0),
                'error' => '',
                'executed' => $executed,
                'skipped_stmts' => $skippedStmts,
            ];
        } catch (Throwable $e) {
            return [
                'ok' => false,
                'version' => $version,
                'skipped' => false,
                'error' => $e->getMessage(),
                'executed' => $executed,
            ];
        }
    }

    private function markApplied(string $version, string $path, ?string $checksum = null): void
    {
        if ($checksum === null && is_readable($path)) {
            $checksum = md5(file_get_contents($path));
        }
        $stmt = $this->conn->prepare(
            'INSERT IGNORE INTO schema_migrations (version, checksum) VALUES (?, ?)'
        );
        if ($stmt) {
            $stmt->bind_param('ss', $version, $checksum);
            $stmt->execute();
            $stmt->close();
        }
    }

    /**
     * @return list<string>
     */
    private function splitStatements(string $sql): array
    {
        // Avoid preg_split('/\R/') — in non-unicode mode \R matches raw 0x85
        // which appears inside UTF-8 Arabic letters (e.g. م = D9 85).
        $normalized = str_replace(["\r\n", "\r"], "\n", $sql);
        $lines = explode("\n", $normalized);
        $buffer = '';
        $out = [];
        $inBlockComment = false;

        foreach ($lines as $line) {
            $trim = trim($line);
            if ($inBlockComment) {
                if (str_contains($trim, '*/')) {
                    $inBlockComment = false;
                }
                continue;
            }
            if ($trim === '' || str_starts_with($trim, '--') || str_starts_with($trim, '#')) {
                continue;
            }
            if (str_starts_with($trim, '/*')) {
                if (!str_contains($trim, '*/')) {
                    $inBlockComment = true;
                }
                continue;
            }
            $buffer .= $line . "\n";
            if (str_ends_with(rtrim($line), ';')) {
                $stmt = trim($buffer);
                // rtrim charlist must NOT use ranges; strip trailing semicolon only
                $stmt = preg_replace('/;\s*$/', '', $stmt) ?? $stmt;
                $stmt = trim($stmt);
                if ($stmt !== '') {
                    $out[] = $stmt;
                }
                $buffer = '';
            }
        }

        $tail = trim($buffer);
        if ($tail !== '') {
            $out[] = preg_replace('/;\s*$/', '', $tail) ?? $tail;
        }
        return $out;
    }

    private function execStatement(string $sql): array
    {
        try {
            if ($this->conn->query($sql)) {
                return ['ok' => true, 'skipped' => false, 'error' => ''];
            }
            $error = $this->conn->error;
        } catch (mysqli_sql_exception $e) {
            $error = $e->getMessage();
        }

        if ($this->isIgnorableError($error ?? '')) {
            return ['ok' => true, 'skipped' => true, 'error' => $error];
        }

        return ['ok' => false, 'skipped' => false, 'error' => $error ?? 'unknown'];
    }

    private function isIgnorableError(string $error): bool
    {
        $needles = [
            'Duplicate column',
            'already exists',
            'Duplicate key name',
            'check that it exists', // drop if not exists variants
        ];
        foreach ($needles as $n) {
            if (stripos($error, $n) !== false) {
                return true;
            }
        }
        return false;
    }
}
