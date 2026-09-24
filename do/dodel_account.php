<?php
include '../includes/connect.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    header('location:../accounts.php');
    exit;
}

function accountDeleteBlocked(string $message): void
{
    echo "<center><br><br><br><h1 class='bg-danger' style='padding: 20px; border-radius: 10px; display: inline-block; font-family: sans-serif; color: #fff;'>"
        . htmlspecialchars($message)
        . "<br><br><button class='btn btn-light' style='padding: 10px 20px; font-size: 20px; cursor: pointer;' onclick='history.go(-1);'>رجوع</button>"
        . "</h1></center>";
    exit;
}

$account = $conn->query("SELECT id, deletable, is_basic FROM acc_head WHERE id = $id")->fetch_assoc();
if (!$account) {
    accountDeleteBlocked('الحساب غير موجود');
}

if ((int) $account['is_basic'] === 1 || (int) $account['deletable'] === 0) {
    accountDeleteBlocked('لا يمكن حذف هذا الحساب لأنه حساب أساسي في النظام');
}

$ops = $conn->query(
    "SELECT COUNT(*) AS c FROM journal_entries
     WHERE account_id = $id AND COALESCE(isdeleted, 0) = 0"
)->fetch_assoc();
if ((int) $ops['c'] > 0) {
    accountDeleteBlocked('لا يمكن حذف هذا الحساب لوجود عمليات مرتبطة به. احذف العمليات أولاً ثم حاول مرة أخرى');
}

$children = $conn->query(
    "SELECT COUNT(*) AS c FROM acc_head
     WHERE parent_id = $id AND COALESCE(isdeleted, 0) = 0"
)->fetch_assoc();
if ((int) $children['c'] > 0) {
    accountDeleteBlocked('لا يمكن حذف هذا الحساب لوجود حسابات فرعية مرتبطة به');
}

$linkedOps = $conn->query(
    "SELECT COUNT(*) AS c FROM ot_head
     WHERE (acc1 = $id OR acc2 = $id OR acc_fund = $id)
       AND COALESCE(isdeleted, 0) = 0"
)->fetch_assoc();
if ((int) $linkedOps['c'] > 0) {
    accountDeleteBlocked('لا يمكن حذف هذا الحساب لوجود فواتير أو سندات مرتبطة به');
}

// إزالة القيود المحذوفة منطقياً حتى لا تمنع الـ FK الحذف الكامل
$conn->query("DELETE FROM journal_entries WHERE account_id = $id AND isdeleted = 1");

if (!$conn->query("DELETE FROM acc_head WHERE id = $id")) {
    accountDeleteBlocked('فشل حذف الحساب: قد يكون مرتبطاً ببيانات أخرى في النظام');
}

$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
if (strpos($referer, 'accounts.php') !== false) {
    header('location:../accounts.php');
} else {
    header('location:../acc_report.php');
}
exit;
