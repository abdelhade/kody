<?php include('includes/header.php') ?>
<?php include('includes/navbar.php') ?>
<?php include('includes/sidebar.php') ?>

<style>
  .att-page .card-title { font-size: 0.95rem; }
  .att-page .form-group label { font-size: 0.8rem; margin-bottom: 0.2rem; }
  .att-page .form-control,
  .att-page .btn { font-size: 0.85rem; }
  .att-page .btn-act { padding: 0.2rem 0.45rem; font-size: 0.75rem; }
  .att-record-card {
    border: 1px solid #dee2e6;
    border-radius: 0.45rem;
    padding: 0.6rem 0.7rem;
    height: 100%;
    background: #fff;
    font-size: 0.82rem;
    box-shadow: 0 1px 2px rgba(0,0,0,0.04);
    transition: box-shadow 0.15s;
  }
  .att-record-card:hover { box-shadow: 0 2px 6px rgba(0,0,0,0.08); }
  .att-record-card .att-row { display: flex; justify-content: space-between; margin-top: 0.25rem; font-size: 0.78rem; color: #6c757d; }
  .att-record-card .att-row span:first-child { color: #495057; font-weight: 600; }
  .att-record-card .att-actions { margin-top: 0.5rem; display: flex; flex-wrap: wrap; gap: 0.35rem; border-top: 1px solid #f0f0f0; padding-top: 0.45rem; }
</style>

<div class="content-wrapper att-page">
  <section class="content-header">
    <div class="container-fluid">
    <?php if ($role['show_attandance'] == 1) { ?>

      <div class="row mb-2 align-items-center">
        <div class="col">
          <h1 class="m-0 text-dark" style="font-size: 1.15rem;">
            <i class="fas fa-clipboard-list text-primary mr-1"></i>
            <?= $lang_attendance_log ?>
          </h1>
        </div>
        <div class="col-auto">
          <a href="add_manualfp.php" class="btn btn-success btn-sm">
            <i class="fas fa-plus mr-1"></i> إضافة
          </a>
        </div>
      </div>

      <div class="card card-outline card-primary shadow-sm mb-2">
        <div class="card-header py-2">
          <h3 class="card-title mb-0">
            <i class="fas fa-filter mr-1"></i> بحث
          </h3>
        </div>
        <div class="card-body py-2">
          <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
            <div class="row align-items-end">
              <div class="col-12 col-sm-6 col-lg-4 form-group mb-2 mb-lg-0">
                <label for="attEmployee">الاسم</label>
                <select required class="form-control form-control-sm" name="employee" id="attEmployee">
                  <option value="0">كل الموظفين</option>
                  <?php
                  $sqlemp = "SELECT * FROM employees WHERE isdeleted != 1";
                  $resemp = $conn->query($sqlemp);
                  while ($rowemp = $resemp->fetch_assoc()) {
                  ?>
                  <option <?php
                    if (isset($_POST['employee']) && $_POST['employee'] == $rowemp['id']) {
                      echo 'selected';
                    }
                  ?> value="<?= $rowemp['id'] ?>"><?= htmlspecialchars($rowemp['name']) ?></option>
                  <?php } ?>
                </select>
              </div>
              <div class="col-6 col-sm-3 col-lg-2 form-group mb-2 mb-lg-0">
                <label for="attFrom">من</label>
                <input required name="fromdate" id="attFrom" class="form-control form-control-sm" type="date"
                  <?php if (isset($_POST['fromdate'])) { echo 'value="' . htmlspecialchars($_POST['fromdate']) . '"'; } ?>>
              </div>
              <div class="col-6 col-sm-3 col-lg-2 form-group mb-2 mb-lg-0">
                <label for="attTo">إلى</label>
                <input required name="todate" id="attTo" class="form-control form-control-sm" type="date"
                  <?php if (isset($_POST['todate'])) { echo 'value="' . htmlspecialchars($_POST['todate']) . '"'; } ?>>
              </div>
              <div class="col-12 col-sm-12 col-lg-2 form-group mb-0">
                <button class="btn btn-primary btn-sm btn-block" type="submit">
                  <i class="fas fa-search mr-1"></i> بحث
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>

      <?php
      if (isset($_POST['fromdate'])) {
        $t1 = $_POST['employee'];
        $t2 = $_POST['fromdate'];
        $t3 = $_POST['todate'];
        if ($t1 == 0) {
          $sql = "SELECT * FROM `attandance` WHERE fpdate BETWEEN '$t2' and '$t3' AND isdeleted != 1 ORDER BY fpdate ASC";
        } else {
          $sql = "SELECT * FROM `attandance` WHERE employee = '$t1' AND fpdate BETWEEN '$t2' and '$t3' AND isdeleted != 1 ORDER BY fpdate ASC";
        }
      } else {
        $today = date('Y-m-d');
        $sql = "SELECT * FROM `attandance` WHERE fpdate BETWEEN '$today' and '$today' AND isdeleted != 1 ORDER BY id DESC LIMIT 60";
      }

      $res = $conn->query($sql);
      $records = [];
      $x = 0;

      while ($row = $res->fetch_assoc()) {
        $x++;
        $empName = '';
        $empid = $row['employee'];
        $rowemp = $conn->query('select * from employees where id = ' . (int) $empid)->fetch_assoc();
        if ($rowemp && $rowemp['id'] > 1) {
          $empName = $rowemp['name'];
        }

        $typeName = '';
        $tybeid = $row['fptybe'];
        $rowtyb = $conn->query('select * from fptybes where id = ' . (int) $tybeid)->fetch_assoc();
        if ($rowtyb) {
          $typeName = $rowtyb['name'];
        }

        $records[] = [
          'num' => $x,
          'id' => $row['id'],
          'name' => $empName,
          'type' => $typeName,
          'date' => $row['fpdate'],
          'time' => $row['time'],
        ];
      }
      ?>

      <div class="card card-outline card-secondary shadow-sm">
        <div class="card-header py-2 d-flex justify-content-between align-items-center flex-wrap">
          <h3 class="card-title mb-0">
            <i class="fas fa-list mr-1"></i> السجلات
          </h3>
          <span class="badge badge-primary"><?= count($records) ?> سجل</span>
        </div>
        <div class="card-body p-2 p-md-3">

          <?php if (empty($records)) { ?>
            <p class="text-muted text-center mb-0 py-3">
              <i class="fas fa-inbox d-block mb-2" style="font-size: 1.5rem;"></i>
              لا توجد سجلات في الفترة المحددة.
            </p>
          <?php } else { ?>

          <div class="row">
            <?php foreach ($records as $rec) { ?>
            <div class="col-12 col-sm-6 col-lg-4 col-xl-3 mb-2">
              <div class="att-record-card">
                <div class="d-flex justify-content-between align-items-start">
                  <strong class="text-truncate pr-1" title="<?= htmlspecialchars($rec['name']) ?>">
                    <?= htmlspecialchars($rec['name']) ?>
                  </strong>
                  <span class="badge badge-secondary flex-shrink-0">#<?= $rec['num'] ?></span>
                </div>
                <div class="att-row">
                  <span>نوع البصمة</span>
                  <span><?= htmlspecialchars($rec['type']) ?></span>
                </div>
                <div class="att-row">
                  <span>التاريخ</span>
                  <span><?= htmlspecialchars($rec['date']) ?></span>
                </div>
                <div class="att-row">
                  <span>الوقت</span>
                  <span><?= htmlspecialchars($rec['time']) ?></span>
                </div>
                <div class="att-actions">
                  <a class="btn btn-warning btn-act" href="edit_manualfp.php?id=<?= (int) $rec['id'] ?>"><?= $lang_edit ?></a>
                  <button type="button" class="btn btn-danger btn-act" data-toggle="modal" data-target="#modal-del-<?= (int) $rec['id'] ?>">حذف</button>
                </div>
              </div>
            </div>
            <?php } ?>
          </div>

          <?php foreach ($records as $rec) { ?>
          <div class="modal fade" id="modal-del-<?= (int) $rec['id'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
              <div class="modal-content bg-danger">
                <div class="modal-header py-2">
                  <h5 class="modal-title" style="font-size: 0.95rem;">تحذير</h5>
                  <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body py-2" style="font-size: 0.85rem;">
                  هل تريد بالتأكيد حذف هذا السجل؟
                </div>
                <div class="modal-footer py-2 justify-content-between">
                  <button type="button" class="btn btn-outline-light btn-sm" data-dismiss="modal">إلغاء</button>
                  <a href="do/dodel_fp.php?id=<?= (int) $rec['id'] ?>" class="btn btn-outline-light btn-sm">حذف</a>
                </div>
              </div>
            </div>
          </div>
          <?php } ?>

          <?php } ?>
        </div>
      </div>

    <?php } else { echo $userErrorMassage; } ?>
    </div>
  </section>
</div>

<?php include('includes/footer.php') ?>
