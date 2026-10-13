<?php 
require_once __DIR__ . '/../includes/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uname = trim((string)($_POST['uname'] ?? ''));
    if ($uname === '') {
        header('Location: ../myunits.php?error=' . urlencode('يرجى إدخال اسم الوحدة'));
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO myunits (uname) VALUES (?)");
    $stmt->bind_param("s", $uname);
    $stmt->execute();
    $stmt->close();

    $stmtLog = $conn->prepare("INSERT INTO `process` (`type`) VALUES (?)");
    $proc = 'add unit: ' . $uname;
    $stmtLog->bind_param("s", $proc);
    $stmtLog->execute();
    $stmtLog->close();

    header('Location: ../myunits.php?msg=saved');
    exit;
}

header('Location: ../myunits.php');
exit;
