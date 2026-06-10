<?php
include '../includes/connect.php';

if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Check if there are linked employees
    $check_sql = "SELECT count(*) as count FROM employees WHERE department = '$id' AND coalesce(isdeleted, 0) != 1";
    $result = $conn->query($check_sql);
    $row = $result->fetch_assoc();
    
    if ($row['count'] > 0) {
        echo "<center><br><br><br><h1 class='bg-danger' style='padding: 20px; border-radius: 10px; display: inline-block; font-family: sans-serif;'>لا يمكن حذف هذا القسم لارتباطه ببيانات أخرى (موظفين)<br><br>
        <button class='btn btn-light' style='padding: 10px 20px; font-size: 20px; cursor: pointer;' onclick='history.go(-1);'>رجوع</button>
        </h1></center>";
        die;
    }

    $conn->query("DELETE FROM departments where id = $id");
    header('location:../departments.php');
}
