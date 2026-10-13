<?php
require_once __DIR__ . '/../includes/connect.php';

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $name = trim((string)($_POST['name'] ?? ''));
    $info = trim((string)($_POST['info'] ?? ''));

    if ($name === '') {
        header("Location: ../add_department.php?error=" . urlencode('يرجى إدخال اسم القسم'));
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO departments (`name`, `info`) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $info);
    $stmt->execute();
    $stmt->close();

    $stmtLog = $conn->prepare("INSERT INTO `process` (`type`) VALUES (?)");
    $procType = 'add department';
    $stmtLog->bind_param("s", $procType);
    $stmtLog->execute();
    $stmtLog->close();

    header("Location: ../departments.php?msg=saved");
    exit;
}

header("Location: ../departments.php");
exit;
