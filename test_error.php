<?php
$conn = new mysqli('localhost', 'root', '', 'kody2');

$shift_date = '2026-05-25';
$start_time = '2026-05-25 00:00:00';
$end_time = '2026-05-25 23:59:59';
$user_id = 1;

$items_query = "
    SELECT 
        COALESCE(c.cname, 'بدون مجموعة') as category_name,
        i.iname as item_name,
        SUM(d.qty_out) as total_qty,
        SUM(d.det_value) as total_value
    FROM fat_details d
    JOIN ot_head h ON d.fatid = h.id
    JOIN myitems i ON d.item_id = i.id
    LEFT JOIN categores c ON i.cat_id = c.id
    WHERE h.pro_date = ?
      AND h.crtime > ?
      AND h.crtime <= ?
      AND h.user = ?
      AND d.isdeleted = 0
      AND h.isdeleted = 0
      AND (h.pro_tybe = 9 OR h.pro_tybe = 3 OR h.pro_tybe = 10 OR h.pro_tybe = 11)
    GROUP BY c.cname, i.iname
    HAVING total_qty > 0
    ORDER BY c.cname ASC, total_qty DESC
";

$stmt = $conn->prepare($items_query);
if (!$stmt) {
    echo "Prepare failed: " . $conn->error;
} else {
    echo "Prepare success!";
}
?>
