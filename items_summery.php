<?php include('includes/header.php'); ?>
<link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<?php include('includes/navbar.php'); ?>
<?php include('includes/sidebar.php'); ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body" id="horsReport">
                    <h1>تقرير المبيعات أصناف</h1>

                    <!-- ✅ نموذج الفلترة -->
                    <form method="GET" class="row mb-4">
                        <div class="col-md-3">
                            <label>من تاريخ:</label>
                            <input type="date" name="from" class="form-control" value="<?= $_GET['from'] ?? '' ?>">
                        </div>
                        <div class="col-md-3">
                            <label>إلى تاريخ:</label>
                            <input type="date" name="to" class="form-control" value="<?= $_GET['to'] ?? '' ?>">
                        </div>
                        <div class="col-md-2">
                            <label>فلتر الكمية:</label>
                            <?php $qtyFilter = $_GET['qtyFilter'] ?? 'all'; ?>
                            <select name="qtyFilter" class="form-control">
                                <option value="all" <?= $qtyFilter == 'all' ? 'selected' : '' ?>>عرض الكل</option>
                                <option value="greater" <?= $qtyFilter == 'greater' ? 'selected' : '' ?>>أكبر من صفر</option>
                                <option value="less" <?= $qtyFilter == 'less' ? 'selected' : '' ?>>أقل من أو يساوي صفر</option>
                                <option value="equal" <?= $qtyFilter == 'equal' ? 'selected' : '' ?>>يساوي صفر</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>ترتيب حسب:</label>
                            <?php $sortBy = $_GET['sort'] ?? 'qty_desc'; ?>
                            <select name="sort" class="form-control">
                                <option value="qty_desc" <?= $sortBy == 'qty_desc' ? 'selected' : '' ?>>الأكثر مبيعاً (كمية)</option>
                                <option value="qty_asc" <?= $sortBy == 'qty_asc' ? 'selected' : '' ?>>الأقل مبيعاً (كمية)</option>
                                <option value="val_desc" <?= $sortBy == 'val_desc' ? 'selected' : '' ?>>الأعلى إيراداً (قيمة)</option>
                                <option value="profit_desc" <?= $sortBy == 'profit_desc' ? 'selected' : '' ?>>الأعلى ربحاً</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>بحث بالاسم أو الكود:</label>
                            <input type="text" name="search" class="form-control" placeholder="بحث..." value="<?= $_GET['search'] ?? '' ?>">
                        </div>
                        <div class="col-md-1">
                            <label style="visibility: hidden;">عرض</label>
                            <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-search"></i></button>
                        </div>
                    </form>

                    <!-- ✅ جدول البيانات -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="itemsSummaryTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>الكود</th>
                                    <th>اسم الصنف</th>
                                    <th>ك المبيعات</th>
                                    <th>ق المبيعات</th>
                                    <th>متوسط البيع</th>
                                    <th>س البيع</th>
                                    <th>س ش متوسط</th>
                                    <th>الربح</th>
                                    <th>الربح/ المبيعات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $limit = 100;
                                $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
                                $offset = ($page - 1) * $limit;

                                $from = $_GET['from'] ?? null;
                                $to = $_GET['to'] ?? null;
                                $search = $_GET['search'] ?? null;

                                $dateFilter = "";
                                if ($from && $to) {
                                    $dateFilter = "AND d.crtime BETWEEN '$from 00:00:00' AND '$to 23:59:59'";
                                } elseif ($from) {
                                    $dateFilter = "AND d.crtime >= '$from 00:00:00'";
                                } elseif ($to) {
                                    $dateFilter = "AND d.crtime <= '$to 23:59:59'";
                                }

                                $searchFilter = "";
                                if ($search) {
                                    $searchEsc = $conn->real_escape_string($search);
                                    $searchFilter = "AND (i.iname LIKE '%$searchEsc%' OR i.code LIKE '%$searchEsc%')";
                                }

                                $havingFilter = "";
                                if ($qtyFilter === 'greater') {
                                    $havingFilter = "HAVING total_qty > 0";
                                } elseif ($qtyFilter === 'less') {
                                    $havingFilter = "HAVING total_qty <= 0";
                                } elseif ($qtyFilter === 'equal') {
                                    $havingFilter = "HAVING total_qty = 0";
                                }

                                // جلب الإجمالي لعمل Pagination
                                $countQuery = "
                                    SELECT COUNT(*) as total FROM (
                                        SELECT i.id, COALESCE(SUM(d.qty_out), 0) AS total_qty
                                        FROM myitems i 
                                        LEFT JOIN fat_details d ON d.item_id = i.id 
                                            AND d.isdeleted = 0 
                                            AND (d.fat_tybe = 9 OR d.fat_tybe = 3)
                                            $dateFilter
                                        WHERE i.isdeleted = 0 $searchFilter
                                        GROUP BY i.id
                                        $havingFilter
                                    ) as sub
                                ";
                                $total_items = $conn->query($countQuery)->fetch_assoc()['total'];
                                $total_pages = ceil($total_items / $limit);

                                // تحديد الترتيب
                                $orderBy = "ORDER BY total_qty DESC"; // افتراضي
                                if ($sortBy === 'qty_asc') {
                                    $orderBy = "ORDER BY total_qty ASC";
                                } elseif ($sortBy === 'val_desc') {
                                    $orderBy = "ORDER BY total_value DESC";
                                } elseif ($sortBy === 'profit_desc') {
                                    $orderBy = "ORDER BY (COALESCE(SUM(d.det_value), 0) - (COALESCE(SUM(d.qty_out), 0) * i.cost_price)) DESC";
                                }

                                // الاستعلام الرئيسي
                                $query = "
                                    SELECT 
                                        i.id, i.code, i.iname, i.price1, i.cost_price,
                                        COALESCE(SUM(d.qty_out), 0) AS total_qty,
                                        COALESCE(SUM(d.det_value), 0) AS total_value
                                    FROM myitems i 
                                    LEFT JOIN fat_details d ON d.item_id = i.id 
                                        AND d.isdeleted = 0 
                                        AND (d.fat_tybe = 9 OR d.fat_tybe = 3)
                                        $dateFilter
                                    WHERE i.isdeleted = 0 $searchFilter
                                    GROUP BY i.id
                                    $havingFilter
                                    $orderBy
                                    LIMIT $limit OFFSET $offset
                                ";

                                $resitm = $conn->query($query);
                                $x = $offset;

                                if ($resitm && $resitm->num_rows > 0) {
                                    while ($rowitm = $resitm->fetch_assoc()) {
                                        $x++;
                                        $qty = floatval($rowitm['total_qty']);
                                        $val = floatval($rowitm['total_value']);
                                        $costPrice = floatval($rowitm['cost_price']);
                                        $price1 = floatval($rowitm['price1']);
                                        
                                        $price = 0;
                                        $profit = 0;
                                        $salesProfit = 0;
                                        $priceClass = '';

                                        if ($qty > 0) {
                                            $price = $val / $qty;
                                            $profit = ($price - $costPrice) * $qty;
                                            $salesProfit = ($val > 0) ? ($profit / $val) * 100 : 0;

                                            if ($price > $price1) {
                                                $priceClass = 'bg-success text-white';
                                            } elseif ($price < $price1) {
                                                $priceClass = 'bg-danger text-white';
                                            }
                                        }
                                ?>
                                        <tr>
                                            <td class="text-center"><?= $x ?></td>
                                            <td class="text-center"><?= htmlspecialchars($rowitm['code']) ?></td>
                                            <td class="text-center"><a class="btn btn-light btn-block" href="item_summery.php?id=<?= $rowitm['id']?>"><?= htmlspecialchars($rowitm['iname']) ?></a></td>
                                            <td class="text-center font-weight-bold text-primary"><?= number_format($qty, 2) ?></td>
                                            <td class="text-center"><?= number_format($val, 2) ?></td>
                                            <td class="text-center <?= $priceClass ?>"><?= number_format($price, 2) ?></td>
                                            <td class="text-center"><?= number_format($price1, 2) ?></td>
                                            <td class="text-center"><?= number_format($costPrice, 2) ?></td>
                                            <td class="text-center font-weight-bold text-success"><?= number_format($profit, 2) ?></td>
                                            <td class="text-center"><?= number_format($salesProfit, 2) ?>%</td>
                                        </tr>
                                <?php 
                                    }
                                } else {
                                    echo "<tr><td colspan='10' class='text-center'>لا توجد بيانات</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination Links -->
                <div class="card-footer">
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm justify-content-center mb-0">
                            <?php
                            $filter_params = "";
                            if (!empty($from)) $filter_params .= "&from=$from";
                            if (!empty($to)) $filter_params .= "&to=$to";
                            if (!empty($qtyFilter) && $qtyFilter != 'all') $filter_params .= "&qtyFilter=$qtyFilter";
                            if (!empty($sortBy) && $sortBy != 'qty_desc') $filter_params .= "&sort=$sortBy";
                            if (!empty($search)) $filter_params .= "&search=" . urlencode($search);

                            // Previous Button
                            if ($page > 1) {
                                echo '<li class="page-item"><a class="page-link" href="?page='.($page-1).$filter_params.'">&laquo; السابق</a></li>';
                            } else {
                                echo '<li class="page-item disabled"><span class="page-link">&laquo; السابق</span></li>';
                            }

                            // Pages
                            $start_page = max(1, $page - 2);
                            $end_page = min($total_pages, $page + 2);

                            if ($start_page > 1) {
                                echo '<li class="page-item"><a class="page-link" href="?page=1'.$filter_params.'">1</a></li>';
                                if ($start_page > 2) {
                                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                }
                            }

                            for ($i = $start_page; $i <= $end_page; $i++) {
                                $active = $i == $page ? 'active' : '';
                                echo "<li class='page-item $active'><a class='page-link' href='?page=$i$filter_params'>$i</a></li>";
                            }

                            if ($end_page < $total_pages) {
                                if ($end_page < $total_pages - 1) {
                                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                }
                                echo '<li class="page-item"><a class="page-link" href="?page='.$total_pages.$filter_params.'">'.$total_pages.'</a></li>';
                            }

                            // Next Button
                            if ($page < $total_pages) {
                                echo '<li class="page-item"><a class="page-link" href="?page='.($page+1).$filter_params.'">التالي &raquo;</a></li>';
                            } else {
                                echo '<li class="page-item disabled"><span class="page-link">التالي &raquo;</span></li>';
                            }
                            ?>
                        </ul>
                        <div class="text-center mt-2">
                            <small class="text-muted">عرض <?= ($offset + 1) ?> - <?= min($offset + $limit, $total_items) ?> من أصل <?= $total_items ?> صنف</small>
                        </div>
                    </nav>
                </div>

            </div>
        </div>
    </section>
</div>

<!-- Scripts -->
<?php include('includes/footer.php'); ?>
