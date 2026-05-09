<?php
include('includes/connect.php');
$r = $conn->query('DESCRIBE myitems');
while($row = $r->fetch_assoc()) {
    print_r($row);
}
?>
