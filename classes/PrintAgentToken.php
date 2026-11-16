<?php
/**
 * توكن قصير العمر لصفحات الطباعة من وكيل الطباعة (بدون سيشن متصفح).
 * اسم القاعدة جزء من التوقيع ويُمرَّر في الرابط (agent_db).
 */
class PrintAgentToken
{
    public static function secret(): string
    {
        $pass = (string) env('DB_PASS', '');
        $extra = (string) env('PRINT_AGENT_SECRET', 'kody-print-agent');
        return hash('sha256', $pass . '|' . $extra);
    }

    public static function normalizeDb(?string $dbName): string
    {
        $dbName = trim((string) $dbName);
        if ($dbName !== '' && preg_match('/^[A-Za-z0-9_]{2,64}$/', $dbName)) {
            return $dbName;
        }
        if (function_exists('kody_preferred_dbname')) {
            return (string) kody_preferred_dbname();
        }
        return 'kody2';
    }

    public static function make(
        int $invoiceId,
        string $scope = 'print',
        int $ttlSeconds = 600,
        ?string $dbName = null
    ): array {
        $db = self::normalizeDb($dbName);
        $exp = time() + max(60, $ttlSeconds);
        $payload = $invoiceId . '|' . $scope . '|' . $exp . '|' . $db;
        $sig = hash_hmac('sha256', $payload, self::secret());
        return [
            'token' => $sig,
            'exp' => $exp,
            'scope' => $scope,
            'id' => $invoiceId,
            'db' => $db,
        ];
    }

    public static function query(
        int $invoiceId,
        string $scope = 'print',
        int $ttlSeconds = 600,
        ?string $dbName = null
    ): string {
        $t = self::make($invoiceId, $scope, $ttlSeconds, $dbName);
        return 'agent_token=' . urlencode($t['token'])
            . '&agent_exp=' . (int) $t['exp']
            . '&agent_scope=' . urlencode($t['scope'])
            . '&agent_db=' . urlencode($t['db']);
    }

    public static function verify(?int $invoiceId = null): bool
    {
        $token = isset($_GET['agent_token']) ? (string) $_GET['agent_token'] : '';
        $exp = isset($_GET['agent_exp']) ? (int) $_GET['agent_exp'] : 0;
        $scope = isset($_GET['agent_scope']) ? (string) $_GET['agent_scope'] : 'print';
        $db = self::normalizeDb(isset($_GET['agent_db']) ? (string) $_GET['agent_db'] : '');
        $id = $invoiceId !== null
            ? (int) $invoiceId
            : (isset($_GET['id']) ? (int) $_GET['id'] : 0);

        if ($token === '' || $exp < time() || $id <= 0 || $db === '') {
            return false;
        }

        $payload = $id . '|' . $scope . '|' . $exp . '|' . $db;
        $expected = hash_hmac('sha256', $payload, self::secret());
        return hash_equals($expected, $token);
    }

    /** يفعّل قاعدة البيانات القادمة مع التوكن قبل connect.php */
    public static function applyForcedDb(): void
    {
        $db = isset($_GET['agent_db']) ? (string) $_GET['agent_db'] : '';
        if ($db === '' || !preg_match('/^[A-Za-z0-9_]{2,64}$/', $db)) {
            return;
        }
        if (!defined('KODY_FORCE_DBNAME')) {
            define('KODY_FORCE_DBNAME', $db);
        }
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION['active_dbname'] = $db;
        }
    }
}
