<?php
$conn = new mysqli('localhost', 'root', '', 'kody2');
$res = $conn->query('SELECT id, date, strttime, endtime, crtime, user FROM closed_orders ORDER BY id DESC LIMIT 5');
while($row = $res->fetch_assoc()) {
    print_r($row);
}
?>
