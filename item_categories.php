<?php include('includes/header.php') ?>
<?php include('includes/navbar.php') ?>
<?php include('includes/sidebar.php') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="card">

                <div class="card-header text-right" style="padding: 15px 20px;">
                    <h3 class="m-0">
                        <i class="fas fa-tags"></i>
                    </h3>
                </div>

                <div class="card-body" style="padding: 20px;">

                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <i class="fas fa-exclamation-triangle"></i>
                            <?php if ($_GET['error'] === 'duplicate'): ?>
                                هذا التصنيف موجود بالفعل. اختر اسماً آخر.
                            <?php elseif ($_GET['error'] === 'empty'): ?>
                                اكتب اسم التصنيف أولاً.
                            <?php else: ?>
                                تعذر حفظ التصنيف. حاول مرة أخرى.
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addCategoryModal">
                            <i class="fas fa-plus"></i> إضافة تصنيف
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th width="80">#</th>
                                    <th>اسم التصنيف</th>
                                    <th width="150" class="text-center">العمليات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $x = 0;
                                $resgrb = $conn->query("SELECT * FROM item_group2 WHERE isdeleted = 0 ORDER BY id ASC");

                                if ($resgrb->num_rows == 0) {
                                    echo '<tr><td colspan="3" class="text-center text-muted">لا توجد تصنيفات</td></tr>';
                                }

                                while ($rowgrb = $resgrb->fetch_assoc()) {
                                    $x++;
                                    $gid = (int) $rowgrb['id'];
                                    $gname = (string) $rowgrb['gname'];
                                ?>
                                <tr>
                                    <td><?= $x ?></td>
                                    <td><?= htmlspecialchars($gname) ?></td>
                                    <td class="text-center">
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-warning btn-edit-category"
                                            title="تعديل"
                                            data-id="<?= $gid ?>"
                                            data-name="<?= htmlspecialchars($gname, ENT_QUOTES) ?>"
                                        >
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a
                                            href="do/dodel_group2.php?id=<?= $gid ?>"
                                            class="btn btn-sm btn-danger"
                                            onclick="return confirm('هل تريد حذف هذا التصنيف؟')"
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
                        إجمالي التصنيفات: <strong><?= $x ?></strong>
                    </small>
                </div>

            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="addCategoryModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="do/doadd_group2.php" method="post">
                <div class="modal-header">
                    <h5 class="modal-title">إضافة تصنيف</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="إغلاق">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-0">
                        <label for="addCategoryName">اسم التصنيف</label>
                        <input id="addCategoryName" type="text" class="form-control" name="gname" placeholder="ادخل تصنيف جديد" required>
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

<div class="modal fade" id="editCategoryModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="editCategoryForm" action="do/doedit_group2.php" method="post">
                <div class="modal-header">
                    <h5 class="modal-title">تعديل التصنيف</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="إغلاق">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-0">
                        <label for="editCategoryName">اسم التصنيف</label>
                        <input id="editCategoryName" type="text" class="form-control" name="gname" required>
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
    $('#addCategoryModal').on('shown.bs.modal', function () {
        $('#addCategoryName').trigger('focus');
    });
    $('.btn-edit-category').on('click', function () {
        var id = $(this).data('id');
        $('#editCategoryForm').attr('action', 'do/doedit_group2.php?id=' + id);
        $('#editCategoryName').val($(this).attr('data-name'));
        $('#editCategoryModal').modal('show');
    });
    $('#editCategoryModal').on('shown.bs.modal', function () {
        $('#editCategoryName').trigger('focus');
    });
});
</script>
