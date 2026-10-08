<?php include('includes/header.php') ?>
<?php include('includes/navbar.php') ?>
<?php include('includes/sidebar.php') ?>
<?php
$search = isset($_GET['search']) ? trim((string) $_GET['search']) : '';
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
if ($page < 1) {
    $page = 1;
}
$limit = 200;

$whereSql = '';
$params = [];
$types = '';

if ($search !== '') {
    $keywords = preg_split('/\s+/', $search);
    $conditions = [];
    foreach ($keywords as $keyword) {
        $keyword = trim($keyword);
        if ($keyword === '') {
            continue;
        }
        $conditions[] = '(m.iname LIKE ? OR m.barcode LIKE ? OR m.code LIKE ? OR m.info LIKE ?)';
        $searchTerm = '%' . $keyword . '%';
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $types .= 'ssss';
    }
    if (!empty($conditions)) {
        $whereSql = ' AND ' . implode(' AND ', $conditions);
    }
}

$sqlCount = 'SELECT COUNT(*) AS total FROM myitems m WHERE m.isdeleted = 1' . $whereSql;
$stmtCount = $conn->prepare($sqlCount);
if ($types !== '') {
    $stmtCount->bind_param($types, ...$params);
}
$stmtCount->execute();
$totalRows = (int) $stmtCount->get_result()->fetch_assoc()['total'];
$stmtCount->close();

$totalPages = (int) ceil($totalRows / $limit);
if ($totalPages < 1) {
    $totalPages = 1;
}
if ($page > $totalPages) {
    $page = $totalPages;
}
$offset = ($page - 1) * $limit;

$sqlData = 'SELECT m.*,
    (SELECT COUNT(*) FROM fat_details fd WHERE fd.item_id = m.id AND IFNULL(fd.isdeleted, 0) = 0) AS move_count
    FROM myitems m
    WHERE m.isdeleted = 1' . $whereSql . ' ORDER BY m.id DESC LIMIT ? OFFSET ?';
$stmtData = $conn->prepare($sqlData);
$dataParams = $params;
$dataParams[] = $limit;
$dataParams[] = $offset;
$dataTypes = $types . 'ii';
$stmtData->bind_param($dataTypes, ...$dataParams);
$stmtData->execute();
$resitm = $stmtData->get_result();
?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">

            <?php if (isset($_GET['restored']) && $_GET['restored'] === '1'): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="إغلاق">&times;</button>
                    <i class="fas fa-check-circle"></i>
                    تم استرجاع الصنف ورجع لقائمة الأصناف.
                </div>
            <?php elseif (isset($_GET['purged']) && $_GET['purged'] === '1'): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="إغلاق">&times;</button>
                    <i class="fas fa-check-circle"></i>
                    تم حذف الصنف نهائياً.
                </div>
            <?php elseif (isset($_GET['purge_error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="إغلاق">&times;</button>
                    <i class="fas fa-exclamation-triangle"></i>
                    <?php
                    $purgeErrors = [
                        'moves' => 'لا يمكن حذف الصنف نهائياً لأن عليه حركة في الفواتير.',
                        'notfound' => 'الصنف غير موجود في الأصناف المحذوفة.',
                    ];
                    echo $purgeErrors[$_GET['purge_error']] ?? 'حدث خطأ أثناء الحذف النهائي، لم يتم حذف أي شيء.';
                    ?>
                </div>
            <?php elseif (isset($_GET['pass'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="إغلاق">&times;</button>
                    <i class="fas fa-exclamation-triangle"></i>
                    كلمة المرور غير صحيحة.
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0"><?= $lang_deleted_items ?? 'الاصناف المحذوفه' ?></h3>
                            <label for="invoicePriceList" class="small text-muted mb-0">الفئة السعرية</label>
                            <?php
                            if (!class_exists('InvoiceProcessor')) {
                                require_once __DIR__ . '/classes/InvoiceProcessor.php';
                            }
                            InvoiceProcessor::echoPriceListSelect($conn, 1, 'form-control form-control-sm');
                            ?>
                        </div>
                        <div class="col-md-5">
                            <input type="text" id="search" class="form-control" placeholder="بحث في الأصناف المحذوفة" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <div class="col text-left">
                            <a href="myitems.php" class="btn btn-primary btn-sm">الاصناف</a>
                        </div>
                    </div>
                    <p class="text-muted mb-0 mt-2">الأصناف دي اتحذفت حذف مؤقت. لو عليها حركة بتفضل محفوظة وتقدر ترجعها.</p>
                </div>

                <div class="card-body" id="table-container">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>م</th>
                                    <th>رقم الصنف</th>
                                    <th>الباركود</th>
                                    <th>الاسم</th>
                                    <th>الكمية</th>
                                    <th id="sellPriceTitle">قطاعي</th>
                                    <th>الحركة</th>
                                    <th>عمليات</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $x = $offset;
                            if ($resitm->num_rows === 0) {
                                echo '<tr><td colspan="8" class="text-center text-muted">لا توجد أصناف محذوفة</td></tr>';
                            }
                            while ($rowitm = $resitm->fetch_assoc()) {
                                $x++;
                                $moves = (int) $rowitm['move_count'];
                            ?>
                                <tr>
                                    <td><?= $x ?></td>
                                    <td><?= (int) $rowitm['id'] ?></td>
                                    <td><?= htmlspecialchars((string) ($rowitm['barcode'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><b><?= htmlspecialchars((string) $rowitm['iname'], ENT_QUOTES, 'UTF-8') ?></b></td>
                                    <td><?= htmlspecialchars((string) $rowitm['itmqty'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <?php
                                        $sell3 = (float) ($rowitm['price3'] ?? 0);
                                        if ($sell3 <= 0) {
                                            $sell3 = (float) ($rowitm['market_price'] ?? 0);
                                        }
                                    ?>
                                    <td class="sell-price" data-price1="<?= (float) $rowitm['price1'] ?>" data-price2="<?= (float) ($rowitm['price2'] ?? 0) ?>" data-price3="<?= $sell3 ?>"><?= htmlspecialchars((string) $rowitm['price1'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td>
                                        <?php if ($moves > 0): ?>
                                            <a class="btn btn-sm btn-light" href="item_summery.php?id=<?= (int) $rowitm['id'] ?>"><?= $moves ?> حركة</a>
                                        <?php else: ?>
                                            <span class="text-muted">بدون حركة</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#restoreitm<?= (int) $rowitm['id'] ?>">
                                            استرجاع
                                        </button>

                                        <div class="modal fade" id="restoreitm<?= (int) $rowitm['id'] ?>">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">استرجاع الصنف</h4>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <form action="do/dorestore_item.php?id=<?= (int) $rowitm['id'] ?>" method="post">
                                                        <div class="modal-body">
                                                            <p>هل تريد استرجاع <b><?= htmlspecialchars((string) $rowitm['iname'], ENT_QUOTES, 'UTF-8') ?></b> إلى قائمة الأصناف؟</p>
                                                            <input type="password" class="form-control" name="password" placeholder="كلمة مرور التعديل" required>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="submit" class="btn btn-success btn-block">استرجاع</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <?php if ($moves > 0): ?>
                                            <button type="button" class="btn btn-danger btn-sm" disabled title="لا يمكن الحذف النهائي لصنف عليه حركة">
                                                حذف نهائي
                                            </button>
                                        <?php else: ?>
                                            <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#purgeitm<?= (int) $rowitm['id'] ?>">
                                                حذف نهائي
                                            </button>

                                            <div class="modal fade" id="purgeitm<?= (int) $rowitm['id'] ?>">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-danger">
                                                            <h4 class="modal-title">حذف نهائي</h4>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <form class="purge-item-form" action="do/doforce_delete_item.php?id=<?= (int) $rowitm['id'] ?>" method="post">
                                                            <div class="modal-body">
                                                                <p>هل تريد حذف <b><?= htmlspecialchars((string) $rowitm['iname'], ENT_QUOTES, 'UTF-8') ?></b> نهائياً؟</p>
                                                                <p class="text-danger small mb-2">سيتم حذف الصنف وباركوداته ووحداته وصوره، ولا يمكن التراجع.</p>
                                                                <input type="password" class="form-control" name="password" placeholder="كلمة مرور التعديل" required>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="submit" class="btn btn-danger btn-block">حذف نهائي</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if ($totalPages > 1): ?>
                        <nav aria-label="Page navigation" class="mt-3">
                            <ul class="pagination pagination-sm justify-content-center flex-wrap">
                                <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>">السابق</a>
                                </li>
                                <li class="page-item disabled">
                                    <span class="page-link"><?= $page ?> / <?= $totalPages ?></span>
                                </li>
                                <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>">التالي</a>
                                </li>
                            </ul>
                        </nav>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
$(document).ready(function() {
    var searchTimer = null;
    $('#search').on('input', function() {
        clearTimeout(searchTimer);
        var val = $(this).val();
        searchTimer = setTimeout(function() {
            var url = 'deleted_items.php?search=' + encodeURIComponent($.trim(val));
            $('#table-container').css('opacity', '0.5');
            $('#table-container').load(url + ' #table-container > *', function() {
                $('#table-container').css('opacity', '1');
                if (typeof applyDeletedPriceList === 'function') applyDeletedPriceList();
            });
            window.history.pushState(null, '', url);
        }, 300);
    });

    $(document).on('submit', '#table-container .purge-item-form', function(e) {
        var $form = $(this);
        if ($form.data('submitting')) {
            e.preventDefault();
            return;
        }
        $form.data('submitting', true);
        $form.find('button[type="submit"]').prop('disabled', true);
    });

    $('#search').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
        }
    });

    $(document).on('click', '#table-container .pagination a', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        if (!url || $(this).parent().hasClass('disabled')) {
            return;
        }
        $('#table-container').css('opacity', '0.5');
        $('#table-container').load(url + ' #table-container > *', function() {
            $('#table-container').css('opacity', '1');
            if (typeof applyDeletedPriceList === 'function') applyDeletedPriceList();
        });
        window.history.pushState(null, '', url);
    });
});
</script>
<script src="js/invoice_price_list.js"></script>
<script>
function applyDeletedPriceList() {
    var label = $('#invoicePriceList option:selected').text();
    $('#sellPriceTitle').text(label);
    $('.sell-price').each(function() {
        var price = priceFromValues($(this).data('price1'), $(this).data('price2'), $(this).data('price3'), selectedInvoicePriceList());
        $(this).text(price);
    });
}
$('#invoicePriceList').on('change', applyDeletedPriceList);
</script>

<?php include('includes/footer.php') ?>
