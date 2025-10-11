<?php include('../includes/connect.php');
$gname = $_POST['gname'];
$conn->query("INSERT INTO item_group2 (gname) values ('$gname')");
$conn->query("INSERT INTO `process`(`type`) VALUES ('add group2')");

header('location:../item_categories.php');