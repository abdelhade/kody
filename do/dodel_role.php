<?php
require_once __DIR__ . '/../includes/connect.php';

$id = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0;
if ($id < 1) {
    header('Location: ../myroles.php');
    exit;
}

// Cannot delete admin role ID 1
if ($id === 1) {
    header('Location: ../myroles.php?error=' . urlencode('لا يمكن حذف الدور الإداري الرئيسي'));
    exit;
}

$stmt = $conn->prepare("SELECT COUNT(*) AS c FROM users WHERE userrole = ? AND COALESCE(isdeleted, 0) != 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$users_count = (int)($res->fetch_assoc()['c'] ?? 0);
$stmt->close();

if ($users_count > 0) {
    header('Location: ../myroles.php?w=del');
    exit;
}

$del = $conn->prepare("DELETE FROM usr_pwrs WHERE id = ?");
$del->bind_param("i", $id);
$del->execute();
$del->close();

$stmtLog = $conn->prepare("INSERT INTO `process` (`type`) VALUES (?)");
$proc = 'delete role: ' . $id;
$stmtLog->bind_param("s", $proc);
$stmtLog->execute();
$stmtLog->close();

header('Location: ../myroles.php?msg=deleted');
exit;
