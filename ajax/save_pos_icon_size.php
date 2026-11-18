<?php
include('../includes/connect.php');

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'message' => 'طريقة الطلب غير صحيحة'], JSON_UNESCAPED_UNICODE);
    exit;
}

$targets = [
    'barcode' => 'pos_icon_size_barcode',
    'clothes' => 'pos_icon_size_clothes',
];
$sizes = ['small', 'medium', 'large', 'xlarge'];
$target = (string) ($_POST['target'] ?? '');
$size = (string) ($_POST['size'] ?? '');

if (!isset($targets[$target]) || !in_array($size, $sizes, true)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'message' => 'حجم غير صحيح'], JSON_UNESCAPED_UNICODE);
    exit;
}

$column = $targets[$target];
try {
    $stmt = $conn->prepare("UPDATE settings SET `$column` = ?");
    $stmt->bind_param('s', $size);
    $stmt->execute();
    $stmt->close();
    echo json_encode(['ok' => true], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'ok' => false,
        'message' => 'شغّل تحديثات قاعدة البيانات من الإعدادات ثم أعد المحاولة',
    ], JSON_UNESCAPED_UNICODE);
}
