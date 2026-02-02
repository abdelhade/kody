<?php include('includes/header.php') ?>
<?php include('includes/navbar.php') ?>
<?php include('includes/sidebar.php') ?>
<?php include('includes/connect.php'); ?>

<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">

      <div class="card card-primary">
        <div class="card-header">
          <h3 class="card-title text-white"> <?= $lang_infoshift ?></h3>
        </div>
        <!-- /.card-header -->
        <!-- form start -->
        <form role="form" action="do/doadd_shift.php" method="POST">
          <div class="card-body">

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="name"> <?= $lang_addhicont_name ?></label>
                  <input name="name" id="name" type="text" class="form-control" placeholder="اسم الورديه" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label><?= $lang_workday ?></label>
                  <div class="row">
                    <div class="col-sm-6">
                      <div class="form-check form-switch">
                        <input name="sat" class="form-check-input" type="checkbox" role="switch" id="sat" checked>
                        <label class="form-check-label" for="sat"><?= $lang_addsh_sat ?></label>
                      </div>
                      <div class="form-check form-switch">
                        <input name="sun" class="form-check-input" type="checkbox" role="switch" id="sun" checked>
                        <label class="form-check-label" for="sun"><?= $lang_addsh_sun ?></label>
                      </div>
                      <div class="form-check form-switch">
                        <input name="mon" class="form-check-input" type="checkbox" role="switch" id="mon" checked>
                        <label class="form-check-label" for="mon"><?= $lang_addsh_mon ?></label>
                      </div>
                      <div class="form-check form-switch">
                        <input name="tus" class="form-check-input" type="checkbox" role="switch" id="tus" checked>
                        <label class="form-check-label" for="tus"><?= $lang_addsh_tue ?></label>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-check form-switch">
                        <input name="wed" class="form-check-input" type="checkbox" role="switch" id="wed" checked>
                        <label class="form-check-label" for="wed"><?= $lang_addsh_wed ?></label>
                      </div>
                      <div class="form-check form-switch">
                        <input name="thur" class="form-check-input" type="checkbox" role="switch" id="thu" checked>
                        <label class="form-check-label" for="thu"><?= $lang_addsh_thu ?></label>
                      </div>
                      <div class="form-check form-switch">
                        <input name="fri" class="form-check-input" type="checkbox" role="switch" id="fri" checked>
                        <label class="form-check-label" for="fri"><?= $lang_addsh_fri ?></label>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <hr>

            <div class="card card-info">
              <div class="card-header">
                <h3 class="card-title text-white"> <?= $lang_Attendance_rules ?></h3>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label><?= $lang_addsh_start ?></label>
                      <input name="shiftstart" type="time" class="form-control" required>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label><?= $lang_addsh_end ?></label>
                      <input name="shiftend" type="time" class="form-control" required>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label><?= $lang_addsh_stardatt ?></label>
                      <input name="instart" type="time" class="form-control">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label><?= $lang_addsh_endatt ?></label>
                      <input name="inend" type="time" class="form-control">
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label><?= $lang_addsh_startout ?></label>
                      <input name="outstart" type="time" class="form-control">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label><?= $lang_addsh_endout ?></label>
                      <input name="outend" type="time" class="form-control">
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-12">
                    <h5 class="mt-3 mb-2"><?= $lang_end_dismissal_date ?></h5>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label><?= $lang_addsh_delaylimits ?></label>
                      <input name="latelimit" type="number" class="form-control" placeholder="0">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label><?= $lang_addsh_earlylimits ?></label>
                      <input name="earlylimit" type="number" class="form-control" placeholder="0">
                    </div>
                  </div>
                </div>

              </div>

              <div class="card-footer px-0">
                <button type="submit" class="btn btn-primary btn-block py-2 text-bold"><i class="fas fa-save mr-2"></i> <?= $lang_addhicont_confirm ?></button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </section>
</div>

<?php include('includes/footer.php') ?>