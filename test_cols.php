<?php
include 'includes/connect.php';
$res = $conn->query("SHOW COLUMNS FROM employees");
while($row = $res->fetch_assoc()) {
    print_r($row);
}
