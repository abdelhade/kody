<?php
include('includes/connect.php');
$r = $conn->query('UPDATE tables SET parent_table_id = NULL WHERE parent_table_id = 0');
echo $r ? 'Fixed: ' . $conn->affected_rows . ' rows updated' : 'Error: ' . $conn->error;
