<?php
require_once __DIR__ . '/includes/connect.php';

$res = $conn->query("SHOW COLUMNS FROM ot_head");
while($row = $res->fetch_assoc()) echo $row['Field'] . " | ";
echo "\n";

$res = $conn->query("SHOW COLUMNS FROM fat_details");
while($row = $res->fetch_assoc()) echo $row['Field'] . " | ";
echo "\n";
?>
