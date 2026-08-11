<?php 
include('includes/header.php');
include('includes/navbar.php');
include('includes/sidebar.php');

// ──────────────────────────────────────────────────────────────
// تأمين جلسة CSRF
// ──────────────────────────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('طلب غير مصرح به (Invalid CSRF Token)');
    }
}

// ──────────────────────────────────────────────────────────────
// تحديد الفترة الزمنية وتحسين الاستعلامات بعدم استخدام DATE()
// ──────────────────────────────────────────────────────────────
$startdate = (isset($_POST['startdate']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_POST['startdate']))
    ? $_POST['startdate'] : date('Y-01-01');
$enddate = (isset($_POST['enddate']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_POST['enddate']))
    ? $_POST['enddate'] : date('Y-m-d');

// تاريخ النهاية + يوم واحد لمعالجة حقول DATETIME بكفاءة وبدون إبطاء الـ Index
$enddate_plus1 = date('Y-m-d', strtotime($enddate . ' +1 day'));

// ──────────────────────────────────────────────────────────────
// دالة مساعدة: تنسيق محاسبي محسن
// ──────────────────────────────────────────────────────────────
function plFormat(float $value, bool $showSign = false): string {
    if ($value < -0.001) {
        return '<span class="loss-text">(' . number_format(abs($value), 2) . ')</span>';
    }
    $formatted = number_format(abs($value), 2);
    return ($showSign && $value > 0.001)
        ? '<span class="profit-text">' . $formatted . '</span>'
        : $formatted;
}

// ── جلب اسم الشركة بأمان ────────────────────────────────────
$company_name = $rowstg['company_name'] ?? 'الشركة';

// ── 1. مبيعات ot_head ──────────────────────────────────────
$stmt_sales = $conn->prepare("
    SELECT
        COALESCE(SUM(CASE WHEN oh.pro_tybe IN (9)                   THEN oh.fat_net ELSE 0 END), 0) AS cashier_sales,
        COALESCE(SUM(CASE WHEN oh.pro_tybe = 3 AND oh.journal_tybe = 3 THEN oh.fat_net ELSE 0 END), 0) AS pos_sales,
        COALESCE(SUM(CASE WHEN oh.pro_tybe IN (10)                  THEN oh.fat_net ELSE 0 END), 0) AS credit_sales,
        COALESCE(SUM(CASE WHEN oh.pro_tybe IN (11)                  THEN oh.fat_net ELSE 0 END), 0) AS returns_total
    FROM ot_head oh
    WHERE oh.isdeleted = 0
      AND oh.pro_date >= ? AND oh.pro_date < ?
");
$stmt_sales->bind_param("ss", $startdate, $enddate_plus1);
$stmt_sales->execute();
$row_sales = $stmt_sales->get_result()->fetch_assoc();
$stmt_sales->close();

$cashier_sales     = (float)$row_sales['cashier_sales'];
$pos_sales         = (float)$row_sales['pos_sales'];
$credit_sales      = (float)$row_sales['credit_sales'];
$returns_total     = (float)$row_sales['returns_total'];

$gross_sales_total = (float)bcadd((string)$cashier_sales, (string)bcadd((string)$pos_sales, (string)$credit_sales, 4), 4);
$net_sales         = (float)bcsub((string)$gross_sales_total, (string)$returns_total, 4);

// ── 2. إيرادات أخرى (32%) من journal_entries ──────────────
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
      AND je.crtime >= ? AND je.crtime < ?
");
$stmt_rev32->bind_param("ss", $startdate, $enddate_plus1);
$stmt_rev32->execute();
$rev32_row     = $stmt_rev32->get_result()->fetch_assoc();
$stmt_rev32->close();
$other_revenues = (float)bcsub((string)$rev32_row['total_credit'], (string)$rev32_row['total_debit'], 4);
if ($other_revenues < 0) $other_revenues = 0.0;

// ── 3. تفاصيل الإيرادات الأخرى (32%) للعرض ──────────────
$stmt_rev32_detail = $conn->prepare("
    SELECT ah.id, ah.code, ah.aname,
        COALESCE(SUM(je.credit), 0) - COALESCE(SUM(je.debit), 0) AS net_credit
    FROM acc_head ah
    LEFT JOIN journal_entries je ON je.account_id = ah.id
        AND je.isdeleted = 0
        AND je.crtime >= ? AND je.crtime < ?
    WHERE ah.code LIKE '32%'
      AND ah.is_basic = 0
      AND ah.isdeleted = 0
    GROUP BY ah.id, ah.code, ah.aname
    HAVING ABS(net_credit) > 0.001
    ORDER BY ah.code
");
$stmt_rev32_detail->bind_param("ss", $startdate, $enddate_plus1);
$stmt_rev32_detail->execute();
$rev32_details = $stmt_rev32_detail->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt_rev32_detail->close();

// ── 4. إجمالي الإيرادات ─────────────────────────────────
$revenue_total = (float)bcadd((string)$net_sales, (string)$other_revenues, 4);

// ── 5. بيان المشتريات من ot_head (للعرض الاستسترشادي فقط) ───
$stmt_purch = $conn->prepare("
    SELECT
        COALESCE(SUM(CASE WHEN oh.pro_tybe IN (1,4) THEN oh.fat_net ELSE 0 END), 0) AS gross_purchases,
        COALESCE(SUM(CASE WHEN oh.pro_tybe IN (2)   THEN oh.fat_net ELSE 0 END), 0) AS purchase_returns
    FROM ot_head oh
    WHERE oh.isdeleted = 0
      AND oh.pro_date >= ? AND oh.pro_date < ?
");
$stmt_purch->bind_param("ss", $startdate, $enddate_plus1);
$stmt_purch->execute();
$row_purch = $stmt_purch->get_result()->fetch_assoc();
$stmt_purch->close();

$gross_purchases    = (float)$row_purch['gross_purchases'];
$purchase_returns   = (float)$row_purch['purchase_returns'];
$net_purchases      = (float)bcsub((string)$gross_purchases, (string)$purchase_returns, 4);

$stmt_purch_det = $conn->prepare("
    SELECT oh.pro_tybe,
           COALESCE(SUM(oh.fat_net), 0) AS total
    FROM ot_head oh
    WHERE oh.isdeleted = 0
      AND oh.pro_tybe IN (1, 4, 2)
      AND oh.pro_date >= ? AND oh.pro_date < ?
    GROUP BY oh.pro_tybe
");
$stmt_purch_det->bind_param("ss", $startdate, $enddate_plus1);
$stmt_purch_det->execute();
$purch_by_type = [];
$res_pd = $stmt_purch_det->get_result();
while ($pd = $res_pd->fetch_assoc()) {
    $purch_by_type[$pd['pro_tybe']] = (float)$pd['total'];
}
$stmt_purch_det->close();

// ── 6a. تكلفة البضاعة المباعة - COGS (42%) من journal_entries ──
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
      AND je.crtime >= ? AND je.crtime < ?
");
$stmt_cos->bind_param("ss", $startdate, $enddate_plus1);
$stmt_cos->execute();
$cos_row       = $stmt_cos->get_result()->fetch_assoc();
$stmt_cos->close();
$cost_of_sales = (float)bcsub((string)$cos_row['total_debit'], (string)$cos_row['total_credit'], 4);
if ($cost_of_sales < 0) $cost_of_sales = 0.0;

// ── 6b. تفاصيل تكلفة البضاعة المباعة (42%) للعرض ──────────────
$stmt_cos_detail = $conn->prepare("
    SELECT ah.id, ah.code, ah.aname,
        COALESCE(SUM(je.debit), 0) - COALESCE(SUM(je.credit), 0) AS net_debit
    FROM acc_head ah
    LEFT JOIN journal_entries je ON je.account_id = ah.id
        AND je.isdeleted = 0
        AND je.crtime >= ? AND je.crtime < ?
    WHERE ah.code LIKE '42%'
      AND ah.is_basic = 0
      AND ah.isdeleted = 0
    GROUP BY ah.id, ah.code, ah.aname
    HAVING ABS(net_debit) > 0.001
    ORDER BY ah.code
");
$stmt_cos_detail->bind_param("ss", $startdate, $enddate_plus1);
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
      AND je.crtime >= ? AND je.crtime < ?
");
$stmt_exp->bind_param("ss", $startdate, $enddate_plus1);
$stmt_exp->execute();
$exp_row        = $stmt_exp->get_result()->fetch_assoc();
$stmt_exp->close();
$total_expenses = (float)bcsub((string)$exp_row['total_debit'], (string)$exp_row['total_credit'], 4);
if ($total_expenses < 0) $total_expenses = 0.0;

// ── 8. تفاصيل المصروفات (44%) للعرض ─────────────────────
$stmt_exp_detail = $conn->prepare("
    SELECT ah.id, ah.code, ah.aname,
        COALESCE(SUM(je.debit), 0) - COALESCE(SUM(je.credit), 0) AS net_debit
    FROM acc_head ah
    LEFT JOIN journal_entries je ON je.account_id = ah.id
        AND je.isdeleted = 0
        AND je.crtime >= ? AND je.crtime < ?
    WHERE ah.code LIKE '44%'
      AND ah.is_basic = 0
      AND ah.isdeleted = 0
    GROUP BY ah.id, ah.code, ah.aname
    HAVING net_debit > 0.001
    ORDER BY ah.code
");
$stmt_exp_detail->bind_param("ss", $startdate, $enddate_plus1);
$stmt_exp_detail->execute();
$expense_items = $stmt_exp_detail->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt_exp_detail->close();

// ── 9. مجمل الربح / صافي الربح (حسابات محاسبية دقيقة) ─────
// معالجة مصدر التكلفة لمنع الخصم المزدوج (Double Counting)
// إذا توفرت حسابات 42% نعتمدها كـ COGS، وإلا نستخدم صافي المشتريات كبديل
$effective_cogs = ($cost_of_sales > 0.001) ? $cost_of_sales : $net_purchases;

$gross_profit = (float)bcsub((string)$revenue_total, (string)$effective_cogs, 4);
$net_profit   = (float)bcsub((string)$gross_profit, (string)$total_expenses, 4);

// ── إجمالي التكاليف والمصروفات للبطاقة الملخصة ─────────────────────
$summary_total_costs = (float)bcadd((string)$effective_cogs, (string)$total_expenses, 4);

// ── بيانات الرسم البياني للإيرادات ───────────────────────
$revenue_items = [];
if ($cashier_sales > 0.001) $revenue_items[] = ['name' => 'مبيعات كاشير', 'value' => $cashier_sales];
if ($pos_sales     > 0.001) $revenue_items[] = ['name' => 'مبيعات تيك-أواي', 'value' => $pos_sales];
if ($credit_sales  > 0.001) $revenue_items[] = ['name' => 'مبيعات آجلة', 'value' => $credit_sales];

foreach ($rev32_details as $r32d) {
    if ($r32d['net_credit'] > 0.001) {
        $revenue_items[] = ['name' => $r32d['aname'], 'value' => (float)$r32d['net_credit']];
    }
}
?>

<style>
.pl-wrapper { max-width: 1000px; margin: 0 auto; }
.pl-report-header {
    background: linear-gradient(135deg, #1a365d 0%, #2d3748 50%, #4a5568 100%);
    color: white; padding: 30px; border-radius: 12px 12px 0 0;
    text-align: center; position: relative; overflow: hidden;
}
.pl-report-header h2  { font-size:1.8rem; margin-bottom:5px; position:relative; z-index:1; }
.pl-report-header h4  { font-size:1.1rem; opacity:.9; position:relative; z-index:1; }
.pl-report-header .period { font-size:.9rem; opacity:.8; margin-top:10px; position:relative; z-index:1; }

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

.pl-section-title {
    padding:12px 20px; font-weight:bold; font-size:1.05rem; margin:0;
    display:flex; align-items:center; gap:10px;
}
.pl-section-title.revenue-title { background:linear-gradient(135deg,#f0fff4,#c6f6d5); color:#22543d; border-right:4px solid #38a169; }
.pl-section-title.expense-title { background:linear-gradient(135deg,#fff5f5,#fed7d7); color:#742a2a; border-right:4px solid #e53e3e; }
.pl-section-title.cost-title    { background:linear-gradient(135deg,#fffaf0,#feebc8); color:#744210; border-right:4px solid #dd6b20; }

.pl-table { width:100%; border-collapse:collapse; }
.pl-table td { padding:10px 20px; border-bottom:1px solid #f0f0f0; font-size:.95rem; }
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

.gross-profit-row {
    padding:14px 20px; display:flex; justify-content:space-between; align-items:center;
    font-weight:bold; font-size:1.05rem; border-top:2px solid; border-bottom:2px solid;
}
.gross-profit-row.gp-profit { background:linear-gradient(135deg,#ebf8ff,#bee3f8); color:#2a4365; border-color:#90cdf4; }
.gross-profit-row.gp-loss   { background:linear-gradient(135deg,#fff5f5,#fed7d7); color:#742a2a; border-color:#fc8181; }
.gross-profit-row .result-value { font-family:'Courier New',monospace; font-size:1.1rem; }

.net-result {
    padding:18px 20px; display:flex; justify-content:space-between;
    align-items:center; font-weight:bold; font-size:1.15rem;
}
.net-result.profit-result { background:linear-gradient(135deg,#c6f6d5,#9ae6b4); color:#22543d; border-top:3px double #38a169; }
.net-result.loss-result   { background:linear-gradient(135deg,#fed7d7,#feb2b2); color:#742a2a; border-top:3px double #e53e3e; }
.net-result .result-value { font-family:'Courier New',monospace; font-size:1.3rem; }

.chart-section   { padding:20px; background:#f8fafc; border-top:1px solid #e2e8f0; }
.chart-container { position:relative; height:300px; max-width:100%; }

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

        <!-- فلتر التاريخ مع حماية CSRF -->
        <div class="card pl-filter-card no-print">
          <div class="card-body" style="padding:15px 20px;">
            <form method="post" class="row align-items-end">
              <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
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
              <div class="card-amount"><?= plFormat($net_profit, true) ?></div>
            </div>
          </div>

          <!-- ── قسم الإيرادات ── -->
          <div class="pl-section-title revenue-title">
            <i class="fas fa-plus-circle"></i> الإيرادات
          </div>
          <table class="pl-table">
            <?php if ($cashier_sales > 0.001): ?>
            <tr>
              <td class="acc-name">مبيعات كاشير (POS)</td>
              <td class="acc-value profit-text"><?= number_format($cashier_sales, 2) ?></td>
            </tr>
            <?php endif; ?>
            <?php if ($pos_sales > 0.001): ?>
            <tr>
              <td class="acc-name">مبيعات تيك-أواي</td>
              <td class="acc-value profit-text"><?= number_format($pos_sales, 2) ?></td>
            </tr>
            <?php endif; ?>
            <?php if ($credit_sales > 0.001): ?>
            <tr>
              <td class="acc-name">مبيعات آجلة</td>
              <td class="acc-value profit-text"><?= number_format($credit_sales, 2) ?></td>
            </tr>
            <?php endif; ?>
            <?php if ($returns_total > 0.001): ?>
            <tr>
              <td class="acc-name" style="padding-right:50px; color:#e53e3e;">مردود المبيعات</td>
              <td class="acc-value loss-text">(<?= number_format($returns_total, 2) ?>)</td>
            </tr>
            <tr style="border-top:1px solid #e2e8f0; background:#f7f7f7;">
              <td class="acc-name" style="font-weight:600;">صافي المبيعات</td>
              <td class="acc-value profit-text" style="font-weight:600;"><?= number_format($net_sales, 2) ?></td>
            </tr>
            <?php endif; ?>

            <?php
            foreach ($rev32_details as $r32d):
                $disp = (float)$r32d['net_credit'];
                if (abs($disp) < 0.001) continue;
            ?>
            <tr>
              <td class="acc-name"><?= htmlspecialchars($r32d['code']) ?> - <?= htmlspecialchars($r32d['aname']) ?></td>
              <td class="acc-value <?= $disp >= 0 ? 'profit-text' : 'loss-text' ?>"><?= plFormat($disp) ?></td>
            </tr>
            <?php endforeach; ?>

            <?php if ($gross_sales_total < 0.001 && $other_revenues < 0.001): ?>
            <tr class="empty-row"><td colspan="2">لا توجد إيرادات مسجلة في هذه الفترة</td></tr>
            <?php endif; ?>
            <tr class="total-row revenue-total">
              <td>إجمالي الإيرادات</td>
              <td class="acc-value"><?= plFormat($revenue_total) ?></td>
            </tr>
          </table>

          <!-- ── قسم تكلفة البضاعة المباعة (COGS) ── -->
          <div class="pl-section-title cost-title">
            <i class="fas fa-minus-circle"></i> تكلفة البضاعة المباعة (COGS)
          </div>
          <table class="pl-table">
            <?php
            // عرض تفاصيل قيود تكلفة المبيعات المباشرة (حسابات 42%)
            foreach ($cos_details as $cos_row):
                $val = (float)$cos_row['net_debit'];
            ?>
            <tr>
              <td class="acc-name"><?= htmlspecialchars($cos_row['code']) ?> - <?= htmlspecialchars($cos_row['aname']) ?></td>
              <td class="acc-value loss-text"><?= plFormat($val) ?></td>
            </tr>
            <?php endforeach; ?>

            <?php
            // عرض المشتريات كبيان استسترشادي أو كبديل في حال عدم وجود قيود 42%
            $purch1 = $purch_by_type[1] ?? 0.0;
            $purch4 = $purch_by_type[4] ?? 0.0;
            $ret2   = $purch_by_type[2] ?? 0.0;
            ?>
            <?php if ($purch1 > 0.001): ?>
            <tr>
              <td class="acc-name">مشتريات نقدية (بيان استسترشادي)</td>
              <td class="acc-value loss-text"><?= number_format($purch1, 2) ?></td>
            </tr>
            <?php endif; ?>
            <?php if ($purch4 > 0.001): ?>
            <tr>
              <td class="acc-name">مشتريات آجلة (بيان استسترشادي)</td>
              <td class="acc-value loss-text"><?= number_format($purch4, 2) ?></td>
            </tr>
            <?php endif; ?>
            <?php if ($ret2 > 0.001): ?>
            <tr>
              <td class="acc-name" style="padding-right:50px; color:#38a169;">مردود المشتريات</td>
              <td class="acc-value profit-text">(<?= number_format($ret2, 2) ?>)</td>
            </tr>
            <?php endif; ?>

            <?php if (empty($cos_details) && $net_purchases < 0.001): ?>
            <tr class="empty-row"><td colspan="2">لا توجد تكاليف أو مشتريات مسجلة في هذه الفترة</td></tr>
            <?php endif; ?>

            <tr class="total-row cost-total">
              <td>إجمالي تكلفة البضاعة المباعة (COGS)</td>
              <td class="acc-value"><?= plFormat($effective_cogs) ?></td>
            </tr>
          </table>

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

var greenColors = ['#38a169','#48bb78','#68d391','#9ae6b4','#c6f6d5','#2f855a','#276749'];
var redColors   = ['#e53e3e','#fc8181','#feb2b2','#f56565','#c53030','#dd6b20','#ed8936','#f6ad55'];

if (revenueData.length > 0 && document.getElementById('revenueChart')) {
    new Chart(document.getElementById('revenueChart'), {
        type: 'doughnut',
        data: {
            labels: revenueData.map(function(i){ return i.name; }),
            datasets: [{ 
                data: revenueData.map(function(i){ return i.value; }),
                backgroundColor: greenColors.slice(0, revenueData.length), 
                borderWidth: 2, 
                borderColor: '#fff' 
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' },
                title: { display: true, text: 'توزيع الإيرادات' }
            }
        }
    });
}

if (expenseData.length > 0 && document.getElementById('expenseChart')) {
    new Chart(document.getElementById('expenseChart'), {
        type: 'doughnut',
        data: {
            labels: expenseData.map(function(i){ return i.name; }),
            datasets: [{ 
                data: expenseData.map(function(i){ return i.value; }),
                backgroundColor: redColors.slice(0, expenseData.length), 
                borderWidth: 2, 
                borderColor: '#fff' 
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' },
                title: { display: true, text: 'توزيع المصروفات' }
            }
        }
    });
}
</script>

<?php include('includes/footer.php'); ?>