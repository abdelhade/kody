<?php
include('includes/connect.php');
$res = $conn->query("SELECT id, pro_id, pro_tybe FROM ot_head ORDER BY id DESC LIMIT 5");
while ($row = $res->fetch_assoc()) {
    print_r($row);
}
?>
