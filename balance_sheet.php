<?php include('includes/header.php') ?>
<?php include('includes/navbar.php') ?>
<?php include('includes/sidebar.php') ?>

<?php
// تحديد الفترة الزمنية
$startdate = isset($_POST['startdate']) ? $_POST['startdate'] : date('Y-01-01');
$enddate = isset($_POST['enddate']) ? $_POST['enddate'] : date('Y-m-d');

// تحديث أرصدة الحسابات بناءً على الفترة المحددة
// دالة لحساب رصيد حساب معين خلال فترة محددة
function getAccountBalance($conn, $account_id, $startdate, $enddate) {
    $sql = "SELECT COALESCE(SUM(debit), 0) as total_debit, COALESCE(SUM(credit), 0) as total_credit 
            FROM journal_entries 
            WHERE account_id = ? AND isdeleted = 0 
            AND DATE(crtime) BETWEEN ? AND ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $account_id, $startdate, $enddate);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result;
}

// دالة لحساب رصيد حساب رئيسي (مجموع الحسابات الفرعية)
function getParentAccountBalance($conn, $parent_code, $startdate, $enddate) {
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
function getSubAccounts($conn, $parent_code, $startdate, $enddate) {
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

// ======= حساب بنود الميزانية =======

// 1. الأصول الثابتة (11)
$assets_fixed = getParentAccountBalance($conn, '11', $startdate, $enddate);
$assets_fixed_balance = $assets_fixed['total_debit'] - $assets_fixed['total_credit'];

// 2. الأصول المتداولة
// الصناديق (121)
$funds = getParentAccountBalance($conn, '121', $startdate, $enddate);
$funds_balance = $funds['total_debit'] - $funds['total_credit'];

// العملاء (122)
$clients = getParentAccountBalance($conn, '122', $startdate, $enddate);
$clients_balance = $clients['total_debit'] - $clients['total_credit'];

// المخازن (123)
$stores = getParentAccountBalance($conn, '123', $startdate, $enddate);
$stores_balance = $stores['total_debit'] - $stores['total_credit'];

// البنوك (124)
$banks = getParentAccountBalance($conn, '124', $startdate, $enddate);
$banks_balance = $banks['total_debit'] - $banks['total_credit'];

// المدينون (125)
$debtors = getParentAccountBalance($conn, '125', $startdate, $enddate);
$debtors_balance = $debtors['total_debit'] - $debtors['total_credit'];

// إجمالي الأصول المتداولة
$current_assets_total = $funds_balance + $clients_balance + $stores_balance + $banks_balance + $debtors_balance;

// إجمالي الأصول
$total_assets = $assets_fixed_balance + $current_assets_total;

// 3. الخصوم المتداولة
// الموردون (211)
$suppliers = getParentAccountBalance($conn, '211', $startdate, $enddate);
$suppliers_balance = $suppliers['total_credit'] - $suppliers['total_debit'];

// الدائنون (212)
$creditors = getParentAccountBalance($conn, '212', $startdate, $enddate);
$creditors_balance = $creditors['total_credit'] - $creditors['total_debit'];

// الموظفون (213)
$employees_acc = getParentAccountBalance($conn, '213', $startdate, $enddate);
$employees_balance = $employees_acc['total_credit'] - $employees_acc['total_debit'];

// إجمالي الخصوم المتداولة
$total_liabilities = $suppliers_balance + $creditors_balance + $employees_balance;

// 4. حقوق الملكية
// رأس المال / الشركاء (221)
$partners = getParentAccountBalance($conn, '221', $startdate, $enddate);
$partners_balance = $partners['total_credit'] - $partners['total_debit'];

// حساب صافي الربح أو الخسارة (الإيرادات - المصروفات)
$revenues = getParentAccountBalance($conn, '32', $startdate, $enddate);
$revenues_total = $revenues['total_credit'] - $revenues['total_debit'];

$expenses = getParentAccountBalance($conn, '44', $startdate, $enddate);
$expenses_total = $expenses['total_debit'] - $expenses['total_credit'];

$net_profit = $revenues_total - $expenses_total;

// إجمالي حقوق الملكية
$total_equity = $partners_balance + $net_profit;

// إجمالي الخصوم وحقوق الملكية
$total_liabilities_equity = $total_liabilities + $total_equity;

// اسم الشركة
$company_name = $rowstg['storename'] ?? 'الشركة';
?>

<style>
.balance-sheet-wrapper {
    max-width: 1100px;
    margin: 0 auto;
}
.report-header {
    background: linear-gradient(135deg, #1e3a5f 0%, #2c5282 50%, #2b6cb0 100%);
    color: white;
    padding: 30px;
    border-radius: 12px 12px 0 0;
    text-align: center;
    position: relative;
    overflow: hidden;
}
.report-header::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.05) 0%, transparent 70%);
    animation: shimmer 8s infinite linear;
}
@keyframes shimmer {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
.report-header h2 {
    font-size: 1.8rem;
    margin-bottom: 5px;
    position: relative;
    z-index: 1;
}
.report-header h4 {
    font-size: 1.1rem;
    opacity: 0.9;
    position: relative;
    z-index: 1;
}
.report-header .period {
    font-size: 0.9rem;
    opacity: 0.8;
    margin-top: 10px;
    position: relative;
    z-index: 1;
}
.section-title {
    background: linear-gradient(135deg, #ebf4ff, #dbeafe);
    padding: 12px 20px;
    font-weight: bold;
    font-size: 1.1rem;
    color: #1e3a5f;
    border-right: 4px solid #2c5282;
    margin-top: 0;
    transition: all 0.3s ease;
}
.section-title:hover {
    border-right-width: 6px;
    background: linear-gradient(135deg, #dbeafe, #bfdbfe);
}
.section-title i {
    margin-left: 8px;
    font-size: 1rem;
}
.bs-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 0;
}
.bs-table td {
    padding: 10px 20px;
    border-bottom: 1px solid #f0f0f0;
    font-size: 0.95rem;
    transition: background 0.2s ease;
}
.bs-table tr:hover td {
    background-color: #f8fafc;
}
.bs-table .account-name {
    padding-right: 40px;
    color: #4a5568;
}
.bs-table .account-value {
    text-align: left;
    font-weight: 500;
    color: #2d3748;
    width: 200px;
    font-family: 'Courier New', monospace;
}
.bs-table .sub-total td {
    font-weight: bold;
    border-top: 2px solid #e2e8f0;
    border-bottom: 2px solid #e2e8f0;
    background: #f7fafc;
    color: #1e3a5f;
    font-size: 1rem;
}
.bs-table .grand-total td {
    font-weight: bold;
    font-size: 1.1rem;
    border-top: 3px double #2c5282;
    border-bottom: 3px double #2c5282;
    background: linear-gradient(135deg, #ebf8ff, #e6f6ff);
    color: #1e3a5f;
    padding: 14px 20px;
}
.balance-match {
    text-align: center;
    padding: 15px;
    border-radius: 0 0 12px 12px;
    font-weight: bold;
    font-size: 1.05rem;
}
.balance-match.matched {
    background: linear-gradient(135deg, #c6f6d5, #9ae6b4);
    color: #22543d;
}
.balance-match.unmatched {
    background: linear-gradient(135deg, #fed7d7, #feb2b2);
    color: #742a2a;
}
.filter-card {
    border: none;
    box-shadow: 0 2px 15px rgba(0,0,0,0.08);
    border-radius: 12px;
    margin-bottom: 20px;
    overflow: hidden;
}
.filter-card .card-body {
    padding: 15px 20px;
}
.report-card {
    border: none;
    box-shadow: 0 4px 25px rgba(0,0,0,0.1);
    border-radius: 12px;
    overflow: hidden;
}
.two-columns {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0;
}
.column-right {
    border-left: 2px solid #e2e8f0;
}
.positive-value { color: #2f855a; }
.negative-value { color: #c53030; }

@media print {
    .main-sidebar, .navbar, .filter-card, .no-print, .main-footer, .content-header { display: none !important; }
    .content-wrapper { margin-left: 0 !important; padding: 0 !important; }
    .report-card { box-shadow: none !important; }
    .balance-sheet-wrapper { max-width: 100%; }
    body { background: white !important; }
}

@media (max-width: 768px) {
    .two-columns {
        grid-template-columns: 1fr;
    }
    .column-right {
        border-left: none;
        border-top: 2px solid #e2e8f0;
    }
}
</style>

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="balance-sheet-wrapper">

        <!-- فلتر التاريخ -->
        <div class="card filter-card no-print">
            <div class="card-body">
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
                        <button type="button" class="btn btn-outline-success btn-block" id="exportExcelBS">
                            <i class="fas fa-file-excel"></i> تصدير Excel
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- التقرير -->
        <div class="card report-card" id="balanceSheetReport">
            <!-- رأس التقرير -->
            <div class="report-header">
                <h2><i class="fas fa-balance-scale"></i> الميزانية العمومية</h2>
                <h4><?= htmlspecialchars($company_name) ?></h4>
                <div class="period">
                    <i class="fas fa-clock"></i> 
                    الفترة من <?= $startdate ?> إلى <?= $enddate ?>
                </div>
            </div>

            <!-- محتوى الميزانية -->
            <div class="two-columns">
                
                <!-- الجانب الأيمن: الأصول -->
                <div class="column-left">
                    <!-- الأصول الثابتة -->
                    <div class="section-title">
                        <i class="fas fa-building"></i> الأصول الثابتة
                    </div>
                    <table class="bs-table">
                        <?php
                        $sub_fixed = getSubAccounts($conn, '11', $startdate, $enddate);
                        while ($row = $sub_fixed->fetch_assoc()) {
                            if (abs($row['balance']) > 0.001) {
                                $val_class = $row['balance'] >= 0 ? 'positive-value' : 'negative-value';
                        ?>
                        <tr>
                            <td class="account-name"><?= htmlspecialchars($row['code']) ?> - <?= htmlspecialchars($row['aname']) ?></td>
                            <td class="account-value <?= $val_class ?>"><?= number_format(abs($row['balance']), 2) ?></td>
                        </tr>
                        <?php }} ?>
                        <tr class="sub-total">
                            <td>إجمالي الأصول الثابتة</td>
                            <td class="account-value"><?= number_format(abs($assets_fixed_balance), 2) ?></td>
                        </tr>
                    </table>

                    <!-- الأصول المتداولة -->
                    <div class="section-title">
                        <i class="fas fa-exchange-alt"></i> الأصول المتداولة
                    </div>
                    <table class="bs-table">
                        <?php
                        // الصناديق
                        $sub_funds = getSubAccounts($conn, '121', $startdate, $enddate);
                        while ($row = $sub_funds->fetch_assoc()) {
                            if (abs($row['balance']) > 0.001) {
                                $val_class = $row['balance'] >= 0 ? 'positive-value' : 'negative-value';
                        ?>
                        <tr>
                            <td class="account-name"><?= htmlspecialchars($row['code']) ?> - <?= htmlspecialchars($row['aname']) ?></td>
                            <td class="account-value <?= $val_class ?>"><?= number_format(abs($row['balance']), 2) ?></td>
                        </tr>
                        <?php }} ?>

                        <?php
                        // العملاء
                        $sub_clients = getSubAccounts($conn, '122', $startdate, $enddate);
                        while ($row = $sub_clients->fetch_assoc()) {
                            if (abs($row['balance']) > 0.001) {
                                $val_class = $row['balance'] >= 0 ? 'positive-value' : 'negative-value';
                        ?>
                        <tr>
                            <td class="account-name"><?= htmlspecialchars($row['code']) ?> - <?= htmlspecialchars($row['aname']) ?></td>
                            <td class="account-value <?= $val_class ?>"><?= number_format(abs($row['balance']), 2) ?></td>
                        </tr>
                        <?php }} ?>

                        <?php
                        // المخازن
                        $sub_stores = getSubAccounts($conn, '123', $startdate, $enddate);
                        while ($row = $sub_stores->fetch_assoc()) {
                            if (abs($row['balance']) > 0.001) {
                                $val_class = $row['balance'] >= 0 ? 'positive-value' : 'negative-value';
                        ?>
                        <tr>
                            <td class="account-name"><?= htmlspecialchars($row['code']) ?> - <?= htmlspecialchars($row['aname']) ?></td>
                            <td class="account-value <?= $val_class ?>"><?= number_format(abs($row['balance']), 2) ?></td>
                        </tr>
                        <?php }} ?>

                        <?php
                        // البنوك
                        $sub_banks = getSubAccounts($conn, '124', $startdate, $enddate);
                        while ($row = $sub_banks->fetch_assoc()) {
                            if (abs($row['balance']) > 0.001) {
                                $val_class = $row['balance'] >= 0 ? 'positive-value' : 'negative-value';
                        ?>
                        <tr>
                            <td class="account-name"><?= htmlspecialchars($row['code']) ?> - <?= htmlspecialchars($row['aname']) ?></td>
                            <td class="account-value <?= $val_class ?>"><?= number_format(abs($row['balance']), 2) ?></td>
                        </tr>
                        <?php }} ?>

                        <?php
                        // المدينون
                        $sub_debtors = getSubAccounts($conn, '125', $startdate, $enddate);
                        while ($row = $sub_debtors->fetch_assoc()) {
                            if (abs($row['balance']) > 0.001) {
                                $val_class = $row['balance'] >= 0 ? 'positive-value' : 'negative-value';
                        ?>
                        <tr>
                            <td class="account-name"><?= htmlspecialchars($row['code']) ?> - <?= htmlspecialchars($row['aname']) ?></td>
                            <td class="account-value <?= $val_class ?>"><?= number_format(abs($row['balance']), 2) ?></td>
                        </tr>
                        <?php }} ?>

                        <tr class="sub-total">
                            <td>إجمالي الأصول المتداولة</td>
                            <td class="account-value"><?= number_format(abs($current_assets_total), 2) ?></td>
                        </tr>
                    </table>

                    <!-- إجمالي الأصول -->
                    <table class="bs-table">
                        <tr class="grand-total">
                            <td><i class="fas fa-coins"></i> إجمالي الأصول</td>
                            <td class="account-value"><?= number_format(abs($total_assets), 2) ?></td>
                        </tr>
                    </table>
                </div>

                <!-- الجانب الأيسر: الخصوم وحقوق الملكية -->
                <div class="column-right">
                    <!-- الخصوم المتداولة -->
                    <div class="section-title">
                        <i class="fas fa-file-invoice-dollar"></i> الخصوم المتداولة
                    </div>
                    <table class="bs-table">
                        <?php
                        // الموردون
                        $sub_suppliers = getSubAccounts($conn, '211', $startdate, $enddate);
                        while ($row = $sub_suppliers->fetch_assoc()) {
                            $bal = $row['balance'] * -1; // عكس الإشارة للخصوم
                            if (abs($bal) > 0.001) {
                                $val_class = $bal >= 0 ? 'positive-value' : 'negative-value';
                        ?>
                        <tr>
                            <td class="account-name"><?= htmlspecialchars($row['code']) ?> - <?= htmlspecialchars($row['aname']) ?></td>
                            <td class="account-value <?= $val_class ?>"><?= number_format(abs($bal), 2) ?></td>
                        </tr>
                        <?php }} ?>

                        <?php
                        // الدائنون
                        $sub_creditors = getSubAccounts($conn, '212', $startdate, $enddate);
                        while ($row = $sub_creditors->fetch_assoc()) {
                            $bal = $row['balance'] * -1;
                            if (abs($bal) > 0.001) {
                                $val_class = $bal >= 0 ? 'positive-value' : 'negative-value';
                        ?>
                        <tr>
                            <td class="account-name"><?= htmlspecialchars($row['code']) ?> - <?= htmlspecialchars($row['aname']) ?></td>
                            <td class="account-value <?= $val_class ?>"><?= number_format(abs($bal), 2) ?></td>
                        </tr>
                        <?php }} ?>

                        <?php
                        // الموظفون
                        $sub_employees = getSubAccounts($conn, '213', $startdate, $enddate);
                        while ($row = $sub_employees->fetch_assoc()) {
                            $bal = $row['balance'] * -1;
                            if (abs($bal) > 0.001) {
                                $val_class = $bal >= 0 ? 'positive-value' : 'negative-value';
                        ?>
                        <tr>
                            <td class="account-name"><?= htmlspecialchars($row['code']) ?> - <?= htmlspecialchars($row['aname']) ?></td>
                            <td class="account-value <?= $val_class ?>"><?= number_format(abs($bal), 2) ?></td>
                        </tr>
                        <?php }} ?>

                        <tr class="sub-total">
                            <td>إجمالي الخصوم المتداولة</td>
                            <td class="account-value"><?= number_format(abs($total_liabilities), 2) ?></td>
                        </tr>
                    </table>

                    <!-- حقوق الملكية -->
                    <div class="section-title">
                        <i class="fas fa-landmark"></i> حقوق الملكية
                    </div>
                    <table class="bs-table">
                        <?php
                        $sub_partners = getSubAccounts($conn, '221', $startdate, $enddate);
                        while ($row = $sub_partners->fetch_assoc()) {
                            $bal = $row['balance'] * -1;
                            if (abs($bal) > 0.001) {
                                $val_class = $bal >= 0 ? 'positive-value' : 'negative-value';
                        ?>
                        <tr>
                            <td class="account-name"><?= htmlspecialchars($row['code']) ?> - <?= htmlspecialchars($row['aname']) ?></td>
                            <td class="account-value <?= $val_class ?>"><?= number_format(abs($bal), 2) ?></td>
                        </tr>
                        <?php }} ?>

                        <tr>
                            <td class="account-name" style="color: <?= $net_profit >= 0 ? '#2f855a' : '#c53030' ?>;">
                                <i class="fas fa-<?= $net_profit >= 0 ? 'arrow-up' : 'arrow-down' ?>"></i>
                                <?= $net_profit >= 0 ? 'صافي الأرباح' : 'صافي الخسائر' ?>
                            </td>
                            <td class="account-value <?= $net_profit >= 0 ? 'positive-value' : 'negative-value' ?>">
                                <?= number_format(abs($net_profit), 2) ?>
                            </td>
                        </tr>

                        <tr class="sub-total">
                            <td>إجمالي حقوق الملكية</td>
                            <td class="account-value"><?= number_format(abs($total_equity), 2) ?></td>
                        </tr>
                    </table>

                    <!-- إجمالي الخصوم وحقوق الملكية -->
                    <table class="bs-table">
                        <tr class="grand-total">
                            <td><i class="fas fa-balance-scale-right"></i> إجمالي الخصوم وحقوق الملكية</td>
                            <td class="account-value"><?= number_format(abs($total_liabilities_equity), 2) ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- حالة التوازن -->
            <?php 
            $diff = abs($total_assets) - abs($total_liabilities_equity);
            $is_balanced = abs($diff) < 0.01;
            ?>
            <div class="balance-match <?= $is_balanced ? 'matched' : 'unmatched' ?>">
                <?php if ($is_balanced) { ?>
                    <i class="fas fa-check-circle"></i> الميزانية متوازنة ✓
                <?php } else { ?>
                    <i class="fas fa-exclamation-triangle"></i> 
                    الميزانية غير متوازنة - الفرق: <?= number_format(abs($diff), 2) ?>
                <?php } ?>
            </div>
        </div>

      </div>
    </div>
  </section>
</div>

<script>
// تصدير Excel
document.getElementById('exportExcelBS')?.addEventListener('click', function() {
    var table = document.getElementById('balanceSheetReport');
    var html = table.outerHTML;
    var url = 'data:application/vnd.ms-excel,' + encodeURIComponent(
        '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:spreadsheet">' +
        '<head><meta charset="UTF-8"><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet>' +
        '<x:Name>الميزانية</x:Name><x:WorksheetOptions><x:DisplayRightToLeft/></x:WorksheetOptions>' +
        '</x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head>' +
        '<body dir="rtl">' + html + '</body></html>'
    );
    var a = document.createElement('a');
    a.href = url;
    a.download = 'balance_sheet_<?= date("Y-m-d") ?>.xls';
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
