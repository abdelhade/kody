<?php include('includes/header.php') ?>
<?php include('includes/navbar.php') ?>
<?php include('includes/sidebar.php') ?>
<?php
$search = isset($_GET['search']) ? trim((string) $_GET['search']) : '';
$group1 = isset($_GET['group1']) ? (int) $_GET['group1'] : 0;
$group2 = isset($_GET['group2']) ? (int) $_GET['group2'] : 0;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
if ($page < 1) {
    $page = 1;
}
$limit = 200;
$offset = ($page - 1) * $limit;

$whereSql = ' WHERE m.isdeleted = 0';
$params = [];
$types = '';

if ($search !== '') {
    $keywords = preg_split('/\s+/', $search);
    foreach ($keywords as $keyword) {
        $keyword = trim((string) $keyword);
        if ($keyword === '') {
            continue;
        }
        $whereSql .= ' AND (m.iname LIKE ? OR m.barcode LIKE ? OR m.code LIKE ?)';
        $term = '%' . $keyword . '%';
        $params[] = $term;
        $params[] = $term;
        $params[] = $term;
        $types .= 'sss';
    }
}
if ($group1 > 0) {
    $whereSql .= ' AND m.group1 = ?';
    $params[] = $group1;
    $types .= 'i';
}
if ($group2 > 0) {
    $whereSql .= ' AND m.group2 = ?';
    $params[] = $group2;
    $types .= 'i';
}

$sqlCount = 'SELECT COUNT(*) AS total FROM myitems m' . $whereSql;
$stmtCount = $conn->prepare($sqlCount);
if ($types !== '') {
    $stmtCount->bind_param($types, ...$params);
}
$stmtCount->execute();
$totalRows = (int) $stmtCount->get_result()->fetch_assoc()['total'];
$stmtCount->close();
$totalPages = max(1, (int) ceil($totalRows / $limit));
if ($page > $totalPages) {
    $page = $totalPages;
    $offset = ($page - 1) * $limit;
}

$sqlData = 'SELECT m.id, m.iname, m.barcode, g1.gname AS group1_name, g2.gname AS group2_name
    FROM myitems m
    LEFT JOIN item_group g1 ON g1.id = m.group1 AND g1.isdeleted = 0
    LEFT JOIN item_group2 g2 ON g2.id = m.group2 AND g2.isdeleted = 0'
    . $whereSql . ' ORDER BY m.iname ASC LIMIT ? OFFSET ?';
$stmtData = $conn->prepare($sqlData);
$dataParams = $params;
$dataParams[] = $limit;
$dataParams[] = $offset;
$stmtData->bind_param($types . 'ii', ...$dataParams);
$stmtData->execute();
$items = $stmtData->get_result();

$groups = $conn->query('SELECT id, gname FROM item_group WHERE isdeleted = 0 ORDER BY gname ASC');
$categories = $conn->query('SELECT id, gname FROM item_group2 WHERE isdeleted = 0 ORDER BY gname ASC');
$groupRows = $groups ? $groups->fetch_all(MYSQLI_ASSOC) : [];
$categoryRows = $categories ? $categories->fetch_all(MYSQLI_ASSOC) : [];
?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <?php if (isset($_GET['saved'])): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <i class="fas fa-check-circle"></i>
                    تم تحديث مجموعة <?= (int) $_GET['saved'] ?> صنف.
                </div>
            <?php elseif (isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <i class="fas fa-exclamation-triangle"></i>
                    <?php if ($_GET['error'] === 'empty'): ?>
                        اختر صنفاً واحداً على الأقل، واختر المجموعة الجديدة.
                    <?php elseif ($_GET['error'] === 'group'): ?>
                        المجموعة المختارة غير موجودة.
                    <?php elseif ($_GET['error'] === 'category'): ?>
                        التصنيف المختار غير موجود.
                    <?php else: ?>
                        تعذر حفظ التعديل. حاول مرة أخرى.
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h3 class="mb-3">تعديل المجموعات للاصناف</h3>
                    <form method="get" class="form-row align-items-end">
                        <div class="col-md-4 mb-2">
                            <label for="search" class="small text-muted mb-1">بحث بالاسم أو الباركود</label>
                            <input id="search" type="text" name="search" class="form-control" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>" placeholder="اسم الصنف أو الباركود">
                        </div>
                        <div class="col-md-3 mb-2">
                            <label for="filter_group1" class="small text-muted mb-1">المجموعة الحالية</label>
                            <select id="filter_group1" name="group1" class="form-control select2">
                                <option value="0">كل المجموعات</option>
                                <?php foreach ($groupRows as $row): ?>
                                    <option value="<?= (int) $row['id'] ?>" <?= $group1 === (int) $row['id'] ? 'selected' : '' ?>><?= htmlspecialchars($row['gname'], ENT_QUOTES, 'UTF-8') ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label for="filter_group2" class="small text-muted mb-1">التصنيف الحالي</label>
                            <select id="filter_group2" name="group2" class="form-control select2">
                                <option value="0">كل التصنيفات</option>
                                <?php foreach ($categoryRows as $row): ?>
                                    <option value="<?= (int) $row['id'] ?>" <?= $group2 === (int) $row['id'] ? 'selected' : '' ?>><?= htmlspecialchars($row['gname'], ENT_QUOTES, 'UTF-8') ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <button type="submit" class="btn btn-primary btn-block">عرض</button>
                        </div>
                    </form>
                </div>

                <form method="post" action="do/doedit_item_groups.php" id="editGroupsForm">
                    <input type="hidden" name="search" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="filter_group1" value="<?= $group1 ?>">
                    <input type="hidden" name="filter_group2" value="<?= $group2 ?>">
                    <input type="hidden" name="page" value="<?= $page ?>">

                    <div class="card-body border-bottom">
                        <div class="form-row align-items-end">
                            <div class="col-md-4 mb-2">
                                <label for="new_group1" class="mb-1">المجموعة الجديدة</label>
                                <select id="new_group1" name="group1" class="form-control select2" required>
                                    <option value="">اختر المجموعة</option>
                                    <?php foreach ($groupRows as $row): ?>
                                        <option value="<?= (int) $row['id'] ?>"><?= htmlspecialchars($row['gname'], ENT_QUOTES, 'UTF-8') ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label for="new_group2" class="mb-1">التصنيف الجديد</label>
                                <select id="new_group2" name="group2" class="form-control select2">
                                    <option value="0">بدون تغيير</option>
                                    <?php foreach ($categoryRows as $row): ?>
                                        <option value="<?= (int) $row['id'] ?>"><?= htmlspecialchars($row['gname'], ENT_QUOTES, 'UTF-8') ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4 mb-2">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i>
                                    حفظ على المحدد (<span id="selectedCount">0</span>)
                                </button>
                            </div>
                        </div>
                        <small class="text-muted">لو سبت التصنيف على «بدون تغيير» المجموعة فقط هي اللي تتحدث.</small>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th width="50" class="text-center">
                                            <input type="checkbox" id="checkAll" title="تحديد الظاهر">
                                        </th>
                                        <th>الاسم</th>
                                        <th>الباركود</th>
                                        <th>المجموعة الحالية</th>
                                        <th>التصنيف الحالي</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($items->num_rows === 0): ?>
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">لا توجد أصناف مطابقة</td>
                                        </tr>
                                    <?php endif; ?>
                                    <?php while ($row = $items->fetch_assoc()): ?>
                                        <tr>
                                            <td class="text-center">
                                                <input type="checkbox" class="item-check" name="item_ids[]" value="<?= (int) $row['id'] ?>">
                                            </td>
                                            <td><?= htmlspecialchars((string) $row['iname'], ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?= htmlspecialchars((string) $row['barcode'], ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?= $row['group1_name'] !== null && $row['group1_name'] !== '' ? htmlspecialchars((string) $row['group1_name'], ENT_QUOTES, 'UTF-8') : '—' ?></td>
                                            <td><?= $row['group2_name'] !== null && $row['group2_name'] !== '' ? htmlspecialchars((string) $row['group2_name'], ENT_QUOTES, 'UTF-8') : '—' ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>

                <div class="card-footer d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        عدد الأصناف: <strong><?= $totalRows ?></strong>
                        <?php if ($totalRows > $limit): ?>
                            — الصفحة <?= $page ?> من <?= $totalPages ?>
                        <?php endif; ?>
                    </small>
                    <?php if ($totalPages > 1): ?>
                        <div>
                            <?php if ($page > 1): ?>
                                <a class="btn btn-sm btn-outline-secondary" href="edit_item_groups.php?<?= htmlspecialchars(http_build_query(['search' => $search, 'group1' => $group1, 'group2' => $group2, 'page' => $page - 1]), ENT_QUOTES, 'UTF-8') ?>">السابق</a>
                            <?php endif; ?>
                            <?php if ($page < $totalPages): ?>
                                <a class="btn btn-sm btn-outline-secondary" href="edit_item_groups.php?<?= htmlspecialchars(http_build_query(['search' => $search, 'group1' => $group1, 'group2' => $group2, 'page' => $page + 1]), ENT_QUOTES, 'UTF-8') ?>">التالي</a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</div>
<?php
$stmtData->close();
$extra_footer_scripts = <<<'HTML'
<script>
$(function () {
    if ($.fn.select2) {
        $('.select2').select2({ theme: 'bootstrap4', width: '100%' });
    }
    function refreshCount() {
        var n = $('.item-check:checked').length;
        $('#selectedCount').text(n);
        var total = $('.item-check').length;
        $('#checkAll').prop('checked', total > 0 && n === total);
    }
    $('#checkAll').on('change', function () {
        $('.item-check').prop('checked', this.checked);
        refreshCount();
    });
    $(document).on('change', '.item-check', refreshCount);
    $('#editGroupsForm').on('submit', function (e) {
        if (!$('#new_group1').val()) {
            e.preventDefault();
            alert('اختر المجموعة الجديدة.');
            return;
        }
        if ($('.item-check:checked').length < 1) {
            e.preventDefault();
            alert('اختر صنفاً واحداً على الأقل.');
        }
    });
});
</script>
HTML;
include('includes/footer.php');
?>
