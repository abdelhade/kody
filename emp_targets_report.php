<?php include('includes/header.php'); ?>
<?php include('includes/navbar.php'); ?>
<?php include('includes/sidebar.php'); ?>
<?php

/* ── Ensure targets table exists ── */
$conn->query("
    CREATE TABLE IF NOT EXISTS `employee_targets` (
        `id`          INT(11)        NOT NULL AUTO_INCREMENT,
        `employee_id` INT(11)        NOT NULL,
        `period_type` ENUM('monthly','yearly') NOT NULL DEFAULT 'monthly',
        `year`        SMALLINT(4)    NOT NULL,
        `month`       TINYINT(2)     NULL,
        `target_value` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
        `notes`       VARCHAR(255)   NULL,
        `crtime`      TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `mdtime`      TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `user`        INT(11)        NOT NULL DEFAULT 1,
        `tenant`      INT(11)        NOT NULL DEFAULT 0,
        `branch`      INT(11)        NOT NULL DEFAULT 0,
        PRIMARY KEY (`id`),
        UNIQUE KEY `uq_emp_target` (`employee_id`,`period_type`,`year`,`month`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
");

/* ── Filter inputs ── */
$view_type   = $_GET['view_type']   ?? 'monthly';   // monthly | yearly
$report_year = (int)($_GET['report_year'] ?? date('Y'));
$report_month= (int)($_GET['report_month'] ?? (int)date('n'));
$dept_filter = (int)($_GET['dept_filter']  ?? 0);
$emp_filter  = (int)($_GET['emp_filter']   ?? 0);

$months_ar = [
    1=>'يناير',2=>'فبراير',3=>'مارس',4=>'أبريل',5=>'مايو',6=>'يونيو',
    7=>'يوليو',8=>'أغسطس',9=>'سبتمبر',10=>'أكتوبر',11=>'نوفمبر',12=>'ديسمبر'
];

/* ── Build date range for sales query ── */
if ($view_type === 'yearly') {
    $date_from = "$report_year-01-01";
    $date_to   = "$report_year-12-31";
    $period_label = "السنة $report_year";
} else {
    $date_from = sprintf('%04d-%02d-01', $report_year, $report_month);
    $date_to   = date('Y-m-t', strtotime($date_from));
    $period_label = ($months_ar[$report_month] ?? '') . " $report_year";
}

/* ── Extra filters ── */
$emp_join_extra  = '';
$target_where    = "t.year = $report_year";

if ($view_type === 'monthly') {
    $target_where .= " AND t.period_type='monthly' AND t.month=$report_month";
} else {
    $target_where .= " AND t.period_type='yearly'";
}

$emp_where_extra = '';
if ($dept_filter > 0) {
    $emp_where_extra .= " AND e.department = $dept_filter";
}
if ($emp_filter > 0) {
    $emp_where_extra .= " AND e.id = $emp_filter";
}

/* ── Main query: employees + their target + actual sales ── */
$sql = "
    SELECT
        e.id                                                    AS emp_id,
        e.name                                                  AS emp_name,
        a.id                                                    AS acc_id,
        dep.name                                                AS dept_name,
        COALESCE(t.target_value, 0)                             AS target_value,
        COALESCE(t.notes, '')                                   AS target_notes,
        COALESCE(t.period_type, '$view_type')                   AS period_type,
        COALESCE(
            (SELECT SUM(h.fat_net)
             FROM ot_head h
             WHERE h.emp_id = a.id
               AND (h.pro_tybe = 3 OR h.pro_tybe = 9)
               AND (h.isdeleted != 1 OR h.isdeleted IS NULL)
               AND h.pro_date BETWEEN '$date_from' AND '$date_to'
            ), 0
        )                                                       AS actual_sales
    FROM employees e
    LEFT JOIN acc_head a      ON a.code LIKE '213%' AND a.aname = e.name AND a.isdeleted != 1
    LEFT JOIN departments dep  ON dep.id = e.department
    LEFT JOIN employee_targets t ON t.employee_id = e.id AND $target_where
    WHERE e.isdeleted != 1 AND e.active = 1
    $emp_where_extra
    ORDER BY e.name ASC
";

$res  = $conn->query($sql);
$data = [];
$grand_target = 0;
$grand_actual = 0;

while ($row = $res->fetch_assoc()) {
    $row['target_value'] = floatval($row['target_value']);
    $row['actual_sales'] = floatval($row['actual_sales']);
    $row['diff']         = $row['actual_sales'] - $row['target_value'];
    $row['pct']          = $row['target_value'] > 0
                           ? round(($row['actual_sales'] / $row['target_value']) * 100, 1)
                           : ($row['actual_sales'] > 0 ? 100 : 0);
    $grand_target += $row['target_value'];
    $grand_actual += $row['actual_sales'];
    $data[] = $row;
}

$grand_diff = $grand_actual - $grand_target;
$grand_pct  = $grand_target > 0 ? round(($grand_actual / $grand_target) * 100, 1) : 0;

/* ── Departments for filter ── */
$res_depts = $conn->query("SELECT id, name FROM departments ORDER BY name");
/* ── Employees for filter ── */
$res_emps  = $conn->query("SELECT id, name FROM employees WHERE isdeleted!=1 AND active=1 ORDER BY name");

/* ── Years range ── */
$years_range = range((int)date('Y') - 3, (int)date('Y') + 1);

/* ── helper: achievement color ── */
function achColor(float $pct): string {
    if ($pct >= 100) return '#16a34a';
    if ($pct >= 75)  return '#d97706';
    if ($pct >= 50)  return '#f59e0b';
    return '#dc2626';
}
function achBg(float $pct): string {
    if ($pct >= 100) return '#dcfce7';
    if ($pct >= 75)  return '#fef3c7';
    if ($pct >= 50)  return '#fff7ed';
    return '#fee2e2';
}
?>

<div class="content-wrapper">
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-8">
                <h1 class="m-0 text-dark">
                    <i class="fas fa-bullseye text-primary ml-2"></i>
                    تقرير تارجيت الموظفين
                    <small class="text-muted" style="font-size:.75rem;"><?= htmlspecialchars($period_label) ?></small>
                </h1>
            </div>
            <div class="col-sm-4 text-left">
                <a href="sales-by-employee.php" class="btn btn-sm btn-outline-info">
                    <i class="fas fa-chart-bar ml-1"></i> مبيعات الموظفين
                </a>
            </div>
        </div>
    </div>
</section>

<section class="content">
<div class="container-fluid">

<!-- ═══════════════ FILTERS ═══════════════ -->
<div class="card card-outline card-primary shadow-sm mb-4">
    <div class="card-header"><h3 class="card-title"><i class="fas fa-filter ml-2"></i>خيارات التقرير</h3></div>
    <div class="card-body">
        <form method="get" action="">
            <div class="row align-items-end">

                <div class="col-md-2 col-6 mb-2">
                    <label class="font-weight-bold text-secondary" style="font-size:.82rem;">نوع الفترة</label>
                    <select name="view_type" class="custom-select custom-select-sm" id="vt_select">
                        <option value="monthly" <?= $view_type==='monthly'?'selected':'' ?>>شهري</option>
                        <option value="yearly"  <?= $view_type==='yearly' ?'selected':'' ?>>سنوي</option>
                    </select>
                </div>

                <div class="col-md-2 col-6 mb-2" id="month_col" <?= $view_type==='yearly'?'style="display:none"':'' ?>>
                    <label class="font-weight-bold text-secondary" style="font-size:.82rem;">الشهر</label>
                    <select name="report_month" class="custom-select custom-select-sm">
                        <?php foreach ($months_ar as $m => $mn): ?>
                        <option value="<?= $m ?>" <?= $m==$report_month?'selected':'' ?>><?= $mn ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-2 col-6 mb-2">
                    <label class="font-weight-bold text-secondary" style="font-size:.82rem;">السنة</label>
                    <select name="report_year" class="custom-select custom-select-sm">
                        <?php foreach ($years_range as $y): ?>
                        <option value="<?= $y ?>" <?= $y==$report_year?'selected':'' ?>><?= $y ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-2 col-6 mb-2">
                    <label class="font-weight-bold text-secondary" style="font-size:.82rem;">القسم</label>
                    <select name="dept_filter" class="custom-select custom-select-sm">
                        <option value="0">كل الأقسام</option>
                        <?php while ($d=$res_depts->fetch_assoc()): ?>
                        <option value="<?= $d['id'] ?>" <?= $d['id']==$dept_filter?'selected':'' ?>><?= htmlspecialchars($d['name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-md-2 col-6 mb-2">
                    <label class="font-weight-bold text-secondary" style="font-size:.82rem;">الموظف</label>
                    <select name="emp_filter" class="custom-select custom-select-sm">
                        <option value="0">كل الموظفين</option>
                        <?php while ($ef=$res_emps->fetch_assoc()): ?>
                        <option value="<?= $ef['id'] ?>" <?= $ef['id']==$emp_filter?'selected':'' ?>><?= htmlspecialchars($ef['name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-md-2 col-12 mb-2">
                    <button type="submit" class="btn btn-primary btn-block btn-sm">
                        <i class="fas fa-search ml-1"></i> عرض التقرير
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- ═══════════════ SUMMARY CARDS ═══════════════ -->
<div class="row mb-4">
    <?php
    $achieved_count = count(array_filter($data, fn($r)=>$r['pct']>=100));
    $no_target_count= count(array_filter($data, fn($r)=>$r['target_value']==0));
    ?>
    <div class="col-6 col-md-3 mb-3">
        <div class="info-box shadow-sm" style="border-radius:10px;">
            <span class="info-box-icon bg-primary" style="border-radius:10px 0 0 10px;"><i class="fas fa-users"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">الموظفين</span>
                <span class="info-box-number"><?= count($data) ?></span>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3 mb-3">
        <div class="info-box shadow-sm" style="border-radius:10px;">
            <span class="info-box-icon bg-info" style="border-radius:10px 0 0 10px;"><i class="fas fa-bullseye"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">إجمالي التارجيت</span>
                <span class="info-box-number"><?= number_format($grand_target, 0) ?> ج.م</span>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3 mb-3">
        <div class="info-box shadow-sm" style="border-radius:10px;">
            <span class="info-box-icon bg-success" style="border-radius:10px 0 0 10px;"><i class="fas fa-chart-line"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">إجمالي المبيعات</span>
                <span class="info-box-number"><?= number_format($grand_actual, 0) ?> ج.م</span>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3 mb-3">
        <div class="info-box shadow-sm" style="border-radius:10px;background:<?= achBg($grand_pct) ?>;">
            <span class="info-box-icon" style="background:<?= achColor($grand_pct) ?>;border-radius:10px 0 0 10px;"><i class="fas fa-percentage text-white"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">نسبة التحقيق الكلية</span>
                <span class="info-box-number" style="color:<?= achColor($grand_pct) ?>;"><?= $grand_pct ?>%</span>
            </div>
        </div>
    </div>
</div>

<!-- ═══════════════ VISUAL BARS ═══════════════ -->
<?php if (count($data) > 0): ?>
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white">
        <h5 class="card-title m-0"><i class="fas fa-chart-bar text-info ml-2"></i>نظرة سريعة — نسبة التحقيق</h5>
    </div>
    <div class="card-body" style="max-height:320px;overflow-y:auto;">
        <?php foreach ($data as $row): ?>
        <?php $pct = min($row['pct'], 150); /* cap bar at 150% */ ?>
        <div class="mb-3">
            <div class="d-flex justify-content-between mb-1">
                <span class="font-weight-bold" style="font-size:.88rem;">
                    <?= htmlspecialchars($row['emp_name']) ?>
                    <?php if ($row['target_value'] == 0): ?>
                        <span class="badge badge-secondary ml-1" style="font-size:.7rem;">بدون تارجيت</span>
                    <?php endif; ?>
                </span>
                <span style="font-size:.85rem;color:<?= achColor($row['pct']) ?>;font-weight:700;"><?= $row['pct'] ?>%</span>
            </div>
            <div class="progress" style="height:14px;border-radius:7px;background:#e5e7eb;">
                <div class="progress-bar" role="progressbar"
                     style="width:<?= min($row['pct'], 100) ?>%;background:<?= achColor($row['pct']) ?>;border-radius:7px;transition:width .6s ease;"
                     aria-valuenow="<?= $row['pct'] ?>" aria-valuemin="0" aria-valuemax="100">
                </div>
            </div>
            <div class="d-flex justify-content-between mt-1" style="font-size:.75rem;color:#6b7280;">
                <span>تارجيت: <?= number_format($row['target_value'],0) ?> ج.م</span>
                <span>فعلي: <?= number_format($row['actual_sales'],0) ?> ج.م</span>
                <span>فرق: <span style="color:<?= $row['diff']>=0?'#16a34a':'#dc2626' ?>;"><?= ($row['diff']>=0?'+':'') . number_format($row['diff'],0) ?> ج.م</span></span>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- ═══════════════ DETAIL TABLE ═══════════════ -->
<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="card-title m-0"><i class="fas fa-table text-primary ml-2"></i>تفاصيل التارجيت</h5>
        <button class="btn btn-sm btn-outline-secondary" onclick="window.print()">
            <i class="fas fa-print ml-1"></i> طباعة
        </button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-bordered table-striped mb-0 text-center" id="targetsTable">
                <thead class="thead-dark">
                    <tr>
                        <th style="width:40px;">#</th>
                        <th class="text-right">الموظف</th>
                        <th>القسم</th>
                        <th>التارجيت (ج.م)</th>
                        <th>المبيعات الفعلية (ج.م)</th>
                        <th>الفرق (ج.م)</th>
                        <th style="width:160px;">نسبة التحقيق</th>
                        <th>الحالة</th>
                        <th>ملاحظات</th>
                    </tr>
                </thead>
                <tbody>
                <?php $x=0; foreach ($data as $row): $x++; ?>
                    <tr>
                        <td><?= $x ?></td>
                        <td class="text-right font-weight-bold">
                            <a href="edit_employee.php?id=<?= (int)$row['emp_id'] ?>" class="text-dark" target="_blank">
                                <?= htmlspecialchars($row['emp_name']) ?>
                            </a>
                        </td>
                        <td><?= htmlspecialchars($row['dept_name'] ?? '—') ?></td>
                        <td class="font-weight-bold">
                            <?php if ($row['target_value'] > 0): ?>
                                <?= number_format($row['target_value'], 2) ?>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="font-weight-bold text-primary"><?= number_format($row['actual_sales'], 2) ?></td>
                        <td class="font-weight-bold" style="color:<?= $row['diff']>=0?'#16a34a':'#dc2626' ?>;">
                            <?= ($row['diff']>=0?'+':'') . number_format($row['diff'], 2) ?>
                        </td>
                        <td>
                            <?php if ($row['target_value'] > 0): ?>
                            <div style="position:relative;">
                                <div class="progress mb-1" style="height:10px;border-radius:5px;background:#e5e7eb;">
                                    <div class="progress-bar"
                                         style="width:<?= min($row['pct'],100) ?>%;background:<?= achColor($row['pct']) ?>;border-radius:5px;">
                                    </div>
                                </div>
                                <span class="font-weight-bold" style="color:<?= achColor($row['pct']) ?>;font-size:.92rem;"><?= $row['pct'] ?>%</span>
                            </div>
                            <?php else: ?>
                                <span class="text-muted" style="font-size:.82rem;">لا يوجد تارجيت</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                            if ($row['target_value'] == 0) {
                                echo '<span class="badge" style="background:#6b7280;color:#fff;padding:.35em .75em;border-radius:6px;">بدون تارجيت</span>';
                            } elseif ($row['pct'] >= 100) {
                                echo '<span class="badge" style="background:#16a34a;color:#fff;padding:.35em .75em;border-radius:6px;"><i class="fas fa-check-circle ml-1"></i>حقق التارجيت</span>';
                            } elseif ($row['pct'] >= 75) {
                                echo '<span class="badge" style="background:#d97706;color:#fff;padding:.35em .75em;border-radius:6px;"><i class="fas fa-minus-circle ml-1"></i>قريب من التارجيت</span>';
                            } elseif ($row['pct'] >= 50) {
                                echo '<span class="badge" style="background:#f59e0b;color:#fff;padding:.35em .75em;border-radius:6px;">متوسط</span>';
                            } else {
                                echo '<span class="badge" style="background:#dc2626;color:#fff;padding:.35em .75em;border-radius:6px;"><i class="fas fa-times-circle ml-1"></i>لم يحقق</span>';
                            }
                            ?>
                        </td>
                        <td class="text-right" style="font-size:.8rem;color:#6b7280;">
                            <?= htmlspecialchars($row['target_notes'] ?: '—') ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
                <tfoot class="bg-light font-weight-bold text-center">
                    <tr>
                        <td colspan="3" class="text-right">الإجمالي:</td>
                        <td><?= number_format($grand_target, 2) ?> ج.م</td>
                        <td class="text-primary"><?= number_format($grand_actual, 2) ?> ج.م</td>
                        <td style="color:<?= $grand_diff>=0?'#16a34a':'#dc2626' ?>;"><?= ($grand_diff>=0?'+':'').number_format($grand_diff,2) ?> ج.م</td>
                        <td colspan="3" style="color:<?= achColor($grand_pct) ?>;font-size:1rem;"><?= $grand_pct ?>%</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<?php else: ?>
<div class="card shadow-sm">
    <div class="card-body text-center py-5 text-muted">
        <i class="fas fa-inbox fa-3x mb-3 d-block" style="color:#d1d5db;"></i>
        <h5>لا يوجد موظفون نشطون</h5>
        <p>تأكد من وجود موظفين مفعلين في النظام</p>
    </div>
</div>
<?php endif; ?>

</div><!-- /container-fluid -->
</section>
</div><!-- /content-wrapper -->

<style>
@media print {
    .main-sidebar, .main-header, .content-header .col-sm-4,
    .card:first-child /* filters */, .card-outline { display:none !important; }
    .content-wrapper { margin:0 !important; }
    .card { box-shadow:none !important; border:1px solid #ddd !important; }
    .badge { border:1px solid #999 !important; }
}
</style>

<script>
/* toggle month field based on view_type */
document.getElementById('vt_select').addEventListener('change', function() {
    document.getElementById('month_col').style.display = this.value === 'yearly' ? 'none' : '';
});
</script>

<?php include('includes/footer.php'); ?>
