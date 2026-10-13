<?php
include('../includes/connect.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user = isset($_SESSION['userid']) ? (int)$_SESSION['userid'] : 1;
$vdate = !empty($_POST['vdate']) ? trim((string)$_POST['vdate']) : date('Y-m-d');
$tybe = isset($_POST['tybe']) ? (int)$_POST['tybe'] : 1;
$val = isset($_POST['val']) ? (float)$_POST['val'] : 0.0;
$account = isset($_POST['account']) ? (int)$_POST['account'] : 0;
$fund_account = isset($_POST['fund_account']) ? (int)$_POST['fund_account'] : 0;

if ($val <= 0 || $account <= 0 || $fund_account <= 0) {
    header('Location: ../vouchers.php?error=invalid_data');
    exit;
}

if ($tybe === 16 && $account === $fund_account) {
    header('Location: ../add_voucher.php?t=transfer&error=same_account');
    exit;
}

if ($tybe === 1) {
    // سند قبض (وارد): الخزينة تستلم (مدين) - العميل/الطرف يدفع (دائن)
    $debit_acc = $fund_account;
    $credit_acc = $account;
    $journal_desc = "سند قبض وارد رقم " . $voucher_id . ($info !== '' ? " - " . $info : "");
    $redirectT = 'recive';
} elseif ($tybe === 16) {
    // تحويل نقدية بين الصناديق: الصندوق المستلم (مدين) - الصندوق المسلّم (دائن)
    $debit_acc = $account;
    $credit_acc = $fund_account;
    $journal_desc = "تحويل نقدية بين الصناديق رقم " . $voucher_id . ($info !== '' ? " - " . $info : "");
    $redirectT = 'transfer';
} else {
    // سند صرف (صادر): الطرف المصروف له (مدين) - الخزينة تدفع (دائن)
    $debit_acc = $account;
    $credit_acc = $fund_account;
    $journal_desc = "سند صرف ودفع رقم " . $voucher_id . ($info !== '' ? " - " . $info : "");
    $redirectT = 'payment';
}

$voucher_id = isset($_POST['voucher_id']) ? (int)$_POST['voucher_id'] : 1;
$cost_center_val = !empty($_POST['cost_center']) ? (int)$_POST['cost_center'] : 0;
$cost_center_sql = ($cost_center_val > 0) ? "'$cost_center_val'" : "NULL";
$info = isset($_POST['info']) ? trim((string)$_POST['info']) : '';
$escaped_info = $conn->real_escape_string($info);

// 1. إدراج رأس السند في ot_head
$sql1 = "INSERT INTO ot_head (pro_id, branch_id, pro_tybe, is_finance, is_journal, journal_tybe, info, pro_date, pro_num, acc1, acc2, acc_fund, pro_value, cost_center, user) 
         VALUES ('$voucher_id', '1', '$tybe', '1', '1', '$tybe', '$escaped_info', '$vdate', '$voucher_id', '$debit_acc', '$credit_acc', '$fund_account', '$val', $cost_center_sql, '$user')";
$conn->query($sql1);
$ins_voucher = (int)$conn->insert_id;

// 2. حساب رقم القيد التالي
$rowjournal = $conn->query("SELECT COALESCE(MAX(journal_id), 0) AS max_id FROM journal_heads")->fetch_assoc();
$journal_id = ((int)($rowjournal['max_id'] ?? 0)) + 1;

$escaped_desc = $conn->real_escape_string($journal_desc);

// 3. إدراج رأس قيد اليومية
$conn->query("INSERT INTO journal_heads (journal_id, op_id, total, jdate, details, pro_tybe, op2, user) 
              VALUES ('$journal_id', '$ins_voucher', '$val', '$vdate', '$escaped_desc', '$tybe', '$ins_voucher', '$user')");
$journal_lastid = (int)$conn->insert_id;

// 4. إدراج أطراف القيد في journal_entries
// الطرف المدين (الخزينة في القبض / الحساب المقابل في الصرف / الخزينة المستلمة في التحويل)
$conn->query("INSERT INTO journal_entries (journal_id, account_id, debit, credit, tybe, op2) 
              VALUES ('$journal_lastid', '$debit_acc', '$val', 0, 0, '$ins_voucher')");

// الطرف الدائن (الحساب المقابل في القبض / الخزينة في الصرف / الخزينة المسلّمة في التحويل)
$conn->query("INSERT INTO journal_entries (journal_id, account_id, debit, credit, tybe, op2) 
              VALUES ('$journal_lastid', '$credit_acc', 0, '$val', 1, '$ins_voucher')");

// 5. مزامنة عمود balance في acc_head
$conn->query("UPDATE acc_head SET balance = (SELECT COALESCE(SUM(debit) - SUM(credit), 0) FROM journal_entries WHERE account_id = '$debit_acc' AND isdeleted = 0) WHERE id = '$debit_acc'");
$conn->query("UPDATE acc_head SET balance = (SELECT COALESCE(SUM(debit) - SUM(credit), 0) FROM journal_entries WHERE account_id = '$credit_acc' AND isdeleted = 0) WHERE id = '$credit_acc'");

$conn->query("INSERT INTO `process` (`type`) VALUES ('add voucher')");

if (!empty($_POST['ins_id'])) {
    $ins = (int)$_POST['ins_id'];
    $conn->query("UPDATE myinstallments SET ins_case = 3, ins_paid = '$val', voucher = '$ins_voucher' WHERE id = '$ins'");
}

header('Location: ../vouchers.php?t=' . $redirectT . '&msg=saved');
exit;
