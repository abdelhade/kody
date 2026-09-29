<?php
header('Content-Type: application/json; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['userid'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'غير مصرح', 'licensed' => false]);
    exit;
}

require_once __DIR__ . '/../includes/license.php';

$key = (string) ($_POST['license_key'] ?? '');
echo json_encode(kody_license_activate($key), JSON_UNESCAPED_UNICODE);
