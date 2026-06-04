<?php include('includes/header.php') ?>
<?php include('includes/navbar.php') ?>
<?php include('includes/sidebar.php') ?>

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
    <?php if ($role['show_attandance'] == 1) { ?>

      <div class="row mb-3">
        <div class="col-sm-6">
          <h1 class="m-0 text-dark">
            <i class="fas fa-fingerprint text-primary mr-2"></i>
            <?= $lang_import_attendance ?>
          </h1>
        </div>
      </div>

      <div class="row">
        <div class="col-md-8 offset-md-2">
          <div class="card card-outline card-primary shadow-sm">
            <div class="card-header">
              <h3 class="card-title">
                <i class="fas fa-file-import mr-1"></i>
                استيراد سجلات أجهزة البصمة
              </h3>
            </div>
            <div class="card-body">
              <p class="text-muted mb-4" style="font-size: 0.95rem;">
                ارفع ملف الحضور المُصدَّر من جهاز البصمة (Excel) لإدخال السجلات تلقائياً في النظام.
              </p>

              <div class="alert alert-info bg-light text-info border-info" style="font-size: 0.95rem;">
                <h5 class="mb-2"><i class="icon fas fa-info-circle"></i> قبل الاستيراد</h5>
                <ul class="mb-0 pr-3">
                  <li>تأكد أن الملف يحتوي على <b>رقم الجهاز (AC-No)</b> و<b>الوقت (Time)</b>.</li>
                  <li>يُقبل الملفات بصيغة <b>.xls</b> و <b>.xlsx</b>.</li>
                  <li>اختر نوع جهاز البصمة (ZKTeco، Advision، Hikvision) عند الرفع.</li>
                </ul>
              </div>

              <div class="text-center mt-4">
                <a href="importfplog.php" class="btn btn-primary btn-lg px-5 shadow-sm" style="border-radius: 30px;">
                  <i class="fas fa-cloud-upload-alt mr-2"></i>
                  استيراد الملفات
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>

    <?php } else { echo $userErrorMassage; } ?>
    </div>
  </section>
</div>

<?php include('includes/footer.php') ?>
