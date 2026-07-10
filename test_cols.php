<?php
require_once 'includes/connect.php';
$res = $conn->query('DESCRIBE journal_entries');
while($row = $res->fetch_assoc()) {
    echo $row['Field'] . "\n";
}
?>
