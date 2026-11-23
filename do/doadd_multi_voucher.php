<?php
include('../includes/connect.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../classes/InvoiceProcessor.php';

$user = isset($_SESSION['userid']) ? (int) $_SESSION['userid'] : 0;
$tybe = isset($_POST['tybe']) ? (int) $_POST['tybe'] : 0;
$kind = (isset($_POST['voucher_kind']) && $_POST['voucher_kind'] === 'payment') ? 'payment' : 'recive';

$multiReceipt = InvoiceProcessor::ACCOUNTING_TYPES['MULTI_RECEIPT'];
$multiPayment = InvoiceProcessor::ACCOUNTING_TYPES['MULTI_PAYMENT'];
if ($tybe !== $multiReceipt && $tybe !== $multiPayment) {
    $tybe = ($kind === 'payment') ? $multiPayment : $multiReceipt;
}
$isPayment = ($tybe === $multiPayment);
$redirectT = $isPayment ? 'payment' : 'recive';
$typeName = $isPayment ? 'سند دفع متعدد' : 'سند قبض متعدد';

$formToken = isset($_POST['form_token']) ? (string) $_POST['form_token'] : '';
if ($formToken === ''
    || empty($_SESSION['multi_voucher_form_token'])
    || !hash_equals((string) $_SESSION['multi_voucher_form_token'], $formToken)
) {
    header('Location: ../vouchers.php?t=' . $redirectT . '&msg=saved');
    exit;
}
unset($_SESSION['multi_voucher_form_token']);

UserDefaults::enforce($conn, $_POST, ['fund_account' => 'fund']);

$vdate = !empty($_POST['vdate']) ? trim((string) $_POST['vdate']) : date('Y-m-d');
$info = isset($_POST['info']) ? trim((string) $_POST['info']) : '';
$fundAccount = isset($_POST['fund_account']) ? (int) $_POST['fund_account'] : 0;

$partyAccounts = (isset($_POST['party_account']) && is_array($_POST['party_account'])) ? $_POST['party_account'] : [];
$partyAmounts = (isset($_POST['party_amount']) && is_array($_POST['party_amount'])) ? $_POST['party_amount'] : [];
$partyNotes = (isset($_POST['party_note']) && is_array($_POST['party_note'])) ? $_POST['party_note'] : [];

$lines = [];
$seen = [];
$total = 0.0;
foreach ($partyAccounts as $idx => $accRaw) {
    $accountId = (int) $accRaw;
    $amount = round((float) str_replace(',', '', (string) ($partyAmounts[$idx] ?? 0)), 2);
    $note = trim((string) ($partyNotes[$idx] ?? ''));
    if ($accountId <= 0 || $amount <= 0) {
        continue;
    }
    if ($accountId === $fundAccount || isset($seen[$accountId])) {
        header('Location: ../add_multi_voucher.php?t=' . $redirectT . '&error=1');
        exit;
    }
    $seen[$accountId] = true;
    $lines[] = ['account' => $accountId, 'amount' => $amount, 'note' => $note];
    $total += $amount;
}
$total = round($total, 2);

if ($fundAccount <= 0 || $lines === [] || $total <= 0) {
    header('Location: ../add_multi_voucher.php?t=' . $redirectT . '&error=1');
    exit;
}

// تأكد إن الصندوق فعلاً خزنة
$stmtFund = $conn->prepare('SELECT id FROM acc_head WHERE id = ? AND is_basic = 0 AND is_fund = 1 LIMIT 1');
$stmtFund->bind_param('i', $fundAccount);
$stmtFund->execute();
$fundOk = (bool) $stmtFund->get_result()->fetch_assoc();
$stmtFund->close();
if (!$fundOk) {
    header('Location: ../add_multi_voucher.php?t=' . $redirectT . '&error=1');
    exit;
}

$firstParty = (int) $lines[0]['account'];
if ($isPayment) {
    // دفع: الأطراف مدينة، الصندوق دائن
    $acc1 = $firstParty;
    $acc2 = $fundAccount;
    $descPrefix = 'سند دفع متعدد رقم ';
} else {
    // قبض: الصندوق مدين، الأطراف دائنة
    $acc1 = $fundAccount;
    $acc2 = $firstParty;
    $descPrefix = 'سند قبض متعدد رقم ';
}

$conn->begin_transaction();
try {
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
    $voucherId = (int) ($stmtNext->get_result()->fetch_assoc()['next_id'] ?? 1);
    $stmtNext->close();

    $journalDesc = $descPrefix . $voucherId . ($info !== '' ? ' - ' . $info : '');
    $branchId = 1;
    $isFinance = 1;
    $isJournal = 1;
    $proNum = (string) $voucherId;

    $stmtHead = $conn->prepare(
        'INSERT INTO ot_head (
            pro_id, branch_id, pro_tybe, is_finance, is_journal, journal_tybe,
            info, pro_date, pro_num, acc1, acc2, acc_fund, pro_value, user
         ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
    );
    $stmtHead->bind_param(
        'iiiiiisssiiidi',
        $voucherId,
        $branchId,
        $tybe,
        $isFinance,
        $isJournal,
        $tybe,
        $info,
        $vdate,
        $proNum,
        $acc1,
        $acc2,
        $fundAccount,
        $total,
        $user
    );
    if (!$stmtHead->execute()) {
        throw new RuntimeException('تعذر حفظ السند');
    }
    $opId = (int) $conn->insert_id;
    $stmtHead->close();

    $rowJournal = $conn->query('SELECT COALESCE(MAX(journal_id), 0) + 1 AS next_id FROM journal_heads')->fetch_assoc();
    $journalId = (int) ($rowJournal['next_id'] ?? 1);

    $stmtJh = $conn->prepare(
        'INSERT INTO journal_heads (journal_id, op_id, total, jdate, details, pro_tybe, op2, user)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
    );
    $stmtJh->bind_param(
        'iidssiii',
        $journalId,
        $opId,
        $total,
        $vdate,
        $journalDesc,
        $tybe,
        $opId,
        $user
    );
    if (!$stmtJh->execute()) {
        throw new RuntimeException('تعذر حفظ رأس القيد');
    }
    $journalLastId = (int) $conn->insert_id;
    $stmtJh->close();

    $stmtDebit = $conn->prepare(
        'INSERT INTO journal_entries (journal_id, account_id, debit, credit, tybe, info, op2)
         VALUES (?, ?, ?, 0, 0, ?, ?)'
    );
    $stmtCredit = $conn->prepare(
        'INSERT INTO journal_entries (journal_id, account_id, debit, credit, tybe, info, op2)
         VALUES (?, ?, 0, ?, 1, ?, ?)'
    );

    if ($isPayment) {
        foreach ($lines as $line) {
            $accountId = (int) $line['account'];
            $amount = (float) $line['amount'];
            $lineInfo = $line['note'] !== '' ? $line['note'] : $typeName;
            $stmtDebit->bind_param('iidsi', $journalLastId, $accountId, $amount, $lineInfo, $opId);
            if (!$stmtDebit->execute()) {
                throw new RuntimeException('تعذر حفظ سطر المدين');
            }
        }
        $fundInfo = $typeName;
        $stmtCredit->bind_param('iidsi', $journalLastId, $fundAccount, $total, $fundInfo, $opId);
        if (!$stmtCredit->execute()) {
            throw new RuntimeException('تعذر حفظ سطر الدائن');
        }
    } else {
        $fundInfo = $typeName;
        $stmtDebit->bind_param('iidsi', $journalLastId, $fundAccount, $total, $fundInfo, $opId);
        if (!$stmtDebit->execute()) {
            throw new RuntimeException('تعذر حفظ سطر المدين');
        }
        foreach ($lines as $line) {
            $accountId = (int) $line['account'];
            $amount = (float) $line['amount'];
            $lineInfo = $line['note'] !== '' ? $line['note'] : $typeName;
            $stmtCredit->bind_param('iidsi', $journalLastId, $accountId, $amount, $lineInfo, $opId);
            if (!$stmtCredit->execute()) {
                throw new RuntimeException('تعذر حفظ سطر الدائن');
            }
        }
    }
    $stmtDebit->close();
    $stmtCredit->close();

    $stmtBal = $conn->prepare(
        'UPDATE acc_head SET balance = (
            SELECT COALESCE(SUM(debit) - SUM(credit), 0) FROM journal_entries
            WHERE account_id = ? AND isdeleted = 0
         ) WHERE id = ?'
    );
    $touched = [$fundAccount => $fundAccount];
    foreach ($lines as $line) {
        $touched[$line['account']] = $line['account'];
    }
    foreach ($touched as $accountId) {
        $balanceId = (int) $accountId;
        $stmtBal->bind_param('ii', $balanceId, $balanceId);
        $stmtBal->execute();
    }
    $stmtBal->close();

    $stmtProcess = $conn->prepare('INSERT INTO `process` (`type`) VALUES (?)');
    $stmtProcess->bind_param('s', $typeName);
    $stmtProcess->execute();
    $stmtProcess->close();

    $conn->commit();
} catch (Throwable $e) {
    $conn->rollback();
    header('Location: ../add_multi_voucher.php?t=' . $redirectT . '&error=1');
    exit;
}

header('Location: ../vouchers.php?t=' . $redirectT . '&msg=saved');
exit;
