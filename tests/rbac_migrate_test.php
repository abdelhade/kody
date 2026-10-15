<?php
/**
 * تجربة تحديثات الصلاحيات على نسخة من قاعدة بيانات (CLI بس).
 *   php tests/rbac_migrate_test.php kody777_rbactest
 *
 * بيشغّل التحديثات الناقصة، وبعدين بيقارن صلاحيات كل دور في الجداول الجديدة
 * بالصلاحيات المحسوبة من usr_pwrs (RbacLegacy::permsFromRow) — لازم يطلعوا زي بعض.
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../includes/load_env.php';
require_once __DIR__ . '/../includes/MigrationRunner.php';
require_once __DIR__ . '/../classes/Can.php';
require_once __DIR__ . '/../tools/RbacLegacy.php';

$db = $argv[1] ?? '';
if ($db === '') {
    fwrite(STDERR, "usage: php tests/rbac_migrate_test.php <database>\n");
    exit(2);
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn = new mysqli(env('DB_HOST', 'localhost'), env('DB_USER', 'root'), env('DB_PASS', ''), $db);
$conn->set_charset('utf8mb4');

$runner = new MigrationRunner($conn);
$result = $runner->runPending();
echo $result['message'], PHP_EOL;
foreach ($result['results'] as $r) {
    printf("  %-40s %s %s\n", $r['version'], $r['ok'] ? 'ok' : 'FAIL', $r['error'] ?? '');
}
if (!$result['success']) {
    exit(1);
}

$count = static fn (string $sql): int => (int) $conn->query($sql)->fetch_row()[0];
printf("permissions=%d roles=%d grants=%d\n",
    $count('SELECT COUNT(*) FROM permissions'),
    $count('SELECT COUNT(*) FROM roles'),
    $count('SELECT COUNT(*) FROM role_has_permissions'));

$orphans = $count('SELECT COUNT(*) FROM users u LEFT JOIN roles r ON r.id = u.userrole WHERE u.userrole > 0 AND r.id IS NULL');
printf("users with a role missing from roles: %d\n", $orphans);

$catalogNames = array_column(RbacLegacy::flatten(), 'name');
$dbNames = array_column($conn->query("SELECT name FROM permissions WHERE guard_name = 'web'")->fetch_all(MYSQLI_ASSOC), 'name');
$missing = array_diff($catalogNames, $dbNames);
printf("catalog permissions missing from db: %d %s\n", count($missing), implode(',', array_slice($missing, 0, 5)));

$mismatch = 0;
$res = $conn->query('SELECT * FROM usr_pwrs');
while ($row = $res->fetch_assoc()) {
    $id = (int) $row['id'];
    $expected = array_keys(RbacLegacy::permsFromRow($row, $id));
    $stmt = $conn->prepare('SELECT p.name FROM role_has_permissions rhp JOIN permissions p ON p.id = rhp.permission_id WHERE rhp.role_id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $actual = array_column($stmt->get_result()->fetch_all(MYSQLI_ASSOC), 'name');
    $stmt->close();

    $onlyDb  = array_diff($actual, $expected);
    $onlyPhp = array_diff($expected, $actual);
    $ok = !$onlyDb && !$onlyPhp;
    $mismatch += $ok ? 0 : 1;
    printf("role %3d %-25s grants=%3d %s\n", $id, mb_substr((string) $row['rollname'], 0, 25), count($actual),
        $ok ? 'ok' : ('MISMATCH db-only=[' . implode(',', array_slice($onlyDb, 0, 6)) . '] php-only=[' . implode(',', array_slice($onlyPhp, 0, 6)) . ']'));
}

exit(($orphans || $missing || $mismatch) ? 1 : 0);
