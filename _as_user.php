<?php
// مؤقت للاختبار المحلي بس — هيتمسح
if (!in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true)) {
    http_response_code(404);
    exit;
}
session_start();
$_SESSION = ['userid' => (int) ($_GET['id'] ?? 0)];
require __DIR__ . '/includes/connect.php';
$_SESSION['login'] = 'test';
$perms = [];
foreach ($conn->query('SELECT name FROM permissions') as $r) {
    if (($_GET['mode'] ?? '') === 'all' || $r['name'] !== 'defaults.change') {
        $perms[$r['name']] = 'x';
    }
}
$_SESSION['auth']['perms'] = $perms;
echo 'ok ' . count($perms);
