<?php include('includes/header.php') ?>
<?php include('includes/navbar.php') ?>
<?php include('includes/sidebar.php') ?>

<?php
$reportGroups = [
    [
        'title' => 'تقارير المبيعات',
        'icon' => 'fa-chart-bar',
        'items' => [
            ['href' => 'operations_summary.php?q=sale', 'icon' => 'fa-calendar-day', 'title' => 'المبيعات اليومية'],
            ['href' => 'items_summery.php', 'icon' => 'fa-boxes', 'title' => $lang_sales_items_report ?? 'المبيعات أصناف'],
            ['href' => 'sales-by-group.php', 'icon' => 'fa-layer-group', 'title' => 'المبيعات مجموعات'],
            ['href' => 'sales-by-hour.php', 'icon' => 'fa-clock', 'title' => 'المبيعات بالساعة'],
            ['href' => 'sales-by-day.php', 'icon' => 'fa-calendar-alt', 'title' => 'المبيعات باليوم'],
            ['href' => 'sales-by-week.php', 'icon' => 'fa-calendar-week', 'title' => 'المبيعات بالأسبوع'],
            ['href' => 'sales-by-month.php', 'icon' => 'fa-calendar', 'title' => 'المبيعات بالشهر'],
            ['href' => 'sales_returns_report.php', 'icon' => 'fa-undo', 'title' => 'مردود المبيعات'],
            ['href' => 'top_products_report.php', 'icon' => 'fa-chart-line', 'title' => 'تحليلي مبيعات'],
            ['href' => 'stagnant-items-report.php', 'icon' => 'fa-exclamation-triangle', 'title' => 'الأصناف الراكدة'],
            ['href' => 'sales-by-employee.php', 'icon' => 'fa-user-tie', 'title' => 'تحقيق مبيعات الموظفين'],
            ['href' => 'sales-by-user.php', 'icon' => 'fa-users-cog', 'title' => 'تحقيق مبيعات المستخدمين'],
            ['href' => 'emp_targets_report.php', 'icon' => 'fa-bullseye', 'title' => 'تارجيت الموظفين'],
            ['href' => 'shift_sales_report.php', 'icon' => 'fa-cash-register', 'title' => 'مبيعات الشيفت'],
            ['href' => 'z_report.php', 'icon' => 'fa-file-invoice', 'title' => 'تقرير إغلاق الشيفت'],
        ],
    ],
    [
        'title' => 'تقارير المشتريات',
        'icon' => 'fa-shopping-cart',
        'items' => [
            ['href' => 'operations_summary.php?q=purchase', 'icon' => 'fa-calendar-day', 'title' => 'المشتريات اليومية'],
            ['href' => 'monthly_purchases.php', 'icon' => 'fa-calendar', 'title' => 'المشتريات بالشهر'],
            ['href' => 'purchase_returns_report.php', 'icon' => 'fa-undo-alt', 'title' => $lang_purchase_return ?? 'فاتورة مردود مشتريات'],
            ['href' => 'consignment_items_report.php', 'icon' => 'fa-handshake', 'title' => 'تقرير أصناف الأمانة'],
            ['href' => 'supplier_settlement_report.php', 'icon' => 'fa-balance-scale', 'title' => 'تقرير تسوية الموردين'],
        ],
    ],
    [
        'title' => 'تقارير مالية',
        'icon' => 'fa-balance-scale',
        'items' => [
            ['href' => 'summary.php', 'icon' => 'fa-file-alt', 'title' => $lang_account_statement ?? 'كشف حساب'],
            ['href' => 'balance_sheet.php', 'icon' => 'fa-balance-scale', 'title' => $lang_balance_sheet ?? 'تقرير الميزانية'],
            ['href' => 'profit_loss.php', 'icon' => 'fa-chart-line', 'title' => $lang_profit_loss ?? 'الأرباح والخسائر'],
            ['href' => 'journals_without_operations.php', 'icon' => 'fa-unlink', 'title' => 'القيود بدون عمليات'],
            ['href' => 'unbalanced_journals.php', 'icon' => 'fa-not-equal', 'title' => 'القيود غيرالمتزنة'],
            ['href' => 'acc_report.php?acc=clients', 'icon' => 'fa-users', 'title' => 'تقرير العملاء'],
        ],
    ],
    [
        'title' => 'تقارير التأجير',
        'icon' => 'fa-building',
        'items' => [
            ['href' => 'rentables.php', 'icon' => 'fa-building', 'title' => 'تقرير الوحدات الإيجارية'],
            ['href' => 'rentcontracts.php?del=0', 'icon' => 'fa-file-contract', 'title' => 'قائمة العقود'],
            ['href' => 'rentcontracts.php?del=1', 'icon' => 'fa-file-excel', 'title' => 'العقود المنتهية'],
            ['href' => 'myrentables.php', 'icon' => 'fa-money-bill-wave', 'title' => 'الأقساط المستحقة'],
        ],
    ],
    [
        'title' => 'تقارير إدارية',
        'icon' => 'fa-clipboard-list',
        'items' => [
            ['href' => 'reps_cl.php', 'icon' => 'fa-clinic-medical', 'title' => $lang_clinic_reports ?? 'تقارير العيادات'],
            ['href' => 'visits_stats.php', 'icon' => 'fa-chart-bar', 'title' => 'إحصائيات الزيارات'],
            ['href' => 'prints.php', 'icon' => 'fa-money-check-alt', 'title' => $lang_sidesalariesreports ?? 'تقارير المرتبات'],
            ['href' => 'attendance_report.php', 'icon' => 'fa-clock', 'title' => 'تقرير الحضور والانصراف'],
            ['href' => 'staff_report.php', 'icon' => 'fa-user-tie', 'title' => 'تقرير الموظفين'],
        ],
    ],
];
?>

<style>
.reports-main-card {
    background: #ffffff;
    border-radius: 20px;
    border: none;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    overflow: hidden;
}
.reports-main-card .card-header {
    background: linear-gradient(135deg, #111844, #4B5694);
    color: #ffffff;
    padding: 1.5rem;
    font-size: 1.4rem;
    font-weight: 700;
    border-bottom: none;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
.report-group {
    margin-bottom: 1.75rem;
}
.report-group:last-child {
    margin-bottom: 0;
}
.report-group-title {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    font-size: 1.05rem;
    font-weight: 700;
    color: #111844;
    margin: 0 0 0.9rem;
    padding-bottom: 0.45rem;
    border-bottom: 2px solid rgba(75, 86, 148, 0.15);
}
.report-card {
    background: #ffffff;
    border-radius: 12px;
    padding: 1rem 1.25rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    color: #111844;
    border: 1px solid rgba(75, 86, 148, 0.1);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.04);
    transition: all 0.25s ease;
    text-decoration: none !important;
    height: 100%;
    position: relative;
    overflow: hidden;
}
.report-card::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    width: 4px;
    background: #4B5694;
}
.report-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 16px -3px rgba(75, 86, 148, 0.12);
    border-color: rgba(75, 86, 148, 0.2);
    color: #111844;
}
.report-card h3 {
    font-size: 1rem;
    font-weight: 700;
    margin: 0;
    color: #111844;
    text-align: right;
}
.report-icon-wrapper {
    width: 48px;
    height: 48px;
    min-width: 48px;
    border-radius: 10px;
    background: rgba(75, 86, 148, 0.12);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: #4B5694;
}
</style>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="card reports-main-card">
                <div class="card-header">
                    <i class="fas fa-chart-line"></i>
                    التقارير
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <input type="text" id="reportSearch" class="form-control" placeholder="بحث في التقارير...">
                    </div>

                    <?php foreach ($reportGroups as $group) { ?>
                        <section class="report-group">
                            <h5 class="report-group-title">
                                <i class="fas <?= htmlspecialchars($group['icon'], ENT_QUOTES, 'UTF-8') ?>"></i>
                                <?= htmlspecialchars($group['title'], ENT_QUOTES, 'UTF-8') ?>
                            </h5>
                            <div class="row">
                                <?php foreach ($group['items'] as $item) { ?>
                                    <div class="col-md-4 mb-3 report-item">
                                        <a href="<?= htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8') ?>" class="report-card">
                                            <div class="report-icon-wrapper">
                                                <i class="fas <?= htmlspecialchars($item['icon'], ENT_QUOTES, 'UTF-8') ?>"></i>
                                            </div>
                                            <h3><?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?></h3>
                                        </a>
                                    </div>
                                <?php } ?>
                            </div>
                        </section>
                    <?php } ?>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    document.getElementById('reportSearch').addEventListener('input', function (e) {
        var query = e.target.value.trim().toLowerCase();
        document.querySelectorAll('.report-group').forEach(function (group) {
            var visible = 0;
            group.querySelectorAll('.report-item').forEach(function (item) {
                var match = query === '' || item.innerText.toLowerCase().indexOf(query) !== -1;
                item.style.display = match ? '' : 'none';
                if (match) {
                    visible++;
                }
            });
            group.style.display = visible ? '' : 'none';
        });
    });
</script>

<?php include('includes/footer.php') ?>
