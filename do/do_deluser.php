<?php
require_once __DIR__ . '/../includes/connect.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$id = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0;
$currentUserId = isset($_SESSION['userid']) ? (int)$_SESSION['userid'] : 0;

if ($id < 1) {
    header('Location: ../users.php');
    exit;
}

// Cannot delete self
if ($id === $currentUserId) {
    header('Location: ../users.php?error=' . urlencode('لا يمكنك حذف حسابك الشخصي أثناء تسجيل الدخول'));
    exit;
}

// Cannot delete root admin if id == 1
if ($id === 1) {
    header('Location: ../users.php?error=' . urlencode('لا يمكن حذف المستخدم الرئيسي للنظام'));
    exit;
}

// Soft delete
$stmt = $conn->prepare("UPDATE `users` SET `isdeleted` = 1 WHERE `id` = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

$stmtLog = $conn->prepare("INSERT INTO `process` (`type`) VALUES (?)");
$proc = 'delete user: ' . $id;
$stmtLog->bind_param("s", $proc);
$stmtLog->execute();
$stmtLog->close();

header('Location: ../users.php?msg=deleted');
exit;