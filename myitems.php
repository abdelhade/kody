<?php include('includes/header.php') ?>
<?php include('includes/navbar.php') ?>
<?php include('includes/sidebar.php') ?>
<?php
$search = isset($_GET['search']) ? trim((string)$_GET['search']) : '';
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

// 1. Get total rows count
$sqlCount = "SELECT COUNT(*) as total FROM myitems WHERE isdeleted = 0" . $whereSql;
$stmtCount = $conn->prepare($sqlCount);
if ($types !== "") {
    $stmtCount->bind_param($types, ...$params);
}
$stmtCount->execute();
$totalRows = $stmtCount->get_result()->fetch_assoc()['total'];
$stmtCount->close();

// Calculate pagination parameters
$totalPages = ceil($totalRows / $limit);
if ($totalPages < 1) $totalPages = 1;
if ($page > $totalPages) {
    $page = $totalPages;
}
$offset = ($page - 1) * $limit;

// 2. Fetch paginated data
$sqlData = "SELECT * FROM myitems WHERE isdeleted = 0" . $whereSql . " ORDER BY id DESC LIMIT ? OFFSET ?";
$stmtData = $conn->prepare($sqlData);

// Append limit and offset to variables
$dataParams = $params;
$dataParams[] = $limit;
$dataParams[] = $offset;
$dataTypes = $types . "ii";

$stmtData->bind_param($dataTypes, ...$dataParams);
$stmtData->execute();
$resitm = $stmtData->get_result();
?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">

            <?php if (isset($_GET['recost']) && $_GET['recost'] === 'ok'): ?>
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
                <div class="col"><h3>الاصناف</h3></div>
                <div class="col-md-6"><input type="text" id="search" class="form-control frst" placeholder="بحث... (اضغط Enter للبحث الشامل)" value="<?= htmlspecialchars($search) ?>"></div>
                <div class="col">
                    <div class="d-flex gap-2 justify-content-end">
                        <a href="add_item.php" id="addNewElement" class="btn btn-primary btn-sm"> f3 جديد</a>
                        <a href="items_factory.php" class="btn btn-info btn-sm"><i class="fas fa-magic"></i> مصنع الأصناف</a>
                        <a href="do/recost.php" class="btn btn-secondary btn-sm">اعادة حساب</a>
                        <button id="reset-manual-prices" class="btn btn-warning btn-sm">إعادة تعيين الحماية</button>
                        <button id="reindex" class="btn btn-secondary btn-sm">اعادة الفهرسة</button>
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
                                <th>سعر البيع</th>
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
                                <td><b><?= $rowitm['price1'] ?></b></td>
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

                                            <p> هل تريد بالتأكيد الحذف <?= $rowitm['iname']?> </p>
                                               
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
            
            <?php if ($totalPages > 1): ?>
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination pagination-sm justify-content-center flex-wrap">
                        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=1&search=<?= urlencode($search) ?>" aria-label="First">
                                <span aria-hidden="true">&laquo;&laquo; الأولى</span>
                            </a>
                        </li>
                        
                        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo; السابق</span>
                            </a>
                        </li>

                        <?php
                        $startPage = max(1, $page - 2);
                        $endPage = min($totalPages, $page + 2);
                        
                        if ($startPage > 1) {
                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        }
                        
                        for ($p = $startPage; $p <= $endPage; $p++): ?>
                            <li class="page-item <?= ($p === $page) ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $p ?>&search=<?= urlencode($search) ?>"><?= $p ?></a>
                            </li>
                        <?php endfor;
                        
                        if ($endPage < $totalPages) {
                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        }
                        ?>

                        <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>" aria-label="Next">
                                <span aria-hidden="true">التالي &raquo;</span>
                            </a>
                        </li>

                        <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $totalPages ?>&search=<?= urlencode($search) ?>" aria-label="Last">
                                <span aria-hidden="true">الأخيرة &raquo;&raquo;</span>
                            </a>
                        </li>
                    </ul>
                    <div class="text-center text-muted small mt-2">
                        عرض الأصناف <?= $offset + 1 ?> إلى <?= min($offset + $limit, $totalRows) ?> من إجمالي <?= $totalRows ?> صنف (صفحة <?= $page ?> من <?= $totalPages ?>)
                    </div>
                </nav>
            <?php endif; ?>
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
    $('#search').on('input', function() {
        clearTimeout(searchTimer);
        var val = $(this).val();
        searchTimer = setTimeout(function() {
            var url = 'myitems.php?search=' + encodeURIComponent($.trim(val));
            $('#table-container').css('opacity', '0.5');
            $('#table-container').load(url + ' #table-container > *', function() {
                $('#table-container').css('opacity', '1');
            });
            window.history.pushState(null, '', url);
        }, 300);
    });

    // منع الإرسال التلقائي للنموذج عند الضغط على Enter في مربع البحث
    $('#search').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
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

<?php include('includes/footer.php') ?>
