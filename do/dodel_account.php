<?php
require_once __DIR__ . '/../includes/connect.php';

$id = isset($_REQUEST['id']) ? (int) $_REQUEST['id'] : 0;
if ($id <= 0) {
    header('Location: ../accounts.php');
    exit;
}

function redirectAccountError(string $message): void
{
    header('Location: ../accounts.php?error=' . urlencode($message));
    exit;
}

$stmt = $conn->prepare("SELECT id, deletable, is_basic FROM acc_head WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$account = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$account) {
    redirectAccountError('الحساب غير موجود');
}

if ((int) $account['is_basic'] === 1 || (int) $account['deletable'] === 0) {
    redirectAccountError('لا يمكن حذف هذا الحساب لأنه حساب أساسي في النظام');
}

$chkEntries = $conn->prepare("SELECT COUNT(*) AS c FROM journal_entries WHERE account_id = ? AND COALESCE(isdeleted, 0) = 0");
$chkEntries->bind_param("i", $id);
$chkEntries->execute();
$ops = (int)($chkEntries->get_result()->fetch_assoc()['c'] ?? 0);
$chkEntries->close();

if ($ops > 0) {
    redirectAccountError('لا يمكن حذف هذا الحساب لوجود قيود يومية مرتبطة به');
}

$chkChildren = $conn->prepare("SELECT COUNT(*) AS c FROM acc_head WHERE parent_id = ? AND COALESCE(isdeleted, 0) = 0");
$chkChildren->bind_param("i", $id);
$chkChildren->execute();
$children = (int)($chkChildren->get_result()->fetch_assoc()['c'] ?? 0);
$chkChildren->close();

if ($children > 0) {
    redirectAccountError('لا يمكن حذف هذا الحساب لوجود حسابات فرعية متفرعة منه');
}

$chkOt = $conn->prepare("SELECT COUNT(*) AS c FROM ot_head WHERE (acc1 = ? OR acc2 = ? OR acc_fund = ?) AND COALESCE(isdeleted, 0) = 0");
$chkOt->bind_param("iii", $id, $id, $id);
$chkOt->execute();
$linkedOps = (int)($chkOt->get_result()->fetch_assoc()['c'] ?? 0);
$chkOt->close();

if ($linkedOps > 0) {
    redirectAccountError('لا يمكن حذف هذا الحساب لوجود فواتير أو سندات مرتبطة به');
}

// Remove any soft-deleted journal entries so FK won't block
$delSoft = $conn->prepare("DELETE FROM journal_entries WHERE account_id = ? AND isdeleted = 1");
$delSoft->bind_param("i", $id);
$delSoft->execute();
$delSoft->close();

$del = $conn->prepare("DELETE FROM acc_head WHERE id = ?");
$del->bind_param("i", $id);
if (!$del->execute()) {
    $del->close();
    redirectAccountError('فشل حذف الحساب: قد يكون مرتبطاً ببيانات أخرى في النظام');
}
$del->close();

header('Location: ../accounts.php?msg=deleted');
exit;
