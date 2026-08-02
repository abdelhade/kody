<?php
session_start();
include('../includes/connect.php');
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['userid'])) {
    echo json_encode(['success' => false, 'error' => 'غير مصرح']);
    exit;
}

$fat_id = isset($_POST['fat_id']) ? intval($_POST['fat_id']) : 0;
if ($fat_id <= 0) {
    echo json_encode(['success' => false, 'error' => 'معرف الصنف غير صحيح']);
    exit;
}

// جلب pro_id لتحديث إجمالي الأوردر بعد الحذف
$stmt = $conn->prepare("SELECT pro_id, det_value FROM fat_details WHERE id = ? AND isdeleted = 0");
$stmt->bind_param("i", $fat_id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$row) {
    echo json_encode(['success' => false, 'error' => 'الصنف غير موجود']);
    exit;
}

$pro_id = intval($row['pro_id']);

// حذف الصنف (soft delete)
$stmt = $conn->prepare("UPDATE fat_details SET isdeleted = 1 WHERE id = ?");
$stmt->bind_param("i", $fat_id);
$ok = $stmt->execute();
$stmt->close();

if (!$ok) {
    echo json_encode(['success' => false, 'error' => 'فشل الحذف: ' . $conn->error]);
    exit;
}

// إعادة حساب إجمالي الأوردر
$stmt = $conn->prepare(
    "UPDATE ot_head SET 
        fat_total = (SELECT COALESCE(SUM(det_value),0) FROM fat_details WHERE pro_id = ? AND isdeleted = 0),
        fat_net   = (SELECT COALESCE(SUM(det_value),0) FROM fat_details WHERE pro_id = ? AND isdeleted = 0)
     WHERE id = ?"
);
$stmt->bind_param("iii", $pro_id, $pro_id, $pro_id);
$stmt->execute();
$stmt->close();

// جلب الإجمالي الجديد
$stmt = $conn->prepare("SELECT fat_net FROM ot_head WHERE id = ?");
$stmt->bind_param("i", $pro_id);
$stmt->execute();
$new_total = $stmt->get_result()->fetch_assoc()['fat_net'] ?? 0;
$stmt->close();

echo json_encode([
    'success'   => true,
    'new_total' => floatval($new_total)
]);
