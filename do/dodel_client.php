<?php
require_once __DIR__ . '/../includes/connect.php';

$id = isset($_REQUEST['id']) ? (int) $_REQUEST['id'] : 0;
if ($id < 1) {
    header('Location: ../clients.php');
    exit;
}

// Check linked reservations
$stmt = $conn->prepare("SELECT COUNT(*) as cnt FROM reservations WHERE client = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
$stmt->close();

if (!empty($row['cnt']) && (int)$row['cnt'] > 0) {
    header('Location: ../clients.php?w=del');
    exit;
}

// Delete client
$del = $conn->prepare("DELETE FROM clients WHERE id = ?");
$del->bind_param("i", $id);
$del->execute();
$del->close();

header('Location: ../clients.php?msg=deleted');
exit;