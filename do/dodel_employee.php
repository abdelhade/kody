<?php
require_once __DIR__ . '/../includes/connect.php';

$id = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0;
if ($id < 1) {
    header('Location: ../employees.php');
    exit;
}

// Soft delete employee
$stmt = $conn->prepare("UPDATE employees SET isdeleted = 1 WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

$stmtLog = $conn->prepare("INSERT INTO `process` (`type`) VALUES (?)");
$proc = 'delete employee: ' . $id;
$stmtLog->bind_param("s", $proc);
$stmtLog->execute();
$stmtLog->close();

header('Location: ../employees.php?msg=deleted');
exit;
