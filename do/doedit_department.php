<?php
require_once __DIR__ . '/../includes/connect.php';

$id = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0;
if ($id < 1) {
    header('Location: ../departments.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $name = trim((string)($_POST['name'] ?? ''));
    $info = trim((string)($_POST['info'] ?? ''));

    if ($name === '') {
        header("Location: ../edit_department.php?id=" . $id . "&error=" . urlencode('يرجى إدخال اسم القسم'));
        exit;
    }

    $stmt = $conn->prepare("UPDATE `departments` SET `name` = ?, `info` = ? WHERE `id` = ?");
    $stmt->bind_param("ssi", $name, $info, $id);
    $stmt->execute();
    $stmt->close();

    header('Location: ../departments.php?msg=updated');
    exit;
}

header('Location: ../departments.php');
exit;
