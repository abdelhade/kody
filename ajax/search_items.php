<?php
// منع أي output قبل JSON
ob_start();

include('../includes/connect.php');

// مسح أي output buffer
ob_end_clean();

// تأكد من JSON header
header('Content-Type: application/json; charset=utf-8');

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;

try {
    if (!$conn) {
        throw new Exception('Database connection failed');
    }
    
    $store_id = isset($_GET['store_id']) ? intval($_GET['store_id']) : 0;
    $balance_subquery = $store_id > 0 ? "COALESCE((SELECT SUM(qty_in - qty_out) FROM fat_details WHERE item_id = myitems.id AND det_store = $store_id AND isdeleted = 0), 0)" : "0";

    $category_cond = $category_id > 0 ? " AND group1 = $category_id" : "";

    if (empty($search)) {
        $query = "SELECT id, iname as name, price1, price2, price3, market_price, price1 as price, barcode, $balance_subquery as balance FROM myitems WHERE isdeleted = 0 $category_cond ORDER BY id DESC LIMIT 200";
    } else {
        $s = $conn->real_escape_string($search);
        $search_terms = explode(' ', $s);
        $name_conds = [];
        foreach ($search_terms as $term) {
            $term = trim($term);
            if (!empty($term)) {
                $name_conds[] = "iname LIKE '%$term%'";
            }
        }
        $name_where = implode(' AND ', $name_conds);
        if (empty($name_where)) $name_where = "1=1";

        $query = "SELECT id, iname as name, price1, price2, price3, market_price, price1 as price, barcode, $balance_subquery as balance FROM myitems 
                  WHERE (($name_where) OR barcode LIKE '%$s%' OR id = '$s') 
                  AND isdeleted = 0 
                  $category_cond
                  ORDER BY iname LIMIT 100";
    }
    $result = $conn->query($query);
    
    if (!$result) {
        throw new Exception($conn->error);
    }
    
    $items = [];
    while ($row = $result->fetch_assoc()) {
        $price3 = (float) ($row['price3'] ?? 0);
        if ($price3 <= 0) {
            $price3 = (float) ($row['market_price'] ?? 0);
        }
        $items[] = [
            'id' => (int)$row['id'],
            'name' => $row['name'],
            'price' => (float)$row['price'],
            'price1' => (float)($row['price1'] ?? 0),
            'price2' => (float)($row['price2'] ?? 0),
            'price3' => $price3,
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
