<?php
include('includes/header.php');
include('includes/navbar.php');
include('includes/sidebar.php');
require_once('includes/payroll_calcs_helper.php');
ensure_payroll_calcs_schema($conn);
?>

<?php
$daysAr = [
    'Saturday' => 'السبت', 'Sunday' => 'الأحد', 'Monday' => 'الإثنين',
    'Tuesday' => 'الثلاثاء', 'Wednesday' => 'الأربعاء', 'Thursday' => 'الخميس', 'Friday' => 'الجمعة'
];
function day_name_ar($date, $daysAr) {
    $n = date('l', strtotime($date));
    return $daysAr[$n] ?? $n;
}
function status_label($statue) {
    if ($statue == 0) return 'اجازة';
    if ($statue == 1) return 'غائب';
    if ($statue == 2) return 'حضور';
    return '—';
}

$logid = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$rowdoc = $conn->query("SELECT * FROM attdocs WHERE id = $logid AND (isdeleted != 1 OR isdeleted IS NULL)")->fetch_assoc();
$rowlogFirst = $conn->query("SELECT * FROM attlog WHERE attdoc = '$logid' ORDER BY day ASC LIMIT 1")->fetch_assoc();
$hasData = $rowdoc && $rowlogFirst;

$rowemp = null;
$rowlast = null;
$startdate = '';
$enddate = '';
$companyName = $rowstg['company_name'] ?? 'FOCUS';

if ($hasData) {
    $empid = (int)$rowdoc['empid'];
    $rowemp = $conn->query("SELECT * FROM employees WHERE id = $empid")->fetch_assoc();
    $rowlast = $conn->query("SELECT * FROM attlog WHERE attdoc = '$logid' ORDER BY day DESC LIMIT 1")->fetch_assoc();
    $startdate = $rowdoc['fromdate'];
    $enddate = $rowdoc['todate'];
}
?>

<div class="content-wrapper attdoc-page">
    <section class="content-header no-print">
        <div class="container-fluid">
            <div class="row mb-2 align-items-center">
                <div class="col-sm-6">
                    <h1 class="m-0">تفاصيل معالجة الحضور</h1>
                </div>
                <div class="col-sm-6 text-left">
                    <?php if ($hasData) { ?>
                    <button type="button" class="btn btn-primary" onclick="window.print()">
                        <i class="fas fa-print"></i> طباعة
                    </button>
                    <a href="calcsalary.php" class="btn btn-secondary">رجوع</a>
                    <?php } ?>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid" id="attdoc-print-area">
            <?php if (!$hasData) { ?>
                <div class="alert alert-danger">يبدو أنك دخلت هذه الصفحة من المكان الخطأ، أو أن المعالجة غير موجودة.</div>
            <?php } else {
                $empname = $conn->real_escape_string($rowemp['name']);
                $totDef = 0;
                $totCur = 0;
                $totExtra = 0;
                $totShort = 0;
                $totDue = 0;
            ?>

            <div class="print-only print-header">
                <h2><?= htmlspecialchars($companyName) ?></h2>
                <p>كشف حضور واستحقاق — معالجة رقم <?= $logid ?></p>
            </div>

            <div class="card attdoc-summary-card">
                <div class="card-body">
                    <div class="row attdoc-meta">
                        <div class="col-md-6">
                            <h3 class="mb-1"><?= htmlspecialchars($rowemp['name']) ?></h3>
                            <p class="text-muted mb-0">معالجة رقم <strong><?= $logid ?></strong></p>
                            <p class="mb-0">الفترة: من <strong><?= $startdate ?></strong> إلى <strong><?= $enddate ?></strong></p>
                            <?php if (!empty($rowdoc['info'])) { ?>
                            <p class="small text-muted mb-0"><?= htmlspecialchars(trim($rowdoc['info'])) ?></p>
                            <?php } ?>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm table-bordered mb-0 summary-mini">
                                <tbody>
                                    <tr><th>الراتب</th><td><?= number_format((float)$rowemp['salary'], 2) ?></td></tr>
                                    <tr><th>أيام الحضور</th><td><?= (int)$rowdoc['attdays'] ?> / <?= (int)$rowdoc['workdays'] ?></td></tr>
                                    <tr><th>ساعات متوقعة (الفترة)</th><td><?= number_format((float)$rowdoc['exphours'], 2) ?> س</td></tr>
                                    <tr><th>ساعات فعلية</th><td><?= number_format((float)$rowdoc['accualhours'], 2) ?> س</td></tr>
                                    <tr><th>فرق الفترة</th>
                                        <?php
                                        $periodDiff = round((float)$rowdoc['accualhours'] - (float)$rowdoc['exphours'], 2);
                                        $pdClass = $periodDiff > 0 ? 'text-success' : ($periodDiff < 0 ? 'text-danger' : '');
                                        ?>
                                        <td class="<?= $pdClass ?>"><strong><?= $periodDiff > 0 ? '+' : '' ?><?= number_format($periodDiff, 2) ?></strong> س</td>
                                    </tr>
                                    <tr><th>المستحق الأساسي</th><td><strong><?= number_format((float)$rowdoc['entitle'], 2) ?></strong></td></tr>
                                    <tr><th>مكافأة</th><td class="text-success">+ <?= number_format((float)($rowdoc['bonus'] ?? 0), 2) ?></td></tr>
                                    <tr><th>تأمين</th><td class="text-danger">- <?= number_format((float)($rowdoc['insurance'] ?? 0), 2) ?></td></tr>
                                    <tr><th>ضريبة دخل</th><td class="text-danger">- <?= number_format((float)($rowdoc['tax'] ?? 0), 2) ?></td></tr>
                                    <tr><th>خصم</th><td class="text-danger">- <?= number_format((float)($rowdoc['deduction'] ?? 0), 2) ?></td></tr>
                                    <?php
                                    $netDisplay = isset($rowdoc['net_pay']) && (float)$rowdoc['net_pay'] != 0
                                        ? (float)$rowdoc['net_pay']
                                        : payroll_net_pay((float)$rowdoc['entitle'], [
                                            'bonus' => (float)($rowdoc['bonus'] ?? 0),
                                            'insurance' => (float)($rowdoc['insurance'] ?? 0),
                                            'tax' => (float)($rowdoc['tax'] ?? 0),
                                            'deduction' => (float)($rowdoc['deduction'] ?? 0),
                                        ]);
                                    ?>
                                    <tr><th>الصافي</th><td class="bg-light"><strong><?= number_format($netDisplay, 2) ?></strong></td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- الحضور -->
            <div class="card attdoc-section">
                <div class="card-header">
                    <h3 class="card-title mb-0">الحضور والانصراف</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0 attdoc-att-table" id="attTable">
                            <thead class="thead-light">
                                <tr>
                                    <th>م</th>
                                    <th>تاريخ</th>
                                    <th>اليوم</th>
                                    <th>الحالة</th>
                                    <th>الشيفت</th>
                                    <th>دخول</th>
                                    <th>خروج</th>
                                    <th>ساعات متوقعة</th>
                                    <th>ساعات العمل</th>
                                    <th>زيادة / نقص</th>
                                    <th>المستحق</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $x = 1;
                                $reslog = $conn->query("SELECT * FROM attlog WHERE attdoc = '$logid' ORDER BY day ASC");
                                while ($rowlog = $reslog->fetch_assoc()) {
                                    $def = (float)$rowlog['defhours'];
                                    $cur = (float)$rowlog['curhours'];
                                    $diff = round($cur - $def, 2);
                                    $due = (float)$rowlog['realdue'];
                                    $totDef += $def;
                                    $totCur += $cur;
                                    if ($diff > 0) {
                                        $totExtra += $diff;
                                    } elseif ($diff < 0) {
                                        $totShort += abs($diff);
                                    }
                                    $totDue += $due;
                                    $diffClass = $diff > 0 ? 'text-success' : ($diff < 0 ? 'text-danger' : '');
                                    $diffText = $diff > 0 ? '+' . number_format($diff, 2) : number_format($diff, 2);
                                ?>
                                <tr>
                                    <td><?= $x++ ?></td>
                                    <td><?= $rowlog['day'] ?></td>
                                    <td><?= day_name_ar($rowlog['day'], $daysAr) ?></td>
                                    <td><span class="badge-status s<?= (int)$rowlog['statue'] ?>"><?= status_label($rowlog['statue']) ?></span></td>
                                    <td><?= $rowlog['starttime'] ?> — <?= $rowlog['endtime'] ?></td>
                                    <td><?= $rowlog['fpin'] ?: '—' ?></td>
                                    <td><?= $rowlog['fpout'] ?: '—' ?></td>
                                    <td class="td-def"><?= number_format($def, 2) ?></td>
                                    <td class="td-cur"><?= number_format($cur, 2) ?></td>
                                    <td class="td-diff <?= $diffClass ?>"><?= $diffText ?></td>
                                    <td class="td-due"><?= number_format($due, 2) ?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                            <tfoot>
                                <tr class="font-weight-bold totals-row">
                                    <th colspan="7" class="text-left">الإجمالي</th>
                                    <th class="sum-def"><?= number_format($totDef, 2) ?></th>
                                    <th class="sum-cur"><?= number_format($totCur, 2) ?></th>
                                    <th class="sum-diff">
                                        <?php if ($totExtra > 0) { ?><span class="text-success">+<?= number_format($totExtra, 2) ?></span><?php } ?>
                                        <?php if ($totShort > 0) { ?><span class="text-danger"> -<?= number_format($totShort, 2) ?></span><?php } ?>
                                        <?php if ($totExtra == 0 && $totShort == 0) { ?>0.00<?php } ?>
                                    </th>
                                    <th class="sum-due"><?= number_format($totDue, 2) ?></th>
                                </tr>
                                <tr class="totals-detail-row">
                                    <th colspan="9" class="text-left">ملخص الساعات</th>
                                    <th colspan="2">
                                        زيادة: <span class="text-success"><?= number_format($totExtra, 2) ?></span>
                                        — نقص: <span class="text-danger"><?= number_format($totShort, 2) ?></span>
                                        — صافي: <strong><?= number_format(round($totCur - $totDef, 2), 2) ?></strong>
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- احتسابات الرواتب -->
            <div class="card attdoc-section">
                <div class="card-header">
                    <h3 class="card-title mb-0">احتسابات الرواتب (مكافأة / تأمين / ضريبة / خصم)</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>م</th>
                                    <th>التاريخ</th>
                                    <th>اليوم</th>
                                    <th>النوع</th>
                                    <th>مبلغ ثابت</th>
                                    <th>نسبة %</th>
                                    <th>القيمة المحتسبة</th>
                                    <th>بيان</th>
                                    <th>ملاحظات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sqlPc = "SELECT * FROM payroll_calcs WHERE isdeleted = 0 AND (emp_id = $empid OR emp_name = '$empname') AND date >= '$startdate' AND date <= '$enddate' ORDER BY date ASC";
                                $resultPc = $conn->query($sqlPc);
                                $pcIdx = 0;
                                $pcBonus = 0;
                                $pcInsurance = 0;
                                $pcTax = 0;
                                $pcDeduction = 0;
                                $baseEnt = (float) $rowdoc['entitle'];
                                if ($resultPc && $resultPc->num_rows > 0) {
                                    while ($rowPc = $resultPc->fetch_assoc()) {
                                        $pcIdx++;
                                        $lineVal = payroll_calc_line_amount($rowPc, $baseEnt);
                                        $tybe = (int) $rowPc['calc_tybe'];
                                        if ($tybe === 1) $pcBonus += $lineVal;
                                        elseif ($tybe === 2) $pcInsurance += $lineVal;
                                        elseif ($tybe === 3) $pcTax += $lineVal;
                                        elseif ($tybe === 4) $pcDeduction += $lineVal;
                                ?>
                                <tr>
                                    <td><?= $pcIdx ?></td>
                                    <td><?= $rowPc['date'] ?></td>
                                    <td><?= day_name_ar($rowPc['date'], $daysAr) ?></td>
                                    <td class="<?= payroll_calc_is_addition($tybe) ? 'text-success' : 'text-danger' ?>">
                                        <?= payroll_calc_type_label($tybe, true) ?>
                                    </td>
                                    <td><?= (float)$rowPc['percent'] > 0 ? '—' : number_format((float)$rowPc['amount'], 2) ?></td>
                                    <td><?= (float)$rowPc['percent'] > 0 ? number_format((float)$rowPc['percent'], 2) . '%' : '—' ?></td>
                                    <td class="<?= payroll_calc_is_addition($tybe) ? 'text-success' : 'text-danger' ?>">
                                        <?= payroll_calc_is_addition($tybe) ? '+' : '−' ?><?= number_format($lineVal, 2) ?>
                                    </td>
                                    <td><?= htmlspecialchars($rowPc['info'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($rowPc['info2'] ?? '') ?></td>
                                </tr>
                                <?php
                                    }
                                } else {
                                ?>
                                <tr><td colspan="9" class="text-center text-muted">لا توجد احتسابات في هذه الفترة</td></tr>
                                <?php } ?>
                            </tbody>
                            <?php if ($pcIdx > 0) { ?>
                            <tfoot>
                                <tr class="font-weight-bold">
                                    <th colspan="6" class="text-left">الإجمالي</th>
                                    <th colspan="3">
                                        مكافأة: <?= number_format($pcBonus, 2) ?> —
                                        تأمين: <?= number_format($pcInsurance, 2) ?> —
                                        ضريبة: <?= number_format($pcTax, 2) ?> —
                                        خصم: <?= number_format($pcDeduction, 2) ?>
                                    </th>
                                </tr>
                            </tfoot>
                            <?php } ?>
                        </table>
                    </div>
                </div>
            </div>

            <!-- الانتاجية -->
            <div class="card attdoc-section">
                <div class="card-header">
                    <h3 class="card-title mb-0">الانتاجية</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>م</th>
                                    <th>التاريخ</th>
                                    <th>اليوم</th>
                                    <th>ع الوحدات</th>
                                    <th>س الوحدة</th>
                                    <th>القيمة</th>
                                    <th>بيان</th>
                                    <th>ملاحظات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT * FROM productions WHERE emp_name = '$empname' AND date >= '$startdate' AND date <= '$enddate' ORDER BY date ASC";
                                $result = $conn->query($sql);
                                $i = 0;
                                $prodSum = 0;
                                if ($result && $result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        $i++;
                                        $prodSum += (float)$row['value'];
                                ?>
                                <tr>
                                    <td><?= $i ?></td>
                                    <td><?= $row['date'] ?></td>
                                    <td><?= day_name_ar($row['date'], $daysAr) ?></td>
                                    <td><?= $row['qty'] ?></td>
                                    <td><?= $row['price'] ?></td>
                                    <td><?= $row['value'] ?></td>
                                    <td><?= htmlspecialchars($row['info'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($row['info2'] ?? '') ?></td>
                                </tr>
                                <?php
                                    }
                                } else {
                                ?>
                                <tr><td colspan="8" class="text-center text-muted">لا توجد سجلات انتاجية في هذه الفترة</td></tr>
                                <?php } ?>
                            </tbody>
                            <?php if ($i > 0) { ?>
                            <tfoot>
                                <tr class="font-weight-bold">
                                    <th colspan="5" class="text-left">إجمالي الانتاجية</th>
                                    <th colspan="3"><?= number_format($prodSum, 2) ?></th>
                                </tr>
                            </tfoot>
                            <?php } ?>
                        </table>
                    </div>
                </div>
            </div>

            <div class="print-only print-footer">
                <p>تاريخ الطباعة: <?= date('Y-m-d H:i') ?></p>
            </div>

            <?php } ?>
        </div>
    </section>
</div>

<style>
.attdoc-page .badge-status {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 12px;
}
.attdoc-page .badge-status.s0 { background: #d4edda; color: #155724; }
.attdoc-page .badge-status.s1 { background: #f8d7da; color: #721c24; }
.attdoc-page .badge-status.s2 { background: #e2e3e5; color: #383d41; }
.attdoc-page .summary-mini th { width: 45%; background: #f8f9fa; }
.attdoc-page .totals-row th,
.attdoc-page .totals-row td { background: #e9ecef; }
.attdoc-page .totals-detail-row th,
.attdoc-page .totals-detail-row td { background: #f8f9fa; font-size: 13px; }
.print-only { display: none; }

@media print {
    @page {
        size: A4 landscape;
        margin: 12mm;
    }
    body {
        background: #fff !important;
        font-size: 11pt;
        direction: rtl;
    }
    .main-header,
    .main-sidebar,
    .content-header,
    .no-print,
    .main-footer {
        display: none !important;
    }
    .content-wrapper,
    .content-wrapper::before,
    .content-wrapper::after {
        margin: 0 !important;
        padding: 0 !important;
        background: #fff !important;
    }
    .content-wrapper {
        min-height: auto !important;
    }
    .attdoc-page .content {
        padding: 0 !important;
    }
    .attdoc-page .card {
        border: 1px solid #000 !important;
        box-shadow: none !important;
        break-inside: avoid;
        margin-bottom: 10px !important;
    }
    .attdoc-page .card-header {
        background: #eee !important;
        border-bottom: 1px solid #000 !important;
        padding: 6px 10px !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    .attdoc-page .table {
        font-size: 10pt;
        color: #000 !important;
    }
    .attdoc-page .table th,
    .attdoc-page .table td {
        border: 1px solid #333 !important;
        padding: 4px 6px !important;
    }
    .attdoc-page .thead-light th {
        background: #ddd !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    .attdoc-page .text-success { color: #006400 !important; }
    .attdoc-page .text-danger { color: #8b0000 !important; }
    .attdoc-page .bg-light {
        background: #f5f5f5 !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    .print-only {
        display: block !important;
    }
    .print-header {
        text-align: center;
        margin-bottom: 12px;
        border-bottom: 2px solid #000;
        padding-bottom: 8px;
    }
    .print-header h2 { margin: 0; font-size: 18pt; }
    .print-header p { margin: 4px 0 0; }
    .print-footer {
        margin-top: 12px;
        text-align: center;
        font-size: 9pt;
        color: #333;
    }
    #attdoc-print-area {
        width: 100%;
    }
}
</style>

<?php include('includes/footer.php') ?>
