<?php
include('../includes/connect.php');

$id = (int) ($_GET['id'] ?? 0);
$gname = trim((string) ($_POST['gname'] ?? ''));
$supplierId = (int) ($_POST['supplier_id'] ?? 0);
if ($id <= 0 || $gname === '' || $supplierId <= 0) {
    header('location:../mygroups.php?error=empty');
    exit;
}

$col = $conn->query("SHOW COLUMNS FROM item_group LIKE 'supplier_id'");
if ($col && $col->num_rows === 0) {
    $conn->query('ALTER TABLE item_group ADD COLUMN supplier_id INT(11) DEFAULT NULL');
}

$stmt = $conn->prepare('SELECT id, isdeleted FROM item_group WHERE gname = ? AND id != ? LIMIT 1');
$stmt->bind_param('si', $gname, $id);
$stmt->execute();
$existing = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($existing) {
    if ((int) $existing['isdeleted'] === 0) {
        header('location:../mygroups.php?error=duplicate');
        exit;
    }
    $oldId = (int) $existing['id'];
    $freed = '__del_' . $oldId;
    $stmt = $conn->prepare('UPDATE item_group SET gname = ? WHERE id = ?');
    $stmt->bind_param('si', $freed, $oldId);
    $stmt->execute();
    $stmt->close();
}

$stmt = $conn->prepare('UPDATE item_group SET gname = ?, supplier_id = ? WHERE id = ? AND isdeleted = 0');
$stmt->bind_param('sii', $gname, $supplierId, $id);
if (!$stmt->execute()) {
    $stmt->close();
    header('location:../mygroups.php?error=save');
    exit;
}
$stmt->close();

header('location:../mygroups.php');
exit;
