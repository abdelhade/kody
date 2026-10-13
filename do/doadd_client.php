<?php
require_once __DIR__ . '/../includes/connect.php';

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $name = trim((string)($_POST['name'] ?? ''));
    if ($name === '') {
        header("Location: ../add_client.php?error=" . urlencode('يرجى كتابة اسم العميل'));
        exit;
    }

    $phone = trim((string)($_POST['phone'] ?? ''));
    $address = trim((string)($_POST['address'] ?? ''));
    $city = trim((string)($_POST['city'] ?? ''));
    $gender = (int)($_POST['gender'] ?? 0);
    $height = trim((string)($_POST['height'] ?? ''));
    $weight = trim((string)($_POST['weight'] ?? ''));
    $dateofbirth = !empty($_POST['dateofbirth']) ? trim((string)$_POST['dateofbirth']) : null;
    $ref = trim((string)($_POST['ref'] ?? ''));
    $diseses = trim((string)($_POST['diseses'] ?? ''));
    $info = trim((string)($_POST['info'] ?? ''));

    $stmt = $conn->prepare("INSERT INTO clients (name, phone, address, city, height, weight, dateofbirth, ref, gender, diseses, info) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssiss", $name, $phone, $address, $city, $height, $weight, $dateofbirth, $ref, $gender, $diseses, $info);
    $stmt->execute();
    $stmt->close();

    $stmtLog = $conn->prepare("INSERT INTO `process` (`type`) VALUES (?)");
    $procType = 'add client';
    $stmtLog->bind_param("s", $procType);
    $stmtLog->execute();
    $stmtLog->close();

    header("Location: ../clients.php?msg=saved");
    exit;
}

header("Location: ../clients.php");
exit;