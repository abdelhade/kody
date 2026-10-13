<?php
require_once __DIR__ . '/../includes/connect.php';

$id = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0;
if ($id < 1) {
    header('Location: ../clients.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $name = trim((string)($_POST['name'] ?? ''));
    if ($name === '') {
        header("Location: ../edit_client.php?id=" . $id . "&error=" . urlencode('يرجى كتابة اسم العميل'));
        exit;
    }

    $phone = trim((string)($_POST['phone'] ?? ''));
    $city = trim((string)($_POST['city'] ?? ''));
    $address = trim((string)($_POST['address'] ?? ''));
    $gender = (int)($_POST['gender'] ?? 0);
    $height = trim((string)($_POST['height'] ?? ''));
    $weight = trim((string)($_POST['weight'] ?? ''));
    $dateofbirth = !empty($_POST['dateofbirth']) ? trim((string)$_POST['dateofbirth']) : null;
    $ref = trim((string)($_POST['ref'] ?? ''));
    $diseses = trim((string)($_POST['diseses'] ?? ''));
    $info = trim((string)($_POST['info'] ?? ''));

    $stmt = $conn->prepare("UPDATE `clients` SET `name` = ?, `phone` = ?, `address` = ?, `city` = ?, `height` = ?, `weight` = ?, `dateofbirth` = ?, `ref` = ?, `diseses` = ?, `info` = ?, `gender` = ? WHERE `id` = ?");
    $stmt->bind_param("ssssssssssii", $name, $phone, $address, $city, $height, $weight, $dateofbirth, $ref, $diseses, $info, $gender, $id);
    $stmt->execute();
    $stmt->close();

    header('Location: ../clients.php?msg=updated');
    exit;
}

header('Location: ../clients.php');
exit;