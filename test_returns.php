<?php
include 'includes/connect.php';
$r = $conn->query('SELECT id, pro_tybe, pro_value, fat_net, profit FROM ot_head WHERE pro_tybe IN (10,11) AND isdeleted != 1 ORDER BY id DESC LIMIT 10');
echo "id | type | pro_value | fat_net | profit\n";
while($row = $r->fetch_assoc()) {
    echo "{$row['id']} | {$row['pro_tybe']} | {$row['pro_value']} | {$row['fat_net']} | {$row['profit']}\n";
}
