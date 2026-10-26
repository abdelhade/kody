<?php include('includes/header.php') ?>
<?php include('includes/navbar.php') ?>
<?php include('includes/sidebar.php') ?>

<?php
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    header('Location: shift_groups.php?error=invalid_id');
    exit;
}

$stmt = $conn->prepare("SELECT * FROM shift_groups WHERE id = ? AND isdeleted = 0");
$stmt->bind_param('i', $id);
$stmt->execute();
$group = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$group) {
    header('Location: shift_groups.php?error=not_found');
    exit;
}

// الورديات المختارة حالياً في هذه المجموعة
$selectedShifts = [];
$resSel = $conn->query("SELECT shift_id FROM shift_group_items WHERE group_id = $id");
while ($r = $resSel->fetch_assoc()) {
    $selectedShifts[] = (int) $r['shift_id'];
}
?>

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2 align-items-center">
        <div class="col">
          <h1 class="m-0 text-dark font-weight-bold" style="font-size: 1.2rem;">
            <i class="fas fa-edit text-warning mr-1"></i>
            تعديل مجموعة الورديات: <?= htmlspecialchars($group['name']) ?>
          </h1>
        </div>
        <div class="col-auto">
          <a href="shift_groups.php" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-right mr-1"></i>
            العودة للمجموعات
          </a>
        </div>
      </div>

      <div class="row justify-content-center">
        <div class="col-12 col-lg-9">
          <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
              <i class="fas fa-exclamation-triangle mr-1"></i>
              <?php
              switch ($_GET['error']) {
                  case 'empty_name': echo 'يجب كتابة اسم المجموعة'; break;
                  case 'no_shifts': echo 'يرجى اختيار وردية واحدة على الأقل للمجموعة'; break;
                  default: echo 'حدث خطأ أثناء الحفظ';
              }
              ?>
            </div>
          <?php endif; ?>

          <form role="form" action="do/doedit_shift_group.php?id=<?= $id ?>" method="POST">
            <div class="card card-outline card-warning shadow-sm mb-3">
              <div class="card-header py-2">
                <h3 class="card-title font-weight-bold">
                  <i class="fas fa-info-circle mr-1"></i>
                  بيانات المجموعة الأساسية
                </h3>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-7">
                    <div class="form-group">
                      <label class="font-weight-bold">اسم مجموعة الورديات <span class="text-danger">*</span></label>
                      <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($group['name']) ?>" required>
                    </div>
                  </div>
                  <div class="col-md-5">
                    <div class="form-group">
                      <label class="font-weight-bold">أقصى نافذة للدقائق للتعرف التلقائي</label>
                      <div class="input-group">
                        <input type="number" name="max_detect_window" class="form-control" value="<?= (int) $group['max_detect_window'] ?>" min="30" max="1440">
                        <div class="input-group-append">
                          <span class="input-group-text">دقيقة</span>
                        </div>
                      </div>
                      <small class="form-text text-muted">الافتراضي 720 دقيقة (12 ساعة) لتغطية كامل دورة اليوم.</small>
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="form-group">
                      <label class="font-weight-bold">ملاحظات / وصف</label>
                      <textarea name="info" class="form-control" rows="2"><?= htmlspecialchars($group['info'] ?? '') ?></textarea>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="card card-outline card-info shadow-sm mb-3">
              <div class="card-header py-2">
                <h3 class="card-title font-weight-bold">
                  <i class="fas fa-clock mr-1"></i>
                  اختيار الورديات التابعة لهذه المجموعة <span class="text-danger">*</span>
                </h3>
              </div>
              <div class="card-body">
                <p class="text-muted small">
                  حدد جميع الورديات التي يمكن أن يعمل بها موظفو هذه المجموعة:
                </p>

                <div class="row">
                  <?php
                  $resShifts = $conn->query("SELECT * FROM shifts WHERE (isdeleted = 0 OR isdeleted IS NULL) ORDER BY name ASC");
                  if ($resShifts && $resShifts->num_rows > 0):
                    while ($s = $resShifts->fetch_assoc()):
                      $start = substr($s['shiftstart'] ?? '', 0, 5);
                      $end = substr($s['shiftend'] ?? '', 0, 5);
                      $hours = (float)($s['hours'] ?? 0);
                      $checked = in_array((int)$s['id'], $selectedShifts, true) ? 'checked' : '';
                  ?>
                    <div class="col-md-6 mb-2">
                      <div class="border rounded p-2 bg-light d-flex align-items-center justify-content-between">
                        <div class="custom-control custom-checkbox">
                          <input type="checkbox" name="shifts[]" value="<?= (int) $s['id'] ?>" class="custom-control-input" id="shift_chk_<?= $s['id'] ?>" <?= $checked ?>>
                          <label class="custom-control-label font-weight-bold" for="shift_chk_<?= $s['id'] ?>">
                            <?= htmlspecialchars($s['name']) ?>
                          </label>
                          <div class="small text-muted mt-1">
                            <i class="far fa-clock"></i> من <?= $start ?> إلى <?= $end ?> (<?= $hours ?> ساعة)
                          </div>
                        </div>
                        <span class="badge badge-secondary p-1">#<?= $s['id'] ?></span>
                      </div>
                    </div>
                  <?php 
                    endwhile;
                  endif;
                  ?>
                </div>
              </div>
            </div>

            <div class="text-left mb-4">
              <button type="submit" class="btn btn-warning px-4 font-weight-bold">
                <i class="fas fa-save mr-1"></i> حفظ التعديلات
              </button>
              <a href="shift_groups.php" class="btn btn-secondary mr-2">إلغاء</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>
</div>

<?php include('includes/footer.php') ?>
