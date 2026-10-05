<?php
include('../includes/connect.php');

$id = (int) ($_GET['id'] ?? 0);
$gname = trim((string) ($_POST['gname'] ?? ''));
if ($id <= 0 || $gname === '') {
    header('location:../item_categories.php?error=empty');
    exit;
}

$stmt = $conn->prepare('SELECT id, isdeleted FROM item_group2 WHERE gname = ? AND id != ? LIMIT 1');
$stmt->bind_param('si', $gname, $id);
$stmt->execute();
$existing = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($existing) {
    if ((int) $existing['isdeleted'] === 0) {
        header('location:../item_categories.php?error=duplicate');
        exit;
    }
    $oldId = (int) $existing['id'];
    $freed = '__del_' . $oldId;
    $stmt = $conn->prepare('UPDATE item_group2 SET gname = ? WHERE id = ?');
    $stmt->bind_param('si', $freed, $oldId);
    $stmt->execute();
    $stmt->close();
}

$stmt = $conn->prepare('UPDATE item_group2 SET gname = ? WHERE id = ? AND isdeleted = 0');
$stmt->bind_param('si', $gname, $id);
if (!$stmt->execute()) {
    $stmt->close();
    header('location:../item_categories.php?error=save');
    exit;
}
$stmt->close();

header('location:../item_categories.php');
exit;
