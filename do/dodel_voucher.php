<?php
require_once __DIR__ . '/../includes/connect.php';
require_once __DIR__ . '/../classes/InvoiceProcessor.php';

$id = isset($_REQUEST['del']) ? (int)$_REQUEST['del'] : (isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0);
if ($id <= 0) {
    header('Location: ../vouchers.php');
    exit;
}

$stmt = $conn->prepare('SELECT pro_tybe FROM ot_head WHERE id = ? AND isdeleted = 0 LIMIT 1');
$stmt->bind_param('i', $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$row) {
    header('Location: ../vouchers.php');
    exit;
}

$pro_tybe = (int) $row['pro_tybe'];
if ($pro_tybe !== 1 && $pro_tybe !== 2 && $pro_tybe !== 16) {
    header('Location: ../vouchers.php?error=' . urlencode('هذا السند مرتبط بعمليات أخرى ولا يمكن حذفه'));
    exit;
}

InvoiceProcessor::softDelete($conn, $id);

$redirectParam = ($pro_tybe === 1) ? 't=recive' : (($pro_tybe === 16) ? 't=transfer' : 't=payment');
header('Location: ../vouchers.php?' . $redirectParam . '&msg=deleted');
exit;
