<?php
error_reporting(E_ALL); ini_set('display_errors', 1);
require 'includes/connect.php';
$tables = ['funds', 'stores', 'clients', 'employees', 'items'];
foreach($tables as $t) {
    $res = $conn->query("SHOW COLUMNS FROM $t");
    if($res) {
        echo "Table $t exists. Columns: ";
        while($row = $res->fetch_assoc()) echo $row['Field'] . ", ";
        echo "\n";
    } else {
        echo "Table $t DOES NOT EXIST\n";
    }
}
