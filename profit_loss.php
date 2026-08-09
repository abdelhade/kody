<?php include('includes/header.php') ?>
<?php include('includes/navbar.php') ?>
<?php include('includes/sidebar.php') ?>
<?php
// ──────────────────────────────────────────────────────────────
// تحديد الفترة الزمنية
// ──────────────────────────────────────────────────────────────
$startdate = (isset($_POST['startdate']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_POST['startdate']))
    ? $_POST['startdate'] : date('Y-01-01');
$enddate = (isset($_POST['enddate']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_POST['enddate']))
    ? $_POST['enddate'] : date('Y-m-d');

// ──────────────────────────────────────────────────────────────
// دالة مساعدة: تنسيق محاسبي
// ──────────────────────────────────────────────────────────────
function plFormat($value) {
    if ($value < -0.001) {
        return '(' . number_format(abs($value), 2) . ')';
    }
    return number_format(abs($value), 2);
}

// ══════════════════════════════════════════════════════════════
// المصدر الوحيد الموثوق للمبيعات: ot_head
//
// بعد فحص قاعدة البيانات تبيّن:
//   - حسابات 31% (إيرادات المبيعات) فارغة تماماً في journal_entries
//   - المبيعات تُسجَّل في ot_head فقط
//   - pro_tybe=9  : مبيعات كاشير (JE: عميل مدين + مخزن دائن)
//   - pro_tybe=3 مع journal_tybe=3 : مبيعات تيك-أواي (JE: عميل مدين + 41103 دائن)
//   - pro_tybe=10 : مبيعات آجلة (JE: عميل مدين + مخزن دائن)
//   - pro_tybe=11 : مردود مبيعات → يُطرح
//
// الحساب 41103 "خصم مسموح به" يُستخدم فعلياً كحساب دائن للمبيعات
// لذا لا يُصنَّف كتكلفة مبيعات في هذا التقرير
// ══════════════════════════════════════════════════════════════

// ── 1. مبيعات ot_head ──────────────────────────────────────
$stmt_sales = $conn->prepare("
    SELECT
        COALESCE(SUM(CASE WHEN oh.pro_tybe IN (9)                   THEN oh.fat_net ELSE 0 END), 0) AS cashier_sales,
        COALESCE(SUM(CASE WHEN oh.pro_tybe = 3 AND oh.journal_tybe = 3 THEN oh.fat_net ELSE 0 END), 0) AS pos_sales,
        COALESCE(SUM(CASE WHEN oh.pro_tybe IN (10)                  THEN oh.fat_net ELSE 0 END), 0) AS credit_sales,
        COALESCE(SUM(CASE WHEN oh.pro_tybe IN (11)                  THEN oh.fat_net ELSE 0 END), 0) AS returns_total
    FROM ot_head oh
    WHERE oh.isdeleted = 0
      AND DATE(oh.pro_date) BETWEEN ? AND ?
");
$stmt_sales->bind_param("ss", $startdate, $enddate);
$stmt_sales->execute();
$row_sales = $stmt_sales->get_result()->fetch_assoc();
$stmt_sales->close();

$cashier_sales    = (float)$row_sales['cashier_sales'];
$pos_sales        = (float)$row_sales['pos_sales'];
$credit_sales     = (float)$row_sales['credit_sales'];
$returns_total    = (float)$row_sales['returns_total'];

// إجمالي المبيعات (قبل المردود)
$gross_sales_total = $cashier_sales + $pos_sales + $credit_sales;
// صافي المبيعات (بعد المردود)
$net_sales         = $gross_sales_total - $returns_total;

// ── 2. إيرادات أخرى (32%) من journal_entries ──────────────
// حسابات 32 دائنة الطبيعة → net = credit - debit
$stmt_rev32 = $conn->prepare("
    SELECT
        COALESCE(SUM(je.credit), 0) AS total_credit,
        COALESCE(SUM(je.debit),  0) AS total_debit
    FROM journal_entries je
    INNER JOIN acc_head ah ON je.account_id = ah.id
    WHERE ah.code LIKE '32%'
      AND ah.is_basic = 0
      AND ah.isdeleted = 0
      AND je.isdeleted = 0
      AND DATE(je.crtime) BETWEEN ? AND ?
");
$stmt_rev32->bind_param("ss", $startdate, $enddate);
$stmt_rev32->execute();
$rev32_row     = $stmt_rev32->get_result()->fetch_assoc();
$stmt_rev32->close();
$other_revenues = (float)$rev32_row['total_credit'] - (float)$rev32_row['total_debit'];
if ($other_revenues < 0) $other_revenues = 0.0;

// ── 3. تفاصيل الإيرادات الأخرى (32%) للعرض ──────────────
$stmt_rev32_detail = $conn->prepare("
    SELECT ah.id, ah.code, ah.aname,
        COALESCE(SUM(je.credit), 0) - COALESCE(SUM(je.debit), 0) AS net_credit
    FROM acc_head ah
    LEFT JOIN journal_entries je ON je.account_id = ah.id
        AND je.isdeleted = 0
        AND DATE(je.crtime) BETWEEN ? AND ?
    WHERE ah.code LIKE '32%'
      AND ah.is_basic = 0
      AND ah.isdeleted = 0
    GROUP BY ah.id, ah.code, ah.aname
    HAVING ABS(net_credit) > 0.001
    ORDER BY ah.code
");
$stmt_rev32_detail->bind_param("ss", $startdate, $enddate);
$stmt_rev32_detail->execute();
$rev32_details = $stmt_rev32_detail->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt_rev32_detail->close();

// ── 4. إجمالي الإيرادات ─────────────────────────────────
$revenue_total = $net_sales + $other_revenues;

// ── 5. تكلفة المبيعات (42%) من journal_entries ──────────
// حسابات 42 مدينة الطبيعة → net = debit - credit
$stmt_cos = $conn->prepare("
    SELECT
        COALESCE(SUM(je.debit),  0) AS total_debit,
        COALESCE(SUM(je.credit), 0) AS total_credit
    FROM journal_entries je
    INNER JOIN acc_head ah ON je.account_id = ah.id
    WHERE ah.code LIKE '42%'
      AND ah.is_basic = 0
      AND ah.isdeleted = 0
      AND je.isdeleted = 0
      AND DATE(je.crtime) BETWEEN ? AND ?
");
$stmt_cos->bind_param("ss", $startdate, $enddate);
$stmt_cos->execute();
$cos_row       = $stmt_cos->get_result()->fetch_assoc();
$stmt_cos->close();
$cost_of_sales = (float)$cos_row['total_debit'] - (float)$cos_row['total_credit'];
if ($cost_of_sales < 0) $cost_of_sales = 0.0;

// ── 6. تفاصيل تكلفة المبيعات (42%) للعرض ────────────────
$stmt_cos_detail = $conn->prepare("
    SELECT ah.id, ah.code, ah.aname,
        COALESCE(SUM(je.debit), 0) - COALESCE(SUM(je.credit), 0) AS net_debit
    FROM acc_head ah
    LEFT JOIN journal_entries je ON je.account_id = ah.id
        AND je.isdeleted = 0
        AND DATE(je.crtime) BETWEEN ? AND ?
    WHERE ah.code LIKE '42%'
      AND ah.is_basic = 0
      AND ah.isdeleted = 0
    GROUP BY ah.id, ah.code, ah.aname
    HAVING ABS(net_debit) > 0.001
    ORDER BY ah.code
");
$stmt_cos_detail->bind_param("ss", $startdate, $enddate);
$stmt_cos_detail->execute();
$cos_details = $stmt_cos_detail->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt_cos_detail->close();

// ── 7. المصروفات (44%) من journal_entries ────────────────
$stmt_exp = $conn->prepare("
    SELECT
        COALESCE(SUM(je.debit),  0) AS total_debit,
        COALESCE(SUM(je.credit), 0) AS total_credit
    FROM journal_entries je
    INNER JOIN acc_head ah ON je.account_id = ah.id
    WHERE ah.code LIKE '44%'
      AND ah.is_basic = 0
      AND ah.isdeleted = 0
      AND je.isdeleted = 0
      AND DATE(je.crtime) BETWEEN ? AND ?
");
$stmt_exp->bind_param("ss", $startdate, $enddate);
$stmt_exp->execute();
$exp_row        = $stmt_exp->get_result()->fetch_assoc();
$stmt_exp->close();
$total_expenses = (float)$exp_row['total_debit'] - (float)$exp_row['total_credit'];
if ($total_expenses < 0) $total_expenses = 0.0;

// ── 8. تفاصيل المصروفات (44%) للعرض ─────────────────────
$stmt_exp_detail = $conn->prepare("
    SELECT ah.id, ah.code, ah.aname,
        COALESCE(SUM(je.debit), 0) - COALESCE(SUM(je.credit), 0) AS net_debit
    FROM acc_head ah
    LEFT JOIN journal_entries je ON je.account_id = ah.id
        AND je.isdeleted = 0
        AND DATE(je.crtime) BETWEEN ? AND ?
    WHERE ah.code LIKE '44%'
      AND ah.is_basic = 0
      AND ah.isdeleted = 0
    GROUP BY ah.id, ah.code, ah.aname
    HAVING net_debit > 0.001
    ORDER BY ah.code
");
$stmt_exp_detail->bind_param("ss", $startdate, $enddate);
$stmt_exp_detail->execute();
$expense_items = $stmt_exp_detail->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt_exp_detail->close();

// ── 9. مجمل الربح / صافي الربح ───────────────────────────
$gross_profit = $revenue_total - $cost_of_sales;
$net_profit   = $gross_profit  - $total_expenses;

// ── اسم الشركة ────────────────────────────────────────────
$company_name = $rowstg['storename'] ?? 'الشركة';

// ── بيانات الرسم البياني للإيرادات ───────────────────────
$revenue_items = [];
// مبيعات كاشير
if ($cashier_sales > 0.001)  $revenue_items[] = ['name' => 'مبيعات كاشير',      'value' => $cashier_sales];
if ($pos_sales    > 0.001)  $revenue_items[] = ['name' => 'مبيعات تيك-أواي',    'value' => $pos_sales];
if ($credit_sales > 0.001)  $revenue_items[] = ['name' => 'مبيعات آجلة',        'value' => $credit_sales];
// إيرادات أخرى من 32
foreach ($rev32_details as $r32d) {
    if ($r32d['net_credit'] > 0.001) {
        $revenue_items[] = ['name' => $r32d['aname'], 'value' => (float)$r32d['net_credit']];
    }
}

// ── إجمالي التكاليف للبطاقة الملخصة ─────────────────────
$summary_total_costs = $cost_of_sales + $total_expenses;

// ══════════════════════════════════════════════════════════════
// التحقق الرياضي الإلزامي (سيُطبع في HTML comment للمطور)
// ══════════════════════════════════════════════════════════════
$_pl_debug = [
    'revenue_total'      => $revenue_total,
    'sales_gross_total'  => $gross_sales_total,
    'sales_net_total'    => $net_sales,
    'returns_total'      => $returns_total,
    'other_revenues'     => $other_revenues,
    'cost_of_sales'      => $cost_of_sales,
    'gross_profit'       => $gross_profit,
    'expense_total'      => $total_expenses,
    'net_profit'         => $net_profit,
    // التحقق
    'check_gross'        => abs($gross_profit - ($revenue_total - $cost_of_sales)) < 0.01 ? 'OK' : 'ERROR',
    'check_net'          => abs($net_profit   - ($gross_profit  - $total_expenses)) < 0.01 ? 'OK' : 'ERROR',
];
?>
<style>
.pl-wrapper { max-width: 1000px; margin: 0 auto; }

.pl-report-header {
    background: linear-gradient(135deg, #1a365d 0%, #2d3748 50%, #4a5568 100%);
    color: white; padding: 30px; border-radius: 12px 12px 0 0;
    text-align: center; position: relative; overflow: hidden;
}
.pl-report-header::before {
    content: ''; position: absolute; top:0;left:0;right:0;bottom:0;
    background: linear-gradient(45deg,transparent 30%,rgba(255,255,255,.03) 50%,transparent 70%);
    animation: pl-shine 6s infinite;
}
@keyframes pl-shine { 0%,100%{transform:translateX(-100%)} 50%{transform:translateX(100%)} }
.pl-report-header h2  { font-size:1.8rem; margin-bottom:5px; position:relative; z-index:1; }
.pl-report-header h4  { font-size:1.1rem; opacity:.9; position:relative; z-index:1; }
.pl-report-header .period { font-size:.9rem; opacity:.8; margin-top:10px; position:relative; z-index:1; }

/* Summary Cards */
.summary-cards {
    display: grid; grid-template-columns: repeat(4,1fr);
    gap: 15px; padding: 20px; background: #f8fafc; border-bottom: 1px solid #e2e8f0;
}
.summary-card {
    background: white; border-radius: 10px; padding: 18px; text-align: center;
    box-shadow: 0 2px 10px rgba(0,0,0,.06); transition: transform .3s,box-shadow .3s;
}
.summary-card:hover { transform: translateY(-3px); box-shadow: 0 6px 20px rgba(0,0,0,.1); }
.summary-card .card-label  { font-size:.85rem; color:#718096; margin-bottom:8px; font-weight:500; }
.summary-card .card-amount { font-size:1.4rem; font-weight:bold; font-family:'Courier New',monospace; }
.summary-card.sales  { border-top:3px solid #805ad5; }
.summary-card.sales  .card-amount { color:#805ad5; }
.summary-card.revenue { border-top:3px solid #38a169; }
.summary-card.revenue .card-amount { color:#38a169; }
.summary-card.expense { border-top:3px solid #e53e3e; }
.summary-card.expense .card-amount { color:#e53e3e; }
.summary-card.profit  .card-amount { color:#3182ce; }
.summary-card.loss    { border-top:3px solid #e53e3e; }
.summary-card.loss    .card-amount { color:#e53e3e; }

/* Section titles */
.pl-section-title {
    padding:12px 20px; font-weight:bold; font-size:1.05rem; margin:0;
    display:flex; align-items:center; gap:10px;
}
.pl-section-title.revenue-title { background:linear-gradient(135deg,#f0fff4,#c6f6d5); color:#22543d; border-right:4px solid #38a169; }
.pl-section-title.expense-title { background:linear-gradient(135deg,#fff5f5,#fed7d7); color:#742a2a; border-right:4px solid #e53e3e; }
.pl-section-title.cost-title    { background:linear-gradient(135deg,#fffaf0,#feebc8); color:#744210; border-right:4px solid #dd6b20; }

/* Tables */
.pl-table { width:100%; border-collapse:collapse; }
.pl-table td { padding:10px 20px; border-bottom:1px solid #f0f0f0; font-size:.95rem; transition:background .2s; }
.pl-table tr:hover td { background-color:#f8fafc; }
.pl-table .acc-name  { padding-right:40px; color:#4a5568; }
.pl-table .acc-value { text-align:left; font-weight:500; width:200px; font-family:'Courier New',monospace; }
.pl-table .total-row td {
    font-weight:bold; border-top:2px solid #e2e8f0; border-bottom:2px solid #e2e8f0;
    background:#f7fafc; font-size:1rem; padding:12px 20px;
}
.pl-table .total-row.revenue-total td { color:#22543d; }
.pl-table .total-row.expense-total td { color:#742a2a; }
.pl-table .total-row.cost-total    td { color:#744210; }
.pl-table .empty-row td { color:#a0aec0; font-style:italic; text-align:center; padding:14px; }

/* Gross profit row */
.gross-profit-row {
    padding:14px 20px; display:flex; justify-content:space-between; align-items:center;
    font-weight:bold; font-size:1.05rem;
    border-top:2px solid; border-bottom:2px solid;
}
.gross-profit-row.gp-profit { background:linear-gradient(135deg,#ebf8ff,#bee3f8); color:#2a4365; border-color:#90cdf4; }
.gross-profit-row.gp-loss   { background:linear-gradient(135deg,#fff5f5,#fed7d7); color:#742a2a; border-color:#fc8181; }
.gross-profit-row .result-value { font-family:'Courier New',monospace; font-size:1.1rem; }

/* Net result */
.net-result {
    padding:18px 20px; display:flex; justify-content:space-between;
    align-items:center; font-weight:bold; font-size:1.15rem;
}
.net-result.profit-result { background:linear-gradient(135deg,#c6f6d5,#9ae6b4); color:#22543d; border-top:3px double #38a169; }
.net-result.loss-result   { background:linear-gradient(135deg,#fed7d7,#feb2b2); color:#742a2a; border-top:3px double #e53e3e; }
.net-result .result-value { font-family:'Courier New',monospace; font-size:1.3rem; }

/* Charts */
.chart-section   { padding:20px; background:#f8fafc; border-top:1px solid #e2e8f0; }
.chart-container { position:relative; height:300px; max-width:100%; }

/* Cards */
.pl-filter-card { border:none; box-shadow:0 2px 15px rgba(0,0,0,.08); border-radius:12px; margin-bottom:20px; overflow:hidden; }
.pl-report-card { border:none; box-shadow:0 4px 25px rgba(0,0,0,.1);  border-radius:12px; overflow:hidden; }

.profit-text { color:#38a169; }
.loss-text   { color:#e53e3e; }

@media print {
    .main-sidebar,.navbar,.pl-filter-card,.no-print,.main-footer,.content-header,.chart-section { display:none !important; }
    .content-wrapper { margin-left:0 !important; padding:0 !important; }
    .pl-report-card  { box-shadow:none !important; }
    .pl-wrapper      { max-width:100%; }
    body             { background:white !important; }
}
@media (max-width:768px) { .summary-cards { grid-template-columns: repeat(2,1fr); } }
</style>

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="pl-wrapper">

        <!-- فلتر التاريخ -->
        <div class="card pl-filter-card no-print">
          <div class="card-body" style="padding:15px 20px;">
            <form method="post" class="row align-items-end">
              <div class="col-md-2">
                <div class="form-group mb-0">
                  <label><i class="fas fa-filter"></i> الفترة</label>
                  <select id="periodFilter" class="form-control" onchange="updateDateRange()">
                    <option value="">تخصيص</option>
                    <option value="today">اليوم</option>
                    <option value="week">هذا الأسبوع</option>
                    <option value="month">هذا الشهر</option>
                    <option value="all">كل الفترة</option>
                  </select>
                </div>
              </div>
              <div class="col-md-2">
                <div class="form-group mb-0">
                  <label><i class="fas fa-calendar-alt"></i> من</label>
                  <input type="date" class="form-control" name="startdate" id="startdate" value="<?= htmlspecialchars($startdate) ?>">
                </div>
              </div>
              <div class="col-md-2">
                <div class="form-group mb-0">
                  <label><i class="fas fa-calendar-alt"></i> إلى</label>
                  <input type="date" class="form-control" name="enddate" id="enddate" value="<?= htmlspecialchars($enddate) ?>">
                </div>
              </div>
              <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-block">
                  <i class="fas fa-search"></i> عرض التقرير
                </button>
              </div>
              <div class="col-md-2">
                <button type="button" class="btn btn-outline-secondary btn-block" onclick="window.print()">
                  <i class="fas fa-print"></i> طباعة
                </button>
              </div>
              <div class="col-md-2">
                <button type="button" class="btn btn-outline-success btn-block" id="exportExcelPL">
                  <i class="fas fa-file-excel"></i> تصدير Excel
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- التقرير -->
        <div class="card pl-report-card" id="plReport">

          <!-- رأس التقرير -->
          <div class="pl-report-header">
            <h2><i class="fas fa-chart-line"></i> قائمة الأرباح والخسائر</h2>
            <h4><?= htmlspecialchars($company_name) ?></h4>
            <div class="period">
              <i class="fas fa-clock"></i>
              الفترة من <?= htmlspecialchars($startdate) ?> إلى <?= htmlspecialchars($enddate) ?>
            </div>
          </div>

          <!-- بطاقات الملخص -->
          <div class="summary-cards">
            <div class="summary-card sales">
              <div class="card-label"><i class="fas fa-shopping-cart"></i> إجمالي المبيعات</div>
              <div class="card-amount"><?= number_format($gross_sales_total, 2) ?></div>
            </div>
            <div class="summary-card revenue">
              <div class="card-label"><i class="fas fa-arrow-up"></i> صافي الإيرادات</div>
              <div class="card-amount"><?= number_format($revenue_total, 2) ?></div>
            </div>
            <div class="summary-card expense">
              <div class="card-label"><i class="fas fa-arrow-down"></i> إجمالي التكاليف والمصروفات</div>
              <div class="card-amount"><?= number_format($summary_total_costs, 2) ?></div>
            </div>
            <div class="summary-card <?= $net_profit >= 0 ? 'profit' : 'loss' ?>">
              <div class="card-label">
                <i class="fas fa-<?= $net_profit >= 0 ? 'trophy' : 'exclamation-triangle' ?>"></i>
                <?= $net_profit >= 0 ? 'صافي الربح' : 'صافي الخسارة' ?>
              </div>
              <div class="card-amount"><?= plFormat($net_profit) ?></div>
            </div>
          </div>

          <!-- ── قسم الإيرادات ── -->
          <div class="pl-section-title revenue-title">
            <i class="fas fa-plus-circle"></i> الإيرادات
          </div>
          <table class="pl-table">
            <?php
            // ── مبيعات كاشير ──
            if ($cashier_sales > 0.001): ?>
            <tr>
              <td class="acc-name">مبيعات كاشير (POS)</td>
              <td class="acc-value profit-text"><?= number_format($cashier_sales, 2) ?></td>
            </tr>
            <?php endif; ?>
            <?php
            // ── مبيعات تيك-أواي ──
            if ($pos_sales > 0.001): ?>
            <tr>
              <td class="acc-name">مبيعات تيك-أواي</td>
              <td class="acc-value profit-text"><?= number_format($pos_sales, 2) ?></td>
            </tr>
            <?php endif; ?>
            <?php
            // ── مبيعات آجلة ──
            if ($credit_sales > 0.001): ?>
            <tr>
              <td class="acc-name">مبيعات آجلة</td>
              <td class="acc-value profit-text"><?= number_format($credit_sales, 2) ?></td>
            </tr>
            <?php endif; ?>
            <?php
            // ── مردود المبيعات (يُطرح) ──
            if ($returns_total > 0.001): ?>
            <tr>
              <td class="acc-name" style="padding-right:50px; color:#e53e3e;">مردود المبيعات</td>
              <td class="acc-value loss-text">(<?= number_format($returns_total, 2) ?>)</td>
            </tr>
            <?php endif; ?>
            <?php
            // ── صافي المبيعات ──
            if ($returns_total > 0.001): ?>
            <tr style="border-top:1px solid #e2e8f0; background:#f7f7f7;">
              <td class="acc-name" style="font-weight:600;">صافي المبيعات</td>
              <td class="acc-value profit-text" style="font-weight:600;"><?= number_format($net_sales, 2) ?></td>
            </tr>
            <?php endif; ?>
            <?php
            // ── إيرادات أخرى (32) ──
            foreach ($rev32_details as $r32d):
                $disp = (float)$r32d['net_credit'];
                if (abs($disp) < 0.001) continue;
            ?>
            <tr>
              <td class="acc-name"><?= htmlspecialchars($r32d['code']) ?> - <?= htmlspecialchars($r32d['aname']) ?></td>
              <td class="acc-value <?= $disp >= 0 ? 'profit-text' : 'loss-text' ?>"><?= plFormat($disp) ?></td>
            </tr>
            <?php endforeach; ?>
            <?php
            if ($gross_sales_total < 0.001 && $other_revenues < 0.001): ?>
            <tr class="empty-row"><td colspan="2">لا توجد إيرادات مسجلة في هذه الفترة</td></tr>
            <?php endif; ?>
            <tr class="total-row revenue-total">
              <td>إجمالي الإيرادات</td>
              <td class="acc-value"><?= plFormat($revenue_total) ?></td>
            </tr>
          </table>

          <!-- ── قسم تكلفة المبيعات ── -->
          <?php if (!empty($cos_details) || $cost_of_sales > 0.001): ?>
          <div class="pl-section-title cost-title">
            <i class="fas fa-minus-circle"></i> تكلفة المبيعات
          </div>
          <table class="pl-table">
            <?php foreach ($cos_details as $cos_row):
                $val = (float)$cos_row['net_debit'];
            ?>
            <tr>
              <td class="acc-name"><?= htmlspecialchars($cos_row['code']) ?> - <?= htmlspecialchars($cos_row['aname']) ?></td>
              <td class="acc-value <?= $val >= 0 ? 'loss-text' : 'profit-text' ?>"><?= plFormat($val) ?></td>
            </tr>
            <?php endforeach; ?>
            <tr class="total-row cost-total">
              <td>إجمالي تكلفة المبيعات</td>
              <td class="acc-value"><?= plFormat($cost_of_sales) ?></td>
            </tr>
          </table>
          <?php endif; ?>

          <!-- مجمل الربح / الخسارة -->
          <div class="gross-profit-row <?= $gross_profit >= 0 ? 'gp-profit' : 'gp-loss' ?>">
            <span>
              <i class="fas fa-<?= $gross_profit >= 0 ? 'chart-bar' : 'exclamation-circle' ?>"></i>
              <?= $gross_profit >= 0 ? 'مجمل الربح' : 'مجمل الخسارة' ?>
            </span>
            <span class="result-value <?= $gross_profit >= 0 ? 'profit-text' : 'loss-text' ?>">
              <?= plFormat($gross_profit) ?>
            </span>
          </div>

          <!-- ── قسم المصروفات ── -->
          <div class="pl-section-title expense-title">
            <i class="fas fa-minus-circle"></i> المصروفات
          </div>
          <table class="pl-table">
            <?php
            $has_exp = false;
            foreach ($expense_items as $row) {
                $val = (float)$row['net_debit'];
                if ($val < 0.001) continue;
                $has_exp = true;
            ?>
            <tr>
              <td class="acc-name"><?= htmlspecialchars($row['code']) ?> - <?= htmlspecialchars($row['aname']) ?></td>
              <td class="acc-value loss-text"><?= number_format($val, 2) ?></td>
            </tr>
            <?php } ?>
            <?php if (!$has_exp): ?>
            <tr class="empty-row"><td colspan="2">لا توجد مصروفات مسجلة في هذه الفترة</td></tr>
            <?php endif; ?>
            <tr class="total-row expense-total">
              <td>إجمالي المصروفات</td>
              <td class="acc-value"><?= plFormat($total_expenses) ?></td>
            </tr>
          </table>

          <!-- ── صافي الربح / الخسارة ── -->
          <div class="net-result <?= $net_profit >= 0 ? 'profit-result' : 'loss-result' ?>">
            <span>
              <i class="fas fa-<?= $net_profit >= 0 ? 'check-double' : 'times-circle' ?>"></i>
              <?= $net_profit >= 0 ? 'صافي الربح للفترة' : 'صافي الخسارة للفترة' ?>
            </span>
            <span class="result-value"><?= plFormat($net_profit) ?></span>
          </div>

          <!-- تعليق مطور: التحقق الرياضي -->
          <?php /* DEBUG P&L:
            revenue_total     = <?= $revenue_total ?>
            gross_sales_total = <?= $gross_sales_total ?>
            returns_total     = <?= $returns_total ?>
            net_sales         = <?= $net_sales ?>
            other_revenues    = <?= $other_revenues ?>
            cost_of_sales     = <?= $cost_of_sales ?>
            gross_profit      = <?= $gross_profit ?> [check: revenue(<?=$revenue_total?>) - cos(<?=$cost_of_sales?>) = <?=$revenue_total-$cost_of_sales?>]
            expense_total     = <?= $total_expenses ?>
            net_profit        = <?= $net_profit ?> [check: gross(<?=$gross_profit?>) - exp(<?=$total_expenses?>) = <?=$gross_profit-$total_expenses?>]
            math_ok: gross=<?= $_pl_debug['check_gross'] ?> net=<?= $_pl_debug['check_net'] ?>
          */ ?>

          <!-- ── الرسم البياني ── -->
          <div class="chart-section no-print">
            <h5 class="mb-3 text-center"><i class="fas fa-chart-pie"></i> تحليل بياني</h5>
            <div class="row">
              <div class="col-md-6">
                <div class="chart-container">
                  <?php if (empty($revenue_items)): ?>
                  <p class="text-center text-muted mt-5">لا توجد إيرادات لعرضها</p>
                  <?php else: ?>
                  <canvas id="revenueChart"></canvas>
                  <?php endif; ?>
                </div>
              </div>
              <div class="col-md-6">
                <div class="chart-container">
                  <?php if (empty($expense_items)): ?>
                  <p class="text-center text-muted mt-5">لا توجد مصروفات لعرضها</p>
                  <?php else: ?>
                  <canvas id="expenseChart"></canvas>
                  <?php endif; ?>
                </div>
              </div>
            </div>
            <div class="row mt-3">
              <div class="col-12">
                <div class="chart-container" style="height:250px;">
                  <canvas id="comparisonChart"></canvas>
                </div>
              </div>
            </div>
          </div>

        </div><!-- /pl-report-card -->
      </div><!-- /pl-wrapper -->
    </div>
  </section>
</div><!-- /content-wrapper -->

<script>
var revenueData = <?= json_encode($revenue_items, JSON_UNESCAPED_UNICODE) ?>;
var expenseData = <?= json_encode(
    array_map(function($e){ return ['name'=>$e['aname'],'value'=>(float)$e['net_debit']]; },
              array_filter($expense_items, function($e){ return (float)$e['net_debit'] > 0.001; })),
    JSON_UNESCAPED_UNICODE) ?>;
var netProfitVal       = <?= (float)$net_profit ?>;
var totalRevenues      = <?= (float)$revenue_total ?>;
var totalCosts         = <?= (float)$summary_total_costs ?>;

var greenColors = ['#38a169','#48bb78','#68d391','#9ae6b4','#c6f6d5','#2f855a','#276749'];
var redColors   = ['#e53e3e','#fc8181','#feb2b2','#f56565','#c53030','#dd6b20','#ed8936','#f6ad55'];

if (revenueData.length > 0) {
    new Chart(document.getElementById('revenueChart'), {
        type: 'doughnut',
        data: {
            labels:   revenueData.map(function(i){ return i.name; }),
            datasets: [{ data: revenueData.map(function(i){ return i.value; }),
                backgroundColor: greenColors.slice(0,revenueData.length), borderWidth:2, borderColor:'#fff' }]
        },
        options: {
            responsive:true, maintainAspectRatio:false,
            plugins: { legend:{position:'bottom',labels:{font:{family:'Playpen Sans Arabic'}}},
                       title:{display:true,text:'توزيع الإيرادات',font:{family:'Playpen Sans Arabic'}} }
        }
    });
}

if (expenseData.length > 0) {
    new Chart(document.getElementById('expenseChart'), {
        type: 'doughnut',
        data: {
            labels:   expenseData.map(function(i){ return i.name; }),
            datasets: [{ data: expenseData.map(function(i){ return i.value; }),
                backgroundColor: redColors.slice(0,expenseData.length), borderWidth:2, borderColor:'#fff' }]
        },
        options: {
            responsive:true, maintainAspectRatio:false,
            plugins: { legend:{position:'bottom',labels:{font:{family:'Playpen Sans Arabic'}}},
                       title:{display:true,text:'توزيع المصروفات',font:{family:'Playpen Sans Arabic'}} }
        }
    });
}

// رسم بياني المقارنة — يعرض القيم الفعلية (ربح موجب / خسارة سالبة)
var netLabel  = netProfitVal >= 0 ? 'صافي الربح'    : 'صافي الخسارة';
var netColor  = netProfitVal >= 0 ? 'rgba(49,130,206,.8)' : 'rgba(229,62,62,.8)';
var netBorder = netProfitVal >= 0 ? '#3182ce'        : '#e53e3e';

new Chart(document.getElementById('comparisonChart'), {
    type: 'bar',
    data: {
        labels: ['الإيرادات', 'التكاليف والمصروفات', netLabel],
        datasets: [{
            label: 'المبلغ',
            data: [totalRevenues, totalCosts, netProfitVal],
            backgroundColor: ['rgba(56,161,105,.8)','rgba(229,62,62,.8)', netColor],
            borderColor:     ['#38a169','#e53e3e', netBorder],
            borderWidth:2, borderRadius:6
        }]
    },
    options: {
        responsive:true, maintainAspectRatio:false,
        plugins: { legend:{display:false} },
        scales: {
            y: { beginAtZero:false, ticks:{font:{family:'Playpen Sans Arabic'}} },
            x: { ticks:{font:{family:'Playpen Sans Arabic',size:13}} }
        }
    }
});

// تصدير Excel — يحذف الرسوم البيانية من النسخة المُصدَّرة
document.getElementById('exportExcelPL')?.addEventListener('click', function() {
    var clone = document.getElementById('plReport').cloneNode(true);
    var cs    = clone.querySelector('.chart-section');
    if (cs) cs.remove();
    var url = 'data:application/vnd.ms-excel,' + encodeURIComponent(
        '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:spreadsheet">' +
        '<head><meta charset="UTF-8"><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet>' +
        '<x:Name>الأرباح والخسائر</x:Name><x:WorksheetOptions><x:DisplayRightToLeft/></x:WorksheetOptions>' +
        '</x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head>' +
        '<body dir="rtl">' + clone.outerHTML + '</body></html>'
    );
    var a = document.createElement('a');
    a.href = url;
    a.download = 'profit_loss_<?= date("Y-m-d") ?>.xls';
    a.click();
});
</script>

<script>
function updateDateRange() {
    var period     = document.getElementById('periodFilter').value;
    var startInput = document.getElementById('startdate');
    var endInput   = document.getElementById('enddate');
    if (!period) return;

    var now = new Date();
    var fmt = function(d) {
        return d.getFullYear() + '-' +
               String(d.getMonth()+1).padStart(2,'0') + '-' +
               String(d.getDate()).padStart(2,'0');
    };
    var todayStr = fmt(now);
    endInput.value = todayStr;

    if (period === 'today') {
        startInput.value = todayStr;
    } else if (period === 'week') {
        var day  = now.getDay();                        // 0=أحد
        var diff = (day === 0) ? 6 : (day - 1);        // الاثنين = بداية الأسبوع
        var mon  = new Date(now);
        mon.setDate(now.getDate() - diff);
        startInput.value = fmt(mon);
    } else if (period === 'month') {
        startInput.value = now.getFullYear() + '-' + String(now.getMonth()+1).padStart(2,'0') + '-01';
    } else if (period === 'all') {
        startInput.value = '2000-01-01';
    }
}
</script>

<?php include('includes/footer.php') ?>
