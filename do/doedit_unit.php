<?php 
require_once __DIR__ . '/../includes/connect.php';

$id = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0;
if ($id < 1) {
    header('Location: ../myunits.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uname = trim((string)($_POST['uname'] ?? ''));
    if ($uname === '') {
        header('Location: ../myunits.php?error=' . urlencode('يرجى إدخال اسم الوحدة'));
        exit;
    }

    $stmt = $conn->prepare("UPDATE myunits SET uname = ? WHERE id = ?");
    $stmt->bind_param("si", $uname, $id);
    $stmt->execute();
    $stmt->close();

    header('Location: ../myunits.php?msg=updated');
    exit;
}

header('Location: ../myunits.php');
exit;
