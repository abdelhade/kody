<?php
session_start();
include('../includes/connect.php');
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['userid'])) {
    echo json_encode(['success' => false, 'message' => 'غير مصرح']);
    exit;
}

$order_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
if ($order_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'معرف الطلب غير صحيح']);
    exit;
}

// التحقق من وجود الطلب
$stmt = $conn->prepare("SELECT id FROM ot_head WHERE id = ? AND isdeleted = 0");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$exists = $stmt->get_result()->num_rows > 0;
$stmt->close();

if (!$exists) {
    echo json_encode(['success' => false, 'message' => 'الطلب غير موجود أو محذوف مسبقاً']);
    exit;
}

$conn->begin_transaction();
try {
    // حذف تفاصيل الطلب
    $stmt = $conn->prepare("UPDATE fat_details SET isdeleted = 1 WHERE pro_id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $stmt->close();

    // حذف رأس الطلب
    $stmt = $conn->prepare("UPDATE ot_head SET isdeleted = 1 WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $stmt->close();

    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'تم حذف الطلب بنجاح']);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
