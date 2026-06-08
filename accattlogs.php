<?php include('includes/header.php') ?>
<?php include('includes/navbar.php') ?>
<?php include('includes/sidebar.php') ?>

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
                                    <tr><th>المستحق الإجمالي</th><td class="bg-light"><strong><?= number_format((float)$rowdoc['entitle'], 2) ?></strong></td></tr>
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

            <!-- الخصومات والإضافيات -->
            <div class="card attdoc-section shadow-sm">
                <div class="card-header bg-gradient-light">
                    <h3 class="card-title mb-0 font-weight-bold text-dark">الخصومات والإضافيات المالية في هذه الفترة</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-0 text-center">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 5%;">م</th>
                                    <th style="width: 15%;">التاريخ</th>
                                    <th style="width: 15%;">اليوم</th>
                                    <th style="width: 15%;">النوع</th>
                                    <th style="width: 15%;">المبلغ</th>
                                    <th>السبب</th>
                                    <th>ملاحظات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql_ft = "SELECT * FROM financial_transactions WHERE emp_name = '$empname' AND date >= '$startdate' AND date <= '$enddate' ORDER BY date ASC";
                                $res_ft = $conn->query($sql_ft);
                                $i_ft = 0;
                                $extraSum = 0;
                                $deductSum = 0;
                                if ($res_ft && $res_ft->num_rows > 0) {
                                    while ($row_ft = $res_ft->fetch_assoc()) {
                                        $i_ft++;
                                        if ($row_ft['type'] == 1) {
                                            $extraSum += (float)$row_ft['amount'];
                                        } else {
                                            $deductSum += (float)$row_ft['amount'];
                                        }
                                        $typeLabel = $row_ft['type'] == 1 ? '<span class="badge badge-success px-3 py-2 bg-success-light text-success" style="border-radius: 20px;">إضافي</span>' : '<span class="badge badge-danger px-3 py-2 bg-danger-light text-danger" style="border-radius: 20px;">خصم</span>';
                                ?>
                                <tr>
                                    <td><?= $i_ft ?></td>
                                    <td><?= $row_ft['date'] ?></td>
                                    <td><?= day_name_ar($row_ft['date'], $daysAr) ?></td>
                                    <td><?= $typeLabel ?></td>
                                    <td class="<?= $row_ft['type'] == 1 ? 'text-success' : 'text-danger' ?> font-weight-bold" style="font-size: 1.05rem;">
                                        <?= $row_ft['type'] == 1 ? '+' : '-' ?><?= number_format($row_ft['amount'], 2) ?> ج.م
                                    </td>
                                    <td class="text-right"><?= htmlspecialchars($row_ft['reason']) ?></td>
                                    <td class="text-muted"><?= htmlspecialchars($row_ft['notes'] ?? '—') ?></td>
                                </tr>
                                <?php
                                    }
                                } else {
                                ?>
                                <tr><td colspan="7" class="text-center text-muted py-3">لا توجد سجلات خصومات أو إضافيات مالية في هذه الفترة</td></tr>
                                <?php } ?>
                            </tbody>
                            <?php if ($i_ft > 0) { ?>
                            <tfoot>
                                <tr class="font-weight-bold bg-light">
                                    <th colspan="4" class="text-left text-secondary">إجمالي الفترة:</th>
                                    <th colspan="3" class="text-right">
                                        <span class="text-success me-3">إضافي: +<?= number_format($extraSum, 2) ?> ج.م</span>
                                        <span class="text-secondary mx-2">|</span>
                                        <span class="text-danger me-3">خصم: -<?= number_format($deductSum, 2) ?> ج.م</span>
                                        <span class="text-secondary mx-2">|</span>
                                        <span class="text-dark">الصافي: <?= ($extraSum - $deductSum >= 0 ? '+' : '') . number_format($extraSum - $deductSum, 2) ?> ج.م</span>
                                    </th>
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
.bg-success-light {
    background-color: rgba(40, 167, 69, 0.15) !important;
}
.bg-danger-light {
    background-color: rgba(220, 53, 69, 0.15) !important;
}
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
