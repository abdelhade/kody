<?php
session_start();
include('../../includes/connect.php');

if (!isset($_SESSION['userid'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['items'])) {
    $items = $_POST['items'];
    $updatedCount = 0;

    $stmt = $conn->prepare("UPDATE myitems SET price1 = ?, manual_price_edit = 1 WHERE id = ?");
    
    // Check if item_units needs update as well (optional, for basic unit)
    $stmtUnit = $conn->prepare("UPDATE item_units SET price1 = ? WHERE item_id = ? AND u_val = 1");

    foreach ($items as $item) {
        $id = (int)$item['id'];
        $price = (float)$item['price1'];

        if ($id > 0) {
            $stmt->bind_param('di', $price, $id);
            if ($stmt->execute()) {
                $updatedCount++;
                
                // Update basic unit price if exists
                $stmtUnit->bind_param('di', $price, $id);
                $stmtUnit->execute();
            }
        }
    }

    $stmt->close();
    $stmtUnit->close();

    echo json_encode(['status' => 'success', 'updated_count' => $updatedCount]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'No items provided']);
}
?>
