<?php
require('includes/connect.php');
echo "\n--- Checking for NULL info in ot_head ---\n";
$res = $conn->query("SELECT id, pro_tybe FROM ot_head WHERE info IS NULL");
while($r = $res->fetch_assoc()) {
    print_r($r);
}
?>
