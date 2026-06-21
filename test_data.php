<?php
require_once __DIR__ . '/includes/connect.php';
$res = $conn->query("SELECT * FROM fat_details WHERE isdeleted = 0 AND (fat_tybe = 9 OR fat_tybe = 3) LIMIT 5");
while($row = $res->fetch_assoc()) print_r($row);
?>
