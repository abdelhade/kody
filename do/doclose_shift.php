<?php
include(__DIR__ . '/../includes/connect.php');
require_once __DIR__ . '/../classes/ShiftCloser.php';

$isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
    || !empty($_POST['ajax']);

function shift_close_respond(bool $isAjax, bool $ok, string $message, int $shiftId = 0, float $diff = 0.0): void
{
    if ($isAjax) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => $ok,
            'message' => $message,
            'error' => $ok ? null : $message,
            'shift_id' => $shiftId,
            'total_diff' => $diff,
            'receipt_url' => $shiftId > 0 ? 'print/closed_session_receipt.php?id=' . $shiftId : null,
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    if ($ok) {
        $_SESSION['success_message'] = $message;
        header('Location: ../print/closed_session_receipt.php?id=' . $shiftId);
    } else {
        $_SESSION['error_message'] = $message;
        header('Location: ../z_report.php');
    }
    exit;
}

if (!isset($_SESSION['userid'])) {
    shift_close_respond($isAjax, false, 'الرجاء تسجيل الدخول أولاً');
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    shift_close_respond($isAjax, false, 'طلب غير صالح');
}

$userId = (int) $_SESSION['userid'];
$closer = new ShiftCloser($conn);

$counted = isset($_POST['counted']) && is_array($_POST['counted']) ? $_POST['counted'] : [];
if (empty($counted)) {
    $legacyAmount = $_POST['actual_cash'] ?? $_POST['cash'] ?? null;
    if ($legacyAmount !== null && $legacyAmount !== '') {
        $primary = $closer->preview($userId)['primary_fund'];
        if ($primary > 0) {
            $counted[$primary] = (float) $legacyAmount;
        }
    }
}

$leftInDrawer = (float) ($_POST['left_in_drawer'] ?? 0);
$notes = (string) ($_POST['notes'] ?? $_POST['info'] ?? '');
$businessDate = isset($today) ? $today : date('Y-m-d');

try {
    $result = $closer->close($userId, $counted, $leftInDrawer, $notes, $businessDate);
} catch (Throwable $e) {
    error_log('Shift Close Error: ' . $e->getMessage());
    $msg = $e instanceof RuntimeException ? $e->getMessage() : 'حدث خطأ أثناء إغلاق الشيفت';
    shift_close_respond($isAjax, false, $msg);
}

$diff = $result['total_diff'];
if (abs($diff) < 0.01) {
    $msg = 'تم إغلاق الشيفت بنجاح — لا يوجد فرق';
} elseif ($diff < 0) {
    $msg = 'تم إغلاق الشيفت — عجز: ' . number_format(abs($diff), 2);
} else {
    $msg = 'تم إغلاق الشيفت — زيادة: ' . number_format($diff, 2);
}

shift_close_respond($isAjax, true, $msg, $result['shift_id'], $diff);
