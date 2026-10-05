<?php
include('../includes/connect.php');
$conn->set_charset('utf8mb4');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

const OPENING_DETAILS = 'قيد الأرصدة الافتتاحية';
const OPENING_DETAILS_CLOSE = 'أرصدة افتتاحية حسابات (قفل مدة)';

function start_balance_fail(mysqli $conn, string $message, bool $inTransaction): void
{
    if ($inTransaction) {
        $conn->rollback();
    }
    $_SESSION['start_balance_error'] = $message;
    header('Location: ../start_balance.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    start_balance_fail($conn, 'طلب غير صالح.', false);
}

if (!isset($_POST['acc_id'], $_POST['newbalance']) || !is_array($_POST['acc_id']) || !is_array($_POST['newbalance'])) {
    start_balance_fail($conn, 'لم تُرسل الأرصدة الافتتاحية. أعد تحميل الصفحة ثم أدخل القيم واحفظ.', false);
}

$accIds = array_values($_POST['acc_id']);
$newBalances = array_values($_POST['newbalance']);
if (count($accIds) === 0 || count($accIds) !== count($newBalances)) {
    start_balance_fail($conn, 'بيانات الأرصدة غير مكتملة. أعد تحميل الصفحة وحاول مرة أخرى.', false);
}

$user = isset($_SESSION['userid']) ? (int) $_SESSION['userid'] : 1;
$info = 'رصيد افتتاحي';
$inTransaction = false;

try {
    $conn->begin_transaction();
    $inTransaction = true;

    $dateRes = $conn->query(
        "SELECT MIN(jdate) AS first_date
         FROM journal_heads
         WHERE isdeleted = 0
           AND details NOT IN ('" . OPENING_DETAILS . "', '" . OPENING_DETAILS_CLOSE . "')"
    );
    $dateRow = $dateRes ? $dateRes->fetch_assoc() : null;
    if (!empty($dateRow['first_date'])) {
        $firstDate = date('Y-m-d', strtotime($dateRow['first_date'] . ' -1 day'));
    } else {
        $firstDate = date('Y-m-d');
    }

    $headRes = $conn->query(
        "SELECT id FROM journal_heads
         WHERE isdeleted = 0
           AND details IN ('" . OPENING_DETAILS . "', '" . OPENING_DETAILS_CLOSE . "')
         ORDER BY jdate ASC, id ASC"
    );
    $openingHeadIds = [];
    if ($headRes) {
        while ($headRow = $headRes->fetch_assoc()) {
            $openingHeadIds[] = (int) $headRow['id'];
        }
    }

    $stmtAcc = $conn->prepare('SELECT id, code, aname, editable, is_basic, isdeleted FROM acc_head WHERE id = ? LIMIT 1');
    $stmtLines = $conn->prepare(
        "SELECT je.id, je.debit, je.credit
         FROM journal_entries je
         INNER JOIN journal_heads jh ON jh.id = je.journal_id
         WHERE je.account_id = ?
           AND je.isdeleted = 0
           AND jh.isdeleted = 0
           AND jh.details IN (?, ?)
         ORDER BY je.id ASC"
    );

    $plans = [];
    $totalDebit = 0.0;
    $totalCredit = 0.0;
    $seen = [];

    for ($i = 0; $i < count($accIds); $i++) {
        $id = (int) $accIds[$i];
        if ($id < 1 || isset($seen[$id])) {
            start_balance_fail($conn, 'قائمة الحسابات غير صالحة أو فيها تكرار.', true);
        }
        $seen[$id] = true;

        if (!is_numeric($newBalances[$i])) {
            start_balance_fail($conn, 'قيمة الرصيد الافتتاحي غير صالحة.', true);
        }

        $balance = (float) $newBalances[$i];
        $asInt = (int) round($balance);
        if (abs($balance - $asInt) > 0.0001) {
            start_balance_fail($conn, 'الرصيد الافتتاحي يُحفظ كعدد صحيح. أزل الكسور ثم أعد الحفظ.', true);
        }
        if ($asInt > 2147483647 || $asInt < -2147483647) {
            start_balance_fail($conn, 'الرصيد الافتتاحي أكبر من الحد المسموح به في القيد.', true);
        }
        $balance = (float) $asInt;

        $stmtAcc->bind_param('i', $id);
        $stmtAcc->execute();
        $acc = $stmtAcc->get_result()->fetch_assoc();
        if (!$acc || (int) $acc['isdeleted'] === 1 || (int) $acc['is_basic'] === 1) {
            start_balance_fail($conn, 'أحد الحسابات غير موجود أو لا يقبل رصيداً افتتاحياً.', true);
        }

        $name = $acc['aname'];
        $d1 = OPENING_DETAILS;
        $d2 = OPENING_DETAILS_CLOSE;
        $stmtLines->bind_param('iss', $id, $d1, $d2);
        $stmtLines->execute();
        $lineRes = $stmtLines->get_result();
        $lines = [];
        $oldSigned = 0.0;
        while ($line = $lineRes->fetch_assoc()) {
            $lines[] = (int) $line['id'];
            $oldSigned += (float) $line['debit'] - (float) $line['credit'];
        }

        $isPlug = ($acc['code'] === '2211' || $acc['aname'] === 'الشريك الرئيسي');
        if ((int) $acc['editable'] === 0 && !$isPlug && abs($balance - $oldSigned) > 0.001) {
            start_balance_fail($conn, 'الحساب «' . $name . '» غير قابل لتعديل الرصيد الافتتاحي.', true);
        }

        $debit = $balance > 0 ? $balance : 0.0;
        $credit = $balance < 0 ? abs($balance) : 0.0;
        $totalDebit += $debit;
        $totalCredit += $credit;

        $plans[] = [
            'id' => $id,
            'name' => $name,
            'balance' => $balance,
            'debit' => $debit,
            'credit' => $credit,
            'tybe' => $balance >= 0 ? 0 : 1,
            'lines' => $lines,
            'plug' => $isPlug,
        ];
    }

    $stmtAcc->close();
    $stmtLines->close();

    $diff = round($totalDebit - $totalCredit, 2);
    $plugManual = isset($_POST['plug_manual']) && (string) $_POST['plug_manual'] === '1';
    if (abs($diff) > 0.001) {
        $plugIndex = null;
        foreach ($plans as $index => $plan) {
            if (!empty($plan['plug'])) {
                $plugIndex = $index;
                break;
            }
        }
        if ($plugIndex === null || $plugManual) {
            start_balance_fail(
                $conn,
                'القيد غير متوازن. إجمالي المدين (' . number_format($totalDebit, 2) . ') لا يساوي إجمالي الدائن (' . number_format($totalCredit, 2) . '). الفرق: ' . number_format($diff, 2) . '. لم يُحفظ أي تغيير.',
                true
            );
        }

        $plugBalance = round($plans[$plugIndex]['balance'] - $diff, 2);
        $plugInt = (int) round($plugBalance);
        if (abs($plugBalance - $plugInt) > 0.0001 || $plugInt > 2147483647 || $plugInt < -2147483647) {
            start_balance_fail($conn, 'تعذر ترحيل فرق الميزانية إلى الشريك الرئيسي. لم يُحفظ أي تغيير.', true);
        }
        $plans[$plugIndex]['balance'] = (float) $plugInt;
        $plans[$plugIndex]['debit'] = $plugInt > 0 ? (float) $plugInt : 0.0;
        $plans[$plugIndex]['credit'] = $plugInt < 0 ? (float) abs($plugInt) : 0.0;
        $plans[$plugIndex]['tybe'] = $plugInt >= 0 ? 0 : 1;
        $totalDebit = 0.0;
        $totalCredit = 0.0;
        foreach ($plans as $plan) {
            $totalDebit += $plan['debit'];
            $totalCredit += $plan['credit'];
        }
        if (abs(round($totalDebit - $totalCredit, 2)) > 0.001) {
            start_balance_fail($conn, 'تعذر موازنة الرصيد الافتتاحي على الشريك الرئيسي. لم يُحفظ أي تغيير.', true);
        }
    }

    $needsInsert = false;
    foreach ($plans as $plan) {
        if (count($plan['lines']) === 0 && $plan['balance'] != 0.0) {
            $needsInsert = true;
            break;
        }
    }

    $journalHeadId = $openingHeadIds[0] ?? 0;
    if ($needsInsert && $journalHeadId === 0) {
        $stmtHead = $conn->prepare('INSERT INTO journal_heads (journal_id, total, details, user, jdate) VALUES (0, 0, ?, ?, ?)');
        $details = OPENING_DETAILS;
        $stmtHead->bind_param('sis', $details, $user, $firstDate);
        $stmtHead->execute();
        $journalHeadId = (int) $conn->insert_id;
        $stmtHead->close();
        if ($journalHeadId < 1) {
            start_balance_fail($conn, 'تعذر إنشاء قيد الأرصدة الافتتاحية. لم يُحفظ أي تغيير.', true);
        }
        $openingHeadIds[] = $journalHeadId;
    }

    $stmtUpdateJe = $conn->prepare('UPDATE journal_entries SET debit = ?, credit = ?, tybe = ? WHERE id = ?');
    $stmtDeleteJe = $conn->prepare('UPDATE journal_entries SET isdeleted = 1, debit = 0, credit = 0 WHERE id = ?');
    $stmtInsertJe = $conn->prepare('INSERT INTO journal_entries (journal_id, account_id, debit, credit, tybe, info) VALUES (?, ?, ?, ?, ?, ?)');
    $stmtRecalc = $conn->prepare(
        'UPDATE acc_head SET balance = (
            SELECT COALESCE(SUM(debit) - SUM(credit), 0)
            FROM journal_entries
            WHERE account_id = ? AND isdeleted = 0
        ) WHERE id = ?'
    );
    $stmtSum = $conn->prepare('SELECT COALESCE(SUM(debit) - SUM(credit), 0) AS s FROM journal_entries WHERE account_id = ? AND isdeleted = 0');
    $stmtBal = $conn->prepare('SELECT balance FROM acc_head WHERE id = ?');

    foreach ($plans as $plan) {
        $id = $plan['id'];
        $debit = $plan['debit'];
        $credit = $plan['credit'];
        $tybe = $plan['tybe'];
        $lines = $plan['lines'];

        if (count($lines) === 0) {
            if ($plan['balance'] != 0.0) {
                $stmtInsertJe->bind_param('iiddis', $journalHeadId, $id, $debit, $credit, $tybe, $info);
                $stmtInsertJe->execute();
                if ($stmtInsertJe->affected_rows < 1) {
                    start_balance_fail($conn, 'تعذر حفظ الرصيد الافتتاحي للحساب «' . $plan['name'] . '». لم يُحفظ أي تغيير.', true);
                }
            }
        } else {
            $keepId = $lines[0];
            $stmtUpdateJe->bind_param('ddii', $debit, $credit, $tybe, $keepId);
            $stmtUpdateJe->execute();
            for ($n = 1; $n < count($lines); $n++) {
                $extraId = $lines[$n];
                $stmtDeleteJe->bind_param('i', $extraId);
                $stmtDeleteJe->execute();
            }
        }

        $stmtRecalc->bind_param('ii', $id, $id);
        $stmtRecalc->execute();

        $stmtSum->bind_param('i', $id);
        $stmtSum->execute();
        $sumRow = $stmtSum->get_result()->fetch_assoc();
        $stmtBal->bind_param('i', $id);
        $stmtBal->execute();
        $balRow = $stmtBal->get_result()->fetch_assoc();
        $fromJournal = isset($sumRow['s']) ? (float) $sumRow['s'] : 0.0;
        $stored = isset($balRow['balance']) ? (float) $balRow['balance'] : 0.0;
        if (abs($stored - $fromJournal) > 0.001) {
            start_balance_fail($conn, 'الرصيد الافتتاحي للحساب «' . $plan['name'] . '» لم يتطابق مع القيود. تم إلغاء الحفظ.', true);
        }
    }

    if (!empty($openingHeadIds)) {
        $stmtHeadUpdate = $conn->prepare('UPDATE journal_heads SET total = ?, jdate = ? WHERE id = ? AND isdeleted = 0');
        foreach ($openingHeadIds as $headId) {
            $stmtHeadUpdate->bind_param('dsi', $totalDebit, $firstDate, $headId);
            $stmtHeadUpdate->execute();
        }
        $stmtHeadUpdate->close();
    }

    $processType = 'تحديث الأرصدة الافتتاحية';
    $processStmt = $conn->prepare('INSERT INTO `process`(`type`) VALUES (?)');
    $processStmt->bind_param('s', $processType);
    $processStmt->execute();
    $processStmt->close();

    $stmtUpdateJe->close();
    $stmtDeleteJe->close();
    $stmtInsertJe->close();
    $stmtRecalc->close();
    $stmtSum->close();
    $stmtBal->close();

    $conn->commit();
    $inTransaction = false;
    $_SESSION['start_balance_ok'] = 'تم حفظ الأرصدة الافتتاحية. إجمالي المدين والدائن: ' . number_format($totalDebit, 2);
    header('Location: ../start_balance.php');
    exit;
} catch (Throwable $e) {
    $logFile = __DIR__ . '/../logs/sql_errors.log';
    @file_put_contents(
        $logFile,
        '[' . date('Y-m-d H:i:s') . '] start_balance: ' . $e->getMessage() . PHP_EOL,
        FILE_APPEND
    );
    start_balance_fail($conn, 'تعذر حفظ الأرصدة الافتتاحية بسبب خطأ في قاعدة البيانات. لم يُحفظ أي تغيير.', $inTransaction);
}
