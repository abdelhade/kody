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

function kody_orphan_journal_sql(string $alias = 'jh'): string
{
    return "{$alias}.isdeleted = 0
        AND NOT EXISTS (
            SELECT 1 FROM ot_head ot
            WHERE ot.isdeleted = 0
              AND (
                (COALESCE({$alias}.op_id, 0) <> 0 AND ot.id = {$alias}.op_id)
                OR (COALESCE({$alias}.op2, 0) <> 0 AND ot.id = {$alias}.op2)
              )
        )";
}

function kody_journal_filter_where(mysqli $conn, string $from, string $to, string $search): string
{
    $where = ['jh.isdeleted = 0'];
    if ($from !== '') {
        $where[] = "jh.jdate >= '" . $conn->real_escape_string($from) . "'";
    }
    if ($to !== '') {
        $where[] = "jh.jdate <= '" . $conn->real_escape_string($to) . "'";
    }
    if ($search !== '') {
        $s = $conn->real_escape_string($search);
        $where[] = "(CAST(jh.journal_id AS CHAR) LIKE '%$s%' OR jh.details LIKE '%$s%' OR CAST(jh.id AS CHAR) LIKE '%$s%' OR CAST(jh.op_id AS CHAR) LIKE '%$s%' OR CAST(jh.op2 AS CHAR) LIKE '%$s%')";
    }
    return implode(' AND ', $where);
}

function kody_delete_orphan_journals(mysqli $conn, array $ids): int
{
    $ids = array_values(array_unique(array_filter(array_map('intval', $ids), static function ($id) {
        return $id > 0;
    })));
    if (!$ids) {
        return 0;
    }

    $deleted = 0;
    $conn->begin_transaction();
    try {
        foreach (array_chunk($ids, 400) as $chunk) {
            $idList = implode(',', $chunk);
            $safe = [];
            $res = $conn->query(
                'SELECT jh.id FROM journal_heads jh WHERE jh.id IN (' . $idList . ') AND ' . kody_orphan_journal_sql('jh')
            );
            if ($res) {
                while ($row = $res->fetch_assoc()) {
                    $safe[] = (int) $row['id'];
                }
            }
            if (!$safe) {
                continue;
            }

            $safeList = implode(',', $safe);
            $accounts = [];
            $accRes = $conn->query(
                'SELECT DISTINCT account_id FROM journal_entries WHERE isdeleted = 0 AND journal_id IN (' . $safeList . ')'
            );
            if ($accRes) {
                while ($acc = $accRes->fetch_assoc()) {
                    $accountId = (int) $acc['account_id'];
                    if ($accountId > 0) {
                        $accounts[$accountId] = $accountId;
                    }
                }
            }

            $conn->query('UPDATE journal_entries SET isdeleted = 1 WHERE isdeleted = 0 AND journal_id IN (' . $safeList . ')');
            $conn->query('UPDATE journal_heads SET isdeleted = 1 WHERE isdeleted = 0 AND id IN (' . $safeList . ')');
            $deleted += $conn->affected_rows;

            if ($accounts) {
                $conn->query(
                    'UPDATE acc_head SET balance = COALESCE((
                        SELECT SUM(je.debit) - SUM(je.credit)
                        FROM journal_entries je
                        WHERE je.account_id = acc_head.id AND je.isdeleted = 0
                    ), 0)
                    WHERE id IN (' . implode(',', $accounts) . ')'
                );
            }
        }
        $conn->commit();
    } catch (Throwable $e) {
        $conn->rollback();
        throw $e;
    }

    return $deleted;
}

function kody_journal_redirect(array $params): void
{
    $query = http_build_query(array_filter($params, static function ($value) {
        return $value !== '' && $value !== null;
    }));
    header('Location: journals_without_operations.php' . ($query !== '' ? '?' . $query : ''));
    exit;
}

$from = '';
$to = '';
$search = '';
$page = 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postedToken = (string) ($_POST['csrf_token'] ?? '');
    if (!hash_equals($_SESSION['csrf_token'], $postedToken)) {
        kody_journal_redirect(['error' => 'csrf']);
    }

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

    try {
        set_time_limit(180);
        $action = (string) ($_POST['bulk_action'] ?? '');
        $deleteId = isset($_POST['delete_id']) ? (int) $_POST['delete_id'] : 0;

        if ($deleteId > 0) {
            $deleted = kody_delete_orphan_journals($conn, [$deleteId]);
            $redirect['deleted'] = $deleted;
            if ($deleted === 0) {
                $redirect['error'] = 'skipped';
            }
        } elseif ($action === 'delete_selected') {
            $ids = isset($_POST['ids']) && is_array($_POST['ids']) ? $_POST['ids'] : [];
            $deleted = kody_delete_orphan_journals($conn, $ids);
            $redirect['deleted'] = $deleted;
            if ($deleted === 0) {
                $redirect['error'] = 'none';
            }
        } elseif ($action === 'delete_filtered') {
            $where = kody_journal_filter_where($conn, $from, $to, $search);
            $ids = [];
            $res = $conn->query(
                'SELECT jh.id FROM journal_heads jh WHERE ' . $where . ' AND ' . kody_orphan_journal_sql('jh')
            );
            if ($res) {
                while ($row = $res->fetch_assoc()) {
                    $ids[] = (int) $row['id'];
                }
            }
            $deleted = kody_delete_orphan_journals($conn, $ids);
            $redirect['page'] = '';
            $redirect['deleted'] = $deleted;
            if ($deleted === 0) {
                $redirect['error'] = 'none';
            }
        } else {
            $redirect['error'] = 'action';
        }
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
$deletedCount = isset($_GET['deleted']) ? (int) $_GET['deleted'] : -1;
$errorCode = isset($_GET['error']) ? (string) $_GET['error'] : '';

$where = kody_journal_filter_where($conn, $from, $to, $search);

$sql = "
    SELECT
        jh.id,
        jh.journal_id,
        jh.jdate,
        jh.details,
        jh.op_id,
        jh.op2,
        MAX(u.uname) AS uname,
        COALESCE(SUM(CASE WHEN je.isdeleted = 0 THEN je.debit ELSE 0 END), 0) AS debit_sum,
        COALESCE(SUM(CASE WHEN je.isdeleted = 0 THEN je.credit ELSE 0 END), 0) AS credit_sum,
        SUM(CASE WHEN je.isdeleted = 0 THEN 1 ELSE 0 END) AS line_count
    FROM journal_heads jh
    LEFT JOIN journal_entries je ON je.journal_id = jh.id
    LEFT JOIN users u ON u.id = jh.`user`
    WHERE " . $where . "
    AND NOT EXISTS (
        SELECT 1 FROM ot_head ot
        WHERE ot.isdeleted = 0
          AND (
            (COALESCE(jh.op_id, 0) <> 0 AND ot.id = jh.op_id)
            OR (COALESCE(jh.op2, 0) <> 0 AND ot.id = jh.op2)
          )
    )
    GROUP BY jh.id, jh.journal_id, jh.jdate, jh.details, jh.op_id, jh.op2
    ORDER BY jh.jdate DESC, jh.journal_id DESC
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
        SELECT je.journal_id, je.debit, je.credit, je.info, ah.code, ah.aname
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

$totalDebit = 0;
$totalCredit = 0;
foreach ($rows as $row) {
    $totalDebit += (float) $row['debit_sum'];
    $totalCredit += (float) $row['credit_sum'];
}

$errorText = '';
if ($errorCode === 'csrf') {
    $errorText = 'انتهت صلاحية الطلب. أعد المحاولة.';
} elseif ($errorCode === 'skipped') {
    $errorText = 'لم يُحذف القيد لأنه مرتبط بعملية قائمة.';
} elseif ($errorCode === 'none') {
    $errorText = 'لم يُحذف أي قيد. اختر قيوداً من الجدول، أو أن القيود المحددة مرتبطة بعملية.';
} elseif ($errorCode === 'action') {
    $errorText = 'اختر إجراءً من قائمة الإجراءات الجماعية.';
} elseif ($errorCode === 'failed') {
    $errorText = 'تعذر حذف القيود. لم يُحفظ أي تغيير.';
}
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-unlink text-warning"></i>
                        القيود بدون عمليات
                    </h3>
                    <a href="unbalanced_journals.php" class="btn btn-sm btn-outline-secondary">القيود غيرالمتزنة</a>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        القيود القائمة التي لا ترتبط بعملية غير محذوفة. يظهر القيد إذا لم يُحفظ له رقم عملية، أو إذا كانت العملية المحفوظة محذوفة أو غير موجودة.
                    </div>

                    <?php if ($deletedCount > 0) { ?>
                        <div class="alert alert-success">تم حذف <?= (int) $deletedCount ?> قيد، وأُعيد حساب أرصدة الحسابات المتأثرة.</div>
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
                            <input type="text" name="search" class="form-control" placeholder="رقم القيد أو البيان أو رقم العملية" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary ml-2">
                                <i class="fas fa-search"></i> عرض
                            </button>
                            <a href="journals_without_operations.php" class="btn btn-secondary">مسح</a>
                        </div>
                    </form>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="info-box bg-warning">
                                <span class="info-box-icon"><i class="fas fa-file-invoice"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">عدد القيود</span>
                                    <span class="info-box-number"><?= count($rows) ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box bg-info">
                                <span class="info-box-icon"><i class="fas fa-arrow-down"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">إجمالي المدين</span>
                                    <span class="info-box-number"><?= number_format($totalDebit, 2) ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box bg-success">
                                <span class="info-box-icon"><i class="fas fa-arrow-up"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">إجمالي الدائن</span>
                                    <span class="info-box-number"><?= number_format($totalCredit, 2) ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form method="post" id="bulkJournalForm">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="from" value="<?= htmlspecialchars($from, ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="to" value="<?= htmlspecialchars($to, ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="search" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="page" value="<?= (int) $page ?>">

                        <div class="d-flex flex-wrap align-items-center mb-3">
                            <select name="bulk_action" id="bulkAction" class="form-control ml-2 mb-2" style="max-width: 320px;">
                                <option value="">إجراءات جماعية</option>
                                <option value="delete_selected">حذف المحدد</option>
                                <option value="delete_filtered">حذف كل نتائج التقرير (<?= (int) $totalRows ?>)</option>
                            </select>
                            <button type="submit" class="btn btn-danger mb-2" id="bulkApply">
                                <i class="fas fa-check"></i> تطبيق
                            </button>
                            <span class="text-muted mr-3 mb-2" id="selectedCount">لم يُحدد شيء</span>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <th class="text-center" style="width: 42px;">
                                            <input type="checkbox" id="checkAll" title="تحديد الكل في الصفحة">
                                        </th>
                                        <th>#</th>
                                        <th>التاريخ</th>
                                        <th>رقم القيد</th>
                                        <th>البيان</th>
                                        <th>المدين</th>
                                        <th>الدائن</th>
                                        <th>السبب</th>
                                        <th>رقم العملية المحفوظ</th>
                                        <th>المستخدم</th>
                                        <th>البنود</th>
                                        <th>إجراء</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!$pageRows) { ?>
                                        <tr>
                                            <td colspan="12" class="text-center text-muted">لا توجد قيود بدون عمليات</td>
                                        </tr>
                                    <?php } ?>
                                    <?php foreach ($pageRows as $i => $row) {
                                        $opId = (int) ($row['op_id'] ?? 0);
                                        $op2 = (int) ($row['op2'] ?? 0);
                                        $noLink = ($opId === 0 && $op2 === 0);
                                        $savedOps = [];
                                        if ($opId !== 0) {
                                            $savedOps[] = $opId;
                                        }
                                        if ($op2 !== 0 && $op2 !== $opId) {
                                            $savedOps[] = $op2;
                                        }
                                        $lines = $linesByJournal[(int) $row['id']] ?? [];
                                        $journalNo = (string) $row['journal_id'];
                                        ?>
                                        <tr>
                                            <td class="text-center">
                                                <input type="checkbox" class="row-check" name="ids[]" value="<?= (int) $row['id'] ?>">
                                            </td>
                                            <td><?= (($page - 1) * $perPage) + $i + 1 ?></td>
                                            <td><?= htmlspecialchars((string) $row['jdate'], ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?= htmlspecialchars($journalNo, ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?= htmlspecialchars((string) ($row['details'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?= number_format((float) $row['debit_sum'], 2) ?></td>
                                            <td><?= number_format((float) $row['credit_sum'], 2) ?></td>
                                            <td>
                                                <?php if ($noLink) { ?>
                                                    <span class="badge badge-secondary">بدون ربط بعملية</span>
                                                <?php } else { ?>
                                                    <span class="badge badge-danger">العملية غير موجودة أو محذوفة</span>
                                                <?php } ?>
                                            </td>
                                            <td><?= $savedOps ? htmlspecialchars(implode(' / ', $savedOps), ENT_QUOTES, 'UTF-8') : '—' ?></td>
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
                                                <button type="submit" name="delete_id" value="<?= (int) $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm(<?= htmlspecialchars(json_encode('حذف القيد رقم ' . $journalNo . '؟', JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8') ?>);">
                                                    <i class="fas fa-trash"></i> حذف
                                                </button>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </form>

                    <?php if ($totalPages > 1) {
                        $startPage = max(1, $page - 3);
                        $endPage = min($totalPages, $page + 3);
                        $pageHref = static function (int $p) use ($filterQuery): string {
                            return 'journals_without_operations.php?' . $filterQuery . ($filterQuery !== '' ? '&' : '') . 'page=' . $p;
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('bulkJournalForm');
    var checkAll = document.getElementById('checkAll');
    var countLabel = document.getElementById('selectedCount');
    var totalRows = <?= (int) $totalRows ?>;
    if (!form) {
        return;
    }

    function rowChecks() {
        return Array.prototype.slice.call(form.querySelectorAll('.row-check'));
    }

    function updateCount() {
        var checks = rowChecks();
        var selected = checks.filter(function (box) { return box.checked; }).length;
        if (countLabel) {
            countLabel.textContent = selected ? ('المحدد في الصفحة: ' + selected) : 'لم يُحدد شيء';
        }
        if (checkAll) {
            checkAll.checked = checks.length > 0 && selected === checks.length;
        }
    }

    if (checkAll) {
        checkAll.addEventListener('change', function () {
            rowChecks().forEach(function (box) {
                box.checked = checkAll.checked;
            });
            updateCount();
        });
    }

    form.addEventListener('change', function (event) {
        if (event.target && event.target.classList.contains('row-check')) {
            updateCount();
        }
    });

    form.addEventListener('submit', function (event) {
        var submitter = event.submitter;
        if (submitter && submitter.name === 'delete_id') {
            return;
        }
        var action = document.getElementById('bulkAction').value;
        if (!action) {
            event.preventDefault();
            alert('اختر إجراءً من قائمة الإجراءات الجماعية.');
            return;
        }
        if (action === 'delete_selected') {
            var selected = rowChecks().filter(function (box) { return box.checked; }).length;
            if (!selected) {
                event.preventDefault();
                alert('حدد قيداً واحداً على الأقل.');
                return;
            }
            if (!confirm('حذف ' + selected + ' قيد محدد؟')) {
                event.preventDefault();
            }
            return;
        }
        if (action === 'delete_filtered') {
            if (!confirm('حذف كل نتائج التقرير (' + totalRows + ' قيد)؟')) {
                event.preventDefault();
            }
        }
    });

    updateCount();
});
</script>

<?php include('includes/footer.php'); ?>
