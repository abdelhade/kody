<?php
$conn = new mysqli('localhost', 'root', '', 'kody2');
$res = $conn->query('SHOW COLUMNS FROM item_group');
while($row = $res->fetch_assoc()) echo $row['Field'] . " ";
echo "\n";
$res = $conn->query('SHOW COLUMNS FROM myitems');
while($row = $res->fetch_assoc()) echo $row['Field'] . " ";
?>
