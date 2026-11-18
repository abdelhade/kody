<?php
session_start();
include('../includes/connect.php');
require_once('../classes/Manufacturing.php');
require_once('../classes/InvoiceProcessor.php');

Can::authorize('manufacturing.add', 'manufacturing.edit');

header('Content-Type: application/json; charset=utf-8');

$itemId = (int) ($_GET['item_id'] ?? 0);
$storeId = (int) ($_GET['store_id'] ?? 0);
$qty = (float) ($_GET['qty'] ?? 0);
$orderId = (int) ($_GET['order_id'] ?? 0);
if ($qty < 0) {
    $qty = 0;
}

$lines = [];
foreach (Manufacturing::recipe($conn, $itemId) as $line) {
    $need = $line['qty'] * $qty;
    $stock = $storeId > 0
        ? InvoiceProcessor::getRealStockQuantity($conn, $line['component_id'], $storeId)
        : InvoiceProcessor::getRealStockQuantity($conn, $line['component_id']);
    if ($orderId > 0 && $storeId > 0) {
        $stmt = $conn->prepare(
            'SELECT COALESCE(SUM(qty_out), 0) AS q FROM fat_details
             WHERE item_id = ? AND det_store = ? AND isdeleted = 0 AND (pro_id = ? OR fatid = ?)'
        );
        $cid = $line['component_id'];
        $stmt->bind_param('iiii', $cid, $storeId, $orderId, $orderId);
        $stmt->execute();
        $stock += (float) ($stmt->get_result()->fetch_assoc()['q'] ?? 0);
        $stmt->close();
    }
    $lines[] = [
        'id' => $line['component_id'],
        'name' => $line['iname'],
        'per' => $line['qty'],
        'qty' => $need,
        'cost' => $line['cost_price'],
        'stock' => $stock,
        'value' => $need * $line['cost_price'],
    ];
}

echo json_encode(['lines' => $lines], JSON_UNESCAPED_UNICODE);
