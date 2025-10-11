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
          <h1><?= $lang_shifts ?></h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title"><a href="add_shift.php" class="btn btn-large btn-primary"> <?= $lang_addshift ?> </a></h3>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <table id="example2" class="table table-bordered table-hover">
              <thead>
                <tr>
                  <th>م</th>
                  <th><?= $lang_publicname ?></th>
                  <th>العطلات</th>
                  <th>عمليات</th>
                </tr>
              </thead>
              <?php

              ?>
              <tbody>
                <?php
                $sqlshft = "SELECT * from shifts order by id desc";
                $resshft = $conn->query($sqlshft);
                $x = 0;
                while ($rowshft = $resshft->fetch_assoc()) {
                  $wd = $rowshft['workingdays'];
                  $week = array(6, 7, 1, 2, 3, 4, 5);
                  $days = explode(",", $wd);


                  $x++;
                ?>
                  <a href="edit_shift.php">
                    <tr>
                      <th><?= $x ?></th>
                      <th><?= $rowshft['name'] ?></th>
                      <th><?php
                          if (!isset($days[0])) {
                            echo "السيت-";
                          }
                          if (!isset($days[1])) {
                            echo "الاحد-";
                          }
                          if (!isset($days[2])) {
                            echo "الاتنين-";
                          }
                          if (!isset($days[3])) {
                            echo "الثلاثاء-";
                          }
                          if (!isset($days[4])) {
                            echo "الاربعاء-";
                          }
                          if (!isset($days[5])) {
                            echo "الخميس-";
                          }
                          if (!isset($days[6])) {
                            echo "الجمعه-";
                          }
                          ?></th>
                      <th>
                        <a href="edit_shift.php?id=<?= $rowshft['id'] ?>" class="btn btn-warning text-center">تعديل</a>
                        <?php
                        $shftidsql = "SELECT shift FROM employees WHERE shift = {$rowshft['id']}";
                        $resid = $conn->query($shftidsql);
                        $rowid = $resid->fetch_assoc();

                        ?><button type="button" class="btn btn-danger text-center" data-toggle="modal" data-target="#deleteModal<?= $rowshft['id'] ?>">حذف</button>

                      </th>
                    </tr>
                  </a>
                <?php } ?>

              </tbody>
              <tfoot>
                <tr>
                  <th>م</th>
                  <th><?= $lang_publicname ?></th>
                  <th>عمليات</th>
                </tr>
              </tfoot>
            </table>

          </div>
          <!-- /.card-body -->
        </div>
        <!-- /.card -->
      </div>
      <!-- /.card-body -->
    </div>
    <!-- /.card -->
</div>
<!-- /.col -->
</div>
<!-- /.row -->
</section>
<!-- /.content -->
</div>






<div class="modal fade" id="deleteModal<?= $rowshft['id'] ?>">
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
        <p>هل تريد بالتأكيد حذف <?= $rowshft['name'] ?></p>
        <form action="do/dodel_employee.php?id=<?= $rowshft['id'] ?>" method="POST">
          <input type="password" name="password" class="form-control form-control-sm  " id="password">
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-outline-light" data-dismiss="modal">الغاء</button>
        <button type="submit" class="btn btn-outline-light">حذف</button>
        </form>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>



<?php include('includes/footer.php') ?>