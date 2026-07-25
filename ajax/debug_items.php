<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ob_start();
include('../includes/connect.php');
ob_clean();

$table_id = isset($_GET['table_id']) ? intval($_GET['table_id']) : 6;

// جلب اسم الطاولة
$r = $conn->query("SELECT tname FROM tables WHERE id = $table_id");
$tname = $r->fetch_assoc()['tname'] ?? '';
echo "<h3>الطاولة: $tname (ID: $table_id)</h3>";

// جلب كل الطلبات
echo "<h4>كل الطلبات في ot_head لهذه الطاولة:</h4>";
$r2 = $conn->query("SELECT id, info, fat_total, fat_disc, fat_net, isdeleted, pro_tybe FROM ot_head WHERE info LIKE '%$tname%' AND pro_tybe = 9 ORDER BY id DESC");
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>ID</th><th>info</th><th>fat_total</th><th>fat_net</th><th>isdeleted</th><th>pro_tybe</th></tr>";
$order_ids = [];
while ($row = $r2->fetch_assoc()) {
    echo "<tr><td>{$row['id']}</td><td>{$row['info']}</td><td>{$row['fat_total']}</td><td>{$row['fat_net']}</td><td>{$row['isdeleted']}</td><td>{$row['pro_tybe']}</td></tr>";
    $order_ids[] = $row['id'];
}
echo "</table>";

if (!empty($order_ids)) {
    $ids = implode(',', $order_ids);
    echo "<h4>كل الأصناف في fat_details لهذه الطلبات:</h4>";
    $r3 = $conn->query("SELECT fd.id, fd.pro_id, fd.item_id, i.iname, fd.qty_out, fd.qty_in, fd.price, fd.isdeleted FROM fat_details fd LEFT JOIN myitems i ON fd.item_id = i.id WHERE fd.pro_id IN ($ids) ORDER BY fd.pro_id, fd.id");
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Detail ID</th><th>Order ID</th><th>Item ID</th><th>اسم الصنف</th><th>qty_out</th><th>qty_in</th><th>price</th><th>isdeleted</th></tr>";
    $total_items = 0;
    while ($row = $r3->fetch_assoc()) {
        echo "<tr><td>{$row['id']}</td><td>{$row['pro_id']}</td><td>{$row['item_id']}</td><td>{$row['iname']}</td><td>{$row['qty_out']}</td><td>{$row['qty_in']}</td><td>{$row['price']}</td><td>{$row['isdeleted']}</td></tr>";
        $total_items++;
    }
    echo "</table>";
    echo "<p><strong>إجمالي الصفوف: $total_items</strong></p>";
}
