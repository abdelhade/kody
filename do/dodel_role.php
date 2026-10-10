<?php include('../includes/connect.php');
$id = intval($_GET['id']);

$users_count = $conn->query("SELECT COUNT(*) AS c FROM users WHERE userrole = $id AND isdeleted != 1")->fetch_assoc()['c'];

if ($users_count > 0) {
    echo "<script>alert('لا يمكن حذف هذا الدور لأنه مرتبط بمستخدمين'); window.location='../myroles.php';</script>";
    exit;
}

$conn->query("DELETE FROM usr_pwrs WHERE id = $id");
$conn->query("INSERT INTO `process`(`type`) VALUES ('delete role')");
header('location:../myroles.php');
