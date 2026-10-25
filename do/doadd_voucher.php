<?php
include('../includes/connect.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user = isset($_SESSION['userid']) ? (int)$_SESSION['userid'] : 1;
$tybe = isset($_POST['tybe']) ? (int)$_POST['tybe'] : 1;
$redirectT = ($tybe === 1) ? 'recive' : (($tybe === 16) ? 'transfer' : 'payment');

// منع تكرار الإرسال: التوكن إجباري ويُستهلك مرة واحدة
$formToken = isset($_POST['form_token']) ? (string)$_POST['form_token'] : '';
if ($formToken === ''
    || empty($_SESSION['voucher_form_token'])
    || !hash_equals((string)$_SESSION['voucher_form_token'], $formToken)
) {
    header('Location: ../vouchers.php?t=' . $redirectT . '&msg=saved');
    exit;
}
unset($_SESSION['voucher_form_token']);

UserDefaults::enforce($conn, $_POST, ['fund_account' => 'fund']);
$vdate = !empty($_POST['vdate']) ? trim((string)$_POST['vdate']) : date('Y-m-d');
$val = isset($_POST['val']) ? (float)$_POST['val'] : 0.0;
$account = isset($_POST['account']) ? (int)$_POST['account'] : 0;
$fund_account = isset($_POST['fund_account']) ? (int)$_POST['fund_account'] : 0;
$cost_center_val = !empty($_POST['cost_center']) ? (int)$_POST['cost_center'] : 0;
$info = isset($_POST['info']) ? trim((string)$_POST['info']) : '';

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
    $descPrefix = 'سند قبض وارد رقم ';
} elseif ($tybe === 16) {
    // تحويل نقدية بين الصناديق: الصندوق المستلم (مدين) - الصندوق المسلّم (دائن)
    $debit_acc = $account;
    $credit_acc = $fund_account;
    $descPrefix = 'تحويل نقدية بين الصناديق رقم ';
} else {
    // سند صرف (صادر): الطرف المصروف له (مدين) - الخزينة تدفع (دائن)
    $debit_acc = $account;
    $credit_acc = $fund_account;
    $descPrefix = 'سند صرف ودفع رقم ';
}

$conn->begin_transaction();
try {
    // قفل آخر صف من نفس النوع ثم حساب الرقم — يمنع تكرار الرقم بين طلبين متزامنين
    $stmtLock = $conn->prepare('SELECT id FROM ot_head WHERE pro_tybe = ? ORDER BY id DESC LIMIT 1 FOR UPDATE');
    $stmtLock->bind_param('i', $tybe);
    $stmtLock->execute();
    $stmtLock->get_result()->fetch_assoc();
    $stmtLock->close();

    $stmtNext = $conn->prepare(
        'SELECT COALESCE(MAX(CAST(pro_id AS UNSIGNED)), 0) + 1 AS next_id FROM ot_head WHERE pro_tybe = ?'
    );
    $stmtNext->bind_param('i', $tybe);
    $stmtNext->execute();
    $voucher_id = (int)($stmtNext->get_result()->fetch_assoc()['next_id'] ?? 1);
    $stmtNext->close();

    $journal_desc = $descPrefix . $voucher_id . ($info !== '' ? ' - ' . $info : '');

    $branch_id = 1;
    $is_finance = 1;
    $is_journal = 1;

    if ($cost_center_val > 0) {
        $stmtHead = $conn->prepare(
            'INSERT INTO ot_head (
                pro_id, branch_id, pro_tybe, is_finance, is_journal, journal_tybe,
                info, pro_date, pro_num, acc1, acc2, acc_fund, pro_value, cost_center, user
             ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmtHead->bind_param(
            'iiiiiissiiiidii',
            $voucher_id,
            $branch_id,
            $tybe,
            $is_finance,
            $is_journal,
            $tybe,
            $info,
            $vdate,
            $voucher_id,
            $debit_acc,
            $credit_acc,
            $fund_account,
            $val,
            $cost_center_val,
            $user
        );
    } else {
        $stmtHead = $conn->prepare(
            'INSERT INTO ot_head (
                pro_id, branch_id, pro_tybe, is_finance, is_journal, journal_tybe,
                info, pro_date, pro_num, acc1, acc2, acc_fund, pro_value, cost_center, user
             ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NULL, ?)'
        );
        $stmtHead->bind_param(
            'iiiiiissiiiidi',
            $voucher_id,
            $branch_id,
            $tybe,
            $is_finance,
            $is_journal,
            $tybe,
            $info,
            $vdate,
            $voucher_id,
            $debit_acc,
            $credit_acc,
            $fund_account,
            $val,
            $user
        );
    }
    if (!$stmtHead->execute()) {
        throw new Exception('فشل إدخال السند: ' . $stmtHead->error);
    }
    $ins_voucher = (int)$conn->insert_id;
    $stmtHead->close();

    $rowjournal = $conn->query('SELECT COALESCE(MAX(journal_id), 0) AS max_id FROM journal_heads')->fetch_assoc();
    $journal_id = ((int)($rowjournal['max_id'] ?? 0)) + 1;

    $stmtJh = $conn->prepare(
        'INSERT INTO journal_heads (journal_id, op_id, total, jdate, details, pro_tybe, op2, user)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
    );
    $stmtJh->bind_param(
        'iidssiii',
        $journal_id,
        $ins_voucher,
        $val,
        $vdate,
        $journal_desc,
        $tybe,
        $ins_voucher,
        $user
    );
    if (!$stmtJh->execute()) {
        throw new Exception('فشل إدخال رأس القيد: ' . $stmtJh->error);
    }
    $journal_lastid = (int)$conn->insert_id;
    $stmtJh->close();

    $stmtJe = $conn->prepare(
        'INSERT INTO journal_entries (journal_id, account_id, debit, credit, tybe, op2)
         VALUES (?, ?, ?, 0, 0, ?)'
    );
    $stmtJe->bind_param('iidi', $journal_lastid, $debit_acc, $val, $ins_voucher);
    if (!$stmtJe->execute()) {
        throw new Exception('فشل إدخال طرف مدين: ' . $stmtJe->error);
    }
    $stmtJe->close();

    $stmtJe = $conn->prepare(
        'INSERT INTO journal_entries (journal_id, account_id, debit, credit, tybe, op2)
         VALUES (?, ?, 0, ?, 1, ?)'
    );
    $stmtJe->bind_param('iidi', $journal_lastid, $credit_acc, $val, $ins_voucher);
    if (!$stmtJe->execute()) {
        throw new Exception('فشل إدخال طرف دائن: ' . $stmtJe->error);
    }
    $stmtJe->close();

    $stmtBal = $conn->prepare(
        'UPDATE acc_head SET balance = (
            SELECT COALESCE(SUM(debit) - SUM(credit), 0) FROM journal_entries
            WHERE account_id = ? AND isdeleted = 0
         ) WHERE id = ?'
    );
    $stmtBal->bind_param('ii', $debit_acc, $debit_acc);
    $stmtBal->execute();
    $stmtBal->bind_param('ii', $credit_acc, $credit_acc);
    $stmtBal->execute();
    $stmtBal->close();

    $conn->query("INSERT INTO `process` (`type`) VALUES ('add voucher')");

    if (!empty($_POST['ins_id'])) {
        $ins = (int)$_POST['ins_id'];
        $stmtIns = $conn->prepare('UPDATE myinstallments SET ins_case = 3, ins_paid = ?, voucher = ? WHERE id = ?');
        $stmtIns->bind_param('dii', $val, $ins_voucher, $ins);
        $stmtIns->execute();
        $stmtIns->close();
    }

    $conn->commit();
} catch (Exception $e) {
    $conn->rollback();
    header('Location: ../add_voucher.php?t=' . $redirectT . '&error=save_failed');
    exit;
}

header('Location: ../vouchers.php?t=' . $redirectT . '&msg=saved');
exit;
