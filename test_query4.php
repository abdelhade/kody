<?php
require_once __DIR__ . '/includes/connect.php';

$from = '2020-01-01';
$to = '2030-01-01';

$sql = "SELECT 
            g.id as group_id,
            g.gname as group_name,
            COALESCE(SUM(fd.qty_out), 0) as total_qty,
            COALESCE(SUM(fd.det_value), 0) as total_sales
        FROM item_group g
        LEFT JOIN myitems i ON i.group1 = g.id AND i.isdeleted = 0
        LEFT JOIN fat_details fd ON fd.item_id = i.id 
            AND fd.isdeleted = 0 
            AND (fd.fat_tybe = 9 OR fd.fat_tybe = 3)
            AND fd.crtime BETWEEN '$from 00:00:00' AND '$to 23:59:59'
        WHERE g.isdeleted = 0
        GROUP BY g.id, g.gname
        ORDER BY total_sales DESC";

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
