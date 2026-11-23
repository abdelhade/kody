<?php
session_start();
include('../includes/connect.php');
require_once __DIR__ . '/../classes/InvoiceProcessor.php';

$usid = (int) ($_SESSION['userid'] ?? 0);
$proTybe = InvoiceProcessor::ACCOUNTING_TYPES['MULTI_JOURNAL'];
$typeName = 'قيد متعدد';

$jdate = trim((string) ($_POST['jdate'] ?? ''));
$details = trim((string) ($_POST['details'] ?? ''));
$postedJournalId = (int) ($_POST['journal_id'] ?? 0);

$depitVals = (isset($_POST['depitval']) && is_array($_POST['depitval'])) ? $_POST['depitval'] : [];
$depitNames = (isset($_POST['depitname']) && is_array($_POST['depitname'])) ? $_POST['depitname'] : [];
$creditVals = (isset($_POST['creditval']) && is_array($_POST['creditval'])) ? $_POST['creditval'] : [];
$creditNames = (isset($_POST['creditname']) && is_array($_POST['creditname'])) ? $_POST['creditname'] : [];

$debits = [];
$credits = [];
foreach ($depitVals as $key => $value) {
    $amount = round((float) $value, 2);
    $accountId = (int) ($depitNames[$key] ?? 0);
    if ($amount > 0 && $accountId > 0) {
        $debits[] = ['account' => $accountId, 'amount' => $amount];
    }
}
foreach ($creditVals as $key => $value) {
    $amount = round((float) $value, 2);
    $accountId = (int) ($creditNames[$key] ?? 0);
    if ($amount > 0 && $accountId > 0) {
        $credits[] = ['account' => $accountId, 'amount' => $amount];
    }
}

$debitTotal = 0.0;
$creditTotal = 0.0;
foreach ($debits as $line) {
    $debitTotal += $line['amount'];
}
foreach ($credits as $line) {
    $creditTotal += $line['amount'];
}
$debitTotal = round($debitTotal, 2);
$creditTotal = round($creditTotal, 2);

$valid = $details !== ''
    && $jdate !== ''
    && $debits !== []
    && $credits !== []
    && abs($debitTotal - $creditTotal) < 0.009
    && $debitTotal > 0;

if (!$valid) {
    header('Location: ../addmulti_journal.php?error=1');
    exit;
}

$conn->begin_transaction();
try {
    $stmtNext = $conn->prepare(
        'SELECT COALESCE(MAX(CAST(pro_id AS UNSIGNED)), 0) + 1 AS next_id FROM ot_head WHERE pro_tybe = ?'
    );
    $stmtNext->bind_param('i', $proTybe);
    $stmtNext->execute();
    $proId = (int) ($stmtNext->get_result()->fetch_assoc()['next_id'] ?? 1);
    $stmtNext->close();

    $acc1 = (int) $debits[0]['account'];
    $acc2 = (int) $credits[0]['account'];
    $isFinance = 1;
    $isJournal = 1;
    $isStock = 0;
    $branchId = 1;

    $stmtHead = $conn->prepare(
        'INSERT INTO ot_head (
            pro_id, branch_id, pro_tybe, is_stock, is_finance, is_journal, journal_tybe,
            info, pro_date, pro_num, acc1, acc2, pro_value, user
         ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
    );
    $proNum = (string) $proId;
    $stmtHead->bind_param(
        'iiiiiiisssiiidi',
        $proId,
        $branchId,
        $proTybe,
        $isStock,
        $isFinance,
        $isJournal,
        $proTybe,
        $details,
        $jdate,
        $proNum,
        $acc1,
        $acc2,
        $debitTotal,
        $usid
    );
    if (!$stmtHead->execute()) {
        throw new RuntimeException('تعذر حفظ القيد');
    }
    $opId = (int) $conn->insert_id;
    $stmtHead->close();

    if ($postedJournalId < 1) {
        $rowJournal = $conn->query('SELECT COALESCE(MAX(journal_id), 0) + 1 AS next_id FROM journal_heads')->fetch_assoc();
        $postedJournalId = (int) ($rowJournal['next_id'] ?? 1);
    }

    $stmtJh = $conn->prepare(
        'INSERT INTO journal_heads (journal_id, op_id, total, jdate, details, pro_tybe, op2, user)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
    );
    $stmtJh->bind_param(
        'iidssiii',
        $postedJournalId,
        $opId,
        $debitTotal,
        $jdate,
        $details,
        $proTybe,
        $opId,
        $usid
    );
    if (!$stmtJh->execute()) {
        throw new RuntimeException('تعذر حفظ القيد');
    }
    $journalLastId = (int) $conn->insert_id;
    $stmtJh->close();

    $stmtDebit = $conn->prepare(
        'INSERT INTO journal_entries (journal_id, account_id, debit, credit, tybe, info, op2)
         VALUES (?, ?, ?, 0, 0, ?, ?)'
    );
    foreach ($debits as $line) {
        $accountId = (int) $line['account'];
        $amount = (float) $line['amount'];
        $stmtDebit->bind_param('iidsi', $journalLastId, $accountId, $amount, $typeName, $opId);
        if (!$stmtDebit->execute()) {
            throw new RuntimeException('تعذر حفظ سطر المدين');
        }
    }
    $stmtDebit->close();

    $stmtCredit = $conn->prepare(
        'INSERT INTO journal_entries (journal_id, account_id, debit, credit, tybe, info, op2)
         VALUES (?, ?, 0, ?, 1, ?, ?)'
    );
    foreach ($credits as $line) {
        $accountId = (int) $line['account'];
        $amount = (float) $line['amount'];
        $stmtCredit->bind_param('iidsi', $journalLastId, $accountId, $amount, $typeName, $opId);
        if (!$stmtCredit->execute()) {
            throw new RuntimeException('تعذر حفظ سطر الدائن');
        }
    }
    $stmtCredit->close();

    $stmtBal = $conn->prepare(
        'UPDATE acc_head SET balance = (
            SELECT COALESCE(SUM(debit) - SUM(credit), 0) FROM journal_entries
            WHERE account_id = ? AND isdeleted = 0
         ) WHERE id = ?'
    );
    $touched = [];
    foreach (array_merge($debits, $credits) as $line) {
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
    header('Location: ../addmulti_journal.php?error=1');
    exit;
}

header('Location: ../daily_journal.php');
exit;
