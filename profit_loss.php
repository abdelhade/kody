<?php include('includes/header.php') ?>
<?php include('includes/navbar.php') ?>
<?php include('includes/sidebar.php') ?>

<?php
// تحديد الفترة الزمنية
$startdate = isset($_POST['startdate']) ? $_POST['startdate'] : date('Y-01-01');
$enddate = isset($_POST['enddate']) ? $_POST['enddate'] : date('Y-m-d');

// دالة لحساب رصيد حساب رئيسي (مجموع الحسابات الفرعية)
function getPLAccountBalance($conn, $parent_code, $startdate, $enddate) {
    $sql = "SELECT COALESCE(SUM(je.debit), 0) as total_debit, COALESCE(SUM(je.credit), 0) as total_credit 
            FROM journal_entries je
            INNER JOIN acc_head ah ON je.account_id = ah.id
            WHERE ah.code LIKE ? AND ah.isdeleted = 0 AND je.isdeleted = 0
            AND DATE(je.crtime) BETWEEN ? AND ?";
    $like_code = $parent_code . '%';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $like_code, $startdate, $enddate);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result;
}

// دالة لجلب الحسابات الفرعية مع أرصدتها
function getPLSubAccounts($conn, $parent_code, $startdate, $enddate) {
    $sql = "SELECT ah.*, 
            COALESCE((SELECT SUM(je.debit) - SUM(je.credit) FROM journal_entries je WHERE je.account_id = ah.id AND je.isdeleted = 0 AND DATE(je.crtime) BETWEEN ? AND ?), 0) as balance
            FROM acc_head ah 
            WHERE ah.code LIKE ? AND ah.code != ? AND ah.isdeleted = 0 AND ah.is_basic = 0
            ORDER BY ah.code";
    $like_code = $parent_code . '%';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $startdate, $enddate, $like_code, $parent_code);
    $stmt->execute();
    return $stmt->get_result();
}

// دالة لجلب الحسابات الفرعية الأساسية (المستوى الأول فقط)
function getPLBasicSubAccounts($conn, $parent_code, $startdate, $enddate) {
    $sql = "SELECT ah.id, ah.code, ah.aname, ah.is_basic,
            COALESCE((
                SELECT SUM(je.debit) - SUM(je.credit) 
                FROM journal_entries je 
                INNER JOIN acc_head sub ON je.account_id = sub.id 
                WHERE sub.code LIKE CONCAT(ah.code, '%') AND sub.isdeleted = 0 AND je.isdeleted = 0 
                AND DATE(je.crtime) BETWEEN ? AND ?
            ), 0) as balance
            FROM acc_head ah 
            WHERE ah.parent_id = (SELECT id FROM acc_head WHERE code = ? AND isdeleted = 0 LIMIT 1)
            AND ah.isdeleted = 0
            ORDER BY ah.code";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $startdate, $enddate, $parent_code);
    $stmt->execute();
    return $stmt->get_result();
}

// ======= حساب بنود الأرباح والخسائر =======

// 1. الإيرادات (32)
$revenues_data = getPLAccountBalance($conn, '32', $startdate, $enddate);
$total_revenues = $revenues_data['total_credit'] - $revenues_data['total_debit'];

// 2. المصروفات (44)
$expenses_data = getPLAccountBalance($conn, '44', $startdate, $enddate);
$total_expenses = $expenses_data['total_debit'] - $expenses_data['total_credit'];

// 3. تكلفة المبيعات (إن وجدت - 41)
$cost_of_sales_data = getPLAccountBalance($conn, '41', $startdate, $enddate);
$total_cost_of_sales = $cost_of_sales_data['total_debit'] - $cost_of_sales_data['total_credit'];

// 4. مجمل الربح
$gross_profit = $total_revenues - $total_cost_of_sales;

// 5. صافي الربح / الخسارة
$net_profit = $total_revenues - $total_expenses - $total_cost_of_sales;

// اسم الشركة
$company_name = $rowstg['storename'] ?? 'الشركة';

// بيانات للرسم البياني
$revenue_items = [];
$expense_items = [];

$rev_result = getPLSubAccounts($conn, '32', $startdate, $enddate);
while ($r = $rev_result->fetch_assoc()) {
    $bal = ($r['balance'] * -1); // الإيرادات طبيعتها دائنة
    if (abs($bal) > 0.001) {
        $revenue_items[] = ['name' => $r['aname'], 'value' => $bal];
    }
}

$exp_result = getPLSubAccounts($conn, '44', $startdate, $enddate);
while ($r = $exp_result->fetch_assoc()) {
    if (abs($r['balance']) > 0.001) {
        $expense_items[] = ['name' => $r['aname'], 'value' => $r['balance']];
    }
}
?>

<style>
.pl-wrapper {
    max-width: 1000px;
    margin: 0 auto;
}
.pl-report-header {
    background: linear-gradient(135deg, #1a365d 0%, #2d3748 50%, #4a5568 100%);
    color: white;
    padding: 30px;
    border-radius: 12px 12px 0 0;
    text-align: center;
    position: relative;
    overflow: hidden;
}
.pl-report-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.03) 50%, transparent 70%);
    animation: pl-shine 6s infinite;
}
@keyframes pl-shine {
    0%, 100% { transform: translateX(-100%); }
    50% { transform: translateX(100%); }
}
.pl-report-header h2 {
    font-size: 1.8rem;
    margin-bottom: 5px;
    position: relative;
    z-index: 1;
}
.pl-report-header h4 {
    font-size: 1.1rem;
    opacity: 0.9;
    position: relative;
    z-index: 1;
}
.pl-report-header .period {
    font-size: 0.9rem;
    opacity: 0.8;
    margin-top: 10px;
    position: relative;
    z-index: 1;
}

/* Summary Cards */
.summary-cards {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
    padding: 20px;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
}
.summary-card {
    background: white;
    border-radius: 10px;
    padding: 18px;
    text-align: center;
    box-shadow: 0 2px 10px rgba(0,0,0,0.06);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.summary-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.1);
}
.summary-card .card-label {
    font-size: 0.85rem;
    color: #718096;
    margin-bottom: 8px;
    font-weight: 500;
}
.summary-card .card-amount {
    font-size: 1.4rem;
    font-weight: bold;
    font-family: 'Courier New', monospace;
}
.summary-card.revenue { border-top: 3px solid #38a169; }
.summary-card.revenue .card-amount { color: #38a169; }
.summary-card.expense { border-top: 3px solid #e53e3e; }
.summary-card.expense .card-amount { color: #e53e3e; }
.summary-card.profit { border-top: 3px solid #3182ce; }
.summary-card.profit .card-amount { color: #3182ce; }
.summary-card.loss { border-top: 3px solid #e53e3e; }
.summary-card.loss .card-amount { color: #e53e3e; }

/* Table styles */
.pl-section-title {
    padding: 12px 20px;
    font-weight: bold;
    font-size: 1.05rem;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
    transition: all 0.3s ease;
}
.pl-section-title.revenue-title {
    background: linear-gradient(135deg, #f0fff4, #c6f6d5);
    color: #22543d;
    border-right: 4px solid #38a169;
}
.pl-section-title.expense-title {
    background: linear-gradient(135deg, #fff5f5, #fed7d7);
    color: #742a2a;
    border-right: 4px solid #e53e3e;
}
.pl-section-title.cost-title {
    background: linear-gradient(135deg, #fffaf0, #feebc8);
    color: #744210;
    border-right: 4px solid #dd6b20;
}

.pl-table {
    width: 100%;
    border-collapse: collapse;
}
.pl-table td {
    padding: 10px 20px;
    border-bottom: 1px solid #f0f0f0;
    font-size: 0.95rem;
    transition: background 0.2s ease;
}
.pl-table tr:hover td {
    background-color: #f8fafc;
}
.pl-table .acc-name {
    padding-right: 40px;
    color: #4a5568;
}
.pl-table .acc-value {
    text-align: left;
    font-weight: 500;
    width: 200px;
    font-family: 'Courier New', monospace;
}
.pl-table .total-row td {
    font-weight: bold;
    border-top: 2px solid #e2e8f0;
    border-bottom: 2px solid #e2e8f0;
    background: #f7fafc;
    font-size: 1rem;
    padding: 12px 20px;
}
.pl-table .total-row.revenue-total td { color: #22543d; }
.pl-table .total-row.expense-total td { color: #742a2a; }
.pl-table .total-row.cost-total td { color: #744210; }

/* Net result */
.net-result {
    padding: 18px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-weight: bold;
    font-size: 1.15rem;
}
.net-result.profit-result {
    background: linear-gradient(135deg, #c6f6d5, #9ae6b4);
    color: #22543d;
    border-top: 3px double #38a169;
}
.net-result.loss-result {
    background: linear-gradient(135deg, #fed7d7, #feb2b2);
    color: #742a2a;
    border-top: 3px double #e53e3e;
}
.net-result .result-value {
    font-family: 'Courier New', monospace;
    font-size: 1.3rem;
}

/* Gross profit separator */
.gross-profit-row {
    padding: 14px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-weight: bold;
    font-size: 1.05rem;
    background: linear-gradient(135deg, #ebf8ff, #bee3f8);
    color: #2a4365;
    border-top: 2px solid #90cdf4;
    border-bottom: 2px solid #90cdf4;
}

/* Chart */
.chart-section {
    padding: 20px;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
}
.chart-container {
    position: relative;
    height: 300px;
    max-width: 100%;
}

/* Filter card */
.pl-filter-card {
    border: none;
    box-shadow: 0 2px 15px rgba(0,0,0,0.08);
    border-radius: 12px;
    margin-bottom: 20px;
    overflow: hidden;
}
.pl-report-card {
    border: none;
    box-shadow: 0 4px 25px rgba(0,0,0,0.1);
    border-radius: 12px;
    overflow: hidden;
}

.profit-text { color: #38a169; }
.loss-text { color: #e53e3e; }

@media print {
    .main-sidebar, .navbar, .pl-filter-card, .no-print, .main-footer, .content-header, .chart-section { display: none !important; }
    .content-wrapper { margin-left: 0 !important; padding: 0 !important; }
    .pl-report-card { box-shadow: none !important; }
    .pl-wrapper { max-width: 100%; }
    body { background: white !important; }
}

@media (max-width: 768px) {
    .summary-cards {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="pl-wrapper">

        <!-- فلتر التاريخ -->
        <div class="card pl-filter-card no-print">
            <div class="card-body" style="padding: 15px 20px;">
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
                            <input type="date" class="form-control" name="startdate" id="startdate" value="<?= $startdate ?>">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group mb-0">
                            <label><i class="fas fa-calendar-alt"></i> إلى</label>
                            <input type="date" class="form-control" name="enddate" id="enddate" value="<?= $enddate ?>">
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
                    الفترة من <?= $startdate ?> إلى <?= $enddate ?>
                </div>
            </div>

            <!-- بطاقات الملخص -->
            <div class="summary-cards">
                <div class="summary-card revenue">
                    <div class="card-label"><i class="fas fa-arrow-up"></i> إجمالي الإيرادات</div>
                    <div class="card-amount"><?= number_format(abs($total_revenues), 2) ?></div>
                </div>
                <div class="summary-card expense">
                    <div class="card-label"><i class="fas fa-arrow-down"></i> إجمالي المصروفات</div>
                    <div class="card-amount"><?= number_format(abs($total_expenses + $total_cost_of_sales), 2) ?></div>
                </div>
                <div class="summary-card <?= $net_profit >= 0 ? 'profit' : 'loss' ?>">
                    <div class="card-label">
                        <i class="fas fa-<?= $net_profit >= 0 ? 'trophy' : 'exclamation-triangle' ?>"></i>
                        <?= $net_profit >= 0 ? 'صافي الربح' : 'صافي الخسارة' ?>
                    </div>
                    <div class="card-amount"><?= number_format(abs($net_profit), 2) ?></div>
                </div>
            </div>

            <!-- الإيرادات -->
            <div class="pl-section-title revenue-title">
                <i class="fas fa-plus-circle"></i> الإيرادات
            </div>
            <table class="pl-table">
                <?php
                $rev_sub = getPLSubAccounts($conn, '32', $startdate, $enddate);
                while ($row = $rev_sub->fetch_assoc()) {
                    $bal = $row['balance'] * -1; // الإيرادات طبيعتها دائنة
                    if (abs($bal) > 0.001) {
                ?>
                <tr>
                    <td class="acc-name"><?= htmlspecialchars($row['code']) ?> - <?= htmlspecialchars($row['aname']) ?></td>
                    <td class="acc-value profit-text"><?= number_format(abs($bal), 2) ?></td>
                </tr>
                <?php }} ?>
                <tr class="total-row revenue-total">
                    <td>إجمالي الإيرادات</td>
                    <td class="acc-value"><?= number_format(abs($total_revenues), 2) ?></td>
                </tr>
            </table>

            <!-- تكلفة المبيعات (إن وجدت) -->
            <?php if (abs($total_cost_of_sales) > 0.001) { ?>
            <div class="pl-section-title cost-title">
                <i class="fas fa-minus-circle"></i> تكلفة المبيعات
            </div>
            <table class="pl-table">
                <?php
                $cost_sub = getPLSubAccounts($conn, '41', $startdate, $enddate);
                while ($row = $cost_sub->fetch_assoc()) {
                    if (abs($row['balance']) > 0.001) {
                ?>
                <tr>
                    <td class="acc-name"><?= htmlspecialchars($row['code']) ?> - <?= htmlspecialchars($row['aname']) ?></td>
                    <td class="acc-value loss-text"><?= number_format(abs($row['balance']), 2) ?></td>
                </tr>
                <?php }} ?>
                <tr class="total-row cost-total">
                    <td>إجمالي تكلفة المبيعات</td>
                    <td class="acc-value">(<?= number_format(abs($total_cost_of_sales), 2) ?>)</td>
                </tr>
            </table>

            <!-- مجمل الربح -->
            <div class="gross-profit-row">
                <span><i class="fas fa-chart-bar"></i> مجمل الربح</span>
                <span class="result-value" style="font-family: 'Courier New', monospace;">
                    <?= number_format(abs($gross_profit), 2) ?>
                </span>
            </div>
            <?php } ?>

            <!-- المصروفات -->
            <div class="pl-section-title expense-title">
                <i class="fas fa-minus-circle"></i> المصروفات
            </div>
            <table class="pl-table">
                <?php
                $exp_sub = getPLSubAccounts($conn, '44', $startdate, $enddate);
                while ($row = $exp_sub->fetch_assoc()) {
                    if (abs($row['balance']) > 0.001) {
                ?>
                <tr>
                    <td class="acc-name"><?= htmlspecialchars($row['code']) ?> - <?= htmlspecialchars($row['aname']) ?></td>
                    <td class="acc-value loss-text"><?= number_format(abs($row['balance']), 2) ?></td>
                </tr>
                <?php }} ?>
                <tr class="total-row expense-total">
                    <td>إجمالي المصروفات</td>
                    <td class="acc-value">(<?= number_format(abs($total_expenses), 2) ?>)</td>
                </tr>
            </table>

            <!-- صافي الربح / الخسارة -->
            <div class="net-result <?= $net_profit >= 0 ? 'profit-result' : 'loss-result' ?>" style="border-radius: 0 0 0 0;">
                <span>
                    <i class="fas fa-<?= $net_profit >= 0 ? 'check-double' : 'times-circle' ?>"></i>
                    <?= $net_profit >= 0 ? 'صافي الربح للفترة' : 'صافي الخسارة للفترة' ?>
                </span>
                <span class="result-value"><?= number_format(abs($net_profit), 2) ?></span>
            </div>

            <!-- الرسم البياني -->
            <div class="chart-section no-print">
                <h5 class="mb-3 text-center"><i class="fas fa-chart-pie"></i> تحليل بياني</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="chart-container">
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="chart-container">
                            <canvas id="expenseChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="chart-container" style="height: 250px;">
                            <canvas id="comparisonChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

      </div>
    </div>
  </section>
</div>

<script>
// بيانات الرسم البياني
var revenueData = <?= json_encode($revenue_items) ?>;
var expenseData = <?= json_encode($expense_items) ?>;

// ألوان
var greenColors = ['#38a169', '#48bb78', '#68d391', '#9ae6b4', '#c6f6d5', '#2f855a', '#276749'];
var redColors = ['#e53e3e', '#fc8181', '#feb2b2', '#f56565', '#c53030', '#e53e3e', '#dd6b20', '#ed8936'];

// رسم بياني الإيرادات
if (revenueData.length > 0) {
    new Chart(document.getElementById('revenueChart'), {
        type: 'doughnut',
        data: {
            labels: revenueData.map(function(i) { return i.name; }),
            datasets: [{
                data: revenueData.map(function(i) { return Math.abs(i.value); }),
                backgroundColor: greenColors.slice(0, revenueData.length),
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom', labels: { font: { family: 'Playpen Sans Arabic' } } } },
            title: { display: true, text: 'توزيع الإيرادات', font: { family: 'Playpen Sans Arabic' } }
        }
    });
}

// رسم بياني المصروفات
if (expenseData.length > 0) {
    new Chart(document.getElementById('expenseChart'), {
        type: 'doughnut',
        data: {
            labels: expenseData.map(function(i) { return i.name; }),
            datasets: [{
                data: expenseData.map(function(i) { return Math.abs(i.value); }),
                backgroundColor: redColors.slice(0, expenseData.length),
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom', labels: { font: { family: 'Playpen Sans Arabic' } } } },
            title: { display: true, text: 'توزيع المصروفات', font: { family: 'Playpen Sans Arabic' } }
        }
    });
}

// رسم بياني المقارنة
new Chart(document.getElementById('comparisonChart'), {
    type: 'bar',
    data: {
        labels: ['الإيرادات', 'المصروفات', '<?= $net_profit >= 0 ? "صافي الربح" : "صافي الخسارة" ?>'],
        datasets: [{
            label: 'المبلغ',
            data: [<?= abs($total_revenues) ?>, <?= abs($total_expenses + $total_cost_of_sales) ?>, <?= abs($net_profit) ?>],
            backgroundColor: ['rgba(56, 161, 105, 0.8)', 'rgba(229, 62, 62, 0.8)', '<?= $net_profit >= 0 ? "rgba(49, 130, 206, 0.8)" : "rgba(229, 62, 62, 0.8)" ?>'],
            borderColor: ['#38a169', '#e53e3e', '<?= $net_profit >= 0 ? "#3182ce" : "#e53e3e" ?>'],
            borderWidth: 2,
            borderRadius: 6
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { font: { family: 'Playpen Sans Arabic' } } },
            x: { ticks: { font: { family: 'Playpen Sans Arabic', size: 13 } } }
        }
    }
});

// تصدير Excel
document.getElementById('exportExcelPL')?.addEventListener('click', function() {
    var table = document.getElementById('plReport');
    var html = table.outerHTML;
    var url = 'data:application/vnd.ms-excel,' + encodeURIComponent(
        '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:spreadsheet">' +
        '<head><meta charset="UTF-8"><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet>' +
        '<x:Name>الأرباح والخسائر</x:Name><x:WorksheetOptions><x:DisplayRightToLeft/></x:WorksheetOptions>' +
        '</x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head>' +
        '<body dir="rtl">' + html + '</body></html>'
    );
    var a = document.createElement('a');
    a.href = url;
    a.download = 'profit_loss_<?= date("Y-m-d") ?>.xls';
    a.click();
});
</script>

<script>
function updateDateRange() {
    let period = document.getElementById('periodFilter').value;
    let startInput = document.getElementById('startdate');
    let endInput = document.getElementById('enddate');
    
    if (!period) return; // تخصيص

    let today = new Date();
    // Helper to format date to YYYY-MM-DD
    const formatDate = (d) => {
        let month = '' + (d.getMonth() + 1),
            day = '' + d.getDate(),
            year = d.getFullYear();
        if (month.length < 2) month = '0' + month;
        if (day.length < 2) day = '0' + day;
        return [year, month, day].join('-');
    };

    let endStr = formatDate(today);
    endInput.value = endStr;

    if (period === 'today') {
        startInput.value = endStr;
    } else if (period === 'week') {
        let firstDay = new Date(today.setDate(today.getDate() - today.getDay()));
        startInput.value = formatDate(firstDay);
    } else if (period === 'month') {
        let firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
        startInput.value = formatDate(firstDay);
    } else if (period === 'all') {
        startInput.value = '2000-01-01'; 
    }
}
</script>

<?php include('includes/footer.php') ?>
