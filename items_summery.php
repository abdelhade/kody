<?php include('includes/header.php'); ?>
<link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<?php include('includes/navbar.php'); ?>
<?php include('includes/sidebar.php'); ?>

<?php
// إعداد خيارات الترتيب والـ SQL الآمن
$sortOptions = [
    'qty_desc' => ['label' => 'الأكثر مبيعاً (كمية)', 'sql' => 'ORDER BY total_qty DESC, total_value DESC'],
    'qty_asc' => ['label' => 'الأقل مبيعاً (كمية)', 'sql' => 'ORDER BY total_qty ASC, total_value ASC'],
    'val_desc' => ['label' => 'الأعلى إيراداً (قيمة)', 'sql' => 'ORDER BY total_value DESC, total_qty DESC'],
    'val_asc' => ['label' => 'الأقل إيراداً (قيمة)', 'sql' => 'ORDER BY total_value ASC, total_qty ASC'],
    'profit_desc' => ['label' => 'الأعلى ربحاً', 'sql' => 'ORDER BY (COALESCE(SUM(d.det_value), 0) - (COALESCE(SUM(d.qty_out), 0) * i.cost_price)) DESC'],
    'profit_asc' => ['label' => 'الأقل ربحاً', 'sql' => 'ORDER BY (COALESCE(SUM(d.det_value), 0) - (COALESCE(SUM(d.qty_out), 0) * i.cost_price)) ASC'],
    'margin_desc' => ['label' => 'الأعلى نسبة ربح %', 'sql' => 'ORDER BY (CASE WHEN COALESCE(SUM(d.det_value), 0) > 0 THEN ((COALESCE(SUM(d.det_value), 0) - (COALESCE(SUM(d.qty_out), 0) * i.cost_price)) / COALESCE(SUM(d.det_value), 0)) * 100 ELSE 0 END) DESC'],
    'margin_asc' => ['label' => 'الأقل نسبة ربح %', 'sql' => 'ORDER BY (CASE WHEN COALESCE(SUM(d.det_value), 0) > 0 THEN ((COALESCE(SUM(d.det_value), 0) - (COALESCE(SUM(d.qty_out), 0) * i.cost_price)) / COALESCE(SUM(d.det_value), 0)) * 100 ELSE 0 END) ASC'],
    'name_asc' => ['label' => 'اسم الصنف (أ - ي)', 'sql' => 'ORDER BY i.iname ASC'],
    'name_desc' => ['label' => 'اسم الصنف (ي - أ)', 'sql' => 'ORDER BY i.iname DESC'],
    'code_asc' => ['label' => 'الكود (تصاعدي)', 'sql' => 'ORDER BY i.code ASC'],
    'code_desc' => ['label' => 'الكود (تنازلي)', 'sql' => 'ORDER BY i.code DESC'],
    'avg_price_desc' => ['label' => 'متوسط سعر البيع (الأعلى)', 'sql' => 'ORDER BY (CASE WHEN COALESCE(SUM(d.qty_out), 0) > 0 THEN COALESCE(SUM(d.det_value), 0) / SUM(d.qty_out) ELSE 0 END) DESC'],
    'avg_price_asc' => ['label' => 'متوسط سعر البيع (الأقل)', 'sql' => 'ORDER BY (CASE WHEN COALESCE(SUM(d.qty_out), 0) > 0 THEN COALESCE(SUM(d.det_value), 0) / SUM(d.qty_out) ELSE 0 END) ASC'],
    'price1_desc' => ['label' => 'سعر البيع (الأعلى)', 'sql' => 'ORDER BY i.price1 DESC'],
    'price1_asc' => ['label' => 'سعر البيع (الأقل)', 'sql' => 'ORDER BY i.price1 ASC'],
    'cost_desc' => ['label' => 'سعر الشراء المتوسط (الأعلى)', 'sql' => 'ORDER BY i.cost_price DESC'],
    'cost_asc' => ['label' => 'سعر الشراء المتوسط (الأقل)', 'sql' => 'ORDER BY i.cost_price ASC'],
];

$from = $_GET['from'] ?? null;
$to = $_GET['to'] ?? null;
$search = $_GET['search'] ?? null;
$qtyFilter = $_GET['qtyFilter'] ?? 'all';
$sortBy = $_GET['sort'] ?? 'qty_desc';
if (!isset($sortOptions[$sortBy])) {
    $sortBy = 'qty_desc';
}

$limit = isset($_GET['limit']) && is_numeric($_GET['limit']) && (int)$_GET['limit'] > 0 ? (int)$_GET['limit'] : 100;
$page = isset($_GET['page']) && is_numeric($_GET['page']) && (int)$_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// دالة لإنشاء عنوان عمود قابل للترتيب
function renderSortTh($colIdx, $type, $title, $descKey, $ascKey, $currentSort, $defaultDir = 'desc') {
    global $from, $to, $qtyFilter, $search, $limit;
    
    $isCurrent = ($currentSort === $descKey || $currentSort === $ascKey);
    $currentDir = ($currentSort === $ascKey) ? 'asc' : (($currentSort === $descKey) ? 'desc' : '');
    
    if ($currentSort === $descKey) {
        $nextSort = $ascKey;
    } elseif ($currentSort === $ascKey) {
        $nextSort = $descKey;
    } else {
        $nextSort = ($defaultDir === 'asc') ? $ascKey : $descKey;
    }
    
    $query = [
        'page' => 1,
        'sort' => $nextSort
    ];
    if (!empty($from)) $query['from'] = $from;
    if (!empty($to)) $query['to'] = $to;
    if (!empty($qtyFilter) && $qtyFilter !== 'all') $query['qtyFilter'] = $qtyFilter;
    if (!empty($search)) $query['search'] = $search;
    if (!empty($limit) && $limit != 100) $query['limit'] = $limit;
    
    $url = '?' . http_build_query($query);
    
    if ($currentDir === 'asc') {
        $icon = '<i class="fas fa-sort-up text-primary sort-indicator ml-1"></i>';
    } elseif ($currentDir === 'desc') {
        $icon = '<i class="fas fa-sort-down text-primary sort-indicator ml-1"></i>';
    } else {
        $icon = '<i class="fas fa-sort text-muted sort-indicator ml-1" style="opacity: 0.35;"></i>';
    }
    
    $thClass = 'sortable-col text-center ' . ($isCurrent ? 'table-primary-subtle font-weight-bold' : '');
    
    return '<th class="' . $thClass . '" data-col-idx="' . $colIdx . '" data-type="' . $type . '" data-current-dir="' . $currentDir . '" data-desc-key="' . $descKey . '" data-asc-key="' . $ascKey . '">
        <a href="' . htmlspecialchars($url) . '" class="sort-link d-flex justify-content-between align-items-center text-nowrap ' . ($isCurrent ? 'font-weight-bold text-primary' : 'text-dark') . '" style="text-decoration:none;" title="انقر للترتيب الشامل لقاعدة البيانات | Shift+نقر للفرز الفوري للصفحة الحالية">
            <span>' . $title . '</span>
            ' . $icon . '
        </a>
    </th>';
}
?>

<style>
.sortable-col {
    cursor: pointer;
    user-select: none;
    transition: background-color 0.15s ease;
}
.sortable-col:hover {
    background-color: #f1f5f9 !important;
}
.sortable-col .sort-link {
    cursor: pointer;
    color: inherit;
    gap: 6px;
    padding: 2px 4px;
}
.sortable-col .sort-link:hover {
    text-decoration: none;
}
.table-primary-subtle {
    background-color: #e8f0fe !important;
}
.btn-client-active {
    background-color: #28a745 !important;
    color: #fff !important;
    border-color: #28a745 !important;
}
</style>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="card shadow-sm">
                <div class="card-body" id="horsReport">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-chart-pie text-primary ml-2"></i> تقرير المبيعات أصناف</h1>
                    </div>

                    <!-- ✅ نموذج الفلترة والترتيب -->
                    <form method="GET" class="mb-4 bg-light p-3 rounded border">
                        <div class="form-row align-items-end">
                            <div class="col-lg-2 col-md-4 col-sm-6 mb-2">
                                <label class="small font-weight-bold">من تاريخ:</label>
                                <input type="date" name="from" class="form-control form-control-sm" value="<?= htmlspecialchars($_GET['from'] ?? '') ?>">
                            </div>
                            <div class="col-lg-2 col-md-4 col-sm-6 mb-2">
                                <label class="small font-weight-bold">إلى تاريخ:</label>
                                <input type="date" name="to" class="form-control form-control-sm" value="<?= htmlspecialchars($_GET['to'] ?? '') ?>">
                            </div>
                            <div class="col-lg-2 col-md-4 col-sm-6 mb-2">
                                <label class="small font-weight-bold">فلتر الكمية:</label>
                                <select name="qtyFilter" class="form-control form-control-sm">
                                    <option value="all" <?= $qtyFilter == 'all' ? 'selected' : '' ?>>عرض الكل</option>
                                    <option value="greater" <?= $qtyFilter == 'greater' ? 'selected' : '' ?>>أكبر من صفر</option>
                                    <option value="less" <?= $qtyFilter == 'less' ? 'selected' : '' ?>>أقل من أو يساوي صفر</option>
                                    <option value="equal" <?= $qtyFilter == 'equal' ? 'selected' : '' ?>>يساوي صفر</option>
                                </select>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-6 mb-2">
                                <label class="small font-weight-bold">ترتيب حسب:</label>
                                <select name="sort" class="form-control form-control-sm" id="sortSelect">
                                    <?php foreach ($sortOptions as $key => $opt): ?>
                                        <option value="<?= $key ?>" <?= $sortBy === $key ? 'selected' : '' ?>><?= $opt['label'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-lg-1 col-md-2 col-sm-6 mb-2">
                                <label class="small font-weight-bold">بالصفحة:</label>
                                <select name="limit" class="form-control form-control-sm">
                                    <option value="50" <?= $limit == 50 ? 'selected' : '' ?>>50</option>
                                    <option value="100" <?= $limit == 100 ? 'selected' : '' ?>>100</option>
                                    <option value="250" <?= $limit == 250 ? 'selected' : '' ?>>250</option>
                                    <option value="500" <?= $limit == 500 ? 'selected' : '' ?>>500</option>
                                    <option value="1000" <?= $limit == 1000 ? 'selected' : '' ?>>1000</option>
                                </select>
                            </div>
                            <div class="col-lg-2 col-md-4 col-sm-12 mb-2">
                                <label class="small font-weight-bold">بحث بالاسم أو الكود:</label>
                                <div class="input-group input-group-sm">
                                    <input type="text" name="search" class="form-control" placeholder="بحث..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-primary" title="بحث وتصفية"><i class="fas fa-search"></i></button>
                                        <a href="items_summery.php" class="btn btn-outline-secondary" title="إعادة ضبط"><i class="fas fa-undo"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- شريط أدوات الجدول ومعلومات الفرز -->
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-2" style="gap: 8px;">
                        <div class="text-muted small">
                            <i class="fas fa-info-circle text-info"></i>
                            <span>انقر على عنوان أي عمود للترتيب الشامل لقاعدة البيانات.</span>
                            <span class="badge badge-light border ml-1 font-weight-normal">الترتيب الحالي: <strong class="text-primary"><?= htmlspecialchars($sortOptions[$sortBy]['label']) ?></strong></span>
                        </div>
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-secondary" id="btnClientSortToggle" title="تبديل بين الفرز الشامل (السيرفر) والفرز الفوري (المتصفح للصفحة الحالية)">
                                <i class="fas fa-bolt text-warning mr-1"></i> <span id="clientSortStatus">تفعيل الفرز الفوري للصفحة الحالية</span>
                            </button>
                        </div>
                    </div>

                    <!-- ✅ جدول البيانات -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover mb-0" id="itemsSummaryTable">
                            <thead class="thead-light">
                                <tr>
                                    <th class="text-center" style="width: 50px;">#</th>
                                    <?= renderSortTh(1, 'text', 'الكود', 'code_desc', 'code_asc', $sortBy, 'asc') ?>
                                    <?= renderSortTh(2, 'text', 'اسم الصنف', 'name_desc', 'name_asc', $sortBy, 'asc') ?>
                                    <?= renderSortTh(3, 'num', 'ك المبيعات', 'qty_desc', 'qty_asc', $sortBy, 'desc') ?>
                                    <?= renderSortTh(4, 'num', 'ق المبيعات', 'val_desc', 'val_asc', $sortBy, 'desc') ?>
                                    <?= renderSortTh(5, 'num', 'متوسط البيع', 'avg_price_desc', 'avg_price_asc', $sortBy, 'desc') ?>
                                    <?= renderSortTh(6, 'num', 'س البيع', 'price1_desc', 'price1_asc', $sortBy, 'desc') ?>
                                    <?= renderSortTh(7, 'num', 'س ش متوسط', 'cost_desc', 'cost_asc', $sortBy, 'desc') ?>
                                    <?= renderSortTh(8, 'num', 'الربح', 'profit_desc', 'profit_asc', $sortBy, 'desc') ?>
                                    <?= renderSortTh(9, 'num', 'الربح/ المبيعات', 'margin_desc', 'margin_asc', $sortBy, 'desc') ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $dateFilter = "";
                                if ($from && $to) {
                                    $fromEsc = $conn->real_escape_string($from);
                                    $toEsc = $conn->real_escape_string($to);
                                    $dateFilter = "AND d.crtime BETWEEN '$fromEsc 00:00:00' AND '$toEsc 23:59:59'";
                                } elseif ($from) {
                                    $fromEsc = $conn->real_escape_string($from);
                                    $dateFilter = "AND d.crtime >= '$fromEsc 00:00:00'";
                                } elseif ($to) {
                                    $toEsc = $conn->real_escape_string($to);
                                    $dateFilter = "AND d.crtime <= '$toEsc 23:59:59'";
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
                                $total_items = (int)($conn->query($countQuery)->fetch_assoc()['total'] ?? 0);
                                $total_pages = max(1, (int)ceil($total_items / $limit));

                                $orderBy = $sortOptions[$sortBy]['sql'];

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
                                            <td class="text-center" data-sort="<?= $x ?>"><?= $x ?></td>
                                            <td class="text-center font-weight-bold" data-sort="<?= htmlspecialchars($rowitm['code']) ?>"><?= htmlspecialchars($rowitm['code']) ?></td>
                                            <td class="text-center" data-sort="<?= htmlspecialchars($rowitm['iname']) ?>">
                                                <a class="btn btn-light btn-block text-right" href="item_summery.php?id=<?= $rowitm['id']?>">
                                                    <?= htmlspecialchars($rowitm['iname']) ?>
                                                </a>
                                            </td>
                                            <td class="text-center font-weight-bold text-primary" data-sort="<?= $qty ?>"><?= number_format($qty, 2) ?></td>
                                            <td class="text-center" data-sort="<?= $val ?>"><?= number_format($val, 2) ?></td>
                                            <td class="text-center <?= $priceClass ?>" data-sort="<?= $price ?>"><?= number_format($price, 2) ?></td>
                                            <td class="text-center" data-sort="<?= $price1 ?>"><?= number_format($price1, 2) ?></td>
                                            <td class="text-center" data-sort="<?= $costPrice ?>"><?= number_format($costPrice, 2) ?></td>
                                            <td class="text-center font-weight-bold text-success" data-sort="<?= $profit ?>"><?= number_format($profit, 2) ?></td>
                                            <td class="text-center" data-sort="<?= $salesProfit ?>"><?= number_format($salesProfit, 2) ?>%</td>
                                        </tr>
                                <?php 
                                    }
                                } else {
                                    echo "<tr><td colspan='10' class='text-center py-4 text-muted'>لا توجد بيانات مطابقة لمعايير البحث</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination Links -->
                <div class="card-footer bg-white border-top">
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm justify-content-center mb-0">
                            <?php
                            $filter_params = "";
                            if (!empty($from)) $filter_params .= "&from=" . urlencode($from);
                            if (!empty($to)) $filter_params .= "&to=" . urlencode($to);
                            if (!empty($qtyFilter) && $qtyFilter != 'all') $filter_params .= "&qtyFilter=" . urlencode($qtyFilter);
                            if (!empty($sortBy) && $sortBy != 'qty_desc') $filter_params .= "&sort=" . urlencode($sortBy);
                            if (!empty($search)) $filter_params .= "&search=" . urlencode($search);
                            if (!empty($limit) && $limit != 100) $filter_params .= "&limit=" . urlencode($limit);

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
                            <small class="text-muted">عرض <?= $total_items > 0 ? ($offset + 1) : 0 ?> - <?= min($offset + $limit, $total_items) ?> من أصل <?= number_format($total_items) ?> صنف</small>
                        </div>
                    </nav>
                </div>

            </div>
        </div>
    </section>
</div>

<!-- Scripts -->
<?php include('includes/footer.php'); ?>

<script>
$(document).ready(function() {
    var isClientSortMode = false;
    var $btnToggle = $('#btnClientSortToggle');
    var $clientSortStatus = $('#clientSortStatus');
    var $table = $('#itemsSummaryTable');

    // تبديل وضع الفرز الفوري
    $btnToggle.on('click', function() {
        isClientSortMode = !isClientSortMode;
        if (isClientSortMode) {
            $btnToggle.addClass('btn-client-active').removeClass('btn-outline-secondary');
            $clientSortStatus.text('الفرز الفوري مفعل (انقر للفرز الفوري بدون إعادة تحميل)');
            if (window.toastr) {
                toastr.info('تم تفعيل الفرز الفوري للصفحة الحالية');
            }
        } else {
            $btnToggle.removeClass('btn-client-active').addClass('btn-outline-secondary');
            $clientSortStatus.text('تفعيل الفرز الفوري للصفحة الحالية');
            if (window.toastr) {
                toastr.info('تم الرجوع للفرز الشامل لقاعدة البيانات');
            }
        }
    });

    // معالجة النقر على عناوين الأعمدة
    $table.find('thead th.sortable-col').on('click', function(e) {
        // إذا كان الفرز الفوري مفعل أو المستخدم ضاغط Shift/Alt
        if (isClientSortMode || e.shiftKey || e.altKey) {
            e.preventDefault();
            var $th = $(this);
            var colIdx = parseInt($th.data('col-idx'), 10);
            var colType = $th.data('type') || 'num';
            var currentDir = $th.data('current-dir') || '';
            var nextDir = (currentDir === 'asc') ? 'desc' : 'asc';

            sortCurrentTableRows(colIdx, colType, nextDir);

            // تحديث مؤشرات الأيقونات في الـ thead
            $table.find('thead th.sortable-col').each(function() {
                var $header = $(this);
                $header.removeClass('table-primary-subtle');
                $header.data('current-dir', '');
                $header.find('.sort-link').removeClass('font-weight-bold text-primary').addClass('text-dark');
                $header.find('.sort-indicator')
                    .attr('class', 'fas fa-sort text-muted sort-indicator ml-1')
                    .css('opacity', '0.35');
            });

            $th.addClass('table-primary-subtle');
            $th.data('current-dir', nextDir);
            $th.find('.sort-link').addClass('font-weight-bold text-primary').removeClass('text-dark');
            $th.find('.sort-indicator')
                .attr('class', 'fas fa-sort-' + (nextDir === 'asc' ? 'up' : 'down') + ' text-primary sort-indicator ml-1')
                .css('opacity', '1');
        }
    });

    // دالة الفرز الفوري للـ DOM
    function sortCurrentTableRows(colIdx, colType, direction) {
        var $tbody = $table.find('tbody');
        var rows = $tbody.find('tr').get();

        if (rows.length <= 1) return;

        rows.sort(function(a, b) {
            var valA = $(a).children('td').eq(colIdx).attr('data-sort');
            var valB = $(b).children('td').eq(colIdx).attr('data-sort');

            if (valA === undefined) valA = $(a).children('td').eq(colIdx).text().trim();
            if (valB === undefined) valB = $(b).children('td').eq(colIdx).text().trim();

            if (colType === 'num') {
                var numA = parseFloat(valA) || 0;
                var numB = parseFloat(valB) || 0;
                return (direction === 'asc') ? (numA - numB) : (numB - numA);
            } else {
                var strA = String(valA || '').trim();
                var strB = String(valB || '').trim();
                var comp = strA.localeCompare(strB, 'ar', { numeric: true, sensitivity: 'base' });
                return (direction === 'asc') ? comp : -comp;
            }
        });

        $.each(rows, function(index, row) {
            $tbody.append(row);
        });
    }

    // مزامنة التغيير في dropdown الترتيب
    $('#sortSelect').on('change', function() {
        $(this).closest('form').submit();
    });
});
</script>
