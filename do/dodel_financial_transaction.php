<?php
include('../includes/connect.php');
$id = (int)$_GET['id'];

$sql = "DELETE FROM `financial_transactions` WHERE snd_id = '$id'";
$conn->query($sql); 

header('location:../financial_transactions.php');
?>
