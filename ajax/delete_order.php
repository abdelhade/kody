<?php
session_start();
include('../includes/connect.php');

header('Content-Type: application/json');

try {
    $orderId = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
    $tableId = isset($_POST['table_id']) ? intval($_POST['table_id']) : 0;
    
    if (!$orderId) {
        throw new Exception('معرف الطلب غير صحيح');
    }
    
    $conn->begin_transaction();
    
    // حذف الأصناف
    $deleteItems = "UPDATE fat_details SET isdeleted = 1 WHERE pro_id = ?";
    $stmt = $conn->prepare($deleteItems);
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    
    // حذف الطلب (soft delete)
    // يمكن أيضاً استخدام حقل خاص لتمييز الطلبات المحذوفة
    
    // تحديث حالة الطاولة (تفريغها)
    if ($tableId > 0) {
        $updateTable = "UPDATE tables SET table_case = 0 WHERE id = ?";
        $stmt = $conn->prepare($updateTable);
        $stmt->bind_param("i", $tableId);
        $stmt->execute();
    }
    
    $conn->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'تم حذف الطلب بنجاح'
    ]);
    
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

