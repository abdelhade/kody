<?php
require_once __DIR__ . '/includes/connect.php';

$from = '2020-01-01';
$to = '2030-01-01';

$sql = "SELECT 
            g.id as group_id,
            COALESCE(g.gname, 'بدون مجموعة') as group_name,
            SUM(fd.qty_out) as total_qty,
            SUM(fd.det_value) as total_sales
        FROM fat_details fd
        JOIN myitems i ON fd.item_id = i.id
        LEFT JOIN item_group g ON i.group1 = g.id
        WHERE fd.isdeleted = 0 
          AND (fd.fat_tybe = 9 OR fd.fat_tybe = 3)
          AND fd.crtime BETWEEN '$from 00:00:00' AND '$to 23:59:59'
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
