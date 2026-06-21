<?php
include('../includes/connect.php');

$id = $_GET['id'];

$sql = "DELETE FROM employees WHERE id = $id";

if ($conn->query($sql) === TRUE) {
    header('location:../employees.php');
} else {
    echo "Error deleting record: " . $conn->error;
}
