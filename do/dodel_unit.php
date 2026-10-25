<?php 
require_once __DIR__ . '/../includes/connect.php';

$id = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0;
if ($id < 1) {
    header('Location: ../myunits.php');
    exit;
}

// Check if linked to items
$chk = $conn->prepare("SELECT COUNT(DISTINCT iu.item_id) as cnt
  FROM item_units iu
  INNER JOIN myitems m ON m.id = iu.item_id AND m.isdeleted = 0
  WHERE iu.unit_id = ? AND COALESCE(iu.isdeleted, 0) = 0");
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
