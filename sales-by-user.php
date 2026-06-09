<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<?php
// Default date range: start of current month to today
$from = date('Y-m-01');
$to   = date('Y-m-d');

if ($_SERVER['REQUEST_METHOD'] == 'POST' || isset($_GET['search_filter'])) {
    if (!empty($_REQUEST['from'])) {
        $from = $_REQUEST['from'];
    }
    if (!empty($_REQUEST['to'])) {
        $to = $_REQUEST['to'];
    }
}

// Retrieve the commission percentage from settings
$user_commission_pct = floatval($rowstg['user_commission'] ?? 0);

// Query to get sales by user
$sql = "SELECT h.user, u.uname as user_name, SUM(h.pro_value) as total_sales
        FROM ot_head h
        LEFT JOIN users u ON h.user = u.id
        WHERE (h.pro_tybe = 9 OR h.pro_tybe = 3)
          AND (h.isdeleted != 1 OR h.isdeleted IS NULL)
          AND h.pro_date BETWEEN ? AND ?
          AND h.user > 0
        GROUP BY h.user, u.uname
        ORDER BY total_sales DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $from, $to);
$stmt->execute();
$res = $stmt->get_result();

$data = [];
$grand_total_sales = 0;
$grand_total_commission = 0;

while ($row = $res->fetch_assoc()) {
    $total_sales = floatval($row['total_sales']);
    $commission_val = ($total_sales * $user_commission_pct) / 100;
    
    $row['commission_pct'] = $user_commission_pct;
    $row['commission_val'] = $commission_val;
    
    $data[] = $row;
    $grand_total_sales += $total_sales;
    $grand_total_commission += $commission_val;
}
$stmt->close();
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark"><i class="fas fa-users-cog text-info ml-2"></i> تحقيق مبيعات المستخدمين</h1>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            
            <!-- Filters Card -->
            <div class="card card-outline card-info shadow-sm mb-4">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-filter ml-2"></i> خيارات البحث</h3>
                </div>
                <div class="card-body">
                    <form action="" method="get">
                        <input type="hidden" name="search_filter" value="1">
                        <div class="row align-items-end">
                            <div class="col-md-4 col-sm-6 col-12 mb-2">
                                <label for="from">من تاريخ:</label>
                                <input type="date" class="form-control" id="from" name="from" value="<?= htmlspecialchars($from) ?>">
                            </div>
                            <div class="col-md-4 col-sm-6 col-12 mb-2">
                                <label for="to">إلى تاريخ:</label>
                                <input type="date" class="form-control" id="to" name="to" value="<?= htmlspecialchars($to) ?>">
                            </div>
                            <div class="col-md-4 col-12 mb-2">
                                <button type="submit" class="btn btn-info btn-block">
                                    <i class="fa fa-search ml-1"></i> عرض التقرير
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Commission Info Alert -->
            <div class="alert alert-secondary border-0 shadow-sm d-flex align-items-center justify-content-between p-3 mb-4" style="background-color: #f8f9fa;">
                <div>
                    <i class="fas fa-percent text-info ml-2" style="font-size: 1.25rem;"></i>
                    <strong>نسبة عمولة المستخدمين الافتراضية:</strong> 
                    <span class="badge badge-info px-3 py-2" style="font-size: 1rem;"><?= number_format($user_commission_pct, 2) ?> %</span>
                </div>
                <a href="setting.php" class="btn btn-sm btn-outline-info">
                    <i class="fas fa-cog ml-1"></i> تعديل النسبة من الإعدادات
                </a>
            </div>

            <!-- Summary Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-6 col-12">
                    <div class="small-box bg-info p-3 text-center text-white shadow-sm" style="border-radius: 10px;">
                        <h5>إجمالي مبيعات المستخدمين</h5>
                        <h3><?= number_format($grand_total_sales, 2) ?> ج.م</h3>
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <div class="small-box bg-success p-3 text-center text-white shadow-sm" style="border-radius: 10px;">
                        <h5>إجمالي عمولات المستخدمين</h5>
                        <h3><?= number_format($grand_total_commission, 2) ?> ج.م</h3>
                    </div>
                </div>
            </div>

            <!-- Data Table Card -->
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped table-bordered mb-0">
                            <thead class="thead-dark text-center">
                                <tr>
                                    <th style="width: 80px;">م</th>
                                    <th>المستخدم</th>
                                    <th>إجمالي المبيعات</th>
                                    <th>العمولة (%)</th>
                                    <th>النسبة (قيمة العمولة)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($data) > 0): ?>
                                    <?php $x = 0; foreach ($data as $row): $x++; ?>
                                        <tr class="text-center">
                                            <td><?= $x ?></td>
                                            <td class="text-right font-weight-bold"><?= htmlspecialchars($row['user_name']) ?></td>
                                            <td class="font-weight-bold text-primary"><?= number_format($row['total_sales'], 2) ?> ج.م</td>
                                            <td><span class="badge badge-info font-weight-normal"><?= number_format($row['commission_pct'], 2) ?> %</span></td>
                                            <td class="font-weight-bold text-success"><?= number_format($row['commission_val'], 2) ?> ج.م</td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted p-4">
                                            <i class="fas fa-exclamation-circle fa-2x mb-2 d-block"></i>
                                            لا توجد بيانات مبيعات للمستخدمين خلال هذه الفترة.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                            <?php if (count($data) > 0): ?>
                                <tfoot class="bg-light font-weight-bold text-center">
                                    <tr>
                                        <td colspan="2" class="text-right">الإجمالي الكلي:</td>
                                        <td class="text-primary" style="font-size: 1.1rem;"><?= number_format($grand_total_sales, 2) ?> ج.م</td>
                                        <td>-</td>
                                        <td class="text-success" style="font-size: 1.1rem;"><?= number_format($grand_total_commission, 2) ?> ج.م</td>
                                    </tr>
                                </tfoot>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

<?php include 'includes/footer.php'; ?>
