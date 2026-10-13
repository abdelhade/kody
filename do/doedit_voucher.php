<?php
include('../includes/connect.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: ../vouchers.php');
    exit;
}

$tybe = isset($_POST['tybe']) ? (int)$_POST['tybe'] : 1;
$account = isset($_POST['account']) ? (int)$_POST['account'] : 0;
$fund_account = isset($_POST['fund_account']) ? (int)$_POST['fund_account'] : 0;
$voucher_id = isset($_POST['voucher_id']) ? (int)$_POST['voucher_id'] : 1;
$vdate = !empty($_POST['vdate']) ? trim((string)$_POST['vdate']) : date('Y-m-d');
$val = isset($_POST['val']) ? (float)$_POST['val'] : 0.0;
$cost_center_val = !empty($_POST['cost_center']) ? (int)$_POST['cost_center'] : 0;
$cost_center_sql = ($cost_center_val > 0) ? "'$cost_center_val'" : "NULL";
$info = isset($_POST['info']) ? trim((string)$_POST['info']) : '';
$escaped_info = $conn->real_escape_string($info);

if ($tybe === 16 && $account === $fund_account) {
    header('Location: ../add_voucher.php?edit=' . $id . '&t=transfer&error=same_account');
    exit;
}

if ($tybe === 1) {
    $acc1 = $fund_account; // مدين (الخزينة)
    $acc2 = $account;      // دائن (العميل)
    $journal_desc = "سند قبض وارد رقم " . $voucher_id . ($info !== '' ? " - " . $info : "");
    $redirectT = 'recive';
} elseif ($tybe === 16) {
    $acc1 = $account;      // مدين (الخزينة المستلمة)
    $acc2 = $fund_account; // دائن (الخزينة المسلّمة)
    $journal_desc = "تحويل نقدية بين الصناديق رقم " . $voucher_id . ($info !== '' ? " - " . $info : "");
    $redirectT = 'transfer';
} else {
    $acc1 = $account;      // مدين (الطرف المقابل)
    $acc2 = $fund_account; // دائن (الخزينة)
    $journal_desc = "سند صرف ودفع رقم " . $voucher_id . ($info !== '' ? " - " . $info : "");
    $redirectT = 'payment';
}

// قراءة الأطراف السابقة قبل التعديل لتحديث أرصدتها أيضاً
$oldRow = $conn->query("SELECT acc1, acc2 FROM ot_head WHERE id = $id LIMIT 1")->fetch_assoc();
$oldAcc1 = $oldRow ? (int)$oldRow['acc1'] : 0;
$oldAcc2 = $oldRow ? (int)$oldRow['acc2'] : 0;

// 1. تحديث ot_head
$sql = "UPDATE `ot_head` SET
`info`='$escaped_info',
`pro_id` = '$voucher_id',
`pro_date`='$vdate',
`acc1`='$acc1',
`acc2`='$acc2',
`pro_value`='$val',
`cost_center`=$cost_center_sql,
`acc_fund`='$fund_account'
 WHERE id = $id";
$conn->query($sql);

// 2. تحديث قيد اليومية المرتبط
$jhead = $conn->query("SELECT id FROM journal_heads WHERE op_id = $id OR op2 = $id LIMIT 1")->fetch_assoc();
$escaped_desc = $conn->real_escape_string($journal_desc);

if ($jhead) {
    $jid = (int)$jhead['id'];
    $conn->query("UPDATE journal_heads SET total = '$val', jdate = '$vdate', details = '$escaped_desc', pro_tybe = '$tybe' WHERE id = $jid");
    $conn->query("DELETE FROM journal_entries WHERE journal_id = $jid");
    $conn->query("INSERT INTO journal_entries (journal_id, account_id, debit, credit, tybe, op2) VALUES ('$jid', '$acc1', '$val', 0, 0, '$id')");
    $conn->query("INSERT INTO journal_entries (journal_id, account_id, debit, credit, tybe, op2) VALUES ('$jid', '$acc2', 0, '$val', 1, '$id')");
} else {
    // في حال عدم وجود قيد سابق، إنشاؤه
    $rowjournal = $conn->query("SELECT COALESCE(MAX(journal_id), 0) AS max_id FROM journal_heads")->fetch_assoc();
    $journal_id = ((int)($rowjournal['max_id'] ?? 0)) + 1;
    $user = isset($_SESSION['userid']) ? (int)$_SESSION['userid'] : 1;
    $conn->query("INSERT INTO journal_heads (journal_id, op_id, total, jdate, details, pro_tybe, op2, user) VALUES ('$journal_id', '$id', '$val', '$vdate', '$escaped_desc', '$tybe', '$id', '$user')");
    $jid = (int)$conn->insert_id;
    $conn->query("INSERT INTO journal_entries (journal_id, account_id, debit, credit, tybe, op2) VALUES ('$jid', '$acc1', '$val', 0, 0, '$id')");
    $conn->query("INSERT INTO journal_entries (journal_id, account_id, debit, credit, tybe, op2) VALUES ('$jid', '$acc2', 0, '$val', 1, '$id')");
}

// 3. مزامنة أرصدة الحسابات المتأثرة (الجديدة والقديمة)
$affectedAccounts = array_unique(array_filter([$acc1, $acc2, $oldAcc1, $oldAcc2]));
foreach ($affectedAccounts as $accId) {
    $conn->query("UPDATE acc_head SET balance = (SELECT COALESCE(SUM(debit) - SUM(credit), 0) FROM journal_entries WHERE account_id = '$accId' AND isdeleted = 0) WHERE id = '$accId'");
}

header('Location: ../vouchers.php?t=' . $redirectT . '&msg=updated');
exit;
