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

            <?php if (isset($_GET['bulk_restored'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="إغلاق">&times;</button>
                    <i class="fas fa-check-circle"></i>
                    تم استرجاع <?= (int) $_GET['bulk_restored'] ?> صنف بنجاح وإعادتها لقائمة الأصناف النشطة.
                </div>
            <?php elseif (isset($_GET['bulk_purged'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="إغلاق">&times;</button>
                    <i class="fas fa-check-circle"></i>
                    تم حذف <?= (int) $_GET['bulk_purged'] ?> صنف نهائياً.
                    <?php if (!empty($_GET['skipped'])): ?>
                        <span class="mr-2 font-weight-bold text-dark">(تم تخطي <?= (int) $_GET['skipped'] ?> صنف لوجود حركات فواتير عليها).</span>
                    <?php endif; ?>
                </div>
            <?php elseif (isset($_GET['purge_error']) && $_GET['purge_error'] === 'all_moves'): ?>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="إغلاق">&times;</button>
                    <i class="fas fa-exclamation-triangle"></i>
                    لم يتم حذف أي صنف: جميع الأصناف المحددة (<?= (int) ($_GET['skipped'] ?? 0) ?>) عليها حركة في الفواتير ولا يمكن حذفها نهائياً.
                </div>
            <?php elseif (isset($_GET['no_selection'])): ?>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="إغلاق">&times;</button>
                    <i class="fas fa-exclamation-circle"></i>
                    يرجى تحديد صنف واحد على الأقل لتنفيذ الإجراء الجماعي.
                </div>
            <?php elseif (isset($_GET['restored']) && $_GET['restored'] === '1'): ?>
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
                    <div class="bulk-toolbar card card-outline card-secondary mb-3 shadow-none border bg-light">
                        <div class="card-body p-2 d-flex flex-wrap align-items-center justify-content-between">
                            <div class="d-flex align-items-center flex-wrap mb-1 mb-md-0">
                                <span class="badge badge-secondary py-2 px-3 font-weight-bold ml-2 mb-1" id="selectedSummaryBadge">
                                    لم يتم تحديد أصناف
                                </span>
                                <button type="button" class="btn btn-outline-info btn-sm ml-2 mb-1" id="selectNoMovesBtn" title="تحديد الأصناف التي ليس عليها حركات فقط للحذف النهائي">
                                    <i class="fas fa-filter"></i> تحديد بدون حركة فقط
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm ml-2 mb-1 d-none" id="deselectAllBtn">
                                    <i class="fas fa-times"></i> إلغاء التحديد
                                </button>
                            </div>
                            <div class="d-flex align-items-center flex-wrap">
                                <button type="button" class="btn btn-success btn-sm ml-2 mb-1 bulk-action-btn" id="bulkRestoreBtn" disabled>
                                    <i class="fas fa-trash-restore"></i> استرجاع المحدد (<span class="selected-count">0</span>)
                                </button>
                                <button type="button" class="btn btn-danger btn-sm mb-1 bulk-action-btn" id="bulkPurgeBtn" disabled>
                                    <i class="fas fa-trash"></i> حذف نهائي للمحدد (<span class="selected-count">0</span>)
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 42px;" class="text-center align-middle">
                                        <input type="checkbox" id="checkAll" title="تحديد الكل في الصفحة">
                                    </th>
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
                                echo '<tr><td colspan="9" class="text-center text-muted">لا توجد أصناف محذوفة</td></tr>';
                            }
                            while ($rowitm = $resitm->fetch_assoc()) {
                                $x++;
                                $moves = (int) $rowitm['move_count'];
                            ?>
                                <tr data-item-id="<?= (int) $rowitm['id'] ?>">
                                    <td class="text-center align-middle">
                                        <input type="checkbox" class="item-check" value="<?= (int) $rowitm['id'] ?>"
                                               data-name="<?= htmlspecialchars((string) $rowitm['iname'], ENT_QUOTES, 'UTF-8') ?>"
                                               data-moves="<?= $moves ?>">
                                    </td>
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

<!-- Modal استرجاع جماعي -->
<div class="modal fade" id="bulkRestoreModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-trash-restore"></i> استرجاع الأصناف المحددة</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="إغلاق">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="do/dobulk_deleted_items.php" method="post" id="bulkRestoreForm">
                <input type="hidden" name="bulk_action" value="restore">
                <input type="hidden" name="search" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="page" value="<?= (int) $page ?>">
                <div id="bulkRestoreHiddenInputs"></div>
                <div class="modal-body">
                    <p class="font-weight-bold mb-2">هل أنت متأكد من استرجاع <span id="bulkRestoreCount" class="text-success font-weight-bold">0</span> صنف إلى قائمة الأصناف النشطة؟</p>
                    <div id="bulkRestoreItemList" class="bg-light p-2 rounded mb-3" style="max-height: 140px; overflow-y: auto; font-size: 0.9rem;"></div>
                    <div class="form-group mb-0">
                        <label for="bulkRestorePass"><i class="fas fa-key"></i> كلمة مرور التعديل:</label>
                        <input type="password" class="form-control" name="password" id="bulkRestorePass" placeholder="أدخل كلمة مرور التعديل" required autocomplete="current-password">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-success"><i class="fas fa-check"></i> تأكيد الاسترجاع</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal حذف نهائي جماعي -->
<div class="modal fade" id="bulkPurgeModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-trash"></i> حذف نهائي للأصناف المحددة</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="إغلاق">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="do/dobulk_deleted_items.php" method="post" id="bulkPurgeForm">
                <input type="hidden" name="bulk_action" value="purge">
                <input type="hidden" name="search" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="page" value="<?= (int) $page ?>">
                <div id="bulkPurgeHiddenInputs"></div>
                <div class="modal-body">
                    <div id="bulkPurgeMovementWarning" class="alert alert-warning mb-2 d-none">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span>تنبيه: يوجد <b><span id="bulkPurgeMovesCount">0</span></b> صنف عليها حركة في الفواتير ولا يمكن حذفها نهائياً وسيتم استثناؤها تلقائياً.</span>
                    </div>
                    <p class="font-weight-bold text-danger mb-2">
                        سيتم حذف <b><span id="bulkPurgeEligibleCount">0</span></b> صنف نهائياً من قاعدة البيانات (بما فيها الباركود والوحدات والصور).
                    </p>
                    <p class="text-muted small mb-3">هذا الإجراء نهائي ولا يمكن التراجع عنه مطلقاً.</p>
                    <div id="bulkPurgeItemList" class="bg-light p-2 rounded mb-3" style="max-height: 140px; overflow-y: auto; font-size: 0.9rem;"></div>
                    <div class="form-group mb-0">
                        <label for="bulkPurgePass"><i class="fas fa-key"></i> كلمة مرور التعديل:</label>
                        <input type="password" class="form-control" name="password" id="bulkPurgePass" placeholder="أدخل كلمة مرور التعديل" required autocomplete="current-password">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-danger" id="bulkPurgeSubmitBtn"><i class="fas fa-trash"></i> تأكيد الحذف النهائي</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    function getCheckedBoxes() {
        return $('#table-container .item-check:checked');
    }

    function updateBulkState() {
        var $allChecks = $('#table-container .item-check');
        var $checked = $allChecks.filter(':checked');
        var total = $allChecks.length;
        var count = $checked.length;
        
        var movesCount = 0;
        var noMovesCount = 0;
        $checked.each(function() {
            var moves = parseInt($(this).data('moves')) || 0;
            if (moves > 0) movesCount++;
            else noMovesCount++;
        });

        $('.selected-count').text(count);
        
        if (count > 0) {
            $('#bulkRestoreBtn').prop('disabled', false);
            $('#bulkPurgeBtn').prop('disabled', noMovesCount === 0);
            $('#deselectAllBtn').removeClass('d-none');
            var badgeText = 'تم تحديد ' + count + ' صنف';
            if (movesCount > 0) {
                badgeText += ' (' + noMovesCount + ' بدون حركة، ' + movesCount + ' بحركة)';
            }
            $('#selectedSummaryBadge').removeClass('badge-secondary').addClass('badge-primary').text(badgeText);
        } else {
            $('#bulkRestoreBtn').prop('disabled', true);
            $('#bulkPurgeBtn').prop('disabled', true);
            $('#deselectAllBtn').addClass('d-none');
            $('#selectedSummaryBadge').removeClass('badge-primary').addClass('badge-secondary').text('لم يتم تحديد أصناف');
        }

        $('#checkAll').prop('checked', total > 0 && count === total);
        if (typeof $('#checkAll').prop === 'function') {
            $('#checkAll').prop('indeterminate', count > 0 && count < total);
        }
    }

    $(document).on('change', '#checkAll', function() {
        var checked = $(this).is(':checked');
        $('#table-container .item-check').prop('checked', checked).each(function() {
            $(this).closest('tr').toggleClass('table-primary', checked);
        });
        updateBulkState();
    });

    $(document).on('change', '.item-check', function() {
        $(this).closest('tr').toggleClass('table-primary', $(this).is(':checked'));
        updateBulkState();
    });

    $(document).on('click', '#selectNoMovesBtn', function() {
        $('#table-container .item-check').each(function() {
            var moves = parseInt($(this).data('moves')) || 0;
            var shouldCheck = (moves === 0);
            $(this).prop('checked', shouldCheck);
            $(this).closest('tr').toggleClass('table-primary', shouldCheck);
        });
        updateBulkState();
    });

    $(document).on('click', '#deselectAllBtn', function() {
        $('#table-container .item-check').prop('checked', false).closest('tr').removeClass('table-primary');
        updateBulkState();
    });

    $(document).on('click', '#bulkRestoreBtn', function() {
        var $checked = getCheckedBoxes();
        if (!$checked.length) return;
        
        var hiddenHtml = '';
        var listHtml = '<ul class="mb-0 pr-3">';
        $checked.each(function() {
            var id = $(this).val();
            var name = $(this).data('name') || ('صنف #' + id);
            hiddenHtml += '<input type="hidden" name="item_ids[]" value="' + id + '">';
            listHtml += '<li>' + $('<div>').text(name).html() + '</li>';
        });
        listHtml += '</ul>';
        
        $('#bulkRestoreHiddenInputs').html(hiddenHtml);
        $('#bulkRestoreCount').text($checked.length);
        $('#bulkRestoreItemList').html(listHtml);
        $('#bulkRestorePass').val('');
        $('#bulkRestoreModal').modal('show');
    });

    $(document).on('click', '#bulkPurgeBtn', function() {
        var $checked = getCheckedBoxes();
        if (!$checked.length) return;

        var hiddenHtml = '';
        var listHtml = '<ul class="mb-0 pr-3">';
        var eligibleCount = 0;
        var movesCount = 0;

        $checked.each(function() {
            var id = $(this).val();
            var name = $(this).data('name') || ('صنف #' + id);
            var moves = parseInt($(this).data('moves')) || 0;
            hiddenHtml += '<input type="hidden" name="item_ids[]" value="' + id + '">';
            if (moves > 0) {
                movesCount++;
                listHtml += '<li class="text-muted"><del>' + $('<div>').text(name).html() + '</del> <span class="badge badge-warning">عليه حركة - سيتم تخطيه</span></li>';
            } else {
                eligibleCount++;
                listHtml += '<li class="text-danger font-weight-bold">' + $('<div>').text(name).html() + '</li>';
            }
        });
        listHtml += '</ul>';

        $('#bulkPurgeHiddenInputs').html(hiddenHtml);
        $('#bulkPurgeItemList').html(listHtml);
        $('#bulkPurgeEligibleCount').text(eligibleCount);
        $('#bulkPurgeMovesCount').text(movesCount);
        
        if (movesCount > 0) {
            $('#bulkPurgeMovementWarning').removeClass('d-none');
        } else {
            $('#bulkPurgeMovementWarning').addClass('d-none');
        }

        if (eligibleCount === 0) {
            $('#bulkPurgeSubmitBtn').prop('disabled', true);
        } else {
            $('#bulkPurgeSubmitBtn').prop('disabled', false);
        }

        $('#bulkPurgePass').val('');
        $('#bulkPurgeModal').modal('show');
    });

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
                updateBulkState();
            });
            window.history.pushState(null, '', url);
        }, 300);
    });

    $(document).on('submit', '#table-container .purge-item-form, #bulkRestoreForm, #bulkPurgeForm', function(e) {
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
            updateBulkState();
        });
        window.history.pushState(null, '', url);
    });

    updateBulkState();
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
