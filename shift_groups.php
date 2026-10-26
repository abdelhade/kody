<?php include('includes/header.php') ?>
<?php include('includes/navbar.php') ?>
<?php include('includes/sidebar.php') ?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1><i class="fas fa-layer-group text-primary mr-2"></i>مجموعات الورديات (التعرف التلقائي / Dynamic Shift)</h1>
        </div>
        <div class="col-sm-6 text-left">
          <a href="shifts.php" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-right mr-1"></i> العودة للورديات
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- Main content -->
  <section class="content">
    <?php if (isset($_GET['success'])): ?>
      <div class="alert alert-success alert-dismissible fade show mx-3">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <i class="fas fa-check-circle mr-1"></i>
        <?php
        switch ($_GET['success']) {
            case 'added': echo 'تمت إضافة مجموعة الورديات بنجاح'; break;
            case 'updated': echo 'تم تعديل مجموعة الورديات بنجاح'; break;
            case 'deleted': echo 'تم حذف مجموعة الورديات بنجاح'; break;
            default: echo 'تمت العملية بنجاح';
        }
        ?>
      </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
      <div class="alert alert-danger alert-dismissible fade show mx-3">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <i class="fas fa-exclamation-triangle mr-1"></i>
        <?php
        switch ($_GET['error']) {
            case 'empty_name': echo 'يجب إدخال اسم المجموعة'; break;
            case 'no_shifts': echo 'يجب اختيار وردية واحدة على الأقل في المجموعة'; break;
            case 'linked_employees': echo 'لا يمكن حذف هذه المجموعة لوجود موظفين مرتبطين بها حالياً'; break;
            case 'invalid_password': echo 'كلمة المرور غير صحيحة'; break;
            case 'missing_password': echo 'يجب إدخال كلمة المرور للتأكيد'; break;
            case 'delete_failed': echo 'فشل حذف المجموعة'; break;
            case 'save_failed': echo 'فشل حفظ التغييرات'; break;
            default: echo 'حدث خطأ أثناء تنفيذ العملية';
        }
        ?>
      </div>
    <?php endif; ?>

    <div class="row">
      <div class="col-12">
        <div class="card card-outline card-primary shadow-sm">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title font-weight-bold">
              قائمة مجموعات الورديات المتاحة
            </h3>
            <div class="card-tools">
              <?php if (Can::check('shifts.add')) { ?>
                <a href="add_shift_group.php" class="btn btn-primary">
                  <i class="fas fa-plus mr-1"></i> إضافة مجموعة جديدة
                </a>
              <?php } ?>
            </div>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <p class="text-muted small">
              <i class="fas fa-info-circle mr-1"></i>
              تُستخدم مجموعات الورديات للموظفين الذين ليس لديهم جدول عمل ثابت؛ حيث يقوم النظام تلقائياً باستنتاج الوردية المناسبة لكل يوم عمل بناءً على توقيت بصمة الدخول الأقرب لبداية الوردية (مع ترجيح التأخير في الحضور).
            </p>
            <table class="table table-bordered table-hover text-center align-middle">
              <thead class="thead-light">
                <tr>
                  <th style="width: 50px;">م</th>
                  <th>اسم المجموعة</th>
                  <th>الورديات المتضمنة</th>
                  <th>أقصى نافذة للاستنتاج</th>
                  <th>الموظفون المرتبطون</th>
                  <th>ملاحظات</th>
                  <th style="width: 140px;">العمليات</th>
                </tr>
              </thead>
              <tbody>
                <?php
                if ($conn) {
                  $sqlGroups = "SELECT g.*, 
                                (SELECT COUNT(*) FROM employees e WHERE e.shift_group_id = g.id AND COALESCE(e.isdeleted, 0) != 1) AS emp_count
                                FROM shift_groups g 
                                WHERE g.isdeleted = 0 
                                ORDER BY g.id DESC";
                  $resGroups = $conn->query($sqlGroups);
                  $x = 0;
                  $canEdit = Can::check('shifts.edit');
                  $canDelete = Can::check('shifts.delete');

                  if ($resGroups && $resGroups->num_rows > 0) {
                    while ($rowGroup = $resGroups->fetch_assoc()) {
                      $x++;
                      $groupId = (int) $rowGroup['id'];

                      // جلب ورديات هذه المجموعة
                      $shiftsRes = $conn->query("
                        SELECT s.id, s.name, s.shiftstart, s.shiftend 
                        FROM shift_group_items sgi
                        INNER JOIN shifts s ON s.id = sgi.shift_id
                        WHERE sgi.group_id = $groupId AND (s.isdeleted = 0 OR s.isdeleted IS NULL)
                        ORDER BY sgi.sort ASC, s.id ASC
                      ");
                      $shiftBadges = [];
                      while ($s = $shiftsRes->fetch_assoc()) {
                        $sName = htmlspecialchars($s['name']);
                        $start = substr($s['shiftstart'], 0, 5);
                        $end = substr($s['shiftend'], 0, 5);
                        $shiftBadges[] = "<span class='badge badge-info p-2 m-1' style='font-size:0.85rem;'><i class='far fa-clock mr-1'></i>{$sName} ({$start} - {$end})</span>";
                      }
                ?>
                  <tr>
                    <td><?= $x ?></td>
                    <td class="font-weight-bold text-primary"><?= htmlspecialchars($rowGroup['name']) ?></td>
                    <td class="text-right">
                      <?= !empty($shiftBadges) ? implode(' ', $shiftBadges) : '<span class="text-muted">لا توجد ورديات</span>' ?>
                    </td>
                    <td>
                      <span class="badge badge-light border"><?= (int) $rowGroup['max_detect_window'] ?> دقيقة (<?= round($rowGroup['max_detect_window'] / 60, 1) ?> س)</span>
                    </td>
                    <td>
                      <?php if ($rowGroup['emp_count'] > 0): ?>
                        <span class="badge badge-success px-2 py-1"><?= (int) $rowGroup['emp_count'] ?> موظف</span>
                      <?php else: ?>
                        <span class="badge badge-secondary px-2 py-1">0</span>
                      <?php endif; ?>
                    </td>
                    <td class="small text-muted"><?= htmlspecialchars($rowGroup['info'] ?? '—') ?></td>
                    <td>
                      <?php if ($canEdit) { ?>
                        <a href="edit_shift_group.php?id=<?= $groupId ?>" class="btn btn-sm btn-warning" title="تعديل">
                          <i class="fas fa-edit"></i>
                        </a>
                      <?php } ?>
                      <?php if ($canDelete) { ?>
                        <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteGroupModal<?= $groupId ?>" title="حذف">
                          <i class="fas fa-trash"></i>
                        </button>

                        <!-- Delete Modal -->
                        <div class="modal fade" id="deleteGroupModal<?= $groupId ?>" tabindex="-1" role="dialog" aria-hidden="true">
                          <div class="modal-dialog" role="document">
                            <div class="modal-content">
                              <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title">تأكيد حذف مجموعة الورديات</h5>
                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                              </div>
                              <form action="do/dodel_shift_group.php?id=<?= $groupId ?>" method="POST">
                                <div class="modal-body text-right">
                                  <p>هل أنت متأكد من حذف مجموعة الورديات: <b><?= htmlspecialchars($rowGroup['name']) ?></b>؟</p>
                                  <?php if ($rowGroup['emp_count'] > 0): ?>
                                    <div class="alert alert-warning small">
                                      <i class="fas fa-exclamation-triangle mr-1"></i>
                                      تنبيه: يوجد <?= (int) $rowGroup['emp_count'] ?> موظف مرتبط بهذه المجموعة. لا يمكن الحذف حتى يتم تحويلهم أولاً.
                                    </div>
                                  <?php endif; ?>
                                  <div class="form-group">
                                    <label>كلمة المرور للتأكيد:</label>
                                    <input type="password" name="password" class="form-control" required autocomplete="current-password">
                                  </div>
                                </div>
                                <div class="modal-footer justify-content-between">
                                  <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                                  <button type="submit" class="btn btn-danger" <?= $rowGroup['emp_count'] > 0 ? 'disabled' : '' ?>>تأكيد الحذف</button>
                                </div>
                              </form>
                            </div>
                          </div>
                        </div>
                      <?php } ?>
                    </td>
                  </tr>
                <?php
                    }
                  } else {
                ?>
                  <tr>
                    <td colspan="7" class="text-center py-4 text-muted">
                      <i class="fas fa-info-circle fa-2x mb-2 d-block text-secondary"></i>
                      لا توجد مجموعات ورديات حتى الآن. اضغط على «إضافة مجموعة جديدة» لإنشاء مجموعة.
                    </td>
                  </tr>
                <?php
                  }
                }
                ?>
              </tbody>
            </table>
          </div>
          <!-- /.card-body -->
        </div>
      </div>
    </div>
  </section>
</div>

<?php include('includes/footer.php') ?>
