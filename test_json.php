<?php
$conn = new mysqli('localhost', 'root', '', 'kody2');
$res = $conn->query('SELECT json_details FROM closed_orders ORDER BY id DESC LIMIT 1');
$row = $res->fetch_assoc();
echo $row['json_details'];
?>
