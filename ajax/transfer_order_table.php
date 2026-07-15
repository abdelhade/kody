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
    
    // جلب اسم الطاولة القديمة
    $old_table_query = "SELECT tname FROM tables WHERE id = ?";
    $stmt = $conn->prepare($old_table_query);
    $stmt->bind_param('i', $old_table_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $old_table_data = $result->fetch_assoc();
    $old_table_name = $old_table_data['tname'] ?? '';
    
    // تحديث الطلب - استبدال اسم الطاولة القديمة بالجديدة في حقل info
    $info_update_query = "UPDATE ot_head SET info = REPLACE(info, ?, ?) WHERE id = ?";
    $stmt = $conn->prepare($info_update_query);
    $stmt->bind_param('ssi', $old_table_name, $new_table_name, $order_id);
    $stmt->execute();
    
    // تحديث حالة الطاولة القديمة (تصبح متاحة)
    $update_old_table = "UPDATE tables SET table_case = 0 WHERE id = ?";
    $stmt = $conn->prepare($update_old_table);
    $stmt->bind_param('i', $old_table_id);
    $old_affected = $stmt->execute();
    
    // تحديث حالة الطاولة الجديدة (تصبح مشغولة)
    $update_new_table = "UPDATE tables SET table_case = 1 WHERE id = ?";
    $stmt = $conn->prepare($update_new_table);
    $stmt->bind_param('i', $new_table_id);
    $new_affected = $stmt->execute();
    
    // تأكيد المعاملة
    $conn->commit();
    
    echo json_encode([
        'success' => true, 
        'message' => 'تم نقل الطلب للطاولة الجديدة بنجاح',
        'old_table_updated' => $old_affected,
        'new_table_updated' => $new_affected
    ]);
    
} catch (Exception $e) {
    // تراجع عن المعاملة في حالة الخطأ
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'خطأ: ' . $e->getMessage()]);
}
?>
