<?php
require_once __DIR__ . '/../includes/connect.php';

$id = isset($_REQUEST['id']) ? (int) $_REQUEST['id'] : 0;
if ($id < 1) {
    header('Location: ../departments.php');
    exit;
}

// Check if there are linked employees
$stmt = $conn->prepare("SELECT COUNT(*) as cnt FROM employees WHERE department = ? AND COALESCE(isdeleted, 0) != 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
$stmt->close();

if (!empty($row['cnt']) && (int)$row['cnt'] > 0) {
    header('Location: ../departments.php?w=del');
    exit;
}

// Soft delete
$upd = $conn->prepare("UPDATE departments SET isdeleted = 1 WHERE id = ?");
$upd->bind_param("i", $id);
$upd->execute();
$upd->close();

header('Location: ../departments.php?msg=deleted');
exit;
