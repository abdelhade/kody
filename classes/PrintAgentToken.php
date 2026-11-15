<?php
/**
 * توكن قصير العمر لصفحات الطباعة من وكيل الطباعة (بدون سيشن متصفح).
 */
class PrintAgentToken
{
    public static function secret(): string
    {
        $dbname = function_exists('kody_preferred_dbname') ? (string) kody_preferred_dbname() : 'kody';
        $pass = (string) env('DB_PASS', '');
        $extra = (string) env('PRINT_AGENT_SECRET', 'kody-print-agent');
        return hash('sha256', $dbname . '|' . $pass . '|' . $extra);
    }

    public static function make(int $invoiceId, string $scope = 'print', int $ttlSeconds = 600): array
    {
        $exp = time() + max(60, $ttlSeconds);
        $payload = $invoiceId . '|' . $scope . '|' . $exp;
        $sig = hash_hmac('sha256', $payload, self::secret());
        return [
            'token' => $sig,
            'exp' => $exp,
            'scope' => $scope,
            'id' => $invoiceId,
        ];
    }

    public static function query(int $invoiceId, string $scope = 'print', int $ttlSeconds = 600): string
    {
        $t = self::make($invoiceId, $scope, $ttlSeconds);
        return 'agent_token=' . urlencode($t['token'])
            . '&agent_exp=' . (int) $t['exp']
            . '&agent_scope=' . urlencode($t['scope']);
    }

    public static function verify(?int $invoiceId = null): bool
    {
        $token = isset($_GET['agent_token']) ? (string) $_GET['agent_token'] : '';
        $exp = isset($_GET['agent_exp']) ? (int) $_GET['agent_exp'] : 0;
        $scope = isset($_GET['agent_scope']) ? (string) $_GET['agent_scope'] : 'print';
        $id = $invoiceId !== null
            ? (int) $invoiceId
            : (isset($_GET['id']) ? (int) $_GET['id'] : 0);

        if ($token === '' || $exp < time() || $id <= 0) {
            return false;
        }

        $payload = $id . '|' . $scope . '|' . $exp;
        $expected = hash_hmac('sha256', $payload, self::secret());
        return hash_equals($expected, $token);
    }
}
