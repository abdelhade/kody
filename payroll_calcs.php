<?php
include('includes/header.php');
include('includes/navbar.php');
include('includes/sidebar.php');
require_once('includes/payroll_calcs_helper.php');
ensure_payroll_calcs_schema($conn);

$typeMeta = [
    1 => ['icon' => 'fa-gift', 'color' => 'success', 'sign' => '+'],
    2 => ['icon' => 'fa-shield-alt', 'color' => 'info', 'sign' => '−'],
    3 => ['icon' => 'fa-file-invoice-dollar', 'color' => 'warning', 'sign' => '−'],
    4 => ['icon' => 'fa-minus-circle', 'color' => 'danger', 'sign' => '−'],
];

$grouped = [];
$sql = "SELECT pc.*, e.name AS emp_display
        FROM payroll_calcs pc
        LEFT JOIN employees e ON e.id = pc.emp_id
        WHERE pc.isdeleted = 0
        ORDER BY pc.snd_id DESC, pc.id ASC";
$result = $conn->query($sql);
while ($result && ($row = $result->fetch_assoc())) {
    $sndId = (int) $row['snd_id'];
    if (!isset($grouped[$sndId])) {
        $grouped[$sndId] = [];
    }
    $grouped[$sndId][] = $row;
}

$totalOps = count($grouped);
$totalLines = 0;
$typeCounts = [1 => 0, 2 => 0, 3 => 0, 4 => 0];
foreach ($grouped as $lines) {
    $totalLines += count($lines);
    $t = (int) ($lines[0]['calc_tybe'] ?? 1);
    if (isset($typeCounts[$t])) {
        $typeCounts[$t]++;
    }
}
?>
<style>
.payroll-calc-page .content-wrapper { background: #f4f6f9; }
.payroll-calc-page .page-hero {
    background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-color) 55%, var(--primary-light) 100%);
    border-radius: 12px; color: #fff; padding: 1.1rem 1.35rem; margin-bottom: 1.25rem;
    box-shadow: 0 4px 14px rgba(75, 86, 148, 0.25);
}
.payroll-calc-page .page-hero h1 { font-size: 1.25rem; font-weight: 700; margin: 0; color: #fff; }
.payroll-calc-page .card { border: none; border-radius: 12px; box-shadow: 0 1px 4px rgba(15,23,42,0.06); }
.payroll-calc-page .small-box { border-radius: 10px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,.06); }
.payroll-calc-page .small-box .icon { font-size: 52px; }
.payroll-calc-page .op-card { border-radius: 12px; overflow: hidden; margin-bottom: 1.25rem; }
.payroll-calc-page .op-card > .card-header {
    background: #fff; border-bottom: 1px solid #eef1f5; padding: 0.85rem 1.1rem;
}
.payroll-calc-page .op-number {
    font-size: 1rem; font-weight: 700; padding: 0.35rem 0.7rem; border-radius: 8px;
    background: var(--primary-color); color: #fff;
}
.payroll-calc-page .line-mini-card {
    border-radius: 10px; height: 100%;
    transition: transform .15s, box-shadow .15s;
}
.payroll-calc-page .line-mini-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(15,23,42,0.1);
}
.payroll-calc-page .line-mini-card .card-body { padding: 0.85rem 1rem; }
.payroll-calc-page .line-amount {
    font-size: 1.15rem; font-weight: 700; line-height: 1.2;
}
.payroll-calc-page .empty-state {
    text-align: center; padding: 3rem 1.5rem; color: #94a3b8;
}
.payroll-calc-page .empty-state i { font-size: 3rem; margin-bottom: 1rem; opacity: .5; }
.payroll-calc-page .op-meta { font-size: 0.875rem; color: #64748b; }
.payroll-calc-page .op-meta i { width: 18px; text-align: center; }
</style>

<div class="payroll-calc-page">
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="page-hero d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h1><i class="fas fa-coins ml-2"></i> احتسابات الموظفين</h1>
                    <small class="d-block mt-1 opacity-90">عملية واحدة — بنود منفصلة — النسخ للعملية كاملة</small>
                </div>
                <a href="add_payroll_calc.php" class="btn btn-light btn-sm font-weight-bold">
                    <i class="fas fa-plus ml-1"></i> عملية جديدة
                </a>
            </div>
        </div>
    </section>

    <section class="content pb-4">
        <div class="container-fluid">

            <!-- إحصائيات -->
            <div class="row mb-3">
                <div class="col-6 col-lg-3 mb-2">
                    <div class="small-box bg-primary mb-0">
                        <div class="inner">
                            <h3><?= $totalOps ?></h3>
                            <p>عمليات</p>
                        </div>
                        <div class="icon"><i class="fas fa-folder-open"></i></div>
                    </div>
                </div>
                <div class="col-6 col-lg-3 mb-2">
                    <div class="small-box bg-secondary mb-0">
                        <div class="inner">
                            <h3><?= $totalLines ?></h3>
                            <p>بنود إجمالية</p>
                        </div>
                        <div class="icon"><i class="fas fa-list-ul"></i></div>
                    </div>
                </div>
                <?php foreach ([1, 4] as $t) {
                    $m = $typeMeta[$t];
                ?>
                <div class="col-6 col-lg-3 mb-2">
                    <div class="small-box bg-<?= $m['color'] ?> mb-0">
                        <div class="inner">
                            <h3><?= $typeCounts[$t] ?></h3>
                            <p><?= payroll_calc_type_label($t) ?> (<?= $m['sign'] ?>)</p>
                        </div>
                        <div class="icon"><i class="fas <?= $m['icon'] ?>"></i></div>
                    </div>
                </div>
                <?php } ?>
            </div>

            <?php if (empty($grouped)) { ?>
            <div class="card">
                <div class="card-body empty-state">
                    <i class="fas fa-inbox d-block"></i>
                    <h5 class="text-secondary">لا توجد عمليات احتساب بعد</h5>
                    <p class="mb-3">أضف عملية جديدة ببنود المكافأة والتأمين والضريبة والخصم</p>
                    <a href="add_payroll_calc.php" class="btn btn-primary">
                        <i class="fas fa-plus ml-1"></i> إضافة عملية
                    </a>
                </div>
            </div>
            <?php } ?>

            <?php
            $opNum = 0;
            foreach ($grouped as $sndId => $lines) {
                $opNum++;
                $first = $lines[0];
                $lineCount = count($lines);
                $opTybe = (int) ($first['calc_tybe'] ?? 1);
                $opMeta = $typeMeta[$opTybe] ?? $typeMeta[1];
            ?>
            <div class="card op-card card-<?= $opMeta['color'] ?> card-outline">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <span class="op-number">#<?= $sndId ?></span>
                        <div>
                            <div class="font-weight-bold text-dark">
                                <span class="badge badge-<?= $opMeta['color'] ?> ml-2">
                                    <i class="fas <?= $opMeta['icon'] ?> ml-1"></i>
                                    <?= payroll_calc_type_label($opTybe, true) ?>
                                </span>
                                عملية <?= $opNum ?>
                            </div>
                            <div class="op-meta mt-1">
                                <span class="ml-3"><i class="far fa-calendar-alt"></i> <?= htmlspecialchars($first['date']) ?></span>
                                <span class="ml-3"><i class="fas fa-list-ol"></i> <?= $lineCount ?> بند</span>
                                <?php if (!empty($first['info'])) { ?>
                                <span class="ml-3"><i class="fas fa-align-right"></i> <?= htmlspecialchars($first['info']) ?></span>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="btn-group">
                        <a href="edit_payroll_calc.php?edit=<?= $sndId ?>" class="btn btn-warning btn-sm" title="تعديل العملية">
                            <i class="fas fa-pen"></i> تعديل
                        </a>
                        <a href="add_payroll_calc.php?copy=<?= $sndId ?>" class="btn btn-info btn-sm" title="نسخ العملية كاملة">
                            <i class="fas fa-copy"></i> نسخ
                        </a>
                    </div>
                </div>
                <div class="card-body bg-light">
                    <div class="row">
                        <?php foreach ($lines as $idx => $line) {
                            $empLabel = $line['emp_display'] ?: ($line['emp_name'] ?? '—');
                            $hasPercent = (float) $line['percent'] > 0;
                            $amountLabel = $hasPercent
                                ? number_format((float) $line['percent'], 2) . '%'
                                : number_format((float) $line['amount'], 2);
                        ?>
                        <div class="col-12 col-md-6 col-xl-4 mb-3">
                            <div class="card card-outline card-<?= $opMeta['color'] ?> line-mini-card mb-0">
                                <div class="card-body py-2 px-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="badge badge-light border mb-1">بند <?= $idx + 1 ?></span>
                                            <div class="font-weight-bold">
                                                <i class="fas fa-user ml-1"></i>
                                                <?= htmlspecialchars($empLabel) ?>
                                            </div>
                                            <?php if (!empty($line['info2'])) { ?>
                                            <div class="op-meta text-truncate" title="<?= htmlspecialchars($line['info2']) ?>">
                                                <?= htmlspecialchars($line['info2']) ?>
                                            </div>
                                            <?php } ?>
                                        </div>
                                        <div class="line-amount <?= payroll_calc_is_addition($opTybe) ? 'text-success' : 'text-danger' ?>">
                                            <?= payroll_calc_is_addition($opTybe) ? '+' : '−' ?><?= $amountLabel ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <?php } ?>

        </div>
    </section>
</div>
</div>

<?php include('includes/footer.php'); ?>
