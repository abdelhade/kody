<?php
// Use AJAX header (no HTML output)
include(__DIR__ . '/../includes/ajax_header.php');

// Set content type to JSON
header('Content-Type: application/json; charset=utf-8');

// Check if order ID is provided
if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    echo json_encode(['success' => false, 'message' => 'معرف الطلب غير صالح']);
    exit;
}

$orderId = intval($_POST['id']);

try {
    // Start transaction
    $conn->begin_transaction();
    
    // 1. Get order details first (for logging or verification)
    $query = "SELECT * FROM ot_head WHERE id = ? AND isdeleted = 0";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $orderId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('الطلب غير موجود أو تم حذفه مسبقاً');
    }
    
    $order = $result->fetch_assoc();
    
    // 2. Mark order as deleted (soft delete)
    $updateQuery = "UPDATE ot_head SET isdeleted = 1 WHERE id = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param('i', $orderId);
    
    if (!$updateStmt->execute()) {
        throw new Exception('فشل في حذف الطلب');
    }
    
    // 3. If there's a table associated with this order, mark it as available
    if (!empty($order['table_id'])) {
        $tableUpdate = "UPDATE tables SET is_occupied = 0 WHERE id = ?";
        $tableStmt = $conn->prepare($tableUpdate);
        $tableStmt->bind_param('i', $order['table_id']);
        $tableStmt->execute();
    }
    
    // 4. Log the deletion (optional - skip if activity_log table doesn't exist)
    $checkTable = $conn->query("SHOW TABLES LIKE 'activity_log'");
    if ($checkTable && $checkTable->num_rows > 0) {
        $logQuery = "INSERT INTO activity_log (user_id, action, details) VALUES (?, 'delete_order', ?)";
        $logStmt = $conn->prepare($logQuery);
        $details = "تم حذف الطلب رقم: " . ($order['pro_id'] ?: $order['id']);
        $logStmt->bind_param('is', $userid, $details);
        $logStmt->execute();
    }
    
    // Commit transaction
    $conn->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'تم حذف الطلب بنجاح'
    ]);
    
} catch (Exception $e) {
    // Rollback transaction on error
    if (isset($conn)) {
        $conn->rollback();
    }
    
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

// Close the database connection
if (isset($conn)) {
    $conn->close();
}

