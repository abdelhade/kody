<?php
include '../includes/connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_GET['id'])) {
    header('Location: ../shift_groups.php?error=invalid_request');
    exit;
}

$id = (int) $_GET['id'];
$password = isset($_POST['password']) ? trim($_POST['password']) : '';

if ($id <= 0) {
    header('Location: ../shift_groups.php?error=invalid_id');
    exit;
}

if ($password === '') {
    header('Location: ../shift_groups.php?error=missing_password');
    exit;
}

if ($password !== ($rowstg['edit_pass'] ?? '')) {
    header('Location: ../shift_groups.php?error=invalid_password');
    exit;
}

// فحص وجود موظفين مرتبطين بالمجموعة
$stmtCheck = $conn->prepare("SELECT COUNT(*) AS count FROM employees WHERE shift_group_id = ? AND COALESCE(isdeleted, 0) != 1");
$stmtCheck->bind_param('i', $id);
$stmtCheck->execute();
$check = $stmtCheck->get_result()->fetch_assoc();
$stmtCheck->close();

if (!empty($check['count']) && (int)$check['count'] > 0) {
    header('Location: ../shift_groups.php?error=linked_employees');
    exit;
}

$stmtDel = $conn->prepare("UPDATE shift_groups SET isdeleted = 1 WHERE id = ?");
$stmtDel->bind_param('i', $id);
if (!$stmtDel->execute()) {
    $stmtDel->close();
    header('Location: ../shift_groups.php?error=delete_failed');
    exit;
}
$stmtDel->close();

header('Location: ../shift_groups.php?success=deleted');
exit;
