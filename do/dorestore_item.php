<?php
include('../includes/connect.php');

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$password = isset($_POST['password']) ? (string) $_POST['password'] : '';
$srvrpass = $rowstg['edit_pass'] ?? '';

if ($id > 0 && $password == $srvrpass) {
    $stmt = $conn->prepare('UPDATE myitems SET isdeleted = 0 WHERE id = ? AND isdeleted = 1');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
    header('Location: ../deleted_items.php?restored=1');
    exit;
}

header('Location: ../deleted_items.php?pass=0');
exit;
