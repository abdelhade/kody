<?php
require_once __DIR__ . '/../includes/connect.php';
require_once __DIR__ . '/../classes/InvoiceProcessor.php';

$isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
    || isset($_REQUEST['ajax'])
    || (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json'));

$sendResponse = function (bool $success, string $message, int $statusCode = 200, string $redirectParam = '') use ($isAjax) {
    if ($isAjax) {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode([
            'success' => $success,
            'message' => $message,
            'error'   => $success ? '' : $message,
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    if ($success) {
        header('Location: ../vouchers.php?' . $redirectParam . '&msg=deleted');
    } else {
        header('Location: ../vouchers.php?' . $redirectParam . ($redirectParam ? '&' : '') . 'error=' . urlencode($message));
    }
    exit;
};

$id = isset($_REQUEST['del']) ? (int)$_REQUEST['del'] : (isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0);
if ($id <= 0) {
    $sendResponse(false, 'معرّف السند غير صحيح', 400);
}

$stmt = $conn->prepare('SELECT pro_tybe, pro_id FROM ot_head WHERE id = ? LIMIT 1');
$stmt->bind_param('i', $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$row) {
    $sendResponse(false, 'السند غير موجود أو تم حذفه مسبقاً', 404);
}

$pro_tybe = (int) $row['pro_tybe'];
if ($pro_tybe !== 1 && $pro_tybe !== 2 && $pro_tybe !== 16) {
    $sendResponse(false, 'هذا السند مرتبط بعمليات أخرى ولا يمكن حذفه من هنا', 400);
}

// فحص الصلاحية
if (!Can::check($pro_tybe === 1 ? 'receipts.delete' : 'payments.delete')) {
    $sendResponse(false, 'ليس لديك صلاحية لحذف هذا السند', 403);
}

$redirectParam = ($pro_tybe === 1) ? 't=recive' : (($pro_tybe === 16) ? 't=transfer' : 't=payment');

$linked = InvoiceProcessor::linkedInvoiceOfVoucher($conn, $id);
if ($linked) {
    $msg = 'لا يمكن حذف السند لأنه مرتبط بالفاتورة رقم ' . $linked['pro_id'] . '، احذف أو عدّل الفاتورة نفسها';
    $sendResponse(false, $msg, 400, $redirectParam);
}

InvoiceProcessor::hardDelete($conn, $id);

$sendResponse(true, 'تم حذف السند بنجاح', 200, $redirectParam);

