<?php
ob_start();
session_start();
include('../includes/connect.php');
ob_end_clean();

header('Content-Type: application/json');

$table_id = intval($_POST['table_id']);
$paid = floatval($_POST['paid']);

if (!$table_id) {
    echo json_encode(['success' => false, 'message' => 'Table ID required']);
    exit;
}

try {
    // تحديث حالة الطاولة
    $update_table = "UPDATE tables SET table_case = 0 WHERE id = ?";
    $stmt = $conn->prepare($update_table);
    $stmt->bind_param("i", $table_id);
    $stmt->execute();
    
    echo json_encode(['success' => true, 'message' => 'Payment successful']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>