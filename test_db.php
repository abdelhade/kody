<?php 
include("includes/connect.php"); 
$res = $conn->query("SHOW CREATE TABLE journal_entries"); 
$row = $res->fetch_row(); 
echo $row[1]."\n\n"; 
$res = $conn->query("SHOW CREATE TABLE journal_heads"); 
$row = $res->fetch_row(); 
echo $row[1]; 
?>
