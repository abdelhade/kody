<?php
include('includes/connect.php');
$r = $conn->query('SELECT * FROM pro_tybes');
while($row = $r->fetch_assoc()) {
    print_r($row);
}
?>
