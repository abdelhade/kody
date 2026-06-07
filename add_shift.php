<?php include('includes/header.php') ?>
<?php include('includes/navbar.php') ?>
<?php include('includes/sidebar.php') ?>

<style>
  .shift-add-page .form-group label { font-size: 0.82rem; font-weight: 600; margin-bottom: 0.25rem; color: #495057; }
  .shift-add-page .form-control { font-size: 0.88rem; border-radius: 0.35rem; }
  .shift-add-page .card-title { font-size: 0.95rem; }
  .shift-add-page .section-hint { font-size: 0.78rem; color: #6c757d; margin-bottom: 0.75rem; }
  .shift-add-page .day-pill {
    border: 1px solid #dee2e6;
    border-radius: 0.4rem;
    padding: 0.45rem 0.5rem;
    background: #fff;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 0.82rem;
    min-height: 2.4rem;
  }
  .shift-add-page .day-pill .custom-control-label { font-weight: 500; font-size: 0.82rem; }
  .shift-add-page .rules-heading {
    font-size: 0.95rem;
    font-weight: 600;
    color: #495057;
    margin: 0.25rem 0 0.5rem;
    padding-bottom: 0.35rem;
    border-bottom: 2px solid #dee2e6;
  }
</style>

<div class="content-wrapper shift-add-page">
  <section class="content-header">
    <div class="container-fluid">

      <div class="row mb-2 align-items-center">
        <div class="col">
          <h1 class="m-0 text-dark" style="font-size: 1.15rem;">
            <i class="fas fa-clock text-primary mr-1"></i>
            <?= $lang_addshift ?>
          </h1>
        </div>
        <div class="col-auto">
          <a href="shifts.php" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-right mr-1"></i>
            <?= $lang_shifts ?>
          </a>
        </div>
      </div>

      <div class="row justify-content-center">
        <div class="col-12 col-lg-10 col-xl-9">
          <form role="form" action="do/doadd_shift.php" method="POST">

            <div class="card card-outline card-primary shadow-sm mb-2">
              <div class="card-header py-2">
                <h3 class="card-title mb-0">
                  <i class="fas fa-info-circle mr-1"></i>
                  <?= $lang_infoshift ?>
                </h3>
              </div>
              <div class="card-body py-3">
                <div class="form-group mb-0">
                  <label for="name"><?= $lang_addhicont_name ?></label>
                  <input name="name" id="name" type="text" class="form-control form-control-sm" placeholder="اسم الوردية" required>
                </div>
              </div>
            </div>

            <div class="card card-outline card-info shadow-sm mb-2">
              <div class="card-header py-2">
                <h3 class="card-title mb-0">
                  <i class="fas fa-calendar-week mr-1"></i>
                  <?= $lang_workday ?>
                </h3>
              </div>
              <div class="card-body py-3">
                <p class="section-hint mb-2">فعّل الأيام التي تُحسب فيها الوردية كأيام عمل.</p>
                <div class="row">
                  <div class="col-6 col-sm-4 col-md-3 mb-2">
                    <div class="day-pill">
                      <label class="mb-0" for="sat"><?= $lang_addsh_sat ?></label>
                      <div class="custom-control custom-switch">
                        <input name="sat" class="custom-control-input" type="checkbox" id="sat" checked>
                        <label class="custom-control-label" for="sat"></label>
                      </div>
                    </div>
                  </div>
                  <div class="col-6 col-sm-4 col-md-3 mb-2">
                    <div class="day-pill">
                      <label class="mb-0" for="sun"><?= $lang_addsh_sun ?></label>
                      <div class="custom-control custom-switch">
                        <input name="sun" class="custom-control-input" type="checkbox" id="sun" checked>
                        <label class="custom-control-label" for="sun"></label>
                      </div>
                    </div>
                  </div>
                  <div class="col-6 col-sm-4 col-md-3 mb-2">
                    <div class="day-pill">
                      <label class="mb-0" for="mon"><?= $lang_addsh_mon ?></label>
                      <div class="custom-control custom-switch">
                        <input name="mon" class="custom-control-input" type="checkbox" id="mon" checked>
                        <label class="custom-control-label" for="mon"></label>
                      </div>
                    </div>
                  </div>
                  <div class="col-6 col-sm-4 col-md-3 mb-2">
                    <div class="day-pill">
                      <label class="mb-0" for="tus"><?= $lang_addsh_tue ?></label>
                      <div class="custom-control custom-switch">
                        <input name="tus" class="custom-control-input" type="checkbox" id="tus" checked>
                        <label class="custom-control-label" for="tus"></label>
                      </div>
                    </div>
                  </div>
                  <div class="col-6 col-sm-4 col-md-3 mb-2">
                    <div class="day-pill">
                      <label class="mb-0" for="wed"><?= $lang_addsh_wed ?></label>
                      <div class="custom-control custom-switch">
                        <input name="wed" class="custom-control-input" type="checkbox" id="wed" checked>
                        <label class="custom-control-label" for="wed"></label>
                      </div>
                    </div>
                  </div>
                  <div class="col-6 col-sm-4 col-md-3 mb-2">
                    <div class="day-pill">
                      <label class="mb-0" for="thur"><?= $lang_addsh_thu ?></label>
                      <div class="custom-control custom-switch">
                        <input name="thur" class="custom-control-input" type="checkbox" id="thur" checked>
                        <label class="custom-control-label" for="thur"></label>
                      </div>
                    </div>
                  </div>
                  <div class="col-6 col-sm-4 col-md-3 mb-2">
                    <div class="day-pill">
                      <label class="mb-0" for="fri"><?= $lang_addsh_fri ?></label>
                      <div class="custom-control custom-switch">
                        <input name="fri" class="custom-control-input" type="checkbox" id="fri" checked>
                        <label class="custom-control-label" for="fri"></label>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <p class="rules-heading">
              <i class="fas fa-user-check text-secondary mr-1"></i>
              <?= $lang_Attendance_rules ?>
            </p>

            <div class="card card-outline card-success shadow-sm mb-2">
              <div class="card-header py-2">
                <h3 class="card-title mb-0">
                  <i class="fas fa-business-time mr-1"></i>
                  مواعيد الوردية
                </h3>
              </div>
              <div class="card-body py-3">
                <div class="row">
                  <div class="col-12 col-sm-6 form-group mb-2 mb-sm-0">
                    <label><?= $lang_addsh_start ?></label>
                    <input name="shiftstart" type="time" class="form-control form-control-sm" required>
                  </div>
                  <div class="col-12 col-sm-6 form-group mb-0">
                    <label><?= $lang_addsh_end ?></label>
                    <input name="shiftend" type="time" class="form-control form-control-sm" required>
                  </div>
                </div>
              </div>
            </div>

            <div class="card card-outline card-info shadow-sm mb-2">
              <div class="card-header py-2">
                <h3 class="card-title mb-0">
                  <i class="fas fa-sign-in-alt mr-1"></i>
                  نطاق تسجيل الحضور
                </h3>
              </div>
              <div class="card-body py-3">
                <div class="row">
                  <div class="col-12 col-sm-6 form-group mb-2 mb-sm-0">
                    <label><?= $lang_addsh_stardatt ?></label>
                    <input name="instart" type="time" class="form-control form-control-sm">
                  </div>
                  <div class="col-12 col-sm-6 form-group mb-0">
                    <label><?= $lang_addsh_endatt ?></label>
                    <input name="inend" type="time" class="form-control form-control-sm">
                  </div>
                </div>
              </div>
            </div>

            <div class="card card-outline card-warning shadow-sm mb-2">
              <div class="card-header py-2">
                <h3 class="card-title mb-0">
                  <i class="fas fa-sign-out-alt mr-1"></i>
                  نطاق تسجيل الانصراف
                </h3>
              </div>
              <div class="card-body py-3">
                <div class="row">
                  <div class="col-12 col-sm-6 form-group mb-2 mb-sm-0">
                    <label><?= $lang_addsh_startout ?></label>
                    <input name="outstart" type="time" class="form-control form-control-sm">
                  </div>
                  <div class="col-12 col-sm-6 form-group mb-0">
                    <label><?= $lang_addsh_endout ?></label>
                    <input name="outend" type="time" class="form-control form-control-sm">
                  </div>
                </div>
              </div>
            </div>

            <div class="card card-outline card-danger shadow-sm mb-2">
              <div class="card-header py-2">
                <h3 class="card-title mb-0">
                  <i class="fas fa-hourglass-half mr-1"></i>
                  حدود التأخير والانصراف
                </h3>
              </div>
              <div class="card-body py-3">
                <div class="row">
                  <div class="col-12 col-sm-6 form-group mb-2 mb-sm-0">
                    <label><?= $lang_addsh_delaylimits ?></label>
                    <input name="latelimit" type="number" min="0" class="form-control form-control-sm" placeholder="0">
                  </div>
                  <div class="col-12 col-sm-6 form-group mb-0">
                    <label><?= $lang_addsh_earlylimits ?></label>
                    <input name="earlylimit" type="number" min="0" class="form-control form-control-sm" placeholder="0">
                  </div>
                </div>
              </div>
            </div>

            <div class="d-flex flex-column flex-sm-row justify-content-center justify-content-sm-between align-items-stretch align-items-sm-center gap-2 mb-3">
              <a href="shifts.php" class="btn btn-outline-secondary btn-sm order-2 order-sm-1">
                <i class="fas fa-times mr-1"></i> إلغاء
              </a>
              <button type="submit" class="btn btn-success btn-sm px-4 order-1 order-sm-2">
                <i class="fas fa-save mr-1"></i>
                <?= $lang_addhicont_confirm ?>
              </button>
            </div>

          </form>
        </div>
      </div>

    </div>
  </section>
</div>

<?php include('includes/footer.php') ?>
