<?php
include('../includes/connect.php');

header('Content-Type: application/json');

$order_id = $_POST['order_id'] ?? '';
$old_table_id = $_POST['old_table_id'] ?? '';
$new_table_id = $_POST['new_table_id'] ?? '';
$new_table_name = $_POST['new_table_name'] ?? '';

if (empty($order_id) || empty($old_table_id) || empty($new_table_id) || empty($new_table_name)) {
    echo json_encode(['success' => false, 'message' => 'بيانات ناقصة']);
    exit;
}

try {
    // بدء المعاملة
    $conn->begin_transaction();
    
    // تحديث الطاولة في الطلب
    $sql = "UPDATE fat_det SET table_id = ?, table_name = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('isi', $new_table_id, $new_table_name, $order_id);
    $stmt->execute();
    
    // تحديث حالة الطاولة القديمة (تصبح متاحة)
    $sql = "UPDATE tables SET table_case = 0 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $old_table_id);
    $stmt->execute();
    
    // تحديث حالة الطاولة الجديدة (تصبح مشغولة)
    $sql = "UPDATE tables SET table_case = 1 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $new_table_id);
    $stmt->execute();
    
    // تأكيد المعاملة
    $conn->commit();
    
    echo json_encode(['success' => true, 'message' => 'تم تحديث الطاولة بنجاح']);
    
} catch (Exception $e) {
    // تراجع عن المعاملة في حالة الخطأ
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'خطأ: ' . $e->getMessage()]);
}
?>
