<?php
// منع أي output قبل JSON
ob_start();

include('../includes/connect.php');

// مسح أي output buffer
ob_end_clean();

// تأكد من JSON header
header('Content-Type: application/json; charset=utf-8');

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

try {
    if (!$conn) {
        throw new Exception('Database connection failed');
    }
    
    $store_id = isset($_GET['store_id']) ? intval($_GET['store_id']) : 0;
    $balance_subquery = $store_id > 0 ? "COALESCE((SELECT SUM(qty_in - qty_out) FROM fat_details WHERE item_id = myitems.id AND det_store = $store_id AND isdeleted = 0), 0)" : "0";

    if (empty($search)) {
        $query = "SELECT id, iname as name, price1 as price, barcode, $balance_subquery as balance FROM myitems WHERE isdeleted = 0 ORDER BY id DESC LIMIT 200";
    } else {
        $s = $conn->real_escape_string($search);
        $query = "SELECT id, iname as name, price1 as price, barcode, $balance_subquery as balance FROM myitems 
                  WHERE (iname LIKE '%$s%' OR barcode LIKE '%$s%' OR id = '$s') 
                  AND isdeleted = 0 
                  ORDER BY iname LIMIT 100";
    }
    $result = $conn->query($query);
    
    if (!$result) {
        throw new Exception($conn->error);
    }
    
    $items = [];
    while ($row = $result->fetch_assoc()) {
        $items[] = [
            'id' => (int)$row['id'],
            'name' => $row['name'],
            'price' => (float)$row['price'],
            'balance' => (float)($row['balance'] ?? 0)
        ];
    }
    
    echo json_encode([
        'success' => true,
        'items' => $items,
        'count' => count($items)
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'خطأ: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

exit;
?>
