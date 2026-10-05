<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/includes/connect.php';

if (empty($_SESSION['login']) || (int) ($_SESSION['userid'] ?? 0) < 1) {
    header('Location: index.php');
    exit;
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function kody_money_amount($value): ?float
{
    $value = str_replace([',', ' '], '', trim((string) $value));
    if ($value === '' || !is_numeric($value)) {
        return null;
    }
    $amount = round((float) $value, 2);
    if ($amount < 0) {
        return null;
    }
    return $amount;
}

function kody_journal_redirect(array $params): void
{
    $query = http_build_query(array_filter($params, static function ($value) {
        return $value !== '' && $value !== null;
    }));
    header('Location: unbalanced_journals.php' . ($query !== '' ? '?' . $query : ''));
    exit;
}

function kody_save_journal_lines(mysqli $conn, int $headId, string $jdate, string $details, array $postedLines): void
{
    $headStmt = $conn->prepare('SELECT id, op_id, op2 FROM journal_heads WHERE id = ? AND isdeleted = 0');
    $headStmt->bind_param('i', $headId);
    $headStmt->execute();
    $head = $headStmt->get_result()->fetch_assoc();
    $headStmt->close();
    if (!$head) {
        throw new RuntimeException('missing');
    }

    if (count($postedLines) < 2) {
        throw new RuntimeException('invalid');
    }

    $validAccounts = [];
    $accRes = $conn->query('SELECT id FROM acc_head WHERE isdeleted = 0');
    if ($accRes) {
        while ($acc = $accRes->fetch_assoc()) {
            $validAccounts[(int) $acc['id']] = true;
        }
    }

    $totalDebit = 0.0;
    $totalCredit = 0.0;
    foreach ($postedLines as $line) {
        if (!isset($validAccounts[$line['account_id']])) {
            throw new RuntimeException('invalid');
        }
        if ($line['debit'] > 0 && $line['credit'] > 0) {
            throw new RuntimeException('invalid');
        }
        if ($line['debit'] <= 0 && $line['credit'] <= 0) {
            throw new RuntimeException('invalid');
        }
        $totalDebit += $line['debit'];
        $totalCredit += $line['credit'];
    }
    if ($totalDebit <= 0 || abs(round($totalDebit - $totalCredit, 2)) > 0.009) {
        throw new RuntimeException('unbalanced');
    }

    $existingAccounts = [];
    $existingIds = [];
    $existStmt = $conn->prepare('SELECT id, account_id FROM journal_entries WHERE journal_id = ? AND isdeleted = 0');
    $existStmt->bind_param('i', $headId);
    $existStmt->execute();
    $existRes = $existStmt->get_result();
    while ($row = $existRes->fetch_assoc()) {
        $existingIds[(int) $row['id']] = true;
        $existingAccounts[(int) $row['account_id']] = (int) $row['account_id'];
    }
    $existStmt->close();

    foreach ($postedLines as $line) {
        if ($line['id'] > 0 && !isset($existingIds[$line['id']])) {
            throw new RuntimeException('invalid');
        }
    }

    $conn->begin_transaction();
    try {
        $stmtUpdate = $conn->prepare('UPDATE journal_entries SET account_id = ?, debit = ?, credit = ?, tybe = ?, info = ? WHERE id = ? AND journal_id = ? AND isdeleted = 0');
        $stmtInsert = $conn->prepare('INSERT INTO journal_entries (journal_id, account_id, debit, credit, tybe, info, op_id, op2) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $stmtDelete = $conn->prepare('UPDATE journal_entries SET isdeleted = 1, debit = 0, credit = 0 WHERE id = ? AND journal_id = ?');
        $opId = (int) ($head['op_id'] ?? 0);
        $op2 = (int) ($head['op2'] ?? 0);
        $kept = [];

        foreach ($postedLines as $line) {
            $accountId = $line['account_id'];
            $debit = $line['debit'];
            $credit = $line['credit'];
            $tybe = $debit > 0 ? 0 : 1;
            $info = $line['info'];
            $lineId = $line['id'];
            if ($lineId > 0) {
                $stmtUpdate->bind_param('iddisii', $accountId, $debit, $credit, $tybe, $info, $lineId, $headId);
                $stmtUpdate->execute();
                $kept[$lineId] = true;
            } else {
                $stmtInsert->bind_param('iiddisii', $headId, $accountId, $debit, $credit, $tybe, $info, $opId, $op2);
                $stmtInsert->execute();
            }
        }

        foreach (array_keys($existingIds) as $oldId) {
            if (!isset($kept[$oldId])) {
                $stmtDelete->bind_param('ii', $oldId, $headId);
                $stmtDelete->execute();
            }
        }
        $stmtUpdate->close();
        $stmtInsert->close();
        $stmtDelete->close();

        $stmtHead = $conn->prepare('UPDATE journal_heads SET total = ?, details = ?, jdate = ? WHERE id = ? AND isdeleted = 0');
        $stmtHead->bind_param('dssi', $totalDebit, $details, $jdate, $headId);
        $stmtHead->execute();
        $stmtHead->close();

        $touched = $existingAccounts;
        foreach ($postedLines as $line) {
            $touched[$line['account_id']] = $line['account_id'];
        }
        $stmtRecalc = $conn->prepare(
            'UPDATE acc_head SET balance = (
                SELECT COALESCE(SUM(debit) - SUM(credit), 0)
                FROM journal_entries
                WHERE account_id = ? AND isdeleted = 0
            ) WHERE id = ?'
        );
        foreach ($touched as $accountId) {
            if ($accountId > 0) {
                $stmtRecalc->bind_param('ii', $accountId, $accountId);
                $stmtRecalc->execute();
            }
        }
        $stmtRecalc->close();

        $conn->commit();
    } catch (Throwable $e) {
        $conn->rollback();
        throw $e;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postedToken = (string) ($_POST['csrf_token'] ?? '');
    $from = (isset($_POST['from']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_POST['from'])) ? $_POST['from'] : '';
    $to = (isset($_POST['to']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_POST['to'])) ? $_POST['to'] : '';
    $search = isset($_POST['search']) ? trim((string) $_POST['search']) : '';
    $page = isset($_POST['page']) ? max(1, (int) $_POST['page']) : 1;
    $redirect = [
        'from' => $from,
        'to' => $to,
        'search' => $search,
        'page' => $page > 1 ? $page : '',
    ];

    if (!hash_equals($_SESSION['csrf_token'], $postedToken)) {
        $redirect['error'] = 'csrf';
        kody_journal_redirect($redirect);
    }

    try {
        $headId = (int) ($_POST['head_id'] ?? 0);
        $jdate = (isset($_POST['jdate']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_POST['jdate'])) ? $_POST['jdate'] : '';
        $details = mb_substr(trim((string) ($_POST['details'] ?? '')), 0, 250);
        $lineIds = isset($_POST['line_id']) && is_array($_POST['line_id']) ? array_values($_POST['line_id']) : [];
        $accounts = isset($_POST['account_id']) && is_array($_POST['account_id']) ? array_values($_POST['account_id']) : [];
        $debits = isset($_POST['debit']) && is_array($_POST['debit']) ? array_values($_POST['debit']) : [];
        $credits = isset($_POST['credit']) && is_array($_POST['credit']) ? array_values($_POST['credit']) : [];
        $infos = isset($_POST['info']) && is_array($_POST['info']) ? array_values($_POST['info']) : [];
        $count = count($accounts);
        if ($headId < 1 || $jdate === '' || $count < 2 || $count !== count($lineIds) || $count !== count($debits) || $count !== count($credits)) {
            throw new RuntimeException('invalid');
        }

        $postedLines = [];
        for ($i = 0; $i < $count; $i++) {
            $debit = kody_money_amount($debits[$i]);
            $credit = kody_money_amount($credits[$i]);
            if ($debit === null || $credit === null) {
                throw new RuntimeException('invalid');
            }
            $postedLines[] = [
                'id' => (int) $lineIds[$i],
                'account_id' => (int) $accounts[$i],
                'debit' => $debit,
                'credit' => $credit,
                'info' => mb_substr(trim((string) ($infos[$i] ?? '')), 0, 150),
            ];
        }

        kody_save_journal_lines($conn, $headId, $jdate, $details, $postedLines);
        $redirect['saved'] = 1;
    } catch (RuntimeException $e) {
        $code = $e->getMessage();
        $redirect['error'] = in_array($code, ['missing', 'invalid', 'unbalanced'], true) ? $code : 'failed';
    } catch (Throwable $e) {
        $redirect['error'] = 'failed';
    }

    kody_journal_redirect($redirect);
}

include('includes/header.php');
include('includes/navbar.php');
include('includes/sidebar.php');

$from = (isset($_GET['from']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['from'])) ? $_GET['from'] : '';
$to = (isset($_GET['to']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['to'])) ? $_GET['to'] : '';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$saved = isset($_GET['saved']) && $_GET['saved'] === '1';
$errorCode = isset($_GET['error']) ? (string) $_GET['error'] : '';

$where = ['jh.isdeleted = 0'];
if ($from !== '') {
    $where[] = "jh.jdate >= '" . $conn->real_escape_string($from) . "'";
}
if ($to !== '') {
    $where[] = "jh.jdate <= '" . $conn->real_escape_string($to) . "'";
}
if ($search !== '') {
    $s = $conn->real_escape_string($search);
    $where[] = "(CAST(jh.journal_id AS CHAR) LIKE '%$s%' OR jh.details LIKE '%$s%' OR CAST(jh.id AS CHAR) LIKE '%$s%')";
}

$sql = "
    SELECT
        jh.id,
        jh.journal_id,
        jh.jdate,
        jh.details,
        jh.op_id,
        jh.op2,
        MAX(u.uname) AS uname,
        MAX(ot.id) AS operation_id,
        MAX(ot.pro_id) AS pro_id,
        MAX(pt.pname) AS pname,
        COALESCE(SUM(je.debit), 0) AS debit_sum,
        COALESCE(SUM(je.credit), 0) AS credit_sum,
        COUNT(je.id) AS line_count
    FROM journal_heads jh
    LEFT JOIN journal_entries je ON je.journal_id = jh.id AND je.isdeleted = 0
    LEFT JOIN users u ON u.id = jh.`user`
    LEFT JOIN ot_head ot ON ot.isdeleted = 0
        AND ot.id = CASE WHEN COALESCE(jh.op_id, 0) <> 0 THEN jh.op_id ELSE jh.op2 END
    LEFT JOIN pro_tybes pt ON pt.id = ot.pro_tybe
    WHERE " . implode(' AND ', $where) . "
    GROUP BY jh.id, jh.journal_id, jh.jdate, jh.details, jh.op_id, jh.op2
    HAVING ABS(COALESCE(SUM(je.debit), 0) - COALESCE(SUM(je.credit), 0)) > 0.009
        OR COUNT(je.id) = 0
    ORDER BY ABS(COALESCE(SUM(je.debit), 0) - COALESCE(SUM(je.credit), 0)) DESC, jh.jdate DESC, jh.journal_id DESC
";

$rows = [];
$res = $conn->query($sql);
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $rows[] = $row;
    }
}

$perPage = 50;
$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$totalRows = count($rows);
$totalPages = max(1, (int) ceil($totalRows / $perPage));
if ($page > $totalPages) {
    $page = $totalPages;
}
$pageRows = array_slice($rows, ($page - 1) * $perPage, $perPage);

$filterQuery = http_build_query(array_filter([
    'from' => $from,
    'to' => $to,
    'search' => $search,
], static function ($value) {
    return $value !== '';
}));

$linesByJournal = [];
if ($pageRows) {
    $ids = array_map('intval', array_column($pageRows, 'id'));
    $lineSql = "
        SELECT je.id, je.journal_id, je.account_id, je.debit, je.credit, je.info, ah.code, ah.aname
        FROM journal_entries je
        LEFT JOIN acc_head ah ON ah.id = je.account_id
        WHERE je.isdeleted = 0 AND je.journal_id IN (" . implode(',', $ids) . ")
        ORDER BY je.journal_id, je.id
    ";
    $lineRes = $conn->query($lineSql);
    if ($lineRes) {
        while ($line = $lineRes->fetch_assoc()) {
            $linesByJournal[(int) $line['journal_id']][] = $line;
        }
    }
}

$accounts = [];
$accList = $conn->query('SELECT id, code, aname FROM acc_head WHERE isdeleted = 0 AND is_basic = 0 ORDER BY code');
if ($accList) {
    while ($acc = $accList->fetch_assoc()) {
        $accounts[] = [
            'id' => (int) $acc['id'],
            'code' => (string) $acc['code'],
            'name' => (string) $acc['aname'],
        ];
    }
}

$editJournals = [];
foreach ($pageRows as $row) {
    $journalLines = [];
    foreach ($linesByJournal[(int) $row['id']] ?? [] as $line) {
        $journalLines[] = [
            'id' => (int) $line['id'],
            'account_id' => (int) $line['account_id'],
            'debit' => (float) $line['debit'],
            'credit' => (float) $line['credit'],
            'info' => (string) ($line['info'] ?? ''),
        ];
    }
    $editJournals[(int) $row['id']] = [
        'id' => (int) $row['id'],
        'journal_id' => (string) $row['journal_id'],
        'jdate' => (string) $row['jdate'],
        'details' => (string) ($row['details'] ?? ''),
        'lines' => $journalLines,
    ];
}

$totalDiff = 0;
foreach ($rows as $row) {
    $totalDiff += abs((float) $row['debit_sum'] - (float) $row['credit_sum']);
}

$errorText = '';
if ($errorCode === 'csrf') {
    $errorText = 'انتهت صلاحية الطلب. أعد المحاولة.';
} elseif ($errorCode === 'unbalanced') {
    $errorText = 'لم يُحفظ القيد لأن المدين لا يساوي الدائن.';
} elseif ($errorCode === 'invalid') {
    $errorText = 'راجع أطراف القيد: كل طرف يحتاج حساباً ومبلغاً في المدين أو الدائن فقط.';
} elseif ($errorCode === 'missing') {
    $errorText = 'القيد غير موجود.';
} elseif ($errorCode === 'failed') {
    $errorText = 'تعذر حفظ القيد. لم يُحفظ أي تغيير.';
}
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-not-equal text-danger"></i>
                        القيود غيرالمتزنة
                    </h3>
                    <a href="journals_without_operations.php" class="btn btn-sm btn-outline-secondary">القيود بدون عمليات</a>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        القيود القائمة التي لا يتساوى فيها إجمالي المدين مع إجمالي الدائن، والقيود التي بلا بنود.
                    </div>

                    <?php if ($saved) { ?>
                        <div class="alert alert-success">تم حفظ القيد وإعادة حساب أرصدة الحسابات.</div>
                    <?php } ?>
                    <?php if ($errorText !== '') { ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($errorText, ENT_QUOTES, 'UTF-8') ?></div>
                    <?php } ?>

                    <form method="get" class="row mb-4 bg-light p-3 rounded">
                        <div class="col-md-3">
                            <label>من تاريخ</label>
                            <input type="date" name="from" class="form-control" value="<?= htmlspecialchars($from, ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <div class="col-md-3">
                            <label>إلى تاريخ</label>
                            <input type="date" name="to" class="form-control" value="<?= htmlspecialchars($to, ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <div class="col-md-3">
                            <label>بحث</label>
                            <input type="text" name="search" class="form-control" placeholder="رقم القيد أو البيان" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary ml-2">
                                <i class="fas fa-search"></i> عرض
                            </button>
                            <a href="unbalanced_journals.php" class="btn btn-secondary">مسح</a>
                        </div>
                    </form>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="info-box bg-danger">
                                <span class="info-box-icon"><i class="fas fa-balance-scale"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">عدد القيود غير المتزنة</span>
                                    <span class="info-box-number"><?= count($rows) ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box bg-warning">
                                <span class="info-box-icon"><i class="fas fa-not-equal"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">مجموع الفروق</span>
                                    <span class="info-box-number"><?= number_format($totalDiff, 2) ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th>#</th>
                                    <th>التاريخ</th>
                                    <th>رقم القيد</th>
                                    <th>البيان</th>
                                    <th>المدين</th>
                                    <th>الدائن</th>
                                    <th>الفرق</th>
                                    <th>العملية</th>
                                    <th>المستخدم</th>
                                    <th>البنود</th>
                                    <th>إجراء</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!$pageRows) { ?>
                                    <tr>
                                        <td colspan="11" class="text-center text-muted">لا توجد قيود غير متزنة</td>
                                    </tr>
                                <?php } ?>
                                <?php foreach ($pageRows as $i => $row) {
                                    $debit = (float) $row['debit_sum'];
                                    $credit = (float) $row['credit_sum'];
                                    $diff = $debit - $credit;
                                    $lines = $linesByJournal[(int) $row['id']] ?? [];
                                    $operationId = (int) ($row['operation_id'] ?? 0);
                                    ?>
                                    <tr>
                                        <td><?= (($page - 1) * $perPage) + $i + 1 ?></td>
                                        <td><?= htmlspecialchars((string) $row['jdate'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars((string) $row['journal_id'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars((string) ($row['details'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= number_format($debit, 2) ?></td>
                                        <td><?= number_format($credit, 2) ?></td>
                                        <td class="text-danger font-weight-bold">
                                            <?php if ((int) $row['line_count'] === 0) { ?>
                                                بدون بنود
                                            <?php } else { ?>
                                                <?= number_format($diff, 2) ?>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <?php if ($operationId > 0) { ?>
                                                <a href="sales.php?edit_id=<?= $operationId ?>">
                                                    <?= htmlspecialchars(trim(($row['pname'] ?? 'عملية') . ' ' . ($row['pro_id'] ?? '')), ENT_QUOTES, 'UTF-8') ?>
                                                </a>
                                            <?php } else { ?>
                                                <span class="text-muted">بدون عملية</span>
                                            <?php } ?>
                                        </td>
                                        <td><?= htmlspecialchars((string) ($row['uname'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                        <td>
                                            <?php if (!$lines) { ?>
                                                <span class="text-muted">بدون بنود</span>
                                            <?php } else { ?>
                                                <?php foreach ($lines as $line) {
                                                    $name = trim(($line['code'] ?? '') . ' ' . ($line['aname'] ?? ''));
                                                    if ($name === '') {
                                                        $name = 'حساب غير موجود';
                                                    }
                                                    $side = ((float) $line['debit'] > 0) ? 'مدين ' . number_format((float) $line['debit'], 2) : 'دائن ' . number_format((float) $line['credit'], 2);
                                                    ?>
                                                    <div><?= htmlspecialchars($name . ' — ' . $side, ENT_QUOTES, 'UTF-8') ?></div>
                                                <?php } ?>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-primary js-edit-journal" data-id="<?= (int) $row['id'] ?>">
                                                <i class="fas fa-edit"></i> تعديل
                                            </button>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if ($totalPages > 1) {
                        $startPage = max(1, $page - 3);
                        $endPage = min($totalPages, $page + 3);
                        $pageHref = static function (int $p) use ($filterQuery): string {
                            return 'unbalanced_journals.php?' . $filterQuery . ($filterQuery !== '' ? '&' : '') . 'page=' . $p;
                        };
                        ?>
                        <nav class="mt-3">
                            <ul class="pagination justify-content-center">
                                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                    <a class="page-link" href="<?= htmlspecialchars($pageHref(max(1, $page - 1)), ENT_QUOTES, 'UTF-8') ?>">السابق</a>
                                </li>
                                <?php for ($p = $startPage; $p <= $endPage; $p++) { ?>
                                    <li class="page-item <?= $p === $page ? 'active' : '' ?>">
                                        <a class="page-link" href="<?= htmlspecialchars($pageHref($p), ENT_QUOTES, 'UTF-8') ?>"><?= $p ?></a>
                                    </li>
                                <?php } ?>
                                <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                                    <a class="page-link" href="<?= htmlspecialchars($pageHref(min($totalPages, $page + 1)), ENT_QUOTES, 'UTF-8') ?>">التالي</a>
                                </li>
                            </ul>
                        </nav>
                    <?php } ?>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="editJournalModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form method="post" id="editJournalForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="editJournalTitle">تعديل القيد</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="إغلاق">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="head_id" id="editHeadId" value="">
                    <input type="hidden" name="from" value="<?= htmlspecialchars($from, ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="to" value="<?= htmlspecialchars($to, ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="search" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="page" value="<?= (int) $page ?>">

                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label>التاريخ</label>
                            <input type="date" name="jdate" id="editJdate" class="form-control" required>
                        </div>
                        <div class="form-group col-md-9">
                            <label>البيان</label>
                            <input type="text" name="details" id="editDetails" class="form-control" maxlength="250">
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>الحساب</th>
                                    <th style="width: 140px;">مدين</th>
                                    <th style="width: 140px;">دائن</th>
                                    <th>بيان الطرف</th>
                                    <th style="width: 70px;"></th>
                                </tr>
                            </thead>
                            <tbody id="editLines"></tbody>
                        </table>
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm" id="addJournalLine">
                        <i class="fas fa-plus"></i> طرف جديد
                    </button>

                    <div class="row mt-3">
                        <div class="col-md-4"><strong>المدين:</strong> <span id="editDebitTotal">0.00</span></div>
                        <div class="col-md-4"><strong>الدائن:</strong> <span id="editCreditTotal">0.00</span></div>
                        <div class="col-md-4"><strong>الفرق:</strong> <span id="editDiff" class="text-danger">0.00</span></div>
                    </div>
                    <p class="text-muted mt-2 mb-0">لا يُحفظ القيد إلا إذا تساوى المدين مع الدائن.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary" id="saveJournalBtn" disabled>حفظ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="application/json" id="journalEditData"><?= json_encode($editJournals, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?></script>
<script type="application/json" id="journalAccounts"><?= json_encode($accounts, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?></script>

<?php
$extra_footer_scripts = <<<'HTML'
<script>
$(function () {
    var journals = JSON.parse(document.getElementById('journalEditData').textContent || '{}');
    var accounts = JSON.parse(document.getElementById('journalAccounts').textContent || '[]');
    var $lines = $('#editLines');

    function money(value) {
        var number = parseFloat(value);
        return isNaN(number) || number < 0 ? 0 : Math.round(number * 100) / 100;
    }

    function accountOptions(selectedId) {
        var html = '<option value="">اختر حساب</option>';
        var found = false;
        accounts.forEach(function (acc) {
            var selected = String(acc.id) === String(selectedId) ? ' selected' : '';
            if (selected) {
                found = true;
            }
            html += '<option value="' + acc.id + '"' + selected + '>' + $('<div>').text(acc.code + ' - ' + acc.name).html() + '</option>';
        });
        if (selectedId && !found) {
            html += '<option value="' + selectedId + '" selected>حساب #' + selectedId + '</option>';
        }
        return html;
    }

    function bindSelect($select) {
        $select.select2({
            width: '100%',
            dir: 'rtl',
            dropdownParent: $('#editJournalModal')
        });
    }

    function addLine(line) {
        line = line || { id: 0, account_id: '', debit: '', credit: '', info: '' };
        var debit = line.debit > 0 ? line.debit : '';
        var credit = line.credit > 0 ? line.credit : '';
        var $row = $('<tr>' +
            '<td><select name="account_id[]" class="form-control account-select" required>' + accountOptions(line.account_id) + '</select></td>' +
            '<td><input type="number" name="debit[]" class="form-control line-debit" min="0" step="0.01" value="' + debit + '"></td>' +
            '<td><input type="number" name="credit[]" class="form-control line-credit" min="0" step="0.01" value="' + credit + '"></td>' +
            '<td><input type="text" name="info[]" class="form-control" maxlength="150"></td>' +
            '<td><button type="button" class="btn btn-sm btn-outline-danger remove-line">حذف</button></td>' +
            '</tr>');
        $row.prepend($('<input type="hidden" name="line_id[]">').val(line.id || 0));
        $row.find('input[name="info[]"]').val(line.info || '');
        $lines.append($row);
        bindSelect($row.find('.account-select'));
    }

    function refreshTotals() {
        var debit = 0;
        var credit = 0;
        var valid = $lines.find('tr').length >= 2;
        $lines.find('tr').each(function () {
            var rowDebit = money($(this).find('.line-debit').val());
            var rowCredit = money($(this).find('.line-credit').val());
            var account = $(this).find('.account-select').val();
            debit += rowDebit;
            credit += rowCredit;
            if (!account || (rowDebit > 0 && rowCredit > 0) || (rowDebit <= 0 && rowCredit <= 0)) {
                valid = false;
            }
        });
        debit = Math.round(debit * 100) / 100;
        credit = Math.round(credit * 100) / 100;
        var diff = Math.round((debit - credit) * 100) / 100;
        $('#editDebitTotal').text(debit.toFixed(2));
        $('#editCreditTotal').text(credit.toFixed(2));
        $('#editDiff').text(diff.toFixed(2)).toggleClass('text-danger', Math.abs(diff) > 0.009).toggleClass('text-success', Math.abs(diff) <= 0.009 && debit > 0);
        $('#saveJournalBtn').prop('disabled', !(valid && debit > 0 && Math.abs(diff) <= 0.009));
    }

    $(document).on('click', '.js-edit-journal', function () {
        var journal = journals[$(this).data('id')];
        if (!journal) {
            return;
        }
        $lines.find('.account-select').each(function () {
            if ($(this).data('select2')) {
                $(this).select2('destroy');
            }
        });
        $lines.empty();
        $('#editHeadId').val(journal.id);
        $('#editJdate').val(journal.jdate);
        $('#editDetails').val(journal.details);
        $('#editJournalTitle').text('تعديل القيد رقم ' + journal.journal_id);
        if (journal.lines && journal.lines.length) {
            journal.lines.forEach(addLine);
        } else {
            addLine();
            addLine();
        }
        refreshTotals();
        $('#editJournalModal').modal('show');
    });

    $('#addJournalLine').on('click', function () {
        addLine();
        refreshTotals();
    });

    $lines.on('click', '.remove-line', function () {
        var $select = $(this).closest('tr').find('.account-select');
        if ($select.data('select2')) {
            $select.select2('destroy');
        }
        $(this).closest('tr').remove();
        refreshTotals();
    });

    $lines.on('input', '.line-debit', function () {
        if (money($(this).val()) > 0) {
            $(this).closest('tr').find('.line-credit').val('');
        }
        refreshTotals();
    });

    $lines.on('input', '.line-credit', function () {
        if (money($(this).val()) > 0) {
            $(this).closest('tr').find('.line-debit').val('');
        }
        refreshTotals();
    });

    $lines.on('change', '.account-select', refreshTotals);

    $('#editJournalModal').on('hidden.bs.modal', function () {
        $lines.find('.account-select').each(function () {
            if ($(this).data('select2')) {
                $(this).select2('destroy');
            }
        });
        $lines.empty();
    });
});
</script>
HTML;
include('includes/footer.php');
?>
