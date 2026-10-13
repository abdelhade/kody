<?php 
require_once __DIR__ . '/../includes/connect.php';

$id = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0;
if ($id < 1) {
    header('Location: ../mygroups.php');
    exit;
}

// Check linked active items
$chk = $conn->prepare("SELECT COUNT(*) AS cnt FROM myitems WHERE group1 = ? AND COALESCE(isdeleted, 0) != 1");
$chk->bind_param("i", $id);
$chk->execute();
$res = $chk->get_result();
$cnt = (int)($res->fetch_assoc()['cnt'] ?? 0);
$chk->close();

if ($cnt > 0) {
    header('Location: ../mygroups.php?w=del');
    exit;
}

$stmt = $conn->prepare("UPDATE item_group SET isdeleted = 1 WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

header('Location: ../mygroups.php?msg=deleted');
exit;