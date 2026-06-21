<?php
$conn = new mysqli('localhost', 'root', '', 'kody2');
$res = $conn->query('SHOW TABLES');
while($row = $res->fetch_row()) {
    echo $row[0] . "\n";
}
?>
