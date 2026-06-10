<?php
include '../includes/connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_GET['id'])) {
    header('Location: ../shifts.php?error=invalid_request');
    exit;
}

$id = (int) $_GET['id'];
$password = isset($_POST['password']) ? $_POST['password'] : '';

if ($id <= 0) {
    header('Location: ../shifts.php?error=invalid_id');
    exit;
}

if ($password === '') {
    header('Location: ../shifts.php?error=missing_password');
    exit;
}

if ($password !== $rowstg['edit_pass']) {
    header('Location: ../shifts.php?error=invalid_password');
    exit;
}

$shift = $conn->query("SELECT id, name FROM shifts WHERE id = $id")->fetch_assoc();
if (!$shift) {
    header('Location: ../shifts.php?error=not_found');
    exit;
}

$check = $conn->query("SELECT COUNT(*) AS count FROM employees WHERE shift = $id AND COALESCE(isdeleted, 0) != 1")->fetch_assoc();
if ($check['count'] > 0) {
    header('Location: ../shifts.php?error=linked_employees');
    exit;
}

if (!$conn->query("DELETE FROM shifts WHERE id = $id")) {
    header('Location: ../shifts.php?error=delete_failed');
    exit;
}

header('Location: ../shifts.php?success=deleted');
exit;
