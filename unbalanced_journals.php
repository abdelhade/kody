<?php
include('includes/header.php');
include('includes/navbar.php');
include('includes/sidebar.php');

$from = (isset($_GET['from']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['from'])) ? $_GET['from'] : '';
$to = (isset($_GET['to']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['to'])) ? $_GET['to'] : '';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

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
        SELECT je.journal_id, je.debit, je.credit, ah.code, ah.aname
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

$totalDiff = 0;
foreach ($rows as $row) {
    $totalDiff += abs((float) $row['debit_sum'] - (float) $row['credit_sum']);
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
                                </tr>
                            </thead>
                            <tbody>
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

<?php include('includes/footer.php'); ?>
