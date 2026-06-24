<?php
include('../includes/connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pass = $_POST['admin_pass'] ?? '';
    
    // Check if password matches the global edit_pass
    if ($pass === $edit_pass) {
        $conn->query("DELETE FROM `attlog` WHERE attdoc > 0");
        $conn->query("DELETE FROM `attdocs`");
        
        header('location:../calcsalary.php?msg=deleted_all');
    } else {
        echo "<script>alert('كلمة المرور غير صحيحة'); history.back();</script>";
    }
} else {
    header('location:../calcsalary.php');
}
?>
