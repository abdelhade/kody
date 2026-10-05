<?php include('includes/header.php') ?>
<?php include('includes/navbar.php') ?>
<?php include('includes/sidebar.php') ?>
<?php
$col = $conn->query("SHOW COLUMNS FROM item_group LIKE 'supplier_id'");
if ($col && $col->num_rows === 0) {
    $conn->query("ALTER TABLE item_group ADD COLUMN supplier_id INT(11) DEFAULT NULL");
}
$supplierRes = $conn->query("SELECT id, aname FROM acc_head WHERE code LIKE '211%' AND code != '211' AND isdeleted = 0 ORDER BY aname");
$suppliers = $supplierRes ? $supplierRes->fetch_all(MYSQLI_ASSOC) : [];
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="card">

                <div class="card-header text-right" style="padding: 15px 20px;">
                    <h3 class="m-0" style="color: white;">
                        <i class="fas fa-layer-group"></i>
                    </h3>
                </div>

                <div class="card-body" style="padding: 20px;">

                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <i class="fas fa-exclamation-triangle"></i>
                            <?php if ($_GET['error'] === 'duplicate'): ?>
                                هذه المجموعة موجودة بالفعل. اختر اسماً آخر.
                            <?php elseif ($_GET['error'] === 'empty'): ?>
                                اكتب اسم المجموعة واختر المورد.
                            <?php else: ?>
                                تعذر حفظ المجموعة. حاول مرة أخرى.
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addGroupModal">
                            <i class="fas fa-plus"></i> إضافة مجموعة
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th width="80">#</th>
                                    <th>اسم المجموعة</th>
                                    <th>المورد</th>
                                    <th width="150" class="text-center">العمليات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $x = 0;
                                $resgrb = $conn->query("SELECT g.*, a.aname AS supplier_name FROM item_group g LEFT JOIN acc_head a ON a.id = g.supplier_id WHERE g.isdeleted = 0 ORDER BY g.id ASC");

                                if ($resgrb->num_rows == 0) {
                                    echo '<tr><td colspan="4" class="text-center text-muted">لا توجد مجموعات</td></tr>';
                                }

                                while ($rowgrb = $resgrb->fetch_assoc()) {
                                    $x++;
                                    $gid = (int) $rowgrb['id'];
                                    $gname = (string) $rowgrb['gname'];
                                    $supplierId = (int) ($rowgrb['supplier_id'] ?? 0);
                                    $supplierName = trim((string) ($rowgrb['supplier_name'] ?? ''));
                                ?>
                                <tr>
                                    <td><?= $x ?></td>
                                    <td><?= htmlspecialchars($gname) ?></td>
                                    <td><?= $supplierName !== '' ? htmlspecialchars($supplierName) : '—' ?></td>
                                    <td class="text-center">
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-warning btn-edit-group"
                                            title="تعديل"
                                            data-id="<?= $gid ?>"
                                            data-name="<?= htmlspecialchars($gname, ENT_QUOTES) ?>"
                                            data-supplier="<?= $supplierId ?>"
                                        >
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a
                                            href="do/dodel_group.php?id=<?= $gid ?>"
                                            class="btn btn-sm btn-danger"
                                            onclick="return confirm('هل تريد حذف هذه المجموعة؟')"
                                        >
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>

                </div>

                <div class="card-footer">
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i>
                        إجمالي المجموعات: <strong><?= $x ?></strong>
                    </small>
                </div>

            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="addGroupModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="do/doadd_group.php" method="post">
                <div class="modal-header">
                    <h5 class="modal-title">إضافة مجموعة</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="إغلاق">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="addGroupName">اسم المجموعة</label>
                        <input id="addGroupName" type="text" class="form-control" name="gname" placeholder="ادخل مجموعة جديدة" required>
                    </div>
                    <div class="form-group mb-0">
                        <label for="addGroupSupplier">المورد</label>
                        <select id="addGroupSupplier" name="supplier_id" class="form-control" required>
                            <option value="">اختر المورد</option>
                            <?php foreach ($suppliers as $sup): ?>
                                <option value="<?= (int) $sup['id'] ?>"><?= htmlspecialchars($sup['aname']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editGroupModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="editGroupForm" action="do/doedit_group.php" method="post">
                <div class="modal-header">
                    <h5 class="modal-title">تعديل المجموعة</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="إغلاق">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="editGroupName">اسم المجموعة</label>
                        <input id="editGroupName" type="text" class="form-control" name="gname" required>
                    </div>
                    <div class="form-group mb-0">
                        <label for="editGroupSupplier">المورد</label>
                        <select id="editGroupSupplier" name="supplier_id" class="form-control" required>
                            <option value="">اختر المورد</option>
                            <?php foreach ($suppliers as $sup): ?>
                                <option value="<?= (int) $sup['id'] ?>"><?= htmlspecialchars($sup['aname']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> حفظ التعديل</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include('includes/footer.php') ?>
<script>
$(function () {
    $('#addGroupModal').on('shown.bs.modal', function () {
        $('#addGroupName').trigger('focus');
    });
    $('.btn-edit-group').on('click', function () {
        var id = $(this).data('id');
        $('#editGroupForm').attr('action', 'do/doedit_group.php?id=' + id);
        $('#editGroupName').val($(this).attr('data-name'));
        $('#editGroupSupplier').val(String($(this).data('supplier') || ''));
        $('#editGroupModal').modal('show');
    });
    $('#editGroupModal').on('shown.bs.modal', function () {
        $('#editGroupName').trigger('focus');
    });
});
</script>
