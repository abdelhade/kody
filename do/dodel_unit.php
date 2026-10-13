<?php 
require_once __DIR__ . '/../includes/connect.php';

$id = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0;
if ($id < 1) {
    header('Location: ../myunits.php');
    exit;
}

// Check if linked to items
$chk = $conn->prepare("SELECT COUNT(*) as cnt FROM myitems WHERE unit = ?");
$chk->bind_param("i", $id);
$chk->execute();
$res = $chk->get_result();
$cnt = (int)($res->fetch_assoc()['cnt'] ?? 0);
$chk->close();

if ($cnt > 0) {
    header('Location: ../myunits.php?w=del');
    exit;
}

$stmt = $conn->prepare("DELETE FROM myunits WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

header('Location: ../myunits.php?msg=deleted');
exit;
