<?php
$isLazyAjax = isset($_GET['lazy_ajax']);

if ($isLazyAjax) {
    // دفعات الـ lazy ترجع صفوف الجدول فقط — بدون header/navbar/sidebar
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['login']) || (int) ($_SESSION['userid'] ?? 0) < 1) {
        http_response_code(401);
        exit;
    }
    header('Content-Type: text/html; charset=utf-8');
    require_once __DIR__ . '/includes/connect.php';
} else {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['login']) || (int) ($_SESSION['userid'] ?? 0) < 1) {
        header('Location: index.php');
        exit;
    }

    require_once __DIR__ . '/includes/connect.php';
    require_once __DIR__ . '/includes/cost_recalculate.php';
    recalculateAverageCosts($conn);

    include('includes/header.php');
    include('includes/navbar.php');
    include('includes/sidebar.php');
}

$search = isset($_GET['search']) ? trim((string)$_GET['search']) : '';
$group1 = isset($_GET['group1']) ? (int)$_GET['group1'] : 0;
$group2 = isset($_GET['group2']) ? (int)$_GET['group2'] : 0;
$filterStoreId = isset($_GET['store_id']) ? (int) $_GET['store_id'] : 0;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$limit = 200;
$offset = ($page - 1) * $limit;

$storesRes = $conn->query("SELECT id, aname FROM acc_head WHERE is_stock = 1 AND isdeleted = 0 ORDER BY aname");
$storesList = $storesRes ? $storesRes->fetch_all(MYSQLI_ASSOC) : [];

// Build search conditions dynamically for multi-keyword fuzzy matching
$whereSql = "";
$params = [];
$types = "";

if ($search !== '') {
    $keywords = preg_split('/\s+/', $search);
    $conditions = [];
    foreach ($keywords as $keyword) {
        $keyword = trim($keyword);
        if ($keyword !== '') {
            $conditions[] = "(iname LIKE ? OR barcode LIKE ? OR code LIKE ? OR info LIKE ?)";
            $searchTerm = '%' . $keyword . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $types .= 'ssss';
        }
    }
    if (!empty($conditions)) {
        $whereSql = " AND " . implode(" AND ", $conditions);
    }
}

if ($group1 > 0) {
    $whereSql .= " AND group1 = ?";
    $params[] = $group1;
    $types .= "i";
}
if ($group2 > 0) {
    $whereSql .= " AND group2 = ?";
    $params[] = $group2;
    $types .= "i";
}

$totalRows = 0;
$totalQty = 0;
$totalCostValue = 0;
$totalSellValue = 0;

if (!$isLazyAjax) {
    // 1. Get total rows count
    $sqlCount = "SELECT COUNT(*) as total FROM myitems WHERE isdeleted = 0" . $whereSql;
    $stmtCount = $conn->prepare($sqlCount);
    if ($types !== "") {
        $stmtCount->bind_param($types, ...$params);
    }
    $stmtCount->execute();
    $totalRows = (int) $stmtCount->get_result()->fetch_assoc()['total'];
    $stmtCount->close();

    // 1.5 إجماليات الكمية/القيمة — حسب المخزن لو متختار
    if ($filterStoreId > 0) {
        $sqlSum = "SELECT
                COALESCE(SUM(COALESCE(st.qty, 0)), 0) AS total_qty,
                COALESCE(SUM(COALESCE(st.qty, 0) * m.cost_price), 0) AS total_cost,
                COALESCE(SUM(COALESCE(st.qty, 0) * m.price1), 0) AS total_sell
            FROM myitems m
            LEFT JOIN (
                SELECT item_id, SUM(qty_in) - SUM(qty_out) AS qty
                FROM fat_details
                WHERE isdeleted = 0 AND det_store = ?
                GROUP BY item_id
            ) st ON st.item_id = m.id
            WHERE m.isdeleted = 0" . $whereSql;
        $stmtSum = $conn->prepare($sqlSum);
        $sumParams = array_merge([$filterStoreId], $params);
        $sumTypes = 'i' . $types;
        $stmtSum->bind_param($sumTypes, ...$sumParams);
    } else {
        $sqlSum = "SELECT SUM(itmqty) as total_qty, SUM(itmqty * cost_price) as total_cost, SUM(itmqty * price1) as total_sell FROM myitems WHERE isdeleted = 0" . $whereSql;
        $stmtSum = $conn->prepare($sqlSum);
        if ($types !== "") {
            $stmtSum->bind_param($types, ...$params);
        }
    }
    $stmtSum->execute();
    $totalsRow = $stmtSum->get_result()->fetch_assoc();
    $stmtSum->close();

    $totalQty = $totalsRow['total_qty'] ?? 0;
    $totalCostValue = $totalsRow['total_cost'] ?? 0;
    $totalSellValue = $totalsRow['total_sell'] ?? 0;
}

// 2. Fetch data
$sqlData = "SELECT * FROM myitems WHERE isdeleted = 0" . $whereSql . " ORDER BY id DESC LIMIT ? OFFSET ?";
$stmtData = $conn->prepare($sqlData);

$dataParams = $params;
$dataParams[] = $limit;
$dataParams[] = $offset;
$dataTypes = $types . "ii";

$stmtData->bind_param($dataTypes, ...$dataParams);
$stmtData->execute();
$resitm = $stmtData->get_result();
$itemRows = $resitm->fetch_all(MYSQLI_ASSOC);
$stmtData->close();

// وحدات الدفعة كلها في استعلام واحد بدل استعلام لكل صنف
$unitsByItem = [];
$storeQtyByItem = [];
if (!empty($itemRows)) {
    $itemIds = implode(',', array_map(static fn($r) => (int) $r['id'], $itemRows));
    $resunt = $conn->query("SELECT iu.*, u.uname FROM item_units iu LEFT JOIN myunits u ON u.id = iu.unit_id WHERE iu.item_id IN ($itemIds)");
    while ($r = $resunt->fetch_assoc()) {
        $unitsByItem[(int) $r['item_id']][] = $r;
    }
    if ($filterStoreId > 0) {
        $resQty = $conn->query(
            "SELECT item_id, COALESCE(SUM(qty_in) - SUM(qty_out), 0) AS qty
             FROM fat_details
             WHERE isdeleted = 0 AND det_store = " . (int) $filterStoreId . "
               AND item_id IN ($itemIds)
             GROUP BY item_id"
        );
        if ($resQty) {
            while ($rq = $resQty->fetch_assoc()) {
                $storeQtyByItem[(int) $rq['item_id']] = (float) $rq['qty'];
            }
        }
    }
}
$shownRows = $offset + count($itemRows);
$canEditItem = Can::check('items.edit');
$canDeleteItem = Can::check('items.delete');

if (!$isLazyAjax):
?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">

            <?php if (isset($_GET['deleted']) && $_GET['deleted'] === '1'): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="إغلاق">&times;</button>
                    <i class="fas fa-check-circle"></i>
                    تم نقل الصنف إلى <a href="deleted_items.php">الأصناف المحذوفة</a>. تقدر ترجعه من هناك.
                </div>
            <?php elseif (isset($_GET['pass'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="إغلاق">&times;</button>
                    <i class="fas fa-exclamation-triangle"></i>
                    كلمة المرور غير صحيحة.
                </div>
            <?php elseif (isset($_GET['recost']) && $_GET['recost'] === 'ok'): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="إغلاق">&times;</button>
                    <i class="fas fa-check-circle"></i>
                    تم إعادة حساب التكاليف بنجاح.
                </div>
            <?php elseif (isset($_GET['recost']) && $_GET['recost'] === 'fail'): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="إغلاق">&times;</button>
                    <i class="fas fa-exclamation-triangle"></i>
                    تعذّر إكمال إعادة حساب التكاليف. تحقق من الاتصال بقاعدة البيانات أو البيانات ثم أعد المحاولة.
                </div>
            <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <div class="row">
                <div class="col">
                    <h3>الاصناف</h3>
                    <?php
                    if (!class_exists('InvoiceProcessor')) {
                        require_once __DIR__ . '/classes/InvoiceProcessor.php';
                    }
                    $priceListNames = [1 => 'قطاعي', 2 => 'جملة', 3 => 'سعر 3'];
                    foreach (InvoiceProcessor::priceLists($conn) as $plist) {
                        $plId = (int) ($plist['id'] ?? 0);
                        if ($plId >= 1 && $plId <= 3 && trim((string) $plist['pname']) !== '') {
                            $priceListNames[$plId] = (string) $plist['pname'];
                        }
                    }
                    ?>
                </div>
                <div class="col-md-7">
                    <div class="d-flex align-items-center flex-nowrap" style="gap:6px;">
                        <input type="text" id="search" class="form-control form-control-sm frst" style="min-width:140px; flex:1.4;" placeholder="بحث..." value="<?= htmlspecialchars($search) ?>">
                        <select id="filter_store" class="form-control form-control-sm" style="min-width:110px; flex:1;" title="المخزن">
                            <option value="0">كل المخازن</option>
                            <?php foreach ($storesList as $st): ?>
                                <option value="<?= (int) $st['id'] ?>" <?= $filterStoreId === (int) $st['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($st['aname']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <select id="filter_group1" class="form-control form-control-sm" style="min-width:110px; flex:1;">
                            <option value="0">كل المجموعات</option>
                            <?php
                            $resg1 = $conn->query("SELECT * FROM item_group WHERE isdeleted = 0");
                            while ($rg1 = $resg1->fetch_assoc()) {
                                $sel = ($group1 == $rg1['id']) ? 'selected' : '';
                                echo "<option value='{$rg1['id']}' $sel>" . htmlspecialchars($rg1['gname']) . "</option>";
                            }
                            ?>
                        </select>
                        <select id="filter_group2" class="form-control form-control-sm" style="min-width:110px; flex:1;">
                            <option value="0">كل التصنيفات</option>
                            <?php
                            $resg2 = $conn->query("SELECT * FROM item_group2 WHERE isdeleted = 0");
                            while ($rg2 = $resg2->fetch_assoc()) {
                                $sel = ($group2 == $rg2['id']) ? 'selected' : '';
                                echo "<option value='{$rg2['id']}' $sel>" . htmlspecialchars($rg2['gname']) . "</option>";
                            }
                            ?>
                        </select>
                        <button id="btn_filter_submit" class="btn btn-primary btn-sm flex-shrink-0">بحث</button>
                    </div>
                </div>
                <div class="col">
                    <div class="d-flex gap-2 justify-content-end flex-wrap">
                        <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#calcModal"><i class="fas fa-calculator"></i> إجمالي القيم</button>
                        <button type="button" class="btn btn-success btn-sm" id="btnExportExcelSql" title="تصدير كل الأصناف المطابقة للبحث والفلترة">
                            <i class="fas fa-file-excel mr-1"></i> تصدير Excel
                        </button>
                        <?php if (Can::check('items.add')) { ?>
                        <a href="add_item.php" id="addNewElement" class="btn btn-primary btn-sm"> f3 جديد</a>
                        <?php } ?>
                        <?php if ($canDeleteItem) { ?>
                        <a href="deleted_items.php" class="btn btn-outline-danger btn-sm">الاصناف المحذوفه</a>
                        <?php } ?>
                        <?php if (Can::check('items.pricing')) { ?>
                        <a href="do/recost.php" class="btn btn-secondary btn-sm">اعادة حساب</a>
                        <?php } ?>
                    </div>
                </div>
                </div> 
                <div class="row"><div id="response-message"></div></div>
            </div>

            <div class="card-body" id="table-container">
                <div class="table-responsive">
                    <table data-page-length='50'  id="horsTable" class="table table-striped"> 
                        <thead>
                            <tr>
                                <th>م</th>
                                <th>رقم الصنف</th>
                                <th>الباركود</th>
                                <th>الاسم</th>
                                <th>الكميه</th>
                                <th>الوحدة</th>
                                <th>الوصف</th>
                                <th><?= htmlspecialchars($priceListNames[1], ENT_QUOTES, 'UTF-8') ?></th>
                                <th><?= htmlspecialchars($priceListNames[2], ENT_QUOTES, 'UTF-8') ?></th>
                                <th><?= htmlspecialchars($priceListNames[3], ENT_QUOTES, 'UTF-8') ?></th>
                                <th>سعر الشراء</th>
                                <th>سعر التكلفة</th>
                                <th class="no-export">عمليات</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php endif; // end !$isLazyAjax ?>
                        <?php
                        $x = $offset;
                        foreach ($itemRows as $rowitm) {
                        $x++;
                            $itemid = (int) $rowitm['id'];
                            $unitRows = $unitsByItem[$itemid] ?? [];
                            $displayQty = $filterStoreId > 0
                                ? (float) ($storeQtyByItem[$itemid] ?? 0)
                                : (float) ($rowitm['itmqty'] ?? 0);
                            $qtyLink = 'item_summery.php?id=' . $itemid;
                            if ($filterStoreId > 0) {
                                $qtyLink .= '&store_id=' . $filterStoreId;
                            }
                            $searchParts = [
                                (string) $rowitm['id'],
                                isset($rowitm['code']) ? (string) $rowitm['code'] : '',
                                isset($rowitm['barcode']) ? (string) $rowitm['barcode'] : '',
                                (string) $rowitm['iname'],
                                isset($rowitm['name2']) ? (string) $rowitm['name2'] : '',
                                isset($rowitm['info']) ? (string) $rowitm['info'] : '',
                            ];
                            foreach ($unitRows as $ur) {
                                $searchParts[] = isset($ur['uname']) ? (string) $ur['uname'] : '';
                                $searchParts[] = isset($ur['unit_barcode']) ? (string) $ur['unit_barcode'] : '';
                                $searchParts[] = isset($ur['u_val']) ? (string) $ur['u_val'] : '';
                            }
                            $dataSearch = htmlspecialchars(implode(' ', array_filter($searchParts)), ENT_QUOTES, 'UTF-8');
                        ?>
                        
                            <tr data-search="<?= $dataSearch ?>">
                                <td><?= $x ?></td>
                                <td><?= $rowitm['id'] ?></td>
                                <td><?= isset($rowitm['barcode']) ? $rowitm['barcode'] : '' ?></td>
                                <td><b><?= $rowitm['iname'] ?></b></td>
                                <td class="qty" data-row-id="<?= $rowitm['id'] ?>" data-original-qty="<?= htmlspecialchars((string) $displayQty, ENT_QUOTES, 'UTF-8') ?>">
                                    <a class="btn btn-sm btn-light" id="item_qty_<?= $rowitm['id'] ?>" href="<?= htmlspecialchars($qtyLink, ENT_QUOTES, 'UTF-8') ?>"><?= $displayQty ?></a>
                                </td>
                                <td class="unit">
                                <select name="" id="item_unit_<?= $rowitm['id'] ?>" class="form-control form-control-sm" data-row-id="<?= $rowitm['id'] ?>">
                                    <?php foreach ($unitRows as $rowunt) { ?>
                                    <option value="<?= $rowunt['u_val']?>">
                                        <?= htmlspecialchars($rowunt['uname']) ?>
                                        [<?= $rowunt['u_val'] ?>]
                                    </option>
                                    <?php } ?>
                                </select>
                                </td>
                                <td><?= $rowitm['info'] ?></td>
                                <?php
                                    $sell3 = (float) ($rowitm['price3'] ?? 0);
                                    if ($sell3 <= 0) {
                                        $sell3 = (float) ($rowitm['market_price'] ?? 0);
                                    }
                                ?>
                                <td><b><?= $rowitm['price1'] ?></b></td>
                                <td><b><?= (float) ($rowitm['price2'] ?? 0) ?></b></td>
                                <td><b><?= $sell3 ?></b></td>
                                <td><b><?= $rowitm['last_price'] ?></b></td>
                                <td><b><?= $rowitm['cost_price'] ?></b></td>
                               
                                    <td class="no-export">
                                        <?php if ($canEditItem) { ?>
                                        <a class="btn btn-warning btn-sm" href="add_item.php?edit=<?= $rowitm['id'] ?>"><i class="fa fa-pen"></i></a>
                                        <?php } ?>
                                        <?php if ($canDeleteItem) { ?>
                                        <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteitm<?= $rowitm['id']?>">
                                            <i class="fa fa-trash"></i>
                                        </button>

                                
                                  <div class="modal fade" id="deleteitm<?= $rowitm['id']?>">
                                    <div class="modal-dialog">
                                    <div class="modal-content bg-danger">
                                        <div class="modal-header">
                                        <h4 class="modal-title">تحذير</h4>
                                        <a href="#">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                            </button>
                                        </a>
                                        </div>

                                        <div class="modal-body">

                                            <p>هل تريد نقل الصنف <b><?= htmlspecialchars((string) $rowitm['iname'], ENT_QUOTES, 'UTF-8') ?></b> إلى الأصناف المحذوفة؟</p>
                                            <p>الصنف مش هيتمسح نهائياً. لو عليه حركة هيفضل محفوظ وتقدر ترجعه من شاشة الأصناف المحذوفة.</p>
                                               
                                            <form action="do/dodel_item.php?id=<?= $rowitm['id'] ?>" method="post">
                                            <input type="password" class="form-control" name="password" id="password">
                                          
                                            </div>
                                            <div class="modal-footer justify-content-between">
                                           <button type="submit" class="btn btn-flat btn-sm btn-outline-light btn-block" id="sub">حذف</button>
                                            </form>  
                                            
                                            
                                        </div>

                                    </div>
                                    <!-- /.modal-content -->
                                    </div>
                                    <!-- /.modal-dialog -->
                                </div>
                                        <?php } ?>

                            
                                </td>
                            </tr>

                            <?php } ?>
                        <?php 
                        if ($isLazyAjax) {
                            exit;
                        }
                        ?>
                        </tbody>
                    </table>

                </div>

                <div id="myitemsShownCounter" class="text-center text-muted small mt-2 mb-1"
                     data-shown="<?= (int) $shownRows ?>" data-total="<?= (int) $totalRows ?>">
                    تم عرض <b id="myitemsShownCount"><?= (int) $shownRows ?></b>
                    من <b id="myitemsTotalCount"><?= (int) $totalRows ?></b> صنف
                </div>
            </div>
        </div>

        </div>

    </section>
</div>

<!-- Modal for totals -->
<div class="modal fade" id="calcModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">احتساب الكميات في أسعارها (للمعروض فقط)</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        إجمالي الكميات
                        <span class="badge badge-primary badge-pill"><?= number_format((float)$totalQty, 2) ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        إجمالي التكلفة (الكمية × سعر التكلفة)
                        <span class="badge badge-warning badge-pill"><?= number_format((float)$totalCostValue, 2) ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        إجمالي البيع (الكمية × سعر القطاعي)
                        <span class="badge badge-success badge-pill"><?= number_format((float)$totalSellValue, 2) ?></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // إيقاف البحث المحلي لعدم التعارض
    $('#search').off('input keyup');

    // بحث حي مباشر من قاعدة البيانات (Live AJAX Search with Debounce)
    var searchTimer = null;
    
    var lazyPage = 1;
    var lazyLimit = <?= (int) $limit ?>;
    var isLoading = false;
    var hasMore = true;

    function myitemsSyncCounter() {
        var $counter = $('#myitemsShownCounter');
        var total = parseInt($counter.data('total'), 10) || 0;
        var shown = $('#horsTable tbody tr[data-search]').length;
        $('#myitemsShownCount').text(shown);
        hasMore = shown < total;
    }
    myitemsSyncCounter();
    
    function myitemsFilterQuery(extra) {
        var q = 'search=' + encodeURIComponent($.trim($('#search').val() || ''))
            + '&group1=' + ($('#filter_group1').val() || 0)
            + '&group2=' + ($('#filter_group2').val() || 0)
            + '&store_id=' + ($('#filter_store').val() || 0);
        return extra ? (extra + '&' + q) : q;
    }

    function triggerSearch() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function() {
            var url = 'myitems.php?' + myitemsFilterQuery();
            $('#table-container').css('opacity', '0.5');
            $('#table-container').load(url + ' #table-container > *', function() {
                $('#table-container').css('opacity', '1');
                if (typeof applySellPriceList === 'function') applySellPriceList();
                lazyPage = 1;
                isLoading = false;
                myitemsSyncCounter();
            });
            window.history.pushState(null, '', url);
        }, 300);
    }

    $('#btn_filter_submit').on('click', triggerSearch);
    // تغيير المخزن يعيد تحميل الصفحة عشان الإجماليات تتحدّث
    $('#filter_store').on('change', function() {
        window.location.href = 'myitems.php?' + myitemsFilterQuery();
    });

    // منع الإرسال التلقائي للنموذج عند الضغط على Enter في مربع البحث وتفعيل البحث
    $('#search').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            triggerSearch();
        }
    });

    // Lazy loading on scroll
    $(window).scroll(function() {
        if (!hasMore || isLoading) return;
        
        if ($(window).scrollTop() + $(window).height() >= $(document).height() - 300) {
            isLoading = true;
            lazyPage++;
            
            var url = 'myitems.php?lazy_ajax=1&page=' + lazyPage + '&' + myitemsFilterQuery();
            
            $('#horsTable tbody').append('<tr id="loading-row"><td colspan="13" class="text-center py-3"><i class="fas fa-spinner fa-spin"></i> جاري تحميل المزيد...</td></tr>');
            
            $.get(url, function(data) {
                $('#loading-row').remove();
                if ($.trim(data) === '') {
                    hasMore = false;
                } else {
                    var $rows = $($.parseHTML($.trim(data), document, false)).filter('tr');
                    $('#horsTable tbody').append($rows);
                    if (typeof applySellPriceList === 'function') applySellPriceList();
                    myitemsSyncCounter();
                    if ($rows.length < lazyLimit) hasMore = false;
                }
                isLoading = false;
            }).fail(function() {
                $('#loading-row').remove();
                isLoading = false;
            });
        }
    });

    // إعادة تعيين حماية الأسعار اليدوية
    $('#reset-manual-prices').click(function() {
        if (confirm('هل أنت متأكد من إعادة تعيين حماية الأسعار؟ سيتم إعادة حساب جميع الأسعار عند الضغط على إعادة حساب')) {
            $.ajax({
                url: 'do/reset_manual_prices.php',
                method: 'POST',
                success: function(response) {
                    alert('تم إعادة التعيين بنجاح');
                    location.reload();
                },
                error: function() {
                    alert('حدث خطأ');
                }
            });
        }
    });
    
    // استمع لتغيرات في جميع قوائم الوحدة باستخدام Event Delegation ليعمل مع البحث الحي
    $(document).on('change', '.unit select', function() {
        // الحصول على معرف الصف من السمة data-row-id
        var rowId = $(this).data('row-id');
        
        // الحصول على قيمة الوحدة المحددة
        var selectedUnitValue = $(this).val();
        
        // الحصول على عنصر الكمية للصف المحدد
        var qtyElement = $('#item_qty_' + rowId);
        
        // الحصول على الكمية الأصلية من السمة data-original-qty
        var originalQty = parseFloat($('.qty[data-row-id="' + rowId + '"]').data('original-qty'));
        
        // التحقق من أن قيمة الوحدة المحددة ليست صفر لتجنب القسمة على صفر
        if (selectedUnitValue != 0) {
            // حساب الكمية الجديدة
            var newQty = originalQty / selectedUnitValue;
            
            // تحديث الكمية المعروضة على الصفحة
            qtyElement.text(newQty.toFixed(2));
        }
    });

    // تصدير الأصناف عبر SQL مباشرة بدون حدود (يشمل كل نتائج البحث والفلترة)
    $(document).on('click', '#btnExportExcelSql', function(e) {
        e.preventDefault();
        var $btn = $(this);
        var originalHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> جاري التصدير...');

        var exportUrl = 'do/export_excel.php?type=items&' + myitemsFilterQuery();

        fetch(exportUrl, { credentials: 'same-origin' })
            .then(function(res) {
                var ct = res.headers.get('Content-Type') || '';
                if (!res.ok || ct.indexOf('spreadsheetml') === -1) {
                    return res.text().then(function(txt) {
                        var msg = $('<div>').html(txt).text().trim();
                        throw new Error(msg.substring(0, 500) || ('HTTP ' + res.status));
                    });
                }
                return res.blob();
            })
            .then(function(blob) {
                var url = URL.createObjectURL(blob);
                var a = document.createElement('a');
                a.href = url;
                a.download = 'دليل_الأصناف.xlsx';
                document.body.appendChild(a);
                a.click();
                a.remove();
                setTimeout(function() { URL.revokeObjectURL(url); }, 10000);
            })
            .catch(function(err) {
                alert(err.message || 'تعذّر تصدير ملف الإكسل');
            })
            .finally(function() {
                $btn.prop('disabled', false).html(originalHtml);
            });
    });
});
</script>
<script>
    $(document).ready(function() {
    $('#reindex').click(function() {
        $.ajax({
            url: 'js/ajax/reindex.php',
            type: 'POST', // or 'GET' depending on your PHP handling
            dataType: 'json', // change to 'text' if not returning JSON
            success: function(response) {
                // Handle success
                $('#response-message').html('Reindexing successful: ' + response.message);
            },
            error: function(xhr, status, error) {
                // Handle error
                $('#response-message').html('An error occurred: ' + error);
            }
        });
    });
});

</script>
<?php include('includes/footer.php') ?>
