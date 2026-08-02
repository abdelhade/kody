<?php
session_start();
include('../includes/connect.php');
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['userid'])) {
    echo json_encode(['success' => false, 'error' => 'غير مصرح']);
    exit;
}

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
if ($order_id <= 0) {
    echo json_encode(['success' => false, 'error' => 'معرف الطلب غير صحيح']);
    exit;
}

$stmt = $conn->prepare(
    "SELECT fd.id, fd.item_id, fd.price, fd.det_value,
            (fd.qty_out - fd.qty_in) as qty,
            COALESCE(m.iname, 'صنف غير معروف') as item_name
     FROM fat_details fd
     LEFT JOIN myitems m ON m.id = fd.item_id
     WHERE fd.pro_id = ? AND fd.isdeleted = 0
     ORDER BY fd.id ASC"
);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

$items = [];
$total = 0;
while ($row = $result->fetch_assoc()) {
    $items[] = [
        'id'        => intval($row['id']),
        'item_id'   => intval($row['item_id']),
        'item_name' => $row['item_name'],
        'qty'       => floatval($row['qty']),
        'price'     => floatval($row['price']),
        'det_value' => floatval($row['det_value']),
    ];
    $total += floatval($row['det_value']);
}

echo json_encode([
    'success' => true,
    'items'   => $items,
    'total'   => $total,
], JSON_UNESCAPED_UNICODE);
