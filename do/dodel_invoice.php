<?php
session_start();
include('../includes/connect.php');

if (!isset($_SESSION['userid'])) {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../sales.php');
    exit;
}

$usid = $_SESSION['userid'];

require_once('../classes/InvoiceProcessor.php');

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$pass = isset($_POST['pass']) ? htmlspecialchars($_POST['pass'], ENT_QUOTES, 'UTF-8') : '';
$q = isset($_POST['q']) ? htmlspecialchars($_POST['q'], ENT_QUOTES, 'UTF-8') : '';

if ($id == 0) {
    header('Location: ../warning.php?error=invalid_id');
    exit;
}

if (empty($pass)) {
    header('Location: ../warning.php?error=missing_password');
    exit;
}

$stmt = $conn->prepare("SELECT edit_pass FROM settings LIMIT 1");
if (!$stmt) {
    die('خطأ في تحضير الاستعلام: ' . $conn->error);
}

$stmt->execute();
$result = $stmt->get_result();
$rowstg = $result->fetch_assoc();
$stmt->close();

if (!$rowstg) {
    header('Location: ../warning.php?error=settings_not_found');
    exit;
}

if ($pass !== $rowstg['edit_pass']) {
    header('Location: ../warning.php?q=' . urlencode($q) . '&error=invalid_password');
    exit;
}

try {
    $invoice = InvoiceProcessor::getActiveInvoice($conn, $id);
    $pro_tybe = intval($invoice['pro_tybe']);
    InvoiceProcessor::softDelete($conn, $id);
} catch (RuntimeException $e) {
    if ($e->getMessage() === 'invoice_not_found') {
        header('Location: ../warning.php?q=' . urlencode($q) . '&error=invoice_not_found');
        exit;
    }
    header('Location: ../warning.php?q=' . urlencode($q) . '&error=delete_failed&id=' . $id . '&msg=' . urlencode($e->getMessage()));
    exit;
} catch (Exception $e) {
    error_log('Delete Error - Invoice ID: ' . $id . ' - Error: ' . $e->getMessage());
    header('Location: ../warning.php?q=' . urlencode($q) . '&error=delete_failed&id=' . $id . '&msg=' . urlencode($e->getMessage()));
    exit;
}

$stmt = $conn->prepare("SELECT pos_type FROM settings LIMIT 1");
$stmt->execute();
$settings = $stmt->get_result()->fetch_assoc();
$stmt->close();

$pos_type = $settings['pos_type'] ?? 'barcode';
$pos_page = ($pos_type === 'clothes') ? '../pos_clothes.php' : '../pos_barcode.php';

$return_url = isset($_POST['return_url']) ? $_POST['return_url'] : '';

if (!empty($return_url)) {
    $return_url = preg_replace('/([?&])success=[^&]*(&|$)/', '$1', $return_url);
    $return_url = rtrim($return_url, '?&');
    $separator = strpos($return_url, '?') !== false ? '&' : '?';
    header("Location: $return_url{$separator}success=deleted");
} else {
    $redirects = [
        InvoiceProcessor::INVOICE_TYPES['PURCHASE'] => '../operations_summary.php?q=purchase',
        InvoiceProcessor::INVOICE_TYPES['SALES'] => '../operations_summary.php?q=sale',
        InvoiceProcessor::INVOICE_TYPES['POS'] => $pos_page
    ];

    $redirect = $redirects[$pro_tybe] ?? '../operations_summary.php?q=' . urlencode($q);
    $separator = strpos($redirect, '?') !== false ? '&' : '?';
    header("Location: $redirect{$separator}success=deleted");
}
exit;
