<?php
require_once __DIR__ . '/includes/connect.php';

$from = '2020-01-01';
$to = '2030-01-01';

$sql = "SELECT 
            COALESCE(g.id, 0) as group_id,
            COALESCE(g.gname, 'بدون مجموعة') as group_name,
            COALESCE(SUM(fd.qty_out), 0) as total_qty,
            COALESCE(SUM(fd.det_value), 0) as total_sales
        FROM myitems i
        LEFT JOIN item_group g ON i.group1 = g.id
        LEFT JOIN fat_details fd ON fd.item_id = i.id 
            AND fd.isdeleted = 0 
            AND (fd.fat_tybe = 9 OR fd.fat_tybe = 3)
            AND fd.crtime BETWEEN '$from 00:00:00' AND '$to 23:59:59'
        WHERE i.isdeleted = 0
        GROUP BY COALESCE(g.id, 0), COALESCE(g.gname, 'بدون مجموعة')
        ORDER BY total_sales DESC, total_qty DESC";

$res = $conn->query($sql);
if (!$res) {
    echo "Error: " . $conn->error;
} else {
    echo "Success! Rows: " . $res->num_rows . "\n";
    while ($row = $res->fetch_assoc()) {
        print_r($row);
    }
}
?>
