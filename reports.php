<?php include('includes/header.php') ?>
<?php include('includes/navbar.php') ?>
<?php include('includes/sidebar.php') ?>

<?php
// الأعمدة = صلاحيات التقارير في الأدوار (rbac_catalog: reports.*)
$reportGroups = [
    [
        'title' => 'تقارير المبيعات',
        'icon' => 'fa-chart-bar',
        'perm' => 'reports.sales',
        'items' => [
            ['href' => 'operations_summary.php?q=sale', 'title' => 'المبيعات اليومية'],
            ['href' => 'operations_summary.php?q=sales_order', 'title' => 'أوامر البيع'],
            ['href' => 'operations_summary.php?q=price_offer', 'title' => 'عروض الأسعار'],
            ['href' => 'items_summery.php', 'title' => $lang_sales_items_report ?? 'المبيعات أصناف'],
            ['href' => 'sales-by-group.php', 'title' => 'المبيعات مجموعات'],
            ['href' => 'sales-by-hour.php', 'title' => 'المبيعات بالساعة'],
            ['href' => 'sales-by-day.php', 'title' => 'المبيعات باليوم'],
            ['href' => 'sales-by-week.php', 'title' => 'المبيعات بالأسبوع'],
            ['href' => 'sales-by-month.php', 'title' => 'المبيعات بالشهر'],
            ['href' => 'sales_returns_report.php', 'title' => 'مردود المبيعات'],
            ['href' => 'top_products_report.php', 'title' => 'تحليلي مبيعات'],
            ['href' => 'stagnant-items-report.php', 'title' => 'الأصناف الراكدة'],
            ['href' => 'sales-by-employee.php', 'title' => 'تحقيق مبيعات الموظفين'],
            ['href' => 'sales-by-user.php', 'title' => 'تحقيق مبيعات المستخدمين'],
            ['href' => 'emp_targets_report.php', 'title' => 'تارجيت الموظفين'],
        ],
    ],
    [
        'title' => 'تقارير الشيفت',
        'icon' => 'fa-cash-register',
        'perm' => 'reports.shift',
        'items' => [
            ['href' => 'shift_sales_report.php', 'title' => 'مبيعات الشيفت'],
            ['href' => 'z_report.php', 'title' => 'تقرير إغلاق الشيفت'],
        ],
    ],
    [
        'title' => 'تقارير المشتريات',
        'icon' => 'fa-shopping-cart',
        'perm' => 'reports.purchases',
        'items' => [
            ['href' => 'operations_summary.php?q=purchase', 'title' => 'المشتريات اليومية'],
            ['href' => 'monthly_purchases.php', 'title' => 'المشتريات بالشهر'],
            ['href' => 'purchase_returns_report.php', 'title' => $lang_purchase_return ?? 'فاتورة مردود مشتريات'],
            ['href' => 'consignment_items_report.php', 'title' => 'تقرير أصناف الأمانة'],
            ['href' => 'supplier_settlement_report.php', 'title' => 'تقرير تسوية الموردين'],
        ],
    ],
    [
        'title' => 'كشوف الحسابات والقوائم المالية',
        'icon' => 'fa-balance-scale',
        'perm' => 'reports.accounting',
        'items' => [
            ['href' => 'operations_summary.php?q=all', 'title' => 'كل العمليات'],
            ['href' => 'summary.php', 'title' => $lang_account_statement ?? 'كشف حساب'],
            ['href' => 'balance_sheet.php', 'title' => $lang_balance_sheet ?? 'تقرير الميزانية'],
            ['href' => 'profit_loss.php', 'title' => $lang_profit_loss ?? 'الأرباح والخسائر'],
            ['href' => 'journals_without_operations.php', 'title' => 'القيود بدون عمليات'],
            ['href' => 'unbalanced_journals.php', 'title' => 'القيود غيرالمتزنة'],
        ],
    ],
    [
        'title' => 'تقارير التأجير',
        'icon' => 'fa-building',
        'perm' => 'reports.rents',
        'items' => [
            ['href' => 'rentables.php', 'title' => 'تقرير الوحدات الإيجارية'],
            ['href' => 'rentcontracts.php?del=0', 'title' => 'قائمة العقود'],
            ['href' => 'rentcontracts.php?del=1', 'title' => 'العقود المنتهية'],
            ['href' => 'myrentables.php', 'title' => 'الأقساط المستحقة'],
        ],
    ],
    [
        'title' => 'تقارير العيادات',
        'icon' => 'fa-clinic-medical',
        'perm' => 'reports.clinics',
        'items' => [
            ['href' => 'reps_cl.php', 'title' => $lang_clinic_reports ?? 'تقارير العيادات'],
        ],
    ],
    [
        'title' => 'إحصائيات الزيارات',
        'icon' => 'fa-chart-bar',
        'perm' => 'reports.visits',
        'items' => [
            ['href' => 'visits_stats.php', 'title' => 'إحصائيات الزيارات'],
        ],
    ],
];

foreach ($reportGroups as $gi => $group) {
    if (!Can::check($group['perm'])) {
        unset($reportGroups[$gi]);
        continue;
    }
    $reportGroups[$gi]['items'] = array_values(array_filter(
        $group['items'],
        fn($item) => Can::canVisit($item['href'])
    ));
    if (empty($reportGroups[$gi]['items'])) {
        unset($reportGroups[$gi]);
    }
}
$reportGroups = array_values($reportGroups);
?>

<style>
.reports-page {
    background: #fff;
    border-radius: 12px;
    border: 1px solid rgba(17, 24, 68, 0.08);
    padding: 1.25rem 1.5rem 1.75rem;
}
.reports-page-title {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    font-size: 1.35rem;
    font-weight: 700;
    color: #111844;
    margin: 0 0 1.25rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid rgba(17, 24, 68, 0.1);
}
.reports-columns {
    display: flex;
    flex-wrap: wrap;
    gap: 1.5rem 2rem;
    align-items: flex-start;
}
.report-column {
    flex: 1 1 200px;
    min-width: 180px;
    max-width: 280px;
}
.report-column-title {
    display: flex;
    align-items: center;
    gap: 0.45rem;
    font-size: 1rem;
    font-weight: 700;
    color: #111844;
    margin: 0 0 0.65rem;
    padding-bottom: 0.4rem;
    border-bottom: 2px solid rgba(75, 86, 148, 0.2);
}
.report-links {
    list-style: none;
    margin: 0;
    padding: 0;
}
.report-links li {
    margin: 0;
}
.report-links a {
    display: block;
    padding: 0.35rem 0;
    color: #1a237e;
    text-decoration: none;
    font-size: 0.95rem;
    line-height: 1.45;
    border-bottom: 1px solid transparent;
    transition: color 0.15s ease, border-color 0.15s ease;
}
.report-links a:hover {
    color: #4B5694;
    border-bottom-color: rgba(75, 86, 148, 0.35);
    text-decoration: none;
}
@media (max-width: 767.98px) {
    .report-column {
        flex: 1 1 100%;
        max-width: none;
    }
}
</style>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="reports-page">
                <h1 class="reports-page-title">
                    <i class="fas fa-chart-line"></i>
                    التقارير
                </h1>

                <div class="mb-3">
                    <input type="text" id="reportSearch" class="form-control" placeholder="بحث في التقارير...">
                </div>

                <?php if (empty($reportGroups)) { ?>
                    <div class="alert alert-info text-center mb-0">لا توجد تقارير متاحة لصلاحياتك</div>
                <?php } else { ?>
                    <div class="reports-columns">
                        <?php foreach ($reportGroups as $group) { ?>
                            <section class="report-column report-group">
                                <h2 class="report-column-title">
                                    <i class="fas <?= htmlspecialchars($group['icon'], ENT_QUOTES, 'UTF-8') ?>"></i>
                                    <?= htmlspecialchars($group['title'], ENT_QUOTES, 'UTF-8') ?>
                                </h2>
                                <ul class="report-links">
                                    <?php foreach ($group['items'] as $item) { ?>
                                        <li class="report-item">
                                            <a href="<?= htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8') ?>">
                                                <?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?>
                                            </a>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </section>
                        <?php } ?>
                    </div>
                <?php } ?>
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
