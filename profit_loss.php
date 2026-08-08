<?php include('includes/header.php') ?>
<?php include('includes/navbar.php') ?>
<?php include('includes/sidebar.php') ?>
<?php
// ──────────────────────────────────────────────────────────────
// تحديد الفترة الزمنية مع تحقق من الصيغة
// ──────────────────────────────────────────────────────────────
$startdate = (isset($_POST['startdate']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_POST['startdate']))
    ? $_POST['startdate'] : date('Y-01-01');
$enddate = (isset($_POST['enddate']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_POST['enddate']))
    ? $_POST['enddate'] : date('Y-m-d');

// ──────────────────────────────────────────────────────────────
// دالة: إجمالي debit/credit لكل حسابات مجموعة (شجرة كاملة)
// تجمع فقط القيود على الحسابات الطرفية (is_basic=0)
// لتجنب double counting مع الحسابات الأب
// ──────────────────────────────────────────────────────────────
function getPLGroupBalance($conn, $group_code, $startdate, $enddate) {
    $sql = "SELECT
                COALESCE(SUM(je.debit),  0) AS total_debit,
                COALESCE(SUM(je.credit), 0) AS total_credit
            FROM journal_entries je
            INNER JOIN acc_head ah ON je.account_id = ah.id
            WHERE ah.code LIKE ?
              AND ah.is_basic = 0
              AND ah.isdeleted = 0
              AND je.isdeleted = 0
              AND DATE(je.crtime) BETWEEN ? AND ?";
    $like = $group_code . '%';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $like, $startdate, $enddate);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $row;
}

// ──────────────────────────────────────────────────────────────
// دالة: الحسابات الطرفية (is_basic=0) مع رصيدها الصافي
// net_balance = debit - credit  (القيمة الخام، التفسير يعتمد على الطبيعة)
// تُرجع فقط الحسابات الطرفية لتجنب double counting
// ──────────────────────────────────────────────────────────────
function getPLLeafAccounts($conn, $group_code, $startdate, $enddate) {
    $sql = "SELECT ah.id, ah.code, ah.aname,
                COALESCE((
                    SELECT SUM(je.debit) - SUM(je.credit)
                    FROM journal_entries je
                    WHERE je.account_id = ah.id
                      AND je.isdeleted = 0
                      AND DATE(je.crtime) BETWEEN ? AND ?
                ), 0) AS net_balance
            FROM acc_head ah
            WHERE ah.code LIKE ?
              AND ah.is_basic = 0
              AND ah.isdeleted = 0
            ORDER BY ah.code";
    $like = $group_code . '%';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $startdate, $enddate, $like);
    $stmt->execute();
    return $stmt->get_result();
}

// ──────────────────────────────────────────────────────────────
// دالة مساعدة: تنسيق محاسبي
//   موجب  →  "1,234.56"
//   سالب  →  "(1,234.56)"
// ──────────────────────────────────────────────────────────────
function plFormat($value) {
    if ($value < -0.001) {
        return '(' . number_format(abs($value), 2) . ')';
    }
    return number_format(abs($value), 2);
}

// ══════════════════════════════════════════════════════════════
// حساب أرقام قائمة الأرباح والخسائر
// ══════════════════════════════════════════════════════════════

// 1. الإيرادات — مصدران:
//    أ) حسابات الإيرادات المحاسبية (32) — دائنة الطبيعة
//    ب) إيرادات المبيعات من ot_head (مبيعات + كاشير) مطروحاً منها مردودات المبيعات
//       تُستخدم كاحتياطي للفواتير القديمة المسجلة في غير 32

// أ) إيرادات حسابات 32
$rev_data       = getPLGroupBalance($conn, '32', $startdate, $enddate);
$rev_from_32    = $rev_data['total_credit'] - $rev_data['total_debit'];

// ب) إيرادات المبيعات من ot_head (3=مبيعات، 9=كاشير) مطروحاً منها (11=مردود مبيعات)
$stmt_sales = $conn->prepare(
    "SELECT
         COALESCE(SUM(CASE WHEN oh.pro_tybe IN (3,9) THEN oh.fat_net ELSE 0 END), 0) AS sales_total,
         COALESCE(SUM(CASE WHEN oh.pro_tybe IN (11)  THEN oh.fat_net ELSE 0 END), 0) AS returns_total
     FROM ot_head oh
     WHERE oh.isdeleted = 0
       AND DATE(oh.pro_date) BETWEEN ? AND ?"
);
$stmt_sales->bind_param("ss", $startdate, $enddate);
$stmt_sales->execute();
$row_sales         = $stmt_sales->get_result()->fetch_assoc();
$stmt_sales->close();
$sales_net_ot      = $row_sales['sales_total'] - $row_sales['returns_total'];

// الإيراد الكلي = الأعلى بين المصدرين (لتجنب الازدواج إذا تم تصحيح الحسابات)
// إذا كانت 32 تحتوي على المبيعات كاملة نستخدمها، وإلا نستخدم ot_head
$total_revenues = max($rev_from_32, $sales_net_ot);

// 2. تكلفة المبيعات (41) — طبيعتها مدينة
//    صافي التكلفة = debit - credit  → موجب = تكلفة فعلية
$cost_data           = getPLGroupBalance($conn, '41', $startdate, $enddate);
$total_cost_of_sales = $cost_data['total_debit'] - $cost_data['total_credit'];
// إذا كانت سالبة (مردودات/خصومات تفوق المشتريات) → صفر
if ($total_cost_of_sales < 0) { $total_cost_of_sales = 0.0; }

// 3. المصروفات (44) — طبيعتها مدينة
//    صافي المصروفات = debit - credit  → موجب = مصروف فعلي
$exp_data       = getPLGroupBalance($conn, '44', $startdate, $enddate);
$total_expenses = $exp_data['total_debit'] - $exp_data['total_credit'];
if ($total_expenses < 0) { $total_expenses = 0.0; }

// 4. مجمل الربح / الخسارة
//    موجب = مجمل ربح | سالب = مجمل خسارة
$gross_profit = $total_revenues - $total_cost_of_sales;

// 5. صافي الربح / الخسارة
//    موجب = صافي ربح | سالب = صافي خسارة
$net_profit = $gross_profit - $total_expenses;

// اسم الشركة
$company_name = $rowstg['storename'] ?? 'الشركة';

// ── بيانات الرسوم البيانية ──
// الإيرادات: طبيعة دائنة → net_balance*-1 للحصول على قيمة موجبة
$revenue_items = [];
$rev_chart = getPLLeafAccounts($conn, '32', $startdate, $enddate);
while ($r = $rev_chart->fetch_assoc()) {
    $val = $r['net_balance'] * -1;  // عكس الإشارة: دائن = موجب
    if ($val > 0.001) {
        $revenue_items[] = ['name' => $r['aname'], 'value' => $val];
    }
}

// المصروفات: طبيعة مدينة → net_balance موجب مباشرة
$expense_items = [];
$exp_chart = getPLLeafAccounts($conn, '44', $startdate, $enddate);
while ($r = $exp_chart->fetch_assoc()) {
    if ($r['net_balance'] > 0.001) {
        $expense_items[] = ['name' => $r['aname'], 'value' => $r['net_balance']];
    }
}

// إجمالي التكاليف للبطاقة الملخصة
$summary_total_costs = $total_cost_of_sales + $total_expenses;
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
              <div class="card-amount"><?= number_format($row_sales['sales_total'], 2) ?></div>
            </div>
            <div class="summary-card revenue">
              <div class="card-label"><i class="fas fa-arrow-up"></i> إجمالي الإيرادات</div>
              <div class="card-amount"><?= number_format($total_revenues, 2) ?></div>
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
            $rev_rows = getPLLeafAccounts($conn, '32', $startdate, $enddate);
            $has_rev  = false;
            while ($row = $rev_rows->fetch_assoc()) {
                // الإيرادات دائنة: net_balance = debit-credit → نعكس للعرض
                $display = $row['net_balance'] * -1;
                if (abs($display) < 0.001) continue;
                $has_rev = true;
            ?>
            <tr>
              <td class="acc-name"><?= htmlspecialchars($row['code']) ?> - <?= htmlspecialchars($row['aname']) ?></td>
              <td class="acc-value <?= $display >= 0 ? 'profit-text' : 'loss-text' ?>"><?= plFormat($display) ?></td>
            </tr>
            <?php } ?>
            <?php
            // إذا لم تكن هناك إيرادات في 32، أظهر المبيعات من ot_head كبند منفصل
            if (!$has_rev && $sales_net_ot > 0.001):
            ?>
            <tr>
              <td class="acc-name">إيرادات المبيعات</td>
              <td class="acc-value profit-text"><?= number_format($sales_net_ot, 2) ?></td>
            </tr>
            <?php
                $has_rev = true;
            endif;
            ?>
            <?php if (!$has_rev): ?>
            <tr class="empty-row"><td colspan="2">لا توجد إيرادات مسجلة في هذه الفترة</td></tr>
            <?php endif; ?>
            <tr class="total-row revenue-total">
              <td>إجمالي الإيرادات</td>
              <td class="acc-value"><?= plFormat($total_revenues) ?></td>
            </tr>
          </table>

          <!-- ── قسم تكلفة المبيعات ── -->
          <?php
          // جلب الحسابات الطرفية لـ 41 ذات رصيد
          $cost_rows_res  = getPLLeafAccounts($conn, '41', $startdate, $enddate);
          $cost_rows_data = [];
          while ($cr = $cost_rows_res->fetch_assoc()) {
              if (abs($cr['net_balance']) > 0.001) { $cost_rows_data[] = $cr; }
          }
          if (!empty($cost_rows_data)):
          ?>
          <div class="pl-section-title cost-title">
            <i class="fas fa-minus-circle"></i> تكلفة المبيعات
          </div>
          <table class="pl-table">
            <?php foreach ($cost_rows_data as $row):
                // تكلفة مدينة: موجب = تكلفة فعلية (أحمر) | سالب = خصم/مردود (أخضر)
            ?>
            <tr>
              <td class="acc-name"><?= htmlspecialchars($row['code']) ?> - <?= htmlspecialchars($row['aname']) ?></td>
              <td class="acc-value <?= $row['net_balance'] >= 0 ? 'loss-text' : 'profit-text' ?>">
                <?= plFormat($row['net_balance']) ?>
              </td>
            </tr>
            <?php endforeach; ?>
            <tr class="total-row cost-total">
              <td>إجمالي تكلفة المبيعات</td>
              <td class="acc-value"><?= plFormat($total_cost_of_sales) ?></td>
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
          <?php endif; ?>

          <!-- ── قسم المصروفات ── -->
          <div class="pl-section-title expense-title">
            <i class="fas fa-minus-circle"></i> المصروفات
          </div>
          <table class="pl-table">
            <?php
            $exp_rows = getPLLeafAccounts($conn, '44', $startdate, $enddate);
            $has_exp  = false;
            while ($row = $exp_rows->fetch_assoc()) {
                // المصروفات مدينة: net_balance موجب = مصروف فعلي
                if ($row['net_balance'] < 0.001) continue;
                $has_exp = true;
            ?>
            <tr>
              <td class="acc-name"><?= htmlspecialchars($row['code']) ?> - <?= htmlspecialchars($row['aname']) ?></td>
              <td class="acc-value loss-text"><?= number_format($row['net_balance'], 2) ?></td>
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
var expenseData = <?= json_encode($expense_items, JSON_UNESCAPED_UNICODE) ?>;
var netProfitVal       = <?= (float)$net_profit ?>;
var totalRevenues      = <?= (float)$total_revenues ?>;
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
