<?php
require_once __DIR__ . '/../includes/connect.php';
require_once __DIR__ . '/../classes/InvoiceProcessor.php';

$id = isset($_REQUEST['del']) ? (int)$_REQUEST['del'] : (isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0);
if ($id <= 0) {
    header('Location: ../vouchers.php');
    exit;
}

$stmt = $conn->prepare('SELECT pro_tybe FROM ot_head WHERE id = ? LIMIT 1');
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
Can::authorize($pro_tybe === 1 ? 'receipts.delete' : 'payments.delete');

$redirectParam = ($pro_tybe === 1) ? 't=recive' : (($pro_tybe === 16) ? 't=transfer' : 't=payment');

$linked = InvoiceProcessor::linkedInvoiceOfVoucher($conn, $id);
if ($linked) {
    $msg = 'لا يمكن حذف السند لأنه مرتبط بالفاتورة رقم ' . $linked['pro_id'] . '، احذف أو عدّل الفاتورة نفسها';
    header('Location: ../vouchers.php?' . $redirectParam . '&error=' . urlencode($msg));
    exit;
}

InvoiceProcessor::hardDelete($conn, $id);

header('Location: ../vouchers.php?' . $redirectParam . '&msg=deleted');
exit;
