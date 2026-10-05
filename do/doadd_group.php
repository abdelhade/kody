<?php
include('../includes/connect.php');

$gname = trim((string) ($_POST['gname'] ?? ''));
$supplierId = (int) ($_POST['supplier_id'] ?? 0);
if ($gname === '' || $supplierId <= 0) {
    header('location:../mygroups.php?error=empty');
    exit;
}

$col = $conn->query("SHOW COLUMNS FROM item_group LIKE 'supplier_id'");
if ($col && $col->num_rows === 0) {
    $conn->query('ALTER TABLE item_group ADD COLUMN supplier_id INT(11) DEFAULT NULL');
}

$stmt = $conn->prepare('SELECT id, isdeleted FROM item_group WHERE gname = ? LIMIT 1');
$stmt->bind_param('s', $gname);
$stmt->execute();
$existing = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($existing) {
    if ((int) $existing['isdeleted'] === 0) {
        header('location:../mygroups.php?error=duplicate');
        exit;
    }
    $id = (int) $existing['id'];
    $stmt = $conn->prepare('UPDATE item_group SET isdeleted = 0, supplier_id = ? WHERE id = ?');
    $stmt->bind_param('ii', $supplierId, $id);
    $stmt->execute();
    $stmt->close();
} else {
    $stmt = $conn->prepare('INSERT INTO item_group (gname, supplier_id) VALUES (?, ?)');
    $stmt->bind_param('si', $gname, $supplierId);
    if (!$stmt->execute()) {
        $stmt->close();
        header('location:../mygroups.php?error=save');
        exit;
    }
    $stmt->close();
}

$conn->query("INSERT INTO `process`(`type`) VALUES ('add group')");
header('location:../mygroups.php');
exit;
