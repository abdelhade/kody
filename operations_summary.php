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
$allowed_order_types = ['takeaway', 'table', 'delivery', 'dine_in'];
if (!in_array($filter_order_type, $allowed_order_types, true)) {
    $filter_order_type = '';
}

// تأكد من عمود الطيار على ot_head
$col_dp = @$conn->query("SHOW COLUMNS FROM ot_head LIKE 'delivery_person_id'");
if ($col_dp && $col_dp->num_rows == 0) {
    @$conn->query("ALTER TABLE ot_head ADD COLUMN delivery_person_id INT(11) DEFAULT NULL AFTER emp2_id");
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

// Pagination
$limit = 9999;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Base JOIN query for search support (acc1 = supplier/client, acc2 = opposite account)
$join_clause = "FROM ot_head ot
    LEFT JOIN acc_head acc1 ON acc1.id = ot.acc1
    LEFT JOIN acc_head acc2 ON acc2.id = ot.acc2";

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
        $resop = $conn->query("SELECT ot.* $join_clause WHERE $where_clause ORDER BY ot.id DESC LIMIT $limit OFFSET $offset");
        break;
    case "sale":
    case "buy_legacy": // للتوافقية
        $report_name = "مبيعات وكاشير ومردودات";
        $where_clause = "(ot.pro_tybe = 3 OR ot.pro_tybe = 9 OR ot.pro_tybe = 10) AND ot.isdeleted != 1 $dateFilter $searchFilter $extraFilter";
        $resop = $conn->query("SELECT ot.* $join_clause WHERE $where_clause ORDER BY ot.id DESC LIMIT $limit OFFSET $offset");
        break;
    default:
        $report_name = "التقرير الشامل";
        $where_clause = "ot.isdeleted != 1 $dateFilter $searchFilter $extraFilter";
        $resop = $conn->query("SELECT ot.* $join_clause WHERE $where_clause ORDER BY ot.id DESC LIMIT $limit OFFSET $offset");
}
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
                    <h3> - محلل العمل اليومي <?= $report_name ?></h3>
                    
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
                        <div class="row">
                            <div class="col-md-2 col-sm-6 col-12 mb-2">
                                <label>من</label>
                                <input class="form-control" type="date" value="<?= $strtdate_display ?>" name="strtdate">
                            </div>
                            <div class="col-md-2 col-sm-6 col-12 mb-2">
                                <label>إلى</label>
                                <input class="form-control" type="date" value="<?= $enddate_display ?>" name="enddate">
                            </div>
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
                                        <i class="fas fa-file-pdf"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-sm" id="" data-page-length="10">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>الوقت و التاريخ</th>
                                    <th>اسم العملية</th>
                                    <th>طريقة الدفع</th>
                                    <th>قيمة العملية</th>
                                    <th>صافي العملية</th>
                                    <th>المدفوع</th>
                                    <th>الحساب</th>
                                    <th>الحساب المقابل</th>
                                    <th>المخزن</th>
                                    <th>الموظف</th>
                                    <th>الربح</th>
                                    <th>المستخدم</th>
                                    <th>معرف</th>
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
                                    <td></td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $x = $offset; // بدء العد من الـ offset
                                while ($rowop = $resop->fetch_assoc()) {
                                    $x++;
                                    $proid = $rowop['id'];
                                    $tybe = $rowop['pro_tybe'];
                                    $is_return = ($tybe == 10);
                                    ?>
                                    <tr class="<?= $is_return ? 'table-danger' : '' ?>">
                                        <td><?= $x ?></td>
                                        <td><?= $rowop['crtime'] ?></td>
                                        <td>
                                            <a class="btn btn-block btn-light border" href="print/<?= ($tybe == 4 || $tybe == 3 || $tybe == 2) ? 'print_sales' : 'receipt' ?>.php?id=<?= $proid ?>" target="_blank">
                                                <?= $conn->query("SELECT pname FROM pro_tybes WHERE id = $tybe")->fetch_assoc()['pname'] ?>
                                            </a>
                                        </td>
                                        <td>
                                            <?php 
                                            $invoice_id = intval($rowop['id']);
                                            $paid_query = $conn->query("SELECT SUM(pro_value) as paid FROM ot_head WHERE op2 = $invoice_id AND isdeleted != 1");
                                            $paid_amount = ($paid_query && $row_paid = $paid_query->fetch_assoc()) ? ($row_paid['paid'] ?? 0) : 0;
                                            
                                            if ($paid_amount < $rowop['fat_net']): 
                                            ?>
                                                <span class="badge badge-warning">أجل</span>
                                            <?php else: ?>
                                                <span class="badge badge-success">نقدي</span>
                                            <?php endif; ?>
                                        </td>
                                         <td class="value <?= $is_return ? 'ret-value' : 'sale-value' ?>"><?= $rowop['pro_value'] ?></td>
                                        <td class="fatnet <?php if($rowop['pro_value'] != $rowop['fat_net']){echo "bg-yellow-300";} ?>">
                                            <?= $rowop['fat_net'] ?>
                                        </td>
                                        <td class="paid-amount">
                                            <?= number_format($paid_amount, 2, '.', '') ?>
                                            <?php 
                                            $remaining = $rowop['fat_net'] - $paid_amount;
                                            if ($remaining > 0): 
                                            ?>
                                                <small class="d-block text-muted" style="font-size: 0.65rem;">(أجل: <?= number_format($remaining, 2, '.', '') ?>)</small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= !empty($rowop['acc1']) ? ($conn->query("SELECT aname FROM acc_head WHERE id = " . intval($rowop['acc1']))->fetch_assoc()['aname'] ?? '') : '' ?></td>
                                        <td><?= !empty($rowop['acc2']) ? ($conn->query("SELECT aname FROM acc_head WHERE id = " . intval($rowop['acc2']))->fetch_assoc()['aname'] ?? '') : '' ?></td>
                                        <td><?= $rowop['store_id'] > 0 ? ($conn->query("SELECT aname FROM acc_head WHERE id = " . intval($rowop['store_id']))->fetch_assoc()['aname'] ?? '') : '' ?></td>
                                        <td><?= $rowop['emp_id'] > 0 ? ($conn->query("SELECT aname FROM acc_head WHERE id = " . intval($rowop['emp_id']))->fetch_assoc()['aname'] ?? '') : '' ?></td>
                                         <td class="prft"><?= $rowop['profit'] ?></td>
                                        <td><?= !empty($rowop['user']) ? ($conn->query("SELECT uname FROM users WHERE id = " . intval($rowop['user']))->fetch_assoc()['uname'] ?? '') : '' ?></td>
                                        <td>
                                            <?= $rowop['id'] ?>
                                            <a href="inv_operations.php?h=<?= md5($proid) ?>&q=<?= $proid ?>&t=<?= md5($tybe) ?>">
                                                <i class="fa fa-barcode"></i>
                                            </a>
                                            <?php $proid = $rowop['id']?>
                                            
                                            <!-- زر التعديل -->
                                            <?php if(in_array($tybe, [3, 4])) { // مبيعات، مشتريات فقط (الكاشير tybe=9 ليس له صفحة تعديل) ?>
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
                                                            
                                                            <form action="do/settle_credit.php" method="POST" onsubmit="return confirm('هل أنت متأكد من تسوية هذا المبلغ؟ سيتم تصفير الأجل واعتباره مدفوعاً.');">
                                                                <input type="hidden" name="id" value="<?= $rowop['id'] ?>">
                                                                <button type="submit" class="btn btn-success">
                                                                    <i class="fa fa-check me-1"></i> تم الدفع (تسوية)
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php endif; ?>

                                            <!-- زر الحذف -->
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

                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </tbody>
                        </table>
                        <?php if($q !== 'purchase' && $q !== 'sale_legacy'): ?>
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
            let profit = 0;
            
            visibleRows.forEach(row => {
                const valEl = row.querySelector(".value");
                const netEl = row.querySelector(".fatnet");
                const paidEl = row.querySelector(".paid-amount");
                const prftEl = row.querySelector(".prft");
                
                const val = valEl ? parseFloat(valEl.textContent.trim()) || 0 : 0;
                const net = netEl ? parseFloat(netEl.textContent.trim()) || 0 : 0;
                const pd = paidEl ? parseFloat(paidEl.textContent.trim()) || 0 : 0;
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
                    }
                }
                
                fatnet += net;
                paid += pd;
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
            setVal("total_profit_val", profit);
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
    });
</script>
<script>
function printBrutal() {
    window.print();
}
</script>

<style>
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
            <div style="font-size: 12px;">الأرباح</div>
            <div style="font-size: 24px; font-weight: 900;" id="brutal_profit">0.00</div>
        </div>
    </div>
</div>

<script>
// Update brutal summary values when page loads
$(document).ready(function() {
    setTimeout(function() {
        document.getElementById('brutal_sales').textContent = document.getElementById('total_sales_val')?.textContent || '0.00';
        document.getElementById('brutal_returns').textContent = document.getElementById('total_returns_val')?.textContent || '0.00';
        document.getElementById('brutal_net').textContent = document.getElementById('net_sales_val')?.textContent || '0.00';
        document.getElementById('brutal_profit').textContent = document.getElementById('total_profit_val')?.textContent || '0.00';
    }, 1000);
});
</script>
<?php include('includes/footer.php'); ?>
