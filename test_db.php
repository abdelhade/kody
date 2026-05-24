<?php
require_once __DIR__ . '/includes/connect.php';

$res = $conn->query("SELECT id, gname FROM item_group LIMIT 5");
while($row = $res->fetch_assoc()) {
    print_r($row);
}

$res = $conn->query("SELECT id, iname, group1 FROM myitems LIMIT 5");
while($row = $res->fetch_assoc()) {
    print_r($row);
}
?>
