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
    $field = $_POST['field'] ?? 'price1';
    $allowed = ['price1' => 'price1', 'price2' => 'price2', 'price3' => 'price3'];
    if (!isset($allowed[$field])) {
        $field = 'price1';
    }
    $column = $allowed[$field];

    if ($column === 'price3') {
        $stmt = $conn->prepare('UPDATE myitems SET price3 = ?, market_price = ?, manual_price_edit = 1 WHERE id = ?');
    } else {
        $stmt = $conn->prepare("UPDATE myitems SET {$column} = ?, manual_price_edit = 1 WHERE id = ?");
    }
    
    // Check if item_units needs update as well (optional, for basic unit)
    $stmtUnit = $conn->prepare("UPDATE item_units SET {$column} = ? WHERE item_id = ? AND u_val = 1");

    foreach ($items as $item) {
        $id = (int)$item['id'];
        $price = isset($item['price']) ? (float)$item['price'] : (float)($item['price1'] ?? 0);

        if ($id > 0) {
            if ($column === 'price3') {
                $stmt->bind_param('ddi', $price, $price, $id);
            } else {
                $stmt->bind_param('di', $price, $id);
            }
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
