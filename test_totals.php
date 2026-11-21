<?php
include 'includes/connect.php';

$today = date('Y-m-d');

$r = $conn->query("
    SELECT 
        COALESCE(SUM(CASE WHEN ot.pro_tybe IN (3,9) THEN ot.pro_value ELSE 0 END),0) AS sales_total,
        COALESCE(SUM(CASE WHEN ot.pro_tybe IN (3,9) THEN ot.fat_net ELSE 0 END),0) AS sales_net,
        COALESCE(SUM(CASE WHEN ot.pro_tybe IN (10,11) THEN ot.pro_value ELSE 0 END),0) AS returns_total,
        COALESCE(SUM(CASE WHEN ot.pro_tybe IN (10,11) THEN ot.fat_net ELSE 0 END),0) AS returns_net,
        COALESCE(SUM(CASE WHEN ot.pro_tybe IN (3,9) THEN ot.profit ELSE 0 END),0)
          - COALESCE(SUM(CASE WHEN ot.pro_tybe IN (10,11) THEN ot.profit ELSE 0 END),0) AS profit_net,
        COALESCE(SUM(CASE WHEN ot.pro_tybe IN (3,9) THEN
            LEAST(
                GREATEST(COALESCE((
                    SELECT SUM(v.pro_value) FROM ot_head v
                    WHERE v.op2 = ot.id AND v.id <> ot.id AND v.isdeleted != 1
                ), 0), 0),
                GREATEST(COALESCE(ot.fat_net, 0), 0)
            )
        ELSE 0 END), 0) AS paid_total_current_code,
        COALESCE(SUM(CASE WHEN ot.pro_tybe IN (3,9) THEN
            LEAST(
                GREATEST(COALESCE((
                    SELECT SUM(v.pro_value) FROM ot_head v
                    WHERE v.op2 = ot.id AND v.id <> ot.id AND v.isdeleted != 1
                ), 0), 0),
                GREATEST(COALESCE(ot.fat_net, 0), 0)
            )
        ELSE 0 END), 0) AS paid_new
    FROM ot_head ot
    WHERE (ot.pro_tybe IN (3,9,11) OR (ot.pro_tybe = 10 AND ot.info LIKE '%مردود مبيعات%'))
    AND ot.isdeleted != 1
    AND DATE(ot.pro_date) = '$today'
");

echo "<pre>";
print_r($r->fetch_assoc());
echo "</pre>";

// تفصيلي للتحقق
echo "<h3>تفصيلي فواتير اليوم</h3>";
$r2 = $conn->query("
    SELECT ot.id, ot.pro_tybe, ot.pro_value, ot.fat_net, ot.profit,
        (SELECT SUM(v.pro_value) FROM ot_head v WHERE v.op2 = ot.id AND v.id <> ot.id AND v.isdeleted != 1) AS paid_linked
    FROM ot_head ot
    WHERE (ot.pro_tybe IN (3,9,11) OR (ot.pro_tybe = 10 AND ot.info LIKE '%مردود مبيعات%'))
    AND ot.isdeleted != 1
    AND DATE(ot.pro_date) = '$today'
    ORDER BY ot.id DESC
");
echo "<table border=1 cellpadding=5>";
echo "<tr><th>id</th><th>type</th><th>pro_value</th><th>fat_net</th><th>profit</th><th>paid_linked</th></tr>";
while($row = $r2->fetch_assoc()) {
    echo "<tr><td>{$row['id']}</td><td>{$row['pro_tybe']}</td><td>{$row['pro_value']}</td><td>{$row['fat_net']}</td><td>{$row['profit']}</td><td>{$row['paid_linked']}</td></tr>";
}
echo "</table>";
?>
