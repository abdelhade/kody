<?php
declare(strict_types=1);

include '../includes/connect.php';

$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    $stmt = $conn->prepare("UPDATE visits SET isdeleted = 1 WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();

    $conn->query("INSERT INTO `process`(`type`) VALUES ('delete visit')");
}

header('Location: ../visits.php');
exit;
