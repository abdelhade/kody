<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<?php
$from = date('Y-m-d');
$to   = date('Y-m-d');

if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['search_filter'])) {
    if (!empty($_REQUEST['from'])) {
        $from = $_REQUEST['from'];
    }
    if (!empty($_REQUEST['to'])) {
        $to = $_REQUEST['to'];
    }
}

// 3/9 مبيعات، 11 مردود مبيعات، 10+مردود مبيعات = مرتجع POS الملابس
// الخصم = خصم البنود + نصيب البند من خصم رأس الفاتورة
// إجمالي المبيعات/المردود قبل الخصم عشان الصافي = إجمالي − خصم − مردود
$sql = "SELECT
            COALESCE(g.id, 0) AS group_id,
            COALESCE(g.gname, 'بدون مجموعة') AS group_name,
            COALESCE(SUM(CASE WHEN h.pro_tybe IN (3, 9) THEN fd.qty_out ELSE 0 END), 0) AS sales_qty,
            COALESCE(SUM(CASE
                WHEN h.pro_tybe = 11 THEN fd.qty_in
                WHEN h.pro_tybe = 10 AND h.info LIKE '%مردود مبيعات%' THEN fd.qty_in
                ELSE 0 END), 0) AS return_qty,
            COALESCE(SUM(CASE WHEN h.pro_tybe IN (3, 9) THEN
                fd.det_value + COALESCE(fd.discount, 0) * (fd.qty_out / COALESCE(NULLIF(fd.u_val, 0), 1))
                ELSE 0 END), 0) AS total_sales,
            COALESCE(SUM(CASE WHEN h.pro_tybe IN (3, 9) THEN
                COALESCE(fd.discount, 0) * (fd.qty_out / COALESCE(NULLIF(fd.u_val, 0), 1))
                + CASE WHEN COALESCE(h.fat_total, 0) > 0
                    THEN COALESCE(h.fat_disc, 0) * (fd.det_value / h.fat_total)
                    ELSE 0 END
                ELSE 0 END), 0) AS sales_discount,
            COALESCE(SUM(CASE
                WHEN h.pro_tybe = 11 OR (h.pro_tybe = 10 AND h.info LIKE '%مردود مبيعات%') THEN
                    COALESCE(fd.discount, 0) * (fd.qty_in / COALESCE(NULLIF(fd.u_val, 0), 1))
                    + CASE WHEN COALESCE(h.fat_total, 0) > 0
                        THEN COALESCE(h.fat_disc, 0) * (fd.det_value / h.fat_total)
                        ELSE 0 END
                ELSE 0 END), 0) AS return_discount,
            COALESCE(SUM(CASE
                WHEN h.pro_tybe = 11 OR (h.pro_tybe = 10 AND h.info LIKE '%مردود مبيعات%') THEN
                    fd.det_value + COALESCE(fd.discount, 0) * (fd.qty_in / COALESCE(NULLIF(fd.u_val, 0), 1))
                ELSE 0 END), 0) AS total_returns
        FROM fat_details fd
        INNER JOIN ot_head h ON h.id = fd.pro_id
        INNER JOIN myitems i ON i.id = fd.item_id AND IFNULL(i.isdeleted, 0) = 0
        LEFT JOIN item_group g ON g.id = i.group1 AND IFNULL(g.isdeleted, 0) = 0
        WHERE IFNULL(fd.isdeleted, 0) = 0
          AND IFNULL(h.isdeleted, 0) = 0
          AND (
                h.pro_tybe IN (3, 9, 11)
                OR (h.pro_tybe = 10 AND h.info LIKE '%مردود مبيعات%')
              )
          AND h.pro_date BETWEEN ? AND ?
        GROUP BY COALESCE(g.id, 0), COALESCE(g.gname, 'بدون مجموعة')
        HAVING sales_qty > 0 OR total_sales > 0 OR return_qty > 0 OR total_returns > 0
        ORDER BY (
            COALESCE(SUM(CASE WHEN h.pro_tybe IN (3, 9) THEN
                fd.det_value
                - CASE WHEN COALESCE(h.fat_total, 0) > 0
                    THEN COALESCE(h.fat_disc, 0) * (fd.det_value / h.fat_total)
                    ELSE 0 END
                ELSE 0 END), 0)
            - COALESCE(SUM(CASE
                WHEN h.pro_tybe = 11 OR (h.pro_tybe = 10 AND h.info LIKE '%مردود مبيعات%') THEN
                    fd.det_value
                    - CASE WHEN COALESCE(h.fat_total, 0) > 0
                        THEN COALESCE(h.fat_disc, 0) * (fd.det_value / h.fat_total)
                        ELSE 0 END
                ELSE 0 END), 0)
        ) DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param('ss', $from, $to);
$stmt->execute();
$res = $stmt->get_result();

$data = [];
$grand_sales_qty        = 0;
$grand_return_qty       = 0;
$grand_total_sales      = 0;
$grand_sales_discount   = 0;
$grand_return_discount  = 0;
$grand_total_discount   = 0;
$grand_total_returns    = 0;
$grand_total_net        = 0;

while ($row = $res->fetch_assoc()) {
    $total_sales     = floatval($row['total_sales']);
    $sales_discount  = floatval($row['sales_discount']);
    $return_discount = floatval($row['return_discount']);
    $total_discount  = $sales_discount - $return_discount;
    $total_returns   = floatval($row['total_returns']);
    $net_sales       = $total_sales - $total_discount - $total_returns;

    $row['total_sales']     = $total_sales;
    $row['sales_discount']  = $sales_discount;
    $row['return_discount'] = $return_discount;
    $row['total_discount']  = $total_discount;
    $row['total_returns']   = $total_returns;
    $row['net_sales']       = $net_sales;

    $data[] = $row;
    $grand_sales_qty        += floatval($row['sales_qty']);
    $grand_return_qty       += floatval($row['return_qty']);
    $grand_total_sales      += $total_sales;
    $grand_sales_discount   += $sales_discount;
    $grand_return_discount  += $return_discount;
    $grand_total_discount   += $total_discount;
    $grand_total_returns    += $total_returns;
    $grand_total_net        += $net_sales;
}
$stmt->close();

function kody_fmt_qty($q) {
    $q = floatval($q);
    return (abs($q - round($q)) < 0.0001) ? number_format($q, 0) : number_format($q, 3);
}
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark"><i class="fas fa-layer-group text-info ml-2"></i> المبيعات مجموعات</h1>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

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

            <div class="row mb-4">
                <div class="col-lg col-md-4 col-sm-6 col-12 mb-2">
                    <div class="small-box bg-info p-3 text-center text-white shadow-sm" style="border-radius: 10px;">
                        <h6>إجمالي المبيعات</h6>
                        <h4><?= number_format($grand_total_sales, 2) ?></h4>
                    </div>
                </div>
                <div class="col-lg col-md-4 col-sm-6 col-12 mb-2">
                    <div class="small-box bg-warning p-3 text-center text-dark shadow-sm" style="border-radius: 10px;"
                         <?php if ($grand_return_discount > 0): ?>title="خصم مبيعات: <?= number_format($grand_sales_discount, 2) ?> − خصم مردود: <?= number_format($grand_return_discount, 2) ?>"<?php endif; ?>>
                        <h6>إجمالي الخصم</h6>
                        <h4><?= number_format($grand_total_discount, 2) ?></h4>
                    </div>
                </div>
                <div class="col-lg col-md-4 col-sm-6 col-12 mb-2">
                    <div class="small-box bg-danger p-3 text-center text-white shadow-sm" style="border-radius: 10px;">
                        <h6>إجمالي المردودات</h6>
                        <h4><?= number_format($grand_total_returns, 2) ?></h4>
                    </div>
                </div>
                <div class="col-lg col-md-6 col-sm-6 col-12 mb-2">
                    <div class="small-box bg-primary p-3 text-center text-white shadow-sm" style="border-radius: 10px;">
                        <h6>صافي المبيعات</h6>
                        <h4><?= number_format($grand_total_net, 2) ?></h4>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table id="myTable" class="table table-hover table-striped table-bordered mb-0">
                            <thead class="thead-dark text-center">
                                <tr>
                                    <th style="width: 50px;">م</th>
                                    <th>المجموعة</th>
                                    <th>الكمية المباعة</th>
                                    <th>كمية المردود</th>
                                    <th>إجمالي المبيعات</th>
                                    <th class="text-warning">إجمالي الخصم</th>
                                    <th class="text-danger">مردود المبيعات</th>
                                    <th class="text-primary">صافي المبيعات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($data) > 0): ?>
                                    <?php $x = 0; foreach ($data as $row): $x++; ?>
                                        <tr class="text-center">
                                            <td><?= $x ?></td>
                                            <td class="text-right font-weight-bold"><?= htmlspecialchars($row['group_name']) ?></td>
                                            <td><?= kody_fmt_qty($row['sales_qty']) ?></td>
                                            <td class="text-danger">
                                                <?php if (floatval($row['return_qty']) > 0): ?>
                                                    <?= kody_fmt_qty($row['return_qty']) ?>
                                                <?php else: ?>
                                                    <span class="text-muted">—</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="font-weight-bold text-info"><?= number_format($row['total_sales'], 2) ?></td>
                                            <td class="font-weight-bold" style="color: #d97706;">
                                                <?php if ($row['total_discount'] > 0): ?>
                                                    <span <?php if ($row['return_discount'] > 0): ?>title="خصم مبيعات: <?= number_format($row['sales_discount'], 2) ?> − خصم مردود: <?= number_format($row['return_discount'], 2) ?>"<?php endif; ?>>
                                                        − <?= number_format($row['total_discount'], 2) ?>
                                                    </span>
                                                <?php elseif ($row['total_discount'] < 0): ?>
                                                    <span <?php if ($row['return_discount'] > 0): ?>title="خصم مبيعات: <?= number_format($row['sales_discount'], 2) ?> − خصم مردود: <?= number_format($row['return_discount'], 2) ?>"<?php endif; ?>>
                                                        + <?= number_format(abs($row['total_discount']), 2) ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-muted">—</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="font-weight-bold text-danger">
                                                <?php if ($row['total_returns'] > 0): ?>
                                                    <span>− <?= number_format($row['total_returns'], 2) ?></span>
                                                <?php else: ?>
                                                    <span class="text-muted">—</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="font-weight-bold text-primary"><?= number_format($row['net_sales'], 2) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center text-muted p-4">
                                            <i class="fas fa-exclamation-circle fa-2x mb-2 d-block"></i>
                                            لا توجد بيانات مبيعات للمجموعات خلال هذه الفترة.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                            <?php if (count($data) > 0): ?>
                                <tfoot class="bg-light font-weight-bold text-center">
                                    <tr>
                                        <td colspan="2" class="text-right">الإجمالي الكلي:</td>
                                        <td><?= kody_fmt_qty($grand_sales_qty) ?></td>
                                        <td class="text-danger"><?= $grand_return_qty > 0 ? kody_fmt_qty($grand_return_qty) : '—' ?></td>
                                        <td class="text-info"><?= number_format($grand_total_sales, 2) ?></td>
                                        <td style="color: #d97706;">
                                            <?php if ($grand_total_discount > 0): ?>
                                                <span <?php if ($grand_return_discount > 0): ?>title="خصم مبيعات: <?= number_format($grand_sales_discount, 2) ?> − خصم مردود: <?= number_format($grand_return_discount, 2) ?>"<?php endif; ?>>
                                                    − <?= number_format($grand_total_discount, 2) ?>
                                                </span>
                                            <?php elseif ($grand_total_discount < 0): ?>
                                                <span <?php if ($grand_return_discount > 0): ?>title="خصم مبيعات: <?= number_format($grand_sales_discount, 2) ?> − خصم مردود: <?= number_format($grand_return_discount, 2) ?>"<?php endif; ?>>
                                                    + <?= number_format(abs($grand_total_discount), 2) ?>
                                                </span>
                                            <?php else: ?>
                                                —
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-danger"><?= $grand_total_returns > 0 ? '− ' . number_format($grand_total_returns, 2) : '—' ?></td>
                                        <td class="text-primary" style="font-size: 1.1rem;"><?= number_format($grand_total_net, 2) ?></td>
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
