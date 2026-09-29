<?php
include('../includes/connect.php');
require_once('../classes/InvoiceProcessor.php');

$id = isset($_GET['del']) ? intval($_GET['del']) : 0;
if ($id <= 0) {
    header('location:../vouchers.php');
    exit;
}

$stmt = $conn->prepare('SELECT pro_tybe FROM ot_head WHERE id = ? AND isdeleted = 0 LIMIT 1');
$stmt->bind_param('i', $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$row) {
    header('location:../vouchers.php');
    exit;
}

$pro_tybe = (int) $row['pro_tybe'];
if ($pro_tybe !== 1 && $pro_tybe !== 2) {
    echo 'هذا السند مرتبط بعمليات أخرى لا يمكن حذف هذا السند';
    exit;
}

InvoiceProcessor::softDelete($conn, $id);

if ($pro_tybe === 1) {
    header('location:../vouchers.php?t=recive');
} else {
    header('location:../vouchers.php?t=payment');
}
exit;
