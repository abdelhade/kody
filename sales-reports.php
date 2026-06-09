<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<style>
.report-card-container {
    padding: 1.5rem 0.5rem;
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
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.04), 0 2px 4px -1px rgba(0, 0, 0, 0.02);
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
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
    background: var(--card-color, #4B5694);
    transition: all 0.25s ease;
}

.report-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 16px -3px rgba(75, 86, 148, 0.12), 0 4px 6px -2px rgba(75, 86, 148, 0.08);
    border-color: rgba(75, 86, 148, 0.2);
}

.report-card:hover::before {
    width: 6px;
}

.report-card h3 {
    font-size: 1rem;
    font-weight: 700;
    margin: 0;
    color: #111844;
    transition: color 0.25s ease;
    text-align: right;
}

.report-icon-wrapper {
    width: 48px;
    height: 48px;
    min-width: 48px;
    border-radius: 10px;
    background: var(--card-bg-light, #EAE0CF);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: var(--card-color, #4B5694);
    transition: all 0.25s ease;
}

.report-card:hover .report-icon-wrapper {
    transform: scale(1.08) rotate(3deg);
    background: var(--card-color, #4B5694);
    color: #ffffff;
}

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
</style>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">

            <div class="card reports-main-card">
                <div class="card-header">
                    <i class="fas fa-chart-bar"></i>
                    تقارير المبيعات
                </div>

                <div class="card-body report-card-container">
                    <div class="row g-3">

                        <!-- المبيعات اليومية -->
                        <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                            <a href="operations_summary.php?q=buy" class="report-card" style="--card-color: #4B5694; --card-bg-light: rgba(75, 86, 148, 0.12);">
                                <div class="report-icon-wrapper">
                                    <i class="fa fa-calendar-day"></i>
                                </div>
                                <h3>المبيعات اليومية</h3>
                            </a>
                        </div>

                        <!-- المبيعات أصناف -->
                        <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                            <a href="items_summery.php" class="report-card" style="--card-color: #7288AE; --card-bg-light: rgba(114, 136, 174, 0.12);">
                                <div class="report-icon-wrapper">
                                    <i class="fa fa-boxes"></i>
                                </div>
                                <h3>المبيعات أصناف</h3>
                            </a>
                        </div>

                        <!-- المبيعات مجموعات -->
                        <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                            <a href="sales-by-group.php" class="report-card" style="--card-color: #111844; --card-bg-light: rgba(17, 24, 68, 0.08);">
                                <div class="report-icon-wrapper">
                                    <i class="fa fa-layer-group"></i>
                                </div>
                                <h3>المبيعات مجموعات</h3>
                            </a>
                        </div>

                        <!-- المبيعات بالساعة -->
                        <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                            <a href="sales-by-hour.php" class="report-card" style="--card-color: #4B5694; --card-bg-light: rgba(75, 86, 148, 0.12);">
                                <div class="report-icon-wrapper">
                                    <i class="fa fa-clock"></i>
                                </div>
                                <h3>المبيعات بالساعة</h3>
                            </a>
                        </div>

                        <!-- المبيعات باليوم -->
                        <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                            <a href="sales-by-day.php" class="report-card" style="--card-color: #7288AE; --card-bg-light: rgba(114, 136, 174, 0.12);">
                                <div class="report-icon-wrapper">
                                    <i class="fa fa-calendar-alt"></i>
                                </div>
                                <h3>المبيعات باليوم</h3>
                            </a>
                        </div>

                        <!-- المبيعات بالأسبوع -->
                        <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                            <a href="sales-by-week.php" class="report-card" style="--card-color: #111844; --card-bg-light: rgba(17, 24, 68, 0.08);">
                                <div class="report-icon-wrapper">
                                    <i class="fa fa-calendar-week"></i>
                                </div>
                                <h3>المبيعات بالأسبوع</h3>
                            </a>
                        </div>

                        <!-- المبيعات بالشهر -->
                        <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                            <a href="sales-by-month.php" class="report-card" style="--card-color: #4B5694; --card-bg-light: rgba(75, 86, 148, 0.12);">
                                <div class="report-icon-wrapper">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <h3>المبيعات بالشهر</h3>
                            </a>
                        </div>

                        <!-- تحليلي مبيعات -->
                        <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                            <a href="top_products_report.php" class="report-card" style="--card-color: #7288AE; --card-bg-light: rgba(114, 136, 174, 0.12);">
                                <div class="report-icon-wrapper">
                                    <i class="fa fa-chart-line"></i>
                                </div>
                                <h3>تحليلي مبيعات</h3>
                            </a>
                        </div>

                        <!-- الأصناف الراكدة -->
                        <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                            <a href="stagnant-items-report.php" class="report-card" style="--card-color: #111844; --card-bg-light: rgba(17, 24, 68, 0.08);">
                                <div class="report-icon-wrapper">
                                    <i class="fa fa-exclamation-triangle"></i>
                                </div>
                                <h3>الأصناف الراكدة</h3>
                            </a>
                        </div>

                        <!-- تحقيق مبيعات الموظفين -->
                        <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                            <a href="sales-by-employee.php" class="report-card" style="--card-color: #4B5694; --card-bg-light: rgba(75, 86, 148, 0.12);">
                                <div class="report-icon-wrapper">
                                    <i class="fa fa-user-tie"></i>
                                </div>
                                <h3>تحقيق مبيعات الموظفين</h3>
                            </a>
                        </div>

                        <!-- تحقيق مبيعات المستخدمين -->
                        <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                            <a href="sales-by-user.php" class="report-card" style="--card-color: #7288AE; --card-bg-light: rgba(114, 136, 174, 0.12);">
                                <div class="report-icon-wrapper">
                                    <i class="fa fa-users-cog"></i>
                                </div>
                                <h3>تحقيق مبيعات المستخدمين</h3>
                            </a>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

<?php include 'includes/footer.php'; ?>
