<?php include('includes/header.php') ?>
<?php include('includes/navbar.php') ?>
<?php include('includes/sidebar.php') ?>
<?php
$search = isset($_GET['search']) ? trim((string)$_GET['search']) : '';
$group1 = isset($_GET['group1']) ? (int)$_GET['group1'] : 0;
$group2 = isset($_GET['group2']) ? (int)$_GET['group2'] : 0;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$limit = 200;

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

// 1. Get total rows count
$sqlCount = "SELECT COUNT(*) as total FROM myitems WHERE isdeleted = 0" . $whereSql;
$stmtCount = $conn->prepare($sqlCount);
if ($types !== "") {
    $stmtCount->bind_param($types, ...$params);
}
$stmtCount->execute();
$totalRows = $stmtCount->get_result()->fetch_assoc()['total'];
$stmtCount->close();

// Calculate pagination parameters (removed)
$offset = 0; // Keeping offset for numbering the table rows if needed

// 2. Fetch data
$sqlData = "SELECT * FROM myitems WHERE isdeleted = 0" . $whereSql . " ORDER BY id DESC";
$stmtData = $conn->prepare($sqlData);

if ($types !== "") {
    $stmtData->bind_param($types, ...$params);
}
$stmtData->execute();
$resitm = $stmtData->get_result();
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
                    <label for="invoicePriceList" class="small text-muted mb-0">الفئة السعرية</label>
                    <?php
                    if (!class_exists('InvoiceProcessor')) {
                        require_once __DIR__ . '/classes/InvoiceProcessor.php';
                    }
                    InvoiceProcessor::echoPriceListSelect($conn, 1, 'form-control form-control-sm');
                    ?>
                </div>
                <div class="col-md-6">
                    <input type="text" id="search" class="form-control frst mb-2" placeholder="بحث... (اضغط Enter للبحث الشامل)" value="<?= htmlspecialchars($search) ?>">
                    <div class="d-flex gap-2">
                        <select id="filter_group1" class="form-control form-control-sm">
                            <option value="0">كل المجموعات</option>
                            <?php
                            $resg1 = $conn->query("SELECT * FROM item_group WHERE isdeleted = 0");
                            while ($rg1 = $resg1->fetch_assoc()) {
                                $sel = ($group1 == $rg1['id']) ? 'selected' : '';
                                echo "<option value='{$rg1['id']}' $sel>" . htmlspecialchars($rg1['gname']) . "</option>";
                            }
                            ?>
                        </select>
                        <select id="filter_group2" class="form-control form-control-sm">
                            <option value="0">كل التصنيفات</option>
                            <?php
                            $resg2 = $conn->query("SELECT * FROM item_group2 WHERE isdeleted = 0");
                            while ($rg2 = $resg2->fetch_assoc()) {
                                $sel = ($group2 == $rg2['id']) ? 'selected' : '';
                                echo "<option value='{$rg2['id']}' $sel>" . htmlspecialchars($rg2['gname']) . "</option>";
                            }
                            ?>
                        </select>
                        <button id="btn_filter_submit" class="btn btn-primary btn-sm">بحث</button>
                    </div>
                </div>
                <div class="col">
                    <div class="d-flex gap-2 justify-content-end">
                        <a href="add_item.php" id="addNewElement" class="btn btn-primary btn-sm"> f3 جديد</a>
                        <a href="deleted_items.php" class="btn btn-outline-danger btn-sm">الاصناف المحذوفه</a>
                        <a href="do/recost.php" class="btn btn-secondary btn-sm">اعادة حساب</a>
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
                                <th id="sellPriceTitle">قطاعي</th>
                                <th>سعر الشراء</th>
                                <th>سعر التكلفة</th>
                                <th>عمليات</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $x = $offset;
                        while ($rowitm = $resitm->fetch_assoc()) {
                        $x++;
                            $itemid = (int) $rowitm['id'];
                            $resunt = $conn->query("SELECT iu.*, u.uname FROM item_units iu LEFT JOIN myunits u ON u.id = iu.unit_id WHERE iu.item_id = $itemid");
                            $unitRows = [];
                            while ($r = $resunt->fetch_assoc()) {
                                $unitRows[] = $r;
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
                                <td class="qty" data-row-id="<?= $rowitm['id'] ?>" data-original-qty="<?= $rowitm['itmqty'] ?>">
                                    <a class="btn btn-sm btn-light" id="item_qty_<?= $rowitm['id'] ?>" href="item_summery.php?id=<?= $rowitm['id'] ?>"><?= $rowitm['itmqty'] ?></a>
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
                                <td class="sell-price" data-price1="<?= (float) $rowitm['price1'] ?>" data-price2="<?= (float) ($rowitm['price2'] ?? 0) ?>" data-price3="<?= $sell3 ?>"><b><?= $rowitm['price1'] ?></b></td>
                                <td><b><?= $rowitm['last_price'] ?></b></td>
                                <td><b><?= $rowitm['cost_price'] ?></b></td>
                               
                                    <td>
                                        <a class="btn btn-warning btn-sm" href="add_item.php?edit=<?= $rowitm['id'] ?>"><i class="fa fa-pen"></i></a>
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

                            
                                </td>
                            </tr>

                            <?php } ?>
                        </tbody>
                    </table>

                </div>
            </div>
            
            <div class="text-center text-muted small mt-2 mb-3">
                إجمالي الأصناف: <?= $totalRows ?>
            </div>
        </div>

        </div>

    </section>
</div>


<script>
$(document).ready(function() {
    // إيقاف البحث المحلي لعدم التعارض
    $('#search').off('input keyup');

    // بحث حي مباشر من قاعدة البيانات (Live AJAX Search with Debounce)
    var searchTimer = null;
    
    function triggerSearch() {
        clearTimeout(searchTimer);
        var val = $('#search').val();
        var g1 = $('#filter_group1').val();
        var g2 = $('#filter_group2').val();
        
        searchTimer = setTimeout(function() {
            var url = 'myitems.php?search=' + encodeURIComponent($.trim(val)) + '&group1=' + g1 + '&group2=' + g2;
            $('#table-container').css('opacity', '0.5');
            $('#table-container').load(url + ' #table-container > *', function() {
                $('#table-container').css('opacity', '1');
                if (typeof applySellPriceList === 'function') applySellPriceList();
            });
            window.history.pushState(null, '', url);
        }, 300);
    }

    $('#btn_filter_submit').on('click', triggerSearch);

    // منع الإرسال التلقائي للنموذج عند الضغط على Enter في مربع البحث وتفعيل البحث
    $('#search').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            triggerSearch();
        }
    });

    // تفعيل التنقل بين الصفحات عبر AJAX لتجنب إعادة تحميل الصفحة بالكامل
    $(document).on('click', '#table-container .pagination a', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        if (url) {
            $('#table-container').css('opacity', '0.5');
            $('#table-container').load(url + ' #table-container > *', function() {
                $('#table-container').css('opacity', '1');
                if (typeof applySellPriceList === 'function') applySellPriceList();
            });
            window.history.pushState(null, '', url);
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
<script src="js/invoice_price_list.js"></script>
<script>
function applySellPriceList() {
    var label = $('#invoicePriceList option:selected').text();
    $('#sellPriceTitle').text(label);
    $('.sell-price').each(function() {
        var price = priceFromValues($(this).data('price1'), $(this).data('price2'), $(this).data('price3'), selectedInvoicePriceList());
        $(this).find('b').text(price);
    });
}
$('#invoicePriceList').on('change', applySellPriceList);
</script>

<?php include('includes/footer.php') ?>
