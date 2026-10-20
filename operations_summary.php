<?php 
include('includes/header.php'); 
include('includes/navbar.php'); 
include('includes/sidebar.php'); 


$q = isset($_GET['q']) ? $_GET['q'] : "all";  // استقبال قيمة q من GET
$today = date('Y-m-d'); // تعريف اليوم الحالي
$strtdate = isset($_GET['strtdate']) ? $conn->real_escape_string($_GET['strtdate']) : null;
$enddate = isset($_GET['enddate']) ? $conn->real_escape_string($_GET['enddate']) : null;
$search = isset($_GET['search']) ? $_GET['search'] : null;
$filter_delivery = isset($_GET['delivery_person']) ? intval($_GET['delivery_person']) : 0;
$filter_order_type = isset($_GET['order_type']) ? trim($_GET['order_type']) : '';
$filter_pro_tybe = isset($_GET['pro_tybe']) ? intval($_GET['pro_tybe']) : 0;
$allowed_order_types = ['takeaway', 'table', 'delivery', 'dine_in'];
if (!in_array($filter_order_type, $allowed_order_types, true)) {
    $filter_order_type = '';
}

// التحقق من صيغة التاريخ
if ($strtdate && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $strtdate)) $strtdate = null;
if ($enddate && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $enddate)) $enddate = null;

$dateFilter = "";
// سيتم تعريف dateFilter بـ alias ot. في الأسفل مع الـ JOIN

$searchFilter = "";
if ($search) {
    $search = $conn->real_escape_string($search);
    $searchFilter = "AND (ot.id LIKE '%$search%' OR ot.pro_id LIKE '%$search%' OR ot.info LIKE '%$search%' OR ot.jal_name LIKE '%$search%' OR acc1.aname LIKE '%$search%' OR acc2.aname LIKE '%$search%')";
}

$extraFilter = "";
if ($filter_delivery > 0) {
    $extraFilter .= " AND (
        ot.delivery_person_id = $filter_delivery
        OR (
            (ot.delivery_person_id IS NULL OR ot.delivery_person_id = 0)
            AND ot.emp2_id = $filter_delivery
            AND (ot.order_type = 'delivery' OR ot.info LIKE '%دليفري%' OR ot.info LIKE '%مندوب التوصيل%')
        )
    )";
}
if ($filter_order_type !== '') {
    if ($filter_order_type === 'delivery') {
        $extraFilter .= " AND (ot.order_type = 'delivery' OR ot.info LIKE '%نوع الطلب: دليفري%')";
    } elseif ($filter_order_type === 'table') {
        $extraFilter .= " AND (ot.order_type = 'table' OR ot.info LIKE '%نوع الطلب: طاولة%' OR ot.info LIKE '%طاولة:%')";
    } elseif ($filter_order_type === 'takeaway') {
        $extraFilter .= " AND (
            ot.order_type = 'takeaway'
            OR (
                (ot.order_type IS NULL OR ot.order_type = '')
                AND (ot.info IS NULL OR ot.info = '' OR ot.info LIKE '%تيك أواي%')
            )
        )
        AND (ot.order_type IS NULL OR ot.order_type = '' OR ot.order_type = 'takeaway')
        AND (ot.info IS NULL OR (ot.info NOT LIKE '%نوع الطلب: دليفري%' AND ot.info NOT LIKE '%نوع الطلب: طاولة%'))";
    } elseif ($filter_order_type === 'dine_in') {
        $extraFilter .= " AND ot.order_type = 'dine_in'";
    }
}
if ($filter_pro_tybe > 0 && ($q === 'all' || $q === '')) {
    $extraFilter .= " AND ot.pro_tybe = $filter_pro_tybe";
}

// Sorting
$sort = isset($_GET['sort']) ? trim($_GET['sort']) : '';
$dir = isset($_GET['dir']) && strtolower($_GET['dir']) === 'asc' ? 'asc' : 'desc';
$dir_sql = strtoupper($dir);

$sort_map = [
    'id' => 'ot.id',
    'date' => 'ot.crtime',
    'pro_date' => 'ot.pro_date',
    'type' => 'pt.pname',
    'payment' => 'ot.fat_net',
    'value' => 'ot.pro_value',
    'pro_value' => 'ot.pro_value',
    'net' => 'ot.fat_net',
    'fat_net' => 'ot.fat_net',
    'profit' => 'ot.profit',
    'acc1' => 'acc1.aname',
    'acc2' => 'acc2.aname',
    'store' => 'st.aname',
    'emp' => 'emp.aname',
    'driver' => 'drv.aname',
    'user' => 'u.uname',
];

$order_by = "ot.id DESC";
if (!empty($sort) && isset($sort_map[$sort])) {
    $order_by = $sort_map[$sort] . " " . $dir_sql . ", ot.id " . $dir_sql;
}

// Pagination
$limit = 9999;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Base JOIN query for search support (acc1 = supplier/client, acc2 = opposite account)
$join_clause = "FROM ot_head ot
    LEFT JOIN acc_head acc1 ON acc1.id = ot.acc1
    LEFT JOIN acc_head acc2 ON acc2.id = ot.acc2
    LEFT JOIN acc_head st ON st.id = ot.store_id
    LEFT JOIN acc_head emp ON emp.id = ot.emp_id
    LEFT JOIN users u ON u.id = ot.user
    LEFT JOIN pro_tybes pt ON pt.id = ot.pro_tybe
    LEFT JOIN acc_head drv ON drv.id = COALESCE(
        NULLIF(ot.delivery_person_id, 0),
        IF(ot.emp2_id IS NOT NULL AND ot.emp2_id <> 0 AND ot.emp2_id <> ot.emp_id AND (ot.order_type = 'delivery' OR ot.info LIKE '%دليفري%' OR ot.info LIKE '%مندوب التوصيل%'), ot.emp2_id, NULL)
    )";

$select_fields = "ot.*, 
    drv.aname AS driver_name, 
    acc1.aname AS acc1_name, 
    acc2.aname AS acc2_name, 
    st.aname AS store_name, 
    emp.aname AS emp_name, 
    u.uname AS user_name, 
    pt.pname AS pro_type_name";

// Replace date filter to use ot. alias
$dateFilter = "";
if ($strtdate && $enddate) {
    $dateFilter = "AND DATE(ot.pro_date) BETWEEN '$strtdate' AND '$enddate'";
} elseif ($strtdate) {
    $dateFilter = "AND DATE(ot.pro_date) >= '$strtdate'";
} elseif ($enddate) {
    $dateFilter = "AND DATE(ot.pro_date) <= '$enddate'";
} else {
    $dateFilter = "AND DATE(ot.pro_date) = '$today'";
}

switch ($q) {
    case "purchase":
    case "sale_legacy": // للتوافقية
        $report_name = "مشتريات";
        $where_clause = "ot.pro_tybe = 4 AND ot.isdeleted != 1 $dateFilter $searchFilter $extraFilter";
        $resop = $conn->query("SELECT $select_fields $join_clause WHERE $where_clause ORDER BY $order_by LIMIT $limit OFFSET $offset");
        break;
    case "sale":
    case "buy_legacy": // للتوافقية
        $report_name = "مبيعات وكاشير ومردودات";
        // 3 مبيعات، 9 كاشير، 11 مردود مبيعات (مش 10 = مردود مشتريات)
        $where_clause = "(ot.pro_tybe = 3 OR ot.pro_tybe = 9 OR ot.pro_tybe = 11) AND ot.isdeleted != 1 $dateFilter $searchFilter $extraFilter";
        $resop = $conn->query("SELECT $select_fields $join_clause WHERE $where_clause ORDER BY $order_by LIMIT $limit OFFSET $offset");
        break;
    case "sales_order":
        $report_name = "أوامر البيع";
        $where_clause = "ot.pro_tybe = 13 AND ot.isdeleted != 1 $dateFilter $searchFilter $extraFilter";
        $resop = $conn->query("SELECT $select_fields $join_clause WHERE $where_clause ORDER BY $order_by LIMIT $limit OFFSET $offset");
        break;
    case "price_offer":
        $report_name = "عروض الأسعار";
        $where_clause = "ot.pro_tybe = 14 AND ot.isdeleted != 1 $dateFilter $searchFilter $extraFilter";
        $resop = $conn->query("SELECT $select_fields $join_clause WHERE $where_clause ORDER BY $order_by LIMIT $limit OFFSET $offset");
        break;
    case "all":
    default:
        $q = 'all';
        $report_name = "كل العمليات";
        $where_clause = "ot.isdeleted != 1 $dateFilter $searchFilter $extraFilter";
        $resop = $conn->query("SELECT $select_fields $join_clause WHERE $where_clause ORDER BY $order_by LIMIT $limit OFFSET $offset");
}
$is_all_operations_report = ($q === 'all');
$is_sales_order_report = in_array($q, ['sales_order', 'price_offer'], true);
$order_doc_count_label = $q === 'price_offer' ? 'عدد العروض' : 'عدد الأوامر';
$order_doc_total_label = $q === 'price_offer' ? 'إجمالي عروض الأسعار' : 'إجمالي أوامر البيع';
?>


<div class="content-wrapper">

<?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
<div class="alert alert-success alert-dismissible fade show mx-3 mt-2" role="alert" style="border-radius:8px;">
    <i class="fas fa-check-circle me-2"></i> تم التعديل بنجاح
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<?php endif; ?>
    <section class="content-header">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3><?= $is_all_operations_report ? 'تقرير كل العمليات' : ('محلل العمل اليومي - ' . $report_name) ?></h3>
                    
                    <?php if (isset($_GET['success']) && $_GET['success'] == 'deleted'): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fa fa-check-circle"></i> تم حذف الفاتورة بنجاح
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>
              
                    <div class="mb-2">
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-outline-secondary quick-date" data-range="today">اليوم</button>
                            <button type="button" class="btn btn-outline-secondary quick-date" data-range="7">آخر 7 أيام</button>
                            <button type="button" class="btn btn-outline-secondary quick-date" data-range="30">آخر 30 يوم</button>
                            <button type="button" class="btn btn-outline-secondary quick-date" data-range="all">الإجمالي</button>
                        </div>
                    </div>
                    <form action="" method="get" id="filterForm">
                    <?php
                        $strtdate_display = $strtdate ?: date("Y-m-d");
                        $enddate_display = $enddate ?: date("Y-m-d");
                        ?>
                        <input type="hidden" name="q" value="<?= htmlspecialchars($q) ?>">
                        <input type="hidden" name="sort" id="sortInput" value="<?= htmlspecialchars($sort) ?>">
                        <input type="hidden" name="dir" id="dirInput" value="<?= htmlspecialchars(strtolower($dir)) ?>">
                        <div class="row">
                            <div class="col-md-2 col-sm-6 col-12 mb-2">
                                <label>من</label>
                                <input class="form-control" type="date" value="<?= $strtdate_display ?>" name="strtdate">
                            </div>
                            <div class="col-md-2 col-sm-6 col-12 mb-2">
                                <label>إلى</label>
                                <input class="form-control" type="date" value="<?= $enddate_display ?>" name="enddate">
                            </div>
                            <?php if ($is_all_operations_report) { ?>
                            <div class="col-md-2 col-sm-6 col-12 mb-2">
                                <label>نوع العملية</label>
                                <select class="form-control" name="pro_tybe">
                                    <option value="">الكل</option>
                                    <?php
                                    $res_types = $conn->query("SELECT id, pname FROM pro_tybes ORDER BY id");
                                    if ($res_types) {
                                        while ($pt = $res_types->fetch_assoc()) {
                                            $sel = ($filter_pro_tybe === (int)$pt['id']) ? 'selected' : '';
                                            echo '<option value="' . (int)$pt['id'] . '" ' . $sel . '>' . htmlspecialchars($pt['pname']) . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <?php } ?>
                            <div class="col-md-2 col-sm-6 col-12 mb-2">
                                <label>نوع الأوردر</label>
                                <select class="form-control" name="order_type">
                                    <option value="">الكل</option>
                                    <option value="takeaway" <?= $filter_order_type === 'takeaway' ? 'selected' : '' ?>>تيك أواي</option>
                                    <option value="table" <?= $filter_order_type === 'table' ? 'selected' : '' ?>>طاولة</option>
                                    <option value="delivery" <?= $filter_order_type === 'delivery' ? 'selected' : '' ?>>دليفري</option>
                                    <option value="dine_in" <?= $filter_order_type === 'dine_in' ? 'selected' : '' ?>>صالة</option>
                                </select>
                            </div>
                            <div class="col-md-2 col-sm-6 col-12 mb-2">
                                <label>الدليفري (الطيار)</label>
                                <select class="form-control" name="delivery_person">
                                    <option value="">الكل</option>
                                    <?php
                                    $res_drivers = $conn->query("SELECT id, aname, code FROM acc_head WHERE code LIKE '126%' AND isdeleted = 0 ORDER BY aname");
                                    if ($res_drivers) {
                                        while ($drv = $res_drivers->fetch_assoc()) {
                                            $sel = ($filter_delivery == (int)$drv['id']) ? 'selected' : '';
                                            echo '<option value="' . (int)$drv['id'] . '" ' . $sel . '>' . htmlspecialchars($drv['aname']) . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-2 col-sm-6 col-12 mb-2">
                                <label>بحث (رقم الفاتورة، <?= $q === 'purchase' ? 'المورد' : 'العميل' ?>، البيان)</label>
                                <input class="form-control" type="text" value="<?= htmlspecialchars($search ?? '') ?>" name="search" placeholder="ابحث هنا...">
                            </div>
                            <div class="col-md-2 col-12 mb-2">
                                <label class="d-none d-md-block">&nbsp;</label>
                                <div class="btn-group w-100">
                                    <button class="btn btn-primary" type="submit" style="flex: 2;">
                                        <i class="fa fa-search"></i> بحث
                                    </button>
                                    <button class="btn btn-info" type="button" id="toggleFiltersBtn" style="flex: 1;" title="تصفية الحقول">
                                        <i class="fa fa-filter"></i>
                                    </button>
                                    <button class="btn btn-dark" type="button" onclick="printBrutal()" style="flex: 1;" title="طباعة بتنسيق Brutal">
                                        <i class="fas fa-print"></i>
                                    </button>
                                    <label class="btn btn-outline-secondary mb-0 d-flex align-items-center justify-content-center" style="flex: 1.6; gap: 4px; cursor: pointer;" title="طباعة الفاتورة كإيصال كاشير بدل A4">
                                        <input type="checkbox" id="printCashierCheck">
                                        <span>كاشير</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-sm" id="operationsTable" data-page-length="10">
                            <thead>
                                <tr>
                                    <th class="sortable text-nowrap" data-col="0" data-type="number" data-sort-key="num" title="انقر للترتيب"># <i class="fa fa-sort text-muted sort-icon ms-1"></i></th>
                                    <th class="sortable text-nowrap" data-col="1" data-type="date" data-sort-key="date" title="انقر للترتيب">الوقت و التاريخ <i class="fa fa-sort text-muted sort-icon ms-1"></i></th>
                                    <th class="sortable text-nowrap" data-col="2" data-type="text" data-sort-key="type" title="انقر للترتيب">اسم العملية <i class="fa fa-sort text-muted sort-icon ms-1"></i></th>
                                    <th class="sortable text-nowrap" data-col="3" data-type="text" data-sort-key="payment" title="انقر للترتيب">طريقة الدفع <i class="fa fa-sort text-muted sort-icon ms-1"></i></th>
                                    <th class="sortable text-nowrap" data-col="4" data-type="number" data-sort-key="pro_value" title="انقر للترتيب">قيمة العملية <i class="fa fa-sort text-muted sort-icon ms-1"></i></th>
                                    <th class="sortable text-nowrap" data-col="5" data-type="number" data-sort-key="fat_net" title="انقر للترتيب">صافي العملية <i class="fa fa-sort text-muted sort-icon ms-1"></i></th>
                                    <th class="sortable text-nowrap" data-col="6" data-type="number" data-sort-key="paid" title="انقر للترتيب">المدفوع <i class="fa fa-sort text-muted sort-icon ms-1"></i></th>
                                    <th class="sortable text-nowrap" data-col="7" data-type="text" data-sort-key="acc1" title="انقر للترتيب">الحساب <i class="fa fa-sort text-muted sort-icon ms-1"></i></th>
                                    <th class="sortable text-nowrap" data-col="8" data-type="text" data-sort-key="acc2" title="انقر للترتيب">الحساب المقابل <i class="fa fa-sort text-muted sort-icon ms-1"></i></th>
                                    <th class="sortable text-nowrap" data-col="9" data-type="text" data-sort-key="store" title="انقر للترتيب">المخزن <i class="fa fa-sort text-muted sort-icon ms-1"></i></th>
                                    <th class="sortable text-nowrap" data-col="10" data-type="text" data-sort-key="emp" title="انقر للترتيب">الموظف <i class="fa fa-sort text-muted sort-icon ms-1"></i></th>
                                    <th class="sortable text-nowrap" data-col="11" data-type="text" data-sort-key="driver" title="انقر للترتيب">الطيار <i class="fa fa-sort text-muted sort-icon ms-1"></i></th>
                                    <th class="sortable text-nowrap" data-col="12" data-type="number" data-sort-key="profit" title="انقر للترتيب">الربح <i class="fa fa-sort text-muted sort-icon ms-1"></i></th>
                                    <th class="sortable text-nowrap" data-col="13" data-type="text" data-sort-key="user" title="انقر للترتيب">المستخدم <i class="fa fa-sort text-muted sort-icon ms-1"></i></th>
                                    <th class="sortable text-nowrap" data-col="14" data-type="number" data-sort-key="id" title="انقر للترتيب">معرف <i class="fa fa-sort text-muted sort-icon ms-1"></i></th>
                                </tr>
                                <tr id="filterRow" style="display: none;">
                                    <td></td>
                                    <td><input type="text" class="form-control form-control-sm column-filter" data-col-idx="1" placeholder="فلتر..."></td>
                                    <td><input type="text" class="form-control form-control-sm column-filter" data-col-idx="2" placeholder="فلتر..."></td>
                                    <td><input type="text" class="form-control form-control-sm column-filter" data-col-idx="3" placeholder="فلتر..."></td>
                                    <td><input type="text" class="form-control form-control-sm column-filter" data-col-idx="4" placeholder="فلتر..."></td>
                                    <td><input type="text" class="form-control form-control-sm column-filter" data-col-idx="5" placeholder="فلتر..."></td>
                                    <td><input type="text" class="form-control form-control-sm column-filter" data-col-idx="6" placeholder="فلتر..."></td>
                                    <td><input type="text" class="form-control form-control-sm column-filter" data-col-idx="7" placeholder="فلتر..."></td>
                                    <td><input type="text" class="form-control form-control-sm column-filter" data-col-idx="8" placeholder="فلتر..."></td>
                                    <td><input type="text" class="form-control form-control-sm column-filter" data-col-idx="9" placeholder="فلتر..."></td>
                                    <td><input type="text" class="form-control form-control-sm column-filter" data-col-idx="10" placeholder="فلتر..."></td>
                                    <td><input type="text" class="form-control form-control-sm column-filter" data-col-idx="11" placeholder="فلتر..."></td>
                                    <td><input type="text" class="form-control form-control-sm column-filter" data-col-idx="12" placeholder="فلتر..."></td>
                                    <td><input type="text" class="form-control form-control-sm column-filter" data-col-idx="13" placeholder="فلتر..."></td>
                                    <td></td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $x = $offset; // بدء العد من الـ offset
                                if (!class_exists('InvoiceProcessor')) {
                                    require_once __DIR__ . '/classes/InvoiceProcessor.php';
                                }
                                $canInvOperations = Can::check('items.pricing');
                                $canSettleCredit = Can::check('receipts.add');
                                while ($rowop = $resop->fetch_assoc()) {
                                    $x++;
                                    $proid = $rowop['id'];
                                    $tybe = $rowop['pro_tybe'];
                                    $is_return = ($tybe == 10);
                                    $is_sales_order = in_array((int) $tybe, [13, 14], true);
                                    ?>
                                    <?php
                                    $pro_type_display = !empty($rowop['pro_type_name']) ? $rowop['pro_type_name'] : ($conn->query("SELECT pname FROM pro_tybes WHERE id = $tybe")->fetch_assoc()['pname'] ?? '');
                                    $acc1_display = !empty($rowop['acc1_name']) ? $rowop['acc1_name'] : (!empty($rowop['acc1']) ? ($conn->query("SELECT aname FROM acc_head WHERE id = " . intval($rowop['acc1']))->fetch_assoc()['aname'] ?? '') : '');
                                    $acc2_display = !empty($rowop['acc2_name']) ? $rowop['acc2_name'] : (!empty($rowop['acc2']) ? ($conn->query("SELECT aname FROM acc_head WHERE id = " . intval($rowop['acc2']))->fetch_assoc()['aname'] ?? '') : '');
                                    $store_display = !empty($rowop['store_name']) ? $rowop['store_name'] : ($rowop['store_id'] > 0 ? ($conn->query("SELECT aname FROM acc_head WHERE id = " . intval($rowop['store_id']))->fetch_assoc()['aname'] ?? '') : '');
                                    $emp_display = !empty($rowop['emp_name']) ? $rowop['emp_name'] : ($rowop['emp_id'] > 0 ? ($conn->query("SELECT aname FROM acc_head WHERE id = " . intval($rowop['emp_id']))->fetch_assoc()['aname'] ?? '') : '');
                                    $driver_display = !empty($rowop['driver_name']) ? $rowop['driver_name'] : '';
                                    $user_display = !empty($rowop['user_name']) ? $rowop['user_name'] : (!empty($rowop['user']) ? ($conn->query("SELECT uname FROM users WHERE id = " . intval($rowop['user']))->fetch_assoc()['uname'] ?? '') : '');
                                    $invoice_id = intval($rowop['id']);
                                    $paid_amount = 0;
                                    if (!$is_sales_order) {
                                        $paid_query = $conn->query("SELECT SUM(pro_value) as paid FROM ot_head WHERE op2 = $invoice_id AND isdeleted != 1");
                                        $paid_amount = ($paid_query && $row_paid = $paid_query->fetch_assoc()) ? (float)($row_paid['paid'] ?? 0) : 0;
                                    }
                                    $payment_sort_val = $is_sales_order ? 0 : ($paid_amount < (float)$rowop['fat_net'] ? 1 : 2);
                                    ?>
                                    <tr class="<?= $is_return ? 'table-danger' : '' ?>">
                                        <td data-sort="<?= $x ?>"><?= $x ?></td>
                                        <td data-sort="<?= htmlspecialchars($rowop['crtime'] ?: ($rowop['pro_date'] ?? '')) ?>"><?= $rowop['crtime'] ?></td>
                                        <td data-sort="<?= htmlspecialchars($pro_type_display) ?>">
                                            <?php
                                            $use_a4 = in_array((int) $tybe, [2, 3, 4, 13, 14], true);
                                            $a4_href = 'print/print_sales.php?id=' . (int) $proid;
                                            $cashier_href = 'print/receipt.php?id=' . (int) $proid . '&src=invoice';
                                            $print_href = $use_a4 ? $a4_href : ('print/receipt.php?id=' . (int) $proid);
                                            ?>
                                            <a class="btn btn-block btn-light border<?= $use_a4 ? ' invoice-print-link' : '' ?>"
                                               href="<?= $print_href ?>"
                                               <?= $use_a4 ? 'data-a4="' . $a4_href . '" data-cashier="' . $cashier_href . '"' : '' ?>
                                               target="_blank">
                                                <?= htmlspecialchars($pro_type_display) ?>
                                            </a>
                                        </td>
                                        <td data-sort="<?= $payment_sort_val ?>">
                                            <?php if ($is_sales_order): ?>
                                                <span class="text-muted">—</span>
                                            <?php elseif ($paid_amount < $rowop['fat_net']): ?>
                                                <span class="badge badge-warning">أجل</span>
                                            <?php else: ?>
                                                <span class="badge badge-success">نقدي</span>
                                            <?php endif; ?>
                                        </td>
                                         <td class="value <?= $is_return ? 'ret-value' : 'sale-value' ?>" data-sort="<?= (float)$rowop['pro_value'] ?>"><?= $rowop['pro_value'] ?></td>
                                        <td class="fatnet <?php if($rowop['pro_value'] != $rowop['fat_net']){echo "bg-yellow-300";} ?>" data-sort="<?= (float)$rowop['fat_net'] ?>">
                                            <?= $rowop['fat_net'] ?>
                                        </td>
                                        <td class="paid-amount" data-sort="<?= (float)$paid_amount ?>" data-paid="<?= (float)$paid_amount ?>" data-remaining="<?= (float)max(0, $rowop['fat_net'] - $paid_amount) ?>">
                                            <?php if ($is_sales_order): ?>
                                                <span class="text-muted">—</span>
                                            <?php else: ?>
                                            <?= number_format($paid_amount, 2, '.', '') ?>
                                            <?php 
                                            $remaining = $rowop['fat_net'] - $paid_amount;
                                            if ($remaining > 0): 
                                            ?>
                                                <small class="d-block text-muted" style="font-size: 0.65rem;">(أجل: <?= number_format($remaining, 2, '.', '') ?>)</small>
                                            <?php endif; ?>
                                            <?php endif; ?>
                                        </td>
                                        <td data-sort="<?= htmlspecialchars($acc1_display) ?>"><?= htmlspecialchars($acc1_display) ?></td>
                                        <td data-sort="<?= htmlspecialchars($acc2_display) ?>"><?= htmlspecialchars($acc2_display) ?></td>
                                        <td data-sort="<?= htmlspecialchars($store_display) ?>"><?= htmlspecialchars($store_display) ?></td>
                                        <td data-sort="<?= htmlspecialchars($emp_display) ?>"><?= htmlspecialchars($emp_display) ?></td>
                                        <td data-sort="<?= htmlspecialchars($driver_display) ?>"><?= htmlspecialchars($driver_display) ?></td>
                                         <td class="prft" data-sort="<?= (float)$rowop['profit'] ?>"><?= $rowop['profit'] ?></td>
                                        <td data-sort="<?= htmlspecialchars($user_display) ?>"><?= htmlspecialchars($user_display) ?></td>
                                        <td data-sort="<?= (int)$rowop['id'] ?>">
                                            <?= $rowop['id'] ?>
                                            <?php if ($canInvOperations) { ?>
                                            <a href="inv_operations.php?h=<?= md5($proid) ?>&q=<?= $proid ?>&t=<?= md5($tybe) ?>">
                                                <i class="fa fa-barcode"></i>
                                            </a>
                                            <?php } ?>
                                            
                                            <!-- زر التعديل -->
                                            <?php if(in_array($tybe, [3, 4, 13, 14]) && Can::check(InvoiceProcessor::permissionFor((int) $tybe, 'edit'))) { // مبيعات، مشتريات، أمر بيع، عرض سعر (الكاشير tybe=9 ليس له صفحة تعديل) ?>
                                            <a href="sales.php?edit_id=<?= $rowop['id'] ?>" class="btn btn-sm btn-warning" title="تعديل">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <?php } ?>

                                            <!-- زر تفاصيل الأجل -->
                                            <?php if (!empty($rowop['jal_amount']) && $rowop['jal_amount'] > 0): ?>
                                            <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#jalModal<?= $rowop['id']?>" title="تفاصيل الأجل">
                                                <i class="fa fa-clock"></i>
                                            </button>

                                            <div class="modal fade" id="jalModal<?= $rowop['id']?>" tabindex="-1" role="dialog" aria-labelledby="jalModalLabel<?= $rowop['id']?>" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-info text-white">
                                                            <h5 class="modal-title" id="jalModalLabel<?= $rowop['id']?>">
                                                                <i class="fa fa-clock"></i> تفاصيل الأجل - فاتورة #<?= $rowop['id'] ?>
                                                            </h5>
                                                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body text-right">
                                                            <div class="form-group border-bottom pb-2">
                                                                <label class="font-weight-bold text-muted">اسم العميل (أجل):</label>
                                                                <p class="h5"><?= !empty($rowop['jal_name']) ? htmlspecialchars($rowop['jal_name']) : '<span class="text-muted">غير محدد</span>' ?></p>
                                                            </div>
                                                            <div class="form-group border-bottom pb-2">
                                                                <label class="font-weight-bold text-muted">قيمة الأجل:</label>
                                                                <p class="h4 text-danger"><?= number_format($rowop['jal_amount'], 2) ?> ج.م</p>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="font-weight-bold text-muted">ملاحظات:</label>
                                                                <p class="mb-0 bg-light p-2 rounded border"><?= !empty($rowop['jal_notes']) ? nl2br(htmlspecialchars($rowop['jal_notes'])) : 'لا توجد ملاحظات' ?></p>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">إغلاق</button>
                                                            
                                                            <?php if ($canSettleCredit) { ?>
                                                            <form action="do/settle_credit.php" method="POST" onsubmit="return confirm('هل أنت متأكد من تسوية هذا المبلغ؟ سيتم تصفير الأجل واعتباره مدفوعاً.');">
                                                                <input type="hidden" name="id" value="<?= $rowop['id'] ?>">
                                                                <button type="submit" class="btn btn-success">
                                                                    <i class="fa fa-check me-1"></i> تم الدفع (تسوية)
                                                                </button>
                                                            </form>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php endif; ?>

                                            <!-- زر الحذف -->
                                            <?php if (Can::check(InvoiceProcessor::permissionFor((int) $tybe, 'delete'))) { ?>
                                            <a href="#" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteModal<?= $rowop['id']?>" data-id="<?= $id; ?>">
                                                <i class="fa fa-trash"></i>
                                            </a>

                                            <form action="do/dodel_invoice.php?id=<?= $rowop['id'] ?>" method="post">
                                                <input type="hidden" name="q" value="<?= $q ?>">
                                                <input type="hidden" name="return_url" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
                                            
                                            <div class="modal fade" id="deleteModal<?= $rowop['id']?>" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="deleteModalLabel">تأكيد الحذف <?= $rowop['id']?></h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="إغلاق">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>هل أنت متأكد من أنك تريد حذف هذه الفاتورة؟</p>
                                                            <p><strong>رقم الفاتورة:</strong> <?= $rowop['id'] ?></p>
                                                            <p><strong>نوع العملية:</strong> <?= $conn->query("SELECT pname FROM pro_tybes WHERE id = $tybe")->fetch_assoc()['pname'] ?></p>
                                                            <label for="pass">كلمة المرور:</label>
                                                            <input type="password" name="pass" class="form-control" placeholder="أدخل كلمة مرور الحذف" required>
                                                            
                                                            <div class="form-check mt-3">
                                                                <input type="checkbox" class="form-check-input" id="forceDelete<?= $rowop['id']?>" name="force_delete" value="1">
                                                                <label class="form-check-label text-warning" for="forceDelete<?= $rowop['id']?>">
                                                                    <small>حذف قسري (تجاهل العمليات المرتبطة)</small>
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                                                            <button type="submit" class="btn btn-danger">حذف</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            </form>
                                            <?php } ?>

                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </tbody>
                        </table>
                        <?php if($is_sales_order_report): ?>
                        <div class="d-flex flex-wrap mt-3 summary-cards" style="gap: 8px;">
                            <div class="flex-fill text-center py-2 px-3 summary-card" style="--c:#6f42c1; min-width:130px;">
                                <small class="d-block"><?= $order_doc_count_label ?></small>
                                <strong id="so_count_val" style="font-size:1.15rem;">0</strong>
                            </div>
                            <div class="flex-fill text-center py-2 px-3 summary-card" style="--c:#17a2b8; min-width:130px;">
                                <small class="d-block"><?= $order_doc_total_label ?></small>
                                <strong id="so_total_val" style="font-size:1.15rem;">0.00</strong>
                            </div>
                            <div class="flex-fill text-center py-2 px-3 summary-card" style="--c:#28a745; min-width:130px;">
                                <small class="d-block">الإجمالي بعد الخصم</small>
                                <strong id="so_fatnet_val" style="font-size:1.15rem;">0.00</strong>
                            </div>
                        </div>
                        <?php elseif($q !== 'purchase' && $q !== 'sale_legacy'): ?>
                        <div class="d-flex flex-wrap mt-3 summary-cards" style="gap: 8px;">
                            <div class="flex-fill text-center py-2 px-3 summary-card" style="--c:#17a2b8; min-width:130px;">
                                <small class="d-block">إجمالي المبيعات</small>
                                <strong id="total_sales_val" style="font-size:1.15rem;">0.00</strong>
                                <small class="d-block text-muted" style="font-size:.7rem;">بعد الخصم: <span id="fatnet_sales">0.00</span></small>
                            </div>
                            <div class="flex-fill text-center py-2 px-3 summary-card" style="--c:#dc3545; min-width:130px;">
                                <small class="d-block">إجمالي المردودات</small>
                                <strong id="total_returns_val" style="font-size:1.15rem;">0.00</strong>
                            </div>
                            <div class="flex-fill text-center py-2 px-3 summary-card" style="--c:#28a745; min-width:130px;">
                                <small class="d-block">الصافي (مبيعات - مردود)</small>
                                <strong id="net_sales_val" style="font-size:1.15rem;">0.00</strong>
                                <small class="d-block text-muted" style="font-size:.7rem;">بعد الخصم: <span id="net_after_returns">0.00</span></small>
                            </div>
                            <div class="flex-fill text-center py-2 px-3 summary-card" style="--c:#007bff; min-width:130px;">
                                <small class="d-block">إجمالي المدفوع (المحصل)</small>
                                <strong id="total_paid_val" style="font-size:1.15rem;">0.00</strong>
                            </div>
                            <div class="flex-fill text-center py-2 px-3 summary-card" style="--c:#fd7e14; min-width:130px;">
                                <small class="d-block">إجمالي الآجل (المتبقي)</small>
                                <strong id="total_remaining_val" style="font-size:1.15rem;">0.00</strong>
                            </div>
                            <div class="flex-fill text-center py-2 px-3 summary-card" style="--c:#00796b; min-width:130px;">
                                <small class="d-block">الأرباح</small>
                                <strong id="total_profit_val" style="font-size:1.15rem;">0.00</strong>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Pagination -->
                <div class="card-footer">
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm justify-content-center mb-0">
                            <?php
                            // حساب إجمالي السجلات
                            $count_query = $conn->query("SELECT COUNT(*) as total $join_clause WHERE $where_clause");
                            $total_items = $count_query->fetch_assoc()['total'];
                            $total_pages = ceil($total_items / $limit);
                            
                            // بناء query string للفلاتر
                            $filter_params = "q=$q";
                            if (!empty($strtdate)) $filter_params .= "&strtdate=" . urlencode($strtdate);
                            if (!empty($enddate)) $filter_params .= "&enddate=" . urlencode($enddate);
                            if (!empty($search)) $filter_params .= "&search=" . urlencode($search);
                            if ($filter_delivery > 0) $filter_params .= "&delivery_person=" . $filter_delivery;
                            if ($filter_order_type !== '') $filter_params .= "&order_type=" . urlencode($filter_order_type);
                            if (!empty($sort)) $filter_params .= "&sort=" . urlencode($sort);
                            if (!empty($dir)) $filter_params .= "&dir=" . urlencode(strtolower($dir));
                            
                            // زر السابق
                            if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="operations_summary.php?<?= $filter_params ?>&page=<?= $page - 1 ?>" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                            <?php else: ?>
                                <li class="page-item disabled">
                                    <span class="page-link">&laquo;</span>
                                </li>
                            <?php endif;
                            
                            // عرض أرقام الصفحات
                            $start_page = max(1, $page - 2);
                            $end_page = min($total_pages, $page + 2);
                            
                            // الصفحة الأولى
                            if ($start_page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="operations_summary.php?<?= $filter_params ?>&page=1">1</a>
                                </li>
                                <?php if ($start_page > 2): ?>
                                    <li class="page-item disabled"><span class="page-link">...</span></li>
                                <?php endif;
                            endif;
                            
                            // الصفحات المحيطة
                            for ($i = $start_page; $i <= $end_page; $i++): ?>
                                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                    <a class="page-link" href="operations_summary.php?<?= $filter_params ?>&page=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor;
                            
                            // الصفحة الأخيرة
                            if ($end_page < $total_pages): 
                                if ($end_page < $total_pages - 1): ?>
                                    <li class="page-item disabled"><span class="page-link">...</span></li>
                                <?php endif; ?>
                                <li class="page-item">
                                    <a class="page-link" href="operations_summary.php?<?= $filter_params ?>&page=<?= $total_pages ?>"><?= $total_pages ?></a>
                                </li>
                            <?php endif;
                            
                            // زر التالي
                            if ($page < $total_pages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="operations_summary.php?<?= $filter_params ?>&page=<?= $page + 1 ?>" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            <?php else: ?>
                                <li class="page-item disabled">
                                    <span class="page-link">&raquo;</span>
                                </li>
                            <?php endif; ?>
                        </ul>
                        <div class="text-center mt-2">
                            <small class="text-muted">
                                عرض <?= ($offset + 1) ?> - <?= min($offset + $limit, $total_items) ?> من <?= $total_items ?> عملية
                            </small>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </section>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const toggleBtn = document.getElementById("toggleFiltersBtn");
        const filterRow = document.getElementById("filterRow");
        
        if (toggleBtn && filterRow) {
            toggleBtn.addEventListener("click", function () {
                if (filterRow.style.display === "none") {
                    filterRow.style.display = "";
                    toggleBtn.classList.add("active");
                } else {
                    filterRow.style.display = "none";
                    toggleBtn.classList.remove("active");
                    // Clear all filters when closing
                    const inputs = filterRow.querySelectorAll("input");
                    inputs.forEach(input => {
                        input.value = "";
                    });
                    filterTable();
                }
            });
        }

        const inputs = document.querySelectorAll(".column-filter");
        inputs.forEach(input => {
            input.addEventListener("input", filterTable);
        });

        function filterTable() {
            const rows = document.querySelectorAll("table tbody tr");
            rows.forEach(row => {
                let showRow = true;
                inputs.forEach(input => {
                    const colIdx = parseInt(input.getAttribute("data-col-idx"));
                    const filterVal = input.value.toLowerCase().trim();
                    if (filterVal) {
                        const cell = row.cells[colIdx];
                        if (cell) {
                            const cellText = cell.textContent.toLowerCase().trim();
                            if (!cellText.includes(filterVal)) {
                                showRow = false;
                            }
                        }
                    }
                });
                row.style.display = showRow ? "" : "none";
            });
            
            recalculateTotals();
        }

        function recalculateTotals() {
            const visibleRows = Array.from(document.querySelectorAll("table tbody tr")).filter(tr => tr.style.display !== "none");
            
            let sales = 0;
            let returns = 0;
            let fatnet = 0;
            let fatnet_sales = 0;
            let fatnet_returns = 0;
            let paid = 0;
            let remaining = 0;
            let profit = 0;
            
            visibleRows.forEach(row => {
                const valEl = row.querySelector(".value");
                const netEl = row.querySelector(".fatnet");
                const paidEl = row.querySelector(".paid-amount");
                const prftEl = row.querySelector(".prft");
                
                const val = valEl ? parseFloat(valEl.textContent.trim()) || 0 : 0;
                const net = netEl ? parseFloat(netEl.textContent.trim()) || 0 : 0;
                const pd = paidEl ? (parseFloat(paidEl.getAttribute("data-paid") ?? paidEl.getAttribute("data-sort")) || 0) : 0;
                const rem = paidEl ? (parseFloat(paidEl.getAttribute("data-remaining")) || 0) : 0;
                const prft = prftEl ? parseFloat(prftEl.textContent.trim()) || 0 : 0;
                
                if (valEl) {
                    if (valEl.classList.contains("ret-value")) {
                        returns += val;
                        fatnet_returns += net;
                        profit -= prft;
                    } else if (valEl.classList.contains("sale-value")) {
                        sales += val;
                        fatnet_sales += net;
                        profit += prft;
                        paid += pd;
                        remaining += rem;
                    }
                }
                
                fatnet += net;
            });
            
            const net = sales - returns;
            const net_after_returns = fatnet_sales - fatnet_returns;

            const setVal = (id, v) => {
                const el = document.getElementById(id);
                if (el) el.textContent = v.toFixed(2);
            };

            setVal("total_sales_val", sales);
            setVal("fatnet_sales", fatnet_sales);
            setVal("total_returns_val", returns);
            setVal("net_after_returns", net_after_returns);
            setVal("net_sales_val", net);
            setVal("total_paid_val", paid);
            setVal("total_remaining_val", remaining);
            setVal("total_profit_val", profit);

            setVal("so_total_val", sales);
            setVal("so_fatnet_val", fatnet_sales);
            const soCountEl = document.getElementById("so_count_val");
            if (soCountEl) soCountEl.textContent = visibleRows.length;
        }

        // Initial calculation
        recalculateTotals();

        // Quick date filters
        const strtInput = document.querySelector('input[name="strtdate"]');
        const endInput  = document.querySelector('input[name="enddate"]');
        const form      = document.getElementById('filterForm');

        document.querySelectorAll('.quick-date').forEach(btn => {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.quick-date').forEach(b => b.classList.remove('active', 'btn-secondary'));
                this.classList.add('active', 'btn-secondary');

                const range = this.dataset.range;
                const today = new Date();
                const fmt = d => d.toISOString().split('T')[0];

                if (range === 'today') {
                    strtInput.value = fmt(today);
                    endInput.value  = fmt(today);
                } else if (range === '7') {
                    const d = new Date(today); d.setDate(d.getDate() - 6);
                    strtInput.value = fmt(d);
                    endInput.value  = fmt(today);
                } else if (range === '30') {
                    const d = new Date(today); d.setDate(d.getDate() - 29);
                    strtInput.value = fmt(d);
                    endInput.value  = fmt(today);
                } else if (range === 'all') {
                    strtInput.value = '2000-01-01';
                    endInput.value  = fmt(today);
                }

                form.submit();
            });
        });

        // Highlight active quick-date button based on current URL params
        (function highlightActive() {
            const params = new URLSearchParams(window.location.search);
            const s = params.get('strtdate');
            const e = params.get('enddate');
            if (!s && !e) return;
            const today = new Date().toISOString().split('T')[0];
            const d7 = new Date(); d7.setDate(d7.getDate() - 6);
            const d30 = new Date(); d30.setDate(d30.getDate() - 29);
            const fmt = d => d.toISOString().split('T')[0];

            let match = null;
            if (s === today && e === today) match = 'today';
            else if (s === fmt(d7) && e === today) match = '7';
            else if (s === fmt(d30) && e === today) match = '30';
            else if (s === '2000-01-01') match = 'all';

            if (match) {
                const el = document.querySelector(`.quick-date[data-range="${match}"]`);
                if (el) el.classList.add('active', 'btn-secondary');
            }
        })();

        // Sorting initialization
        (function initTableSorting() {
            const table = document.getElementById("operationsTable") || document.querySelector(".table-responsive table");
            if (!table) return;

            const thead = table.querySelector("thead");
            const tbody = table.querySelector("tbody");
            if (!thead || !tbody) return;

            const headers = thead.querySelectorAll("tr:first-child th.sortable");

            function updateIcons(activeTh, direction) {
                headers.forEach(h => {
                    h.removeAttribute("data-sort-dir");
                    h.classList.remove("sorted-asc", "sorted-desc");
                    const icon = h.querySelector(".sort-icon");
                    if (icon) {
                        icon.className = "fa fa-sort text-muted sort-icon ms-1";
                    }
                });
                if (activeTh) {
                    activeTh.setAttribute("data-sort-dir", direction);
                    activeTh.classList.add(direction === "asc" ? "sorted-asc" : "sorted-desc");
                    const icon = activeTh.querySelector(".sort-icon");
                    if (icon) {
                        icon.className = direction === "asc" 
                            ? "fa fa-sort-up text-primary sort-icon ms-1" 
                            : "fa fa-sort-down text-primary sort-icon ms-1";
                    }
                }
            }

            function sortRows(colIdx, type, direction) {
                const rows = Array.from(tbody.querySelectorAll("tr"));
                if (rows.length === 0) return;

                rows.sort((rowA, rowB) => {
                    const cellA = rowA.cells[colIdx];
                    const cellB = rowB.cells[colIdx];
                    if (!cellA || !cellB) return 0;

                    let valA = cellA.getAttribute("data-sort");
                    if (valA === null || valA === undefined) valA = cellA.textContent.trim();
                    let valB = cellB.getAttribute("data-sort");
                    if (valB === null || valB === undefined) valB = cellB.textContent.trim();

                    let cmp = 0;
                    if (type === "number") {
                        const nA = parseFloat(valA) || 0;
                        const nB = parseFloat(valB) || 0;
                        cmp = nA - nB;
                    } else if (type === "date") {
                        const tA = Date.parse((valA + "").replace(" ", "T")) || 0;
                        const tB = Date.parse((valB + "").replace(" ", "T")) || 0;
                        cmp = tA - tB;
                    } else {
                        cmp = (valA + "").localeCompare(valB + "", "ar", { numeric: true, sensitivity: "base" });
                    }

                    if (cmp === 0) {
                        const idA = parseInt(rowA.cells[14]?.getAttribute("data-sort") || 0, 10);
                        const idB = parseInt(rowB.cells[14]?.getAttribute("data-sort") || 0, 10);
                        cmp = idA - idB;
                    }

                    return direction === "asc" ? cmp : -cmp;
                });

                const frag = document.createDocumentFragment();
                rows.forEach((r, i) => {
                    if (colIdx !== 0 && r.cells[0]) {
                        r.cells[0].textContent = (i + 1);
                    }
                    frag.appendChild(r);
                });
                tbody.appendChild(frag);
            }

            headers.forEach(th => {
                th.addEventListener("click", function(e) {
                    if (e.target.closest("button, a, input, select")) return;

                    const colIdx = parseInt(this.getAttribute("data-col"), 10);
                    const type = this.getAttribute("data-type") || "text";
                    const sortKey = this.getAttribute("data-sort-key") || "";
                    const currentDir = this.getAttribute("data-sort-dir") || "none";
                    const newDir = (currentDir === "asc") ? "desc" : "asc";

                    updateIcons(this, newDir);
                    sortRows(colIdx, type, newDir);

                    // Update URL and form inputs without full page reload
                    const url = new URL(window.location.href);
                    if (sortKey) {
                        url.searchParams.set("sort", sortKey);
                    }
                    url.searchParams.set("dir", newDir);
                    window.history.replaceState({}, "", url.toString());

                    const sortInput = document.getElementById("sortInput");
                    const dirInput = document.getElementById("dirInput");
                    if (sortInput && sortKey) sortInput.value = sortKey;
                    if (dirInput) dirInput.value = newDir;
                });
            });

            // Initial highlight if URL had sort
            const currentSort = <?= json_encode($sort) ?>;
            const currentDir = <?= json_encode(strtolower($dir)) ?> || "desc";
            if (currentSort) {
                const matchingTh = Array.from(headers).find(h => h.getAttribute("data-sort-key") === currentSort);
                if (matchingTh) {
                    updateIcons(matchingTh, currentDir);
                }
            } else {
                const idTh = Array.from(headers).find(h => h.getAttribute("data-sort-key") === "id");
                if (idTh) {
                    updateIcons(idTh, "desc");
                }
            }
        })();
    });
</script>
<script>
function printBrutal() {
    window.print();
}
(function() {
    var COOKIE = 'invoice_print_cashier';
    function readCookie() {
        var m = document.cookie.match(/(?:^|; )invoice_print_cashier=([^;]*)/);
        return m ? decodeURIComponent(m[1]) : '0';
    }
    function writeCookie(value) {
        var exp = new Date();
        exp.setFullYear(exp.getFullYear() + 1);
        document.cookie = COOKIE + '=' + encodeURIComponent(value) + '; expires=' + exp.toUTCString() + '; path=/; SameSite=Lax';
    }
    function applyLinks(on) {
        document.querySelectorAll('.invoice-print-link').forEach(function(a) {
            var href = on ? a.getAttribute('data-cashier') : a.getAttribute('data-a4');
            if (href) a.setAttribute('href', href);
        });
    }
    document.addEventListener('DOMContentLoaded', function() {
        var cb = document.getElementById('printCashierCheck');
        var on = readCookie() === '1';
        applyLinks(on);
        if (!cb) return;
        cb.checked = on;
        cb.addEventListener('change', function() {
            writeCookie(cb.checked ? '1' : '0');
            applyLinks(cb.checked);
        });
    });
})();
</script>

<style>
th.sortable {
    cursor: pointer !important;
    user-select: none !important;
    position: relative;
    transition: background-color .15s ease-in-out, color .15s ease-in-out;
}
th.sortable:hover {
    background-color: rgba(0, 123, 255, 0.08) !important;
}
th.sortable.sorted-asc,
th.sortable.sorted-desc {
    background-color: rgba(0, 123, 255, 0.15) !important;
    color: #0056b3 !important;
}
th.sortable .sort-icon {
    font-size: 0.85em;
    margin-right: 4px;
    vertical-align: middle;
}
.summary-card {
    border: 2px solid var(--c);
    border-radius: 8px;
    background: transparent;
    color: var(--c);
    transition: background-color .15s ease-in-out, color .15s ease-in-out;
}
.summary-card small:not(.text-muted) {
    color: var(--c);
    opacity: .85;
}
.summary-card strong {
    color: var(--c);
}
.summary-card:hover {
    background: var(--c);
    color: #fff;
}
.summary-card:hover small,
.summary-card:hover strong {
    color: #fff !important;
    opacity: 1;
}

@media print {
    body * {
        visibility: hidden;
    }
    #printableTable, #printableTable * {
        visibility: visible;
    }
    #printableTable {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        background: white !important;
        color: black !important;
        padding: 20px;
    }
    
    /* Brutalist Styling for Print */
    .brutal-header {
        border-bottom: 5px solid #000;
        margin-bottom: 20px;
        padding-bottom: 10px;
        text-transform: uppercase;
        font-weight: 900;
        font-size: 32px;
        background: #ffff00 !important;
        -webkit-print-color-adjust: exact;
        padding: 10px;
    }
    
    .brutal-table {
        width: 100%;
        border-collapse: collapse;
        border: 4px solid #000 !important;
    }
    
    .brutal-table th {
        background: #000 !important;
        color: #fff !important;
        border: 2px solid #000;
        padding: 12px;
        font-weight: 900;
        text-transform: uppercase;
        -webkit-print-color-adjust: exact;
    }
    
    .brutal-table td {
        border: 2px solid #000;
        padding: 10px;
        font-weight: 700;
    }
    
    .brutal-table tr:nth-child(even) {
        background: #f0f0f0 !important;
        -webkit-print-color-adjust: exact;
    }
    
    .brutal-summary-box {
        border: 4px solid #000;
        padding: 15px;
        margin-top: 20px;
        display: inline-block;
        min-width: 200px;
        margin-right: 15px;
        background: #fff !important;
        box-shadow: 8px 8px 0px #000;
        -webkit-print-color-adjust: exact;
    }
    
    .brutal-summary-box.bg-info { background: #00ffff !important; }
    .brutal-summary-box.bg-danger { background: #ff00ff !important; color: white !important; }
    .brutal-summary-box.bg-success { background: #ffff00 !important; }
    
    .no-print { display: none !important; }
}

/* Base style for the printable section when visible */
#printableTable {
    display: none;
}
@media print {
    #printableTable {
        display: block;
    }
}
</style>

<!-- Hidden Section for Brutal Print -->
<div id="printableTable">
    <div class="brutal-header" style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <div style="font-size: 40px;"><?= $settings['company_name'] ?? 'FOCUS' ?></div>
            <div style="font-size: 20px;"><?= $report_name ?></div>
        </div>
        <div style="text-align: left; border-left: 5px solid #000; padding-left: 15px;">
            <div style="font-size: 14px;">من: <?= $strtdate_display ?></div>
            <div style="font-size: 14px;">إلى: <?= $enddate_display ?></div>
            <div style="font-size: 12px; margin-top: 5px;">تاريخ الطباعة: <?= date('Y-m-d H:i') ?></div>
        </div>
    </div>
    
    <table class="brutal-table">
        <thead>
            <tr>
                <th>#</th>
                <th>التاريخ</th>
                <th>البيان</th>
                <th>القيمة</th>
                <th>الصافي</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            // Re-run query for printing or use original results
            if(isset($resop) && $resop->num_rows > 0) {
                mysqli_data_seek($resop, 0); // Reset pointer
                $i = 1;
                while($row = $resop->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>".$i++."</td>";
                    echo "<td>".$row['pro_date']."</td>";
                    echo "<td>".$row['info']."</td>";
                    echo "<td>".$row['pro_value']."</td>";
                    echo "<td>".$row['fat_net']."</td>";
                    echo "</tr>";
                }
            }
            ?>
        </tbody>
    </table>
    
    <?php if ($is_sales_order_report): ?>
    <div style="margin-top: 30px;">
        <div class="brutal-summary-box">
            <div style="font-size: 12px;"><?= $order_doc_count_label ?></div>
            <div style="font-size: 24px; font-weight: 900;" id="brutal_so_count">0</div>
        </div>
        <div class="brutal-summary-box">
            <div style="font-size: 12px;"><?= $order_doc_total_label ?></div>
            <div style="font-size: 24px; font-weight: 900;" id="brutal_so_total">0.00</div>
        </div>
        <div class="brutal-summary-box">
            <div style="font-size: 12px;">الإجمالي بعد الخصم</div>
            <div style="font-size: 24px; font-weight: 900;" id="brutal_so_fatnet">0.00</div>
        </div>
    </div>
    <?php else: ?>
    <div style="margin-top: 30px;">
        <div class="brutal-summary-box">
            <div style="font-size: 12px;">إجمالي المبيعات</div>
            <div style="font-size: 24px; font-weight: 900;" id="brutal_sales">0.00</div>
        </div>
        <div class="brutal-summary-box">
            <div style="font-size: 12px;">إجمالي المردودات</div>
            <div style="font-size: 24px; font-weight: 900;" id="brutal_returns">0.00</div>
        </div>
        <div class="brutal-summary-box">
            <div style="font-size: 12px;">الصافي النهائي</div>
            <div style="font-size: 24px; font-weight: 900;" id="brutal_net">0.00</div>
        </div>
        <div class="brutal-summary-box">
            <div style="font-size: 12px;">المدفوع (المحصل)</div>
            <div style="font-size: 24px; font-weight: 900;" id="brutal_paid">0.00</div>
        </div>
        <div class="brutal-summary-box">
            <div style="font-size: 12px;">الآجل (المتبقي)</div>
            <div style="font-size: 24px; font-weight: 900;" id="brutal_remaining">0.00</div>
        </div>
        <div class="brutal-summary-box">
            <div style="font-size: 12px;">الأرباح</div>
            <div style="font-size: 24px; font-weight: 900;" id="brutal_profit">0.00</div>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
// Update brutal summary values when page loads
$(document).ready(function() {
    setTimeout(function() {
        var copyVal = function (targetId, sourceId, fallback) {
            var target = document.getElementById(targetId);
            if (!target) return;
            target.textContent = document.getElementById(sourceId)?.textContent || fallback;
        };
        copyVal('brutal_sales', 'total_sales_val', '0.00');
        copyVal('brutal_returns', 'total_returns_val', '0.00');
        copyVal('brutal_net', 'net_sales_val', '0.00');
        copyVal('brutal_paid', 'total_paid_val', '0.00');
        copyVal('brutal_remaining', 'total_remaining_val', '0.00');
        copyVal('brutal_profit', 'total_profit_val', '0.00');
        copyVal('brutal_so_count', 'so_count_val', '0');
        copyVal('brutal_so_total', 'so_total_val', '0.00');
        copyVal('brutal_so_fatnet', 'so_fatnet_val', '0.00');
    }, 1000);
});
</script>
<?php include('includes/footer.php'); ?>
