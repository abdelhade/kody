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
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.04), 0 2px 4px -1px rgba(0,0,0,0.02);
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    text-decoration: none !important;
    height: 100%;
    position: relative;
    overflow: hidden;
}
.report-card::before {
    content: '';
    position: absolute;
    top: 0; right: 0; bottom: 0;
    width: 4px;
    background: var(--card-color, #4B5694);
    transition: all 0.25s ease;
}
.report-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 16px -3px rgba(75,86,148,0.12), 0 4px 6px -2px rgba(75,86,148,0.08);
    border-color: rgba(75, 86, 148, 0.2);
}
.report-card:hover::before { width: 6px; }
.report-card h3 {
    font-size: 1rem;
    font-weight: 700;
    margin: 0;
    color: #111844;
    text-align: right;
}
.report-icon-wrapper {
    width: 48px; height: 48px; min-width: 48px;
    border-radius: 10px;
    background: var(--card-bg-light, #EAE0CF);
    display: flex; align-items: center; justify-content: center;
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
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
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
                    <i class="fas fa-shopping-cart"></i>
                    تقارير المشتريات
                </div>

                <div class="card-body report-card-container">
                    <div class="row g-3">

                        <!-- المشتريات اليومية -->
                        <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                            <a href="operations_summary.php?q=purchase" class="report-card"
                               style="--card-color: #4B5694; --card-bg-light: rgba(75, 86, 148, 0.12);">
                                <div class="report-icon-wrapper">
                                    <i class="fa fa-calendar-day"></i>
                                </div>
                                <h3>المشتريات اليومية</h3>
                            </a>
                        </div>

                        <!-- المشتريات بالشهر -->
                        <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                            <a href="monthly_purchases.php"
                               class="report-card"
                               style="--card-color: #2E7D32; --card-bg-light: rgba(46, 125, 50, 0.12);">
                                <div class="report-icon-wrapper">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <h3>المشتريات بالشهر</h3>
                            </a>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

<?php include 'includes/footer.php'; ?>
