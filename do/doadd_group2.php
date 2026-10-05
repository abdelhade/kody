<?php
include('../includes/connect.php');

$gname = trim((string) ($_POST['gname'] ?? ''));
if ($gname === '') {
    header('location:../item_categories.php?error=empty');
    exit;
}

$stmt = $conn->prepare('SELECT id, isdeleted FROM item_group2 WHERE gname = ? LIMIT 1');
$stmt->bind_param('s', $gname);
$stmt->execute();
$existing = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($existing) {
    if ((int) $existing['isdeleted'] === 0) {
        header('location:../item_categories.php?error=duplicate');
        exit;
    }
    $id = (int) $existing['id'];
    $stmt = $conn->prepare('UPDATE item_group2 SET isdeleted = 0 WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
} else {
    $stmt = $conn->prepare('INSERT INTO item_group2 (gname) VALUES (?)');
    $stmt->bind_param('s', $gname);
    if (!$stmt->execute()) {
        $stmt->close();
        header('location:../item_categories.php?error=save');
        exit;
    }
    $stmt->close();
}

$conn->query("INSERT INTO `process`(`type`) VALUES ('add group2')");
header('location:../item_categories.php');
exit;
