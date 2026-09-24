<?php include('includes/header.php'); ?>

<?php
$sittingpass = $sittingpass ?? 'hadi@1234';
$postedPass = isset($_POST['password']) ? (string) $_POST['password'] : null;
?>

<?php if ($postedPass === null): ?>

<div class="content-wrapper">
  <section class="content">
    <div class="container py-4">
      <div class="row justify-content-center">
        <div class="col-12 col-sm-10 col-md-6 col-lg-4">
          <div class="card card-outline card-primary shadow-sm">
            <div class="card-header text-center border-0 pt-4">
              <div class="mb-2 text-primary" style="font-size:2.5rem;"><i class="fas fa-shield-alt"></i></div>
              <h3 class="card-title font-weight-bold mb-0">إعدادات النظام</h3>
              <p class="text-muted small mb-0 mt-2">أدخل كلمة مرور الإعدادات للمتابعة</p>
            </div>
            <div class="card-body pt-0">
              <form method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') ?>" autocomplete="off">
                <div class="form-group">
                  <label for="settings-gate-password">كلمة المرور</label>
                  <input type="password"
                         name="password"
                         id="settings-gate-password"
                         class="form-control form-control-lg frst"
                         required
                         autocomplete="current-password"
                         placeholder="••••••••">
                </div>
                <button type="submit" class="btn btn-primary btn-block btn-lg">
                  <i class="fas fa-sign-in-alt ml-1"></i> متابعة
                </button>
              </form>
            </div>
          </div>
          <p class="text-center text-muted small mt-3 mb-0">
            <i class="fas fa-info-circle"></i> هذه الشاشة تحمي التعديلات الحساسة للنظام.
          </p>
        </div>
      </div>
    </div>
  </section>
</div>

<?php elseif ($postedPass !== $sittingpass): ?>

<div class="content-wrapper">
  <section class="content">
    <div class="container py-5">
      <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
          <div class="alert alert-danger shadow-sm text-center mb-0">
            <i class="fas fa-times-circle fa-2x mb-3 d-block"></i>
            <h4 class="alert-heading">كلمة المرور غير صحيحة</h4>
            <p class="mb-3">لا يمكن فتح صفحة الإعدادات دون كلمة المرور الصحيحة.</p>
            <a href="setting.php" class="btn btn-outline-danger">
              <i class="fas fa-redo ml-1"></i> إعادة المحاولة
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<?php else: ?>

<?php include('includes/navbar.php'); ?>
<?php include('includes/sidebar.php'); ?>

<style>
  .settings-vertical-pills .nav-link {
    color: #4a5568 !important;
    background-color: transparent;
    border-right: 4px solid transparent;
  }
  .settings-vertical-pills .nav-link.active {
    color: #007bff !important;
    background-color: #ffffff !important;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
    border-right: 4px solid #007bff !important;
  }
  .settings-vertical-pills .nav-link:hover:not(.active) {
    background-color: #e2e8f0 !important;
    color: #2d3748 !important;
  }
</style>

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2 align-items-center">
        <div class="col-sm-6">
          <h1 class="m-0 text-dark"><i class="fas fa-sliders-h text-primary ml-2"></i> الإعدادات العامة</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-left m-0 bg-transparent p-0">
            <li class="breadcrumb-item"><a href="dashboard.php">الرئيسية</a></li>
            <li class="breadcrumb-item active">الإعدادات</li>
          </ol>
        </div>
      </div>
      <div class="alert alert-warning alert-dismissible fade show border-0 shadow-sm">
        <button type="button" class="close" data-dismiss="alert" aria-label="إغلاق">&times;</button>
        <i class="fas fa-exclamation-triangle ml-2"></i>
        <strong>تنبيه:</strong> التعديل في هذه القائمة يؤثر على سلوك النظام بالكامل. راجع القيم قبل الحفظ.
      </div>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid">

      <form action="do/doedit_settings.php" method="post" id="settings-main-form" enctype="multipart/form-data">

        <div class="row">
          <!-- قائمة التبويبات الجانبية (شبه تطبيقات سطح المكتب) -->
          <div class="col-md-3 mb-4">
            <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
              <div class="card-header bg-dark text-white border-0 py-3 text-center" style="background-color: #1a202c !important;">
                <h5 class="mb-0 fw-bold"><i class="fas fa-cog fa-spin ml-2"></i> لوحة الإعدادات</h5>
              </div>
              <div class="card-body p-2 bg-light">
                <div class="nav flex-column nav-pills settings-vertical-pills" id="settingsTabs" role="tablist" aria-orientation="vertical">
                  <a class="nav-link active py-3 px-4 mb-2 d-flex align-items-center" id="company-tab" data-toggle="pill" href="#tab-company" role="tab" aria-controls="tab-company" aria-selected="true" style="border-radius: 8px; font-weight: 600; transition: all 0.2s ease;">
                    <i class="fas fa-building ml-3" style="font-size: 1.1rem; width: 20px;"></i> الشركة واللغة
                  </a>
                  <a class="nav-link py-3 px-4 mb-2 d-flex align-items-center" id="pos-tab" data-toggle="pill" href="#tab-pos" role="tab" aria-controls="tab-pos" aria-selected="false" style="border-radius: 8px; font-weight: 600; transition: all 0.2s ease;">
                    <i class="fas fa-cash-register ml-3" style="font-size: 1.1rem; width: 20px;"></i> نقطة البيع (POS)
                  </a>
                  <a class="nav-link py-3 px-4 mb-2 d-flex align-items-center" id="themes-tab" data-toggle="pill" href="#tab-themes" role="tab" aria-controls="tab-themes" aria-selected="false" style="border-radius: 8px; font-weight: 600; transition: all 0.2s ease;">
                    <i class="fas fa-palette ml-3" style="font-size: 1.1rem; width: 20px;"></i> سمة النظام (Themes)
                  </a>
                  <a class="nav-link py-3 px-4 mb-2 d-flex align-items-center" id="commissions-tab" data-toggle="pill" href="#tab-commissions" role="tab" aria-controls="tab-commissions" aria-selected="false" style="border-radius: 8px; font-weight: 600; transition: all 0.2s ease;">
                    <i class="fas fa-percent ml-3" style="font-size: 1.1rem; width: 20px;"></i> إعدادات العمولات
                  </a>
                  <a class="nav-link py-3 px-4 mb-2 d-flex align-items-center" id="sidebar-tab" data-toggle="pill" href="#tab-sidebar" role="tab" aria-controls="tab-sidebar" aria-selected="false" style="border-radius: 8px; font-weight: 600; transition: all 0.2s ease;">
                    <i class="fas fa-eye ml-3" style="font-size: 1.1rem; width: 20px;"></i> الشريط الجانبي
                  </a>
                  <a class="nav-link py-3 px-4 d-flex align-items-center" id="print-tab" data-toggle="pill" href="#tab-print" role="tab" aria-controls="tab-print" aria-selected="false" style="border-radius: 8px; font-weight: 600; transition: all 0.2s ease;">
                    <i class="fas fa-print ml-3" style="font-size: 1.1rem; width: 20px;"></i> إعدادات الطباعة
                  </a>
                  <a class="nav-link py-3 px-4 mb-2 d-flex align-items-center" id="database-tab" data-toggle="pill" href="#tab-database" role="tab" aria-controls="tab-database" aria-selected="false" style="border-radius: 8px; font-weight: 600; transition: all 0.2s ease;">
                    <i class="fas fa-database ml-3" style="font-size: 1.1rem; width: 20px;"></i> قاعدة البيانات
                  </a>
                </div>
              </div>
            </div>
          </div>

          <!-- محتوى التبويبات الفعلي -->
          <div class="col-md-9">
            <div class="tab-content" id="settingsTabsContent">
              
              <!-- 1. الشركة واللغة -->
              <div class="tab-pane fade show active" id="tab-company" role="tabpanel" aria-labelledby="company-tab">
                <div class="card card-primary card-outline shadow-sm border-0" style="border-radius: 12px;">
                  <div class="card-header bg-white py-3">
                    <h3 class="card-title text-primary font-weight-bold mb-0"><i class="fas fa-building ml-2"></i> بيانات الشركة واللغة</h3>
                  </div>
                  <div class="card-body">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="companyname">اسم الشركة</label>
                          <input type="text" class="form-control" id="companyname" name="companyname"
                                 value="<?= htmlspecialchars($rowstg['company_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="companytel">هاتف الشركة</label>
                          <input type="text" class="form-control" id="companytel" name="companytel"
                                 value="<?= htmlspecialchars($rowstg['company_tel'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                      </div>
                      <div class="col-12">
                        <div class="form-group">
                          <label for="companyadd">عنوان الشركة</label>
                          <input type="text" class="form-control" id="companyadd" name="companyadd"
                                 value="<?= htmlspecialchars($rowstg['company_add'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                      </div>
                      <!-- حقل رفع لوجو الشركة -->
                      <div class="col-12">
                        <div class="form-group">
                          <label>شعار الشركة (اللوجو)</label>
                          <div class="d-flex align-items-center flex-wrap" style="gap: 16px;">
                            <?php
                              $current_logo = $rowstg['company_logo'] ?? '';
                              $logo_src = '';
                              if (!empty($current_logo) && file_exists(__DIR__ . '/assets/logo/' . $current_logo)) {
                                  $logo_src = 'assets/logo/' . htmlspecialchars($current_logo, ENT_QUOTES, 'UTF-8');
                              } elseif (file_exists(__DIR__ . '/assets/logo/logo.jpg')) {
                                  $logo_src = 'assets/logo/logo.jpg';
                              }
                            ?>
                            <?php if ($logo_src): ?>
                              <img id="logo-preview" src="<?= $logo_src ?>?v=<?= time() ?>" alt="لوجو الشركة"
                                style="height: 70px; width: auto; max-width: 200px; border-radius: 8px; border: 2px solid #dee2e6; object-fit: contain; background: #f8f9fa; padding: 4px;">
                            <?php else: ?>
                              <div id="logo-preview-placeholder" style="height: 70px; width: 120px; border-radius: 8px; border: 2px dashed #dee2e6; display: flex; align-items: center; justify-content: center; background: #f8f9fa; color: #adb5bd; font-size: 0.8rem;">
                                <i class="fas fa-image fa-2x"></i>
                              </div>
                            <?php endif; ?>
                            <div>
                              <div class="custom-file" style="width: 280px;">
                                <input type="file" class="custom-file-input" id="company_logo" name="company_logo" accept="image/jpeg,image/png,image/gif,image/webp,image/svg+xml">
                                <label class="custom-file-label text-right" for="company_logo">اختر صورة اللوجو...</label>
                              </div>
                              <small class="form-text text-muted mt-1">الصيغ المسموحة: JPG, PNG, GIF, WEBP, SVG — اتركها فارغة للإبقاء على اللوجو الحالي.</small>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="lang-select">لغة الواجهة</label>
                          <select class="form-control" id="lang-select" name="lang">
                            <option value="ar" <?= (($rowstg['lang'] ?? '') === 'ar') ? 'selected' : '' ?>>العربية</option>
                            <option value="en" <?= (($rowstg['lang'] ?? '') === 'en') ? 'selected' : '' ?>>English</option>
                            <option value="fr" <?= (($rowstg['lang'] ?? '') === 'fr') ? 'selected' : '' ?>>Français</option>
                            <option value="gr" <?= (($rowstg['lang'] ?? '') === 'gr') ? 'selected' : '' ?>>Deutsch</option>
                            <option value="sp" <?= (($rowstg['lang'] ?? '') === 'sp') ? 'selected' : '' ?>>Español</option>
                            <option value="trk" <?= (($rowstg['lang'] ?? '') === 'trk') ? 'selected' : '' ?>>Türkçe</option>
                            <option value="ch" <?= (($rowstg['lang'] ?? '') === 'ch') ? 'selected' : '' ?>>中文</option>
                            <option value="hn" <?= (($rowstg['lang'] ?? '') === 'hn') ? 'selected' : '' ?>>हिन्दी</option>
                            <option value="urd" <?= (($rowstg['lang'] ?? '') === 'urd') ? 'selected' : '' ?>>اردو</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="edit_pass">كلمة مرور حماية التعديل داخل النظام</label>
                          <input type="text" class="form-control" id="edit_pass" name="edit_pass"
                                 value="<?= htmlspecialchars($rowstg['edit_pass'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                          <small class="form-text text-muted">تُستخدم في بعض شاشات التعديل الحساسة.</small>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="editpass">الترخيص / رقم إضافي</label>
                          <input type="text" class="form-control" id="editpass" name="editpass"
                                 value="<?= htmlspecialchars($rowstg['lic'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- 2. نقطة البيع -->
              <div class="tab-pane fade" id="tab-pos" role="tabpanel" aria-labelledby="pos-tab">
                <div class="card card-outline card-info shadow-sm border-0" style="border-radius: 12px;">
                  <div class="card-header bg-white py-3">
                    <h3 class="card-title text-info font-weight-bold mb-0"><i class="fas fa-cash-register ml-2"></i> إعدادات نقطة البيع (POS)</h3>
                  </div>
                  <div class="card-body">
                    <div class="row">
                      <div class="col-lg-6">
                        <div class="form-group">
                          <label for="pos_type">نوع نظام POS</label>
                          <select class="form-control" id="pos_type" name="pos_type">
                            <option value="barcode" <?= (($rowstg['pos_type'] ?? 'barcode') === 'barcode') ? 'selected' : '' ?>>POS عادي (باركود)</option>
                            <option value="clothes" <?= (($rowstg['pos_type'] ?? 'barcode') === 'clothes') ? 'selected' : '' ?>>POS ملابس</option>
                          </select>
                          <small class="form-text text-muted">يحدد نوع واجهة POS من القائمة.</small>
                        </div>
                      </div>
                      <div class="col-lg-6">
                        <div class="form-group">
                          <label class="d-block">حماية POS بكلمة مرور</label>
                          <div class="custom-control custom-switch pt-1">
                            <input type="checkbox" class="custom-control-input" id="pos_has_password" name="pos_has_password" value="1"
                                   <?= (!empty($rowstg['pos_has_password'])) ? 'checked' : '' ?>>
                            <label class="custom-control-label" for="pos_has_password">طلب مسح باركود قبل فتح POS</label>
                          </div>
                          <small class="form-text text-muted">عند التفعيل يُطلب التحقق قبل استخدام الكاشير.</small>
                        </div>
                      </div>
                    </div>
                    <hr class="my-3">
                    <h5 class="text-muted mb-3"><i class="fas fa-link ml-2"></i> الحسابات الافتراضية للكاشير</h5>
                    <div class="row">
                      <div class="col-md-6 col-lg-4">
                        <div class="form-group">
                          <label for="acc_rent">إيجار مستحق (حساب)</label>
                          <input type="number" class="form-control" id="acc_rent" name="acc_rent"
                                 value="<?= htmlspecialchars((string)($rowstg['acc_rent'] ?? '0'), ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                      </div>
                      <div class="col-md-6 col-lg-4">
                        <div class="form-group">
                          <label for="def_pos_client">عميل الكاشير الافتراضي</label>
                          <input type="number" class="form-control" id="def_pos_client" name="def_pos_client"
                                 value="<?= htmlspecialchars((string)($rowstg['def_pos_client'] ?? '0'), ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                      </div>
                      <div class="col-md-6 col-lg-4">
                        <div class="form-group">
                          <label for="def_pos_store">مخزن الكاشير الافتراضي</label>
                          <input type="number" class="form-control" id="def_pos_store" name="def_pos_store"
                                 value="<?= htmlspecialchars((string)($rowstg['def_pos_store'] ?? '0'), ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                      </div>
                      <div class="col-md-6 col-lg-4">
                        <div class="form-group">
                          <label for="def_pos_employee">موظف الكاشير الافتراضي</label>
                          <input type="number" class="form-control" id="def_pos_employee" name="def_pos_employee"
                                 value="<?= htmlspecialchars((string)($rowstg['def_pos_employee'] ?? '0'), ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                      </div>
                      <div class="col-md-6 col-lg-4">
                        <div class="form-group">
                          <label for="def_pos_fund">صندوق الكاشير الافتراضي</label>
                          <input type="number" class="form-control" id="def_pos_fund" name="def_pos_fund"
                                 value="<?= htmlspecialchars((string)($rowstg['def_pos_fund'] ?? '0'), ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- 3. سمة النظام -->
              <div class="tab-pane fade" id="tab-themes" role="tabpanel" aria-labelledby="themes-tab">
                <div class="card card-outline card-secondary shadow-sm border-0" style="border-radius: 12px;">
                  <div class="card-header bg-white py-3">
                    <h3 class="card-title text-secondary font-weight-bold mb-0"><i class="fas fa-palette ml-2"></i> سمة النظام (Themes)</h3>
                  </div>
                  <div class="card-body">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="bodycolor">اختر السمة الموحدة للنظام</label>
                          <select class="form-control" id="bodycolor" name="bodycolor">
                            <option value="solarized_white" <?= (($rowstg['bodycolor'] ?? '') === 'solarized_white') ? 'selected' : '' ?>>Solarized White (فاتح مريح)</option>
                            <option value="monokai" <?= (($rowstg['bodycolor'] ?? '') === 'monokai') ? 'selected' : '' ?>>Monokai (داكن الكلاسيكي)</option>
                            <option value="tokyo_night" <?= (($rowstg['bodycolor'] ?? '') === 'tokyo_night') ? 'selected' : '' ?>>Tokyo Night (داكن حديث)</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="ui_font">نوع الخط</label>
                          <?php
                            $fontCookie = isset($_COOKIE['ui_font']) ? preg_replace('/[^a-z0-9_]/', '', (string) $_COOKIE['ui_font']) : 'playpen';
                            $fontOptions = [
                              'playpen' => 'Playpen Sans Arabic (الافتراضي)',
                              'arabic_script' => 'Arabic Type Script (Amiri)',
                              'cairo' => 'Cairo',
                              'tajawal' => 'Tajawal',
                              'source_sans' => 'Source Sans Pro',
                              'tahoma' => 'Tahoma',
                              'segoe' => 'Segoe UI',
                              'arial' => 'Arial',
                            ];
                            if ($fontCookie === '' || !isset($fontOptions[$fontCookie])) {
                              $fontCookie = 'playpen';
                            }
                          ?>
                          <select class="form-control" id="ui_font" name="ui_font">
                            <?php
                              $fontFamilyPreview = [
                                'playpen' => "'Playpen Sans Arabic', cursive",
                                'arabic_script' => "'Amiri', 'Traditional Arabic', serif",
                                'cairo' => "'Cairo', sans-serif",
                                'tajawal' => "'Tajawal', sans-serif",
                                'source_sans' => "'Source Sans Pro', sans-serif",
                                'tahoma' => 'Tahoma, sans-serif',
                                'segoe' => "'Segoe UI', sans-serif",
                                'arial' => 'Arial, sans-serif',
                              ];
                              foreach ($fontOptions as $fontKey => $fontLabel):
                            ?>
                              <option value="<?= htmlspecialchars($fontKey, ENT_QUOTES, 'UTF-8') ?>"
                                      <?= ($fontCookie === $fontKey) ? 'selected' : '' ?>
                                      style="font-family: <?= htmlspecialchars($fontFamilyPreview[$fontKey], ENT_QUOTES, 'UTF-8') ?>">
                                <?= htmlspecialchars($fontLabel, ENT_QUOTES, 'UTF-8') ?>
                              </option>
                            <?php endforeach; ?>
                          </select>
                          <small class="form-text text-muted">يُحفظ على هذا الجهاز في الكوكيز ويُطبَّق فوراً على الواجهة.</small>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- 4. إعدادات العمولات -->
              <div class="tab-pane fade" id="tab-commissions" role="tabpanel" aria-labelledby="commissions-tab">
                <div class="card card-outline card-danger shadow-sm border-0" style="border-radius: 12px;">
                  <div class="card-header bg-white py-3">
                    <h3 class="card-title text-danger font-weight-bold mb-0"><i class="fas fa-percent ml-2"></i> إعدادات العمولات</h3>
                  </div>
                  <div class="card-body">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="emp_commission">عمولة الموظفين (%)</label>
                          <input type="number" step="0.01" min="0" max="100" class="form-control" id="emp_commission" name="emp_commission"
                                 value="<?= htmlspecialchars((string)($rowstg['emp_commission'] ?? '0.00'), ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="user_commission">عمولة المستخدمين (%)</label>
                          <input type="number" step="0.01" min="0" max="100" class="form-control" id="user_commission" name="user_commission"
                                 value="<?= htmlspecialchars((string)($rowstg['user_commission'] ?? '0.00'), ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- 5. ظهور القوائم -->
              <div class="tab-pane fade" id="tab-sidebar" role="tabpanel" aria-labelledby="sidebar-tab">
                <div class="card card-outline card-warning shadow-sm border-0" style="border-radius: 12px;">
                  <div class="card-header bg-white py-3">
                    <h3 class="card-title text-warning font-weight-bold mb-0"><i class="fas fa-eye ml-2"></i> ظهور القوائم في الشريط الجانبي</h3>
                  </div>
                  <div class="card-body p-0">
                    <div class="table-responsive">
                      <table class="table table-hover table-striped mb-0 text-center">
                        <thead class="thead-light">
                          <tr>
                            <th class="py-3" style="width:60%">القائمة</th>
                            <th class="py-3" style="width:40%">الظهور (1 = ظاهر، 0 = مخفي)</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td class="text-right px-4"><i class="fas fa-key text-warning ml-2"></i> التأجير</td>
                            <td class="px-4"><input type="number" name="showrent" class="form-control form-control-sm mx-auto" style="max-width: 120px;" min="0" max="1" step="1"
                                       value="<?= htmlspecialchars((string)($rowstg['showrent'] ?? '0'), ENT_QUOTES, 'UTF-8') ?>"></td>
                          </tr>
                          <tr>
                            <td class="text-right px-4"><i class="fas fa-clinic-medical text-info ml-2"></i> العيادات</td>
                            <td class="px-4"><input type="number" name="showclinc" class="form-control form-control-sm mx-auto" style="max-width: 120px;" min="0" max="1" step="1"
                                       value="<?= htmlspecialchars((string)($rowstg['showclinc'] ?? '0'), ENT_QUOTES, 'UTF-8') ?>"></td>
                          </tr>
                          <tr>
                            <td class="text-right px-4"><i class="fas fa-users text-primary ml-2"></i> الموارد البشرية</td>
                            <td class="px-4"><input type="number" name="showhr" class="form-control form-control-sm mx-auto" style="max-width: 120px;" min="0" max="1" step="1"
                                       value="<?= htmlspecialchars((string)($rowstg['showhr'] ?? '0'), ENT_QUOTES, 'UTF-8') ?>"></td>
                          </tr>
                          <tr>
                            <td class="text-right px-4"><i class="fas fa-bolt text-warning ml-2"></i> Pulse (التقييم اللحظي)</td>
                            <td class="px-4"><input type="number" name="showpulse" class="form-control form-control-sm mx-auto" style="max-width: 120px;" min="0" max="1" step="1"
                                       value="<?= htmlspecialchars((string)($rowstg['showpulse'] ?? '1'), ENT_QUOTES, 'UTF-8') ?>"></td>
                          </tr>
                          <tr>
                            <td class="text-right px-4"><i class="fas fa-user-check text-success ml-2"></i> الحضور</td>
                            <td class="px-4"><input type="number" name="showatt" class="form-control form-control-sm mx-auto" style="max-width: 120px;" min="0" max="1" step="1"
                                       value="<?= htmlspecialchars((string)($rowstg['showatt'] ?? '0'), ENT_QUOTES, 'UTF-8') ?>"></td>
                          </tr>
                          <tr>
                            <td class="text-right px-4"><i class="fas fa-money-bill-wave text-secondary ml-2"></i> المرتبات</td>
                            <td class="px-4"><input type="number" name="showpayroll" class="form-control form-control-sm mx-auto" style="max-width: 120px;" min="0" max="1" step="1"
                                       value="<?= htmlspecialchars((string)($rowstg['showpayroll'] ?? '0'), ENT_QUOTES, 'UTF-8') ?>"></td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>

              <!-- 6. إعدادات الطباعة -->
              <div class="tab-pane fade" id="tab-print" role="tabpanel" aria-labelledby="print-tab">
                <div class="card card-outline card-dark shadow-sm border-0" style="border-radius: 12px;">
                  <div class="card-header bg-white py-3">
                    <h3 class="card-title text-dark font-weight-bold mb-0"><i class="fas fa-print ml-2"></i> إعدادات الطباعة للفواتير</h3>
                  </div>
                  <div class="card-body">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="d-block">إظهار اللوجو (الشعار)</label>
                          <div class="custom-control custom-switch pt-1">
                            <input type="checkbox" class="custom-control-input" id="receipt_show_logo" name="receipt_show_logo" value="1"
                                   <?= (!isset($rowstg['receipt_show_logo']) || !empty($rowstg['receipt_show_logo'])) ? 'checked' : '' ?>>
                            <label class="custom-control-label" for="receipt_show_logo">عرض شعار الشركة في الفاتورة المطبوعة</label>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="d-block">إظهار بيانات العميل</label>
                          <div class="custom-control custom-switch pt-1">
                            <input type="checkbox" class="custom-control-input" id="receipt_show_client" name="receipt_show_client" value="1"
                                   <?= (!isset($rowstg['receipt_show_client']) || !empty($rowstg['receipt_show_client'])) ? 'checked' : '' ?>>
                            <label class="custom-control-label" for="receipt_show_client">عرض بيانات العميل في الفواتير (دليفري/آجل)</label>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="receipt_paper_width">عرض ورقة الطباعة</label>
                          <select class="form-control" id="receipt_paper_width" name="receipt_paper_width">
                            <option value="78mm" <?= (($rowstg['receipt_paper_width'] ?? '78mm') === '78mm') ? 'selected' : '' ?>>80mm (الافتراضي 78mm)</option>
                            <option value="58mm" <?= (($rowstg['receipt_paper_width'] ?? '') === '58mm') ? 'selected' : '' ?>>58mm (طابعة صغيرة)</option>
                            <option value="100%" <?= (($rowstg['receipt_paper_width'] ?? '') === '100%') ? 'selected' : '' ?>>100% (كامل الشاشة / A4)</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="receipt_font_size">حجم الخط الأساسي (px)</label>
                          <input type="number" class="form-control" id="receipt_font_size" name="receipt_font_size"
                                 value="<?= htmlspecialchars((string)($rowstg['receipt_font_size'] ?? '14'), ENT_QUOTES, 'UTF-8') ?>" min="8" max="24">
                        </div>
                      </div>
                      <div class="col-12">
                        <div class="form-group">
                          <label for="receipt_header_text">نص أعلى الفاتورة (الهيدر)</label>
                          <textarea class="form-control" id="receipt_header_text" name="receipt_header_text" rows="2" placeholder="مثال: أهلاً بكم في شركتنا"><?= htmlspecialchars((string)($rowstg['receipt_header_text'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                          <small class="form-text text-muted">يظهر أسفل اللوجو مباشرةً قبل بيانات الفاتورة.</small>
                        </div>
                      </div>
                      <div class="col-12">
                        <div class="form-group">
                          <label for="receipt_footer_text">نص أسفل الفاتورة (الفوتر)</label>
                          <textarea class="form-control" id="receipt_footer_text" name="receipt_footer_text" rows="2" placeholder="❤ perfect place to grow"><?= htmlspecialchars((string)($rowstg['receipt_footer_text'] ?? '❤ perfect place to grow'), ENT_QUOTES, 'UTF-8') ?></textarea>
                          <small class="form-text text-muted">يمكن كتابة سياسة الاسترجاع أو رسالة شكر للعميل.</small>
                        </div>
                      </div>
                      <div class="col-12">
                        <div class="form-group">
                          <label for="receipt_notes_text">ملاحظات / شروط وأحكام</label>
                          <textarea class="form-control" id="receipt_notes_text" name="receipt_notes_text" rows="3" placeholder="مثال: لا يُقبل الاسترجاع بعد 7 أيام من الشراء"><?= htmlspecialchars((string)($rowstg['receipt_notes_text'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                          <small class="form-text text-muted">يظهر في نهاية الفاتورة تحت الفوتر بخط أصغر.</small>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- 7. قاعدة البيانات -->
              <div class="tab-pane fade" id="tab-database" role="tabpanel" aria-labelledby="database-tab">
                <div class="card card-outline card-success shadow-sm border-0 mb-3" style="border-radius: 12px;">
                  <div class="card-header bg-white py-3">
                    <h3 class="card-title text-success font-weight-bold mb-0"><i class="fas fa-database ml-2"></i> تحديث وإدارة قاعدة البيانات</h3>
                  </div>
                  <div class="card-body">
                    <p class="text-muted mb-3">يشغّل التحديثات الناقصة من مجلد <code>update/</code> مرة واحدة لكل إصدار عبر <code>schema_migrations</code>.</p>
                    <div id="db-migration-status" class="alert alert-secondary mb-3">جاري تحميل حالة التحديثات...</div>
                    <div class="d-flex flex-wrap gap-2" style="gap:10px;">
                      <button type="button" class="btn btn-success" id="btnRunMigrations">
                        <i class="fas fa-sync-alt ml-1"></i> تحديث قاعدة البيانات
                      </button>
                      <button type="button" class="btn btn-outline-warning" id="btnRestoreBackup">
                        <i class="fas fa-file-import ml-1"></i> استعادة نسخة احتياطية
                      </button>
                      <a href="pre_start.php" class="btn btn-outline-primary" target="_blank">
                        <i class="fas fa-plus-circle ml-1"></i> إنشاء قاعدة جديدة
                      </a>
                      <input type="file" id="backupFileInput" accept=".sql,application/sql,text/plain" style="display:none;">
                    </div>
                    <ul id="db-pending-list" class="mt-3 mb-0 small text-muted"></ul>
                  </div>
                </div>

                <div class="card card-outline card-primary shadow-sm border-0" style="border-radius: 12px;">
                  <div class="card-header bg-white py-3">
                    <h3 class="card-title text-primary font-weight-bold mb-0"><i class="fas fa-calendar-alt ml-2"></i> المدد وقواعد البيانات المرتبطة</h3>
                  </div>
                  <div class="card-body">
                    <p class="text-muted small mb-3">
                      كل مدة = قاعدة بيانات مستقلة. قفل المدة ينشئ قاعدة جديدة وينقل الأرصدة الافتتاحية (حسابات + مخزون) بدون حركات الفترة المقفلة.
                    </p>

                    <div class="form-group">
                      <label for="periodDbSelect">القاعدة / المدة النشطة</label>
                      <div class="input-group">
                        <select id="periodDbSelect" class="form-control"></select>
                        <div class="input-group-append">
                          <button type="button" class="btn btn-primary" id="btnSwitchPeriod">
                            <i class="fas fa-exchange-alt ml-1"></i> تبديل
                          </button>
                        </div>
                      </div>
                      <small class="text-muted" id="periodCurrentLabel">جاري التحميل...</small>
                    </div>

                    <hr>

                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <h6 class="font-weight-bold text-danger"><i class="fas fa-lock ml-1"></i> قفل المدة الحالية</h6>
                        <p class="small text-muted">يُقفل العمل على المدة الحالية ويُفتح مدة جديدة بأرصدة افتتاحية من الإقفال.</p>
                        <div class="form-group">
                          <label>اسم القاعدة الجديدة</label>
                          <input type="text" class="form-control" id="closeDbName" placeholder="مثال: kody_2026" pattern="[A-Za-z0-9_]{2,64}">
                        </div>
                        <div class="form-group">
                          <label>وصف المدة</label>
                          <input type="text" class="form-control" id="closeDbLabel" placeholder="مثال: مدة 2026">
                        </div>
                        <button type="button" class="btn btn-danger btn-block" id="btnClosePeriod">
                          <i class="fas fa-lock ml-1"></i> قفل المدة ونقل الأرصدة
                        </button>
                      </div>
                      <div class="col-md-6 mb-3">
                        <h6 class="font-weight-bold text-info"><i class="fas fa-plus-circle ml-1"></i> قاعدة بيانات جديدة (فارغة)</h6>
                        <p class="small text-muted">ينشئ قاعدة من الهيكل الافتراضي بدون نقل أرصدة، ويضيفها لمجموعة المدد.</p>
                        <div class="form-group">
                          <label>اسم القاعدة</label>
                          <input type="text" class="form-control" id="newDbName" placeholder="مثال: kody_demo" pattern="[A-Za-z0-9_]{2,64}">
                        </div>
                        <div class="form-group">
                          <label>الوصف</label>
                          <input type="text" class="form-control" id="newDbLabel" placeholder="مثال: تجريبي">
                        </div>
                        <button type="button" class="btn btn-info btn-block" id="btnCreatePeriodDb">
                          <i class="fas fa-database ml-1"></i> إنشاء قاعدة جديدة
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

            </div>

            <!-- زر حفظ التغييرات أسفل التبويبات -->
            <div class="card card-outline card-success shadow-sm mb-4 border-0" style="border-radius: 12px;">
              <div class="card-body d-flex flex-wrap align-items-center justify-content-between py-3">
                <div class="mb-2 mb-md-0 text-right">
                  <strong class="text-dark"><i class="fas fa-save ml-2 text-success"></i> حفظ جميع التغييرات</strong>
                  <span class="text-muted d-block small mt-1">بعد حفظ التعديلات سيتم توجيهك إلى لوحة التحكم الرئيسية.</span>
                </div>
                <button type="submit" class="btn btn-success btn-lg px-5 font-weight-bold" style="border-radius: 8px;">
                  <i class="fas fa-check ml-2"></i> تأكيد الحفظ
                </button>
              </div>
            </div>

          </div>
        </div>

      </form>

    </div>
  </section>
</div>

<?php endif; ?>

<script>
// Preview اللوجو لما المستخدم يختار صورة
document.addEventListener('DOMContentLoaded', function () {
  var logoInput = document.getElementById('company_logo');
  if (logoInput) {
    logoInput.addEventListener('change', function () {
      var file = this.files[0];
      if (!file) return;

      // تحديث اسم الملف في الـ label
      var label = this.nextElementSibling;
      if (label) label.textContent = file.name;

      // عرض preview
      var reader = new FileReader();
      reader.onload = function (e) {
        var preview = document.getElementById('logo-preview');
        var placeholder = document.getElementById('logo-preview-placeholder');
        if (preview) {
          preview.src = e.target.result;
        } else if (placeholder) {
          var img = document.createElement('img');
          img.id = 'logo-preview';
          img.src = e.target.result;
          img.alt = 'لوجو الشركة';
          img.style.cssText = 'height:70px;width:auto;max-width:200px;border-radius:8px;border:2px solid #dee2e6;object-fit:contain;background:#f8f9fa;padding:4px;';
          placeholder.replaceWith(img);
        }
      };
      reader.readAsDataURL(file);
    });
  }

  // نوع الخط — يُحفظ في الكوكيز ويُطبَّق فوراً
  var fontSelect = document.getElementById('ui_font');
  if (fontSelect) {
    var fontMap = {
      playpen: "'Playpen Sans Arabic', cursive",
      arabic_script: "'Amiri', 'Traditional Arabic', 'Arabic Typesetting', serif",
      cairo: "'Cairo', 'Segoe UI', Tahoma, sans-serif",
      tajawal: "'Tajawal', 'Segoe UI', Tahoma, sans-serif",
      source_sans: "'Source Sans Pro', 'Segoe UI', Tahoma, sans-serif",
      tahoma: "Tahoma, 'Segoe UI', Arial, sans-serif",
      segoe: "'Segoe UI', Tahoma, Arial, sans-serif",
      arial: "Arial, Tahoma, sans-serif"
    };

    function setUiFontCookie(key) {
      var exp = new Date();
      exp.setFullYear(exp.getFullYear() + 1);
      document.cookie = 'ui_font=' + encodeURIComponent(key) + '; expires=' + exp.toUTCString() + '; path=/; SameSite=Lax';
    }

    function ensureFontStylesheet(key) {
      var id = 'ui-font-extra-' + key;
      if (document.getElementById(id)) return;
      var href = '';
      if (key === 'arabic_script') {
        href = 'https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&display=swap';
      } else if (key === 'cairo') {
        href = 'https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap';
      } else if (key === 'tajawal') {
        href = 'https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap';
      } else if (key === 'source_sans') {
        href = 'assets/libs/source-sans-pro-local.css';
      }
      if (!href) return;
      var link = document.createElement('link');
      link.id = id;
      link.rel = 'stylesheet';
      link.href = href;
      document.head.appendChild(link);
    }

    function applyUiFont(key) {
      if (!fontMap[key]) key = 'playpen';
      ensureFontStylesheet(key);
      document.documentElement.style.setProperty('--app-font-family', fontMap[key]);
      if (document.body) {
        document.body.style.fontFamily = fontMap[key];
        document.body.setAttribute('data-ui-font', key);
      }
      setUiFontCookie(key);
    }

    fontSelect.addEventListener('change', function () {
      applyUiFont(this.value);
    });

    // تأكيد الحفظ عند إرسال نموذج الإعدادات أيضاً
    var settingsForm = document.getElementById('settings-main-form');
    if (settingsForm) {
      settingsForm.addEventListener('submit', function () {
        applyUiFont(fontSelect.value);
      });
    }
  }

  // ─── Database migrations panel ───
  function renderMigrationStatus(status) {
    var box = document.getElementById('db-migration-status');
    var list = document.getElementById('db-pending-list');
    if (!box) return;
    if (!status) {
      box.className = 'alert alert-warning mb-3';
      box.textContent = 'تعذر قراءة حالة التحديثات';
      return;
    }
    if (status.pending > 0) {
      box.className = 'alert alert-warning mb-3';
      box.innerHTML = 'يوجد <strong>' + status.pending + '</strong> تحديث ناقص من أصل ' + status.total
        + (status.latest_applied ? ' — آخر مطبّق: <code>' + status.latest_applied + '</code>' : '');
    } else {
      box.className = 'alert alert-success mb-3';
      box.innerHTML = 'قاعدة البيانات محدّثة (' + status.applied + '/' + status.total + ')'
        + (status.latest_applied ? ' — <code>' + status.latest_applied + '</code>' : '');
    }
    if (list) {
      list.innerHTML = '';
      (status.pending_list || []).forEach(function (name) {
        var li = document.createElement('li');
        li.textContent = name;
        list.appendChild(li);
      });
    }
  }

  function loadMigrationStatus() {
    fetch('ajax/run_migrations.php?action=status', { credentials: 'same-origin' })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        if (data.success) renderMigrationStatus(data.status);
        else renderMigrationStatus(null);
      })
      .catch(function () { renderMigrationStatus(null); });
  }

  var btnMig = document.getElementById('btnRunMigrations');
  if (btnMig) {
    loadMigrationStatus();
    btnMig.addEventListener('click', function () {
      if (!confirm('تطبيق التحديثات الناقصة على قاعدة البيانات؟')) return;
      btnMig.disabled = true;
      btnMig.innerHTML = '<i class="fas fa-spinner fa-spin ml-1"></i> جاري التحديث...';
      var fd = new FormData();
      fd.append('action', 'run');
      fetch('ajax/run_migrations.php', { method: 'POST', body: fd, credentials: 'same-origin' })
        .then(function (r) { return r.json(); })
        .then(function (data) {
          alert(data.message || (data.success ? 'تم' : 'فشل'));
          if (data.status) renderMigrationStatus(data.status);
          else loadMigrationStatus();
        })
        .catch(function () { alert('خطأ في الاتصال بالخادم'); })
        .finally(function () {
          btnMig.disabled = false;
          btnMig.innerHTML = '<i class="fas fa-sync-alt ml-1"></i> تحديث قاعدة البيانات';
        });
    });
  }

  function validateDbName(name) {
    name = (name || '').trim();
    if (!name) return 'أدخل اسم قاعدة البيانات';
    if (!/^[A-Za-z0-9_]{2,64}$/.test(name)) {
      return 'اسم غير صالح: حروف إنجليزية/أرقام/_ فقط، من 2 إلى 64 حرفاً';
    }
    var reserved = ['mysql', 'information_schema', 'performance_schema', 'sys'];
    if (reserved.indexOf(name.toLowerCase()) !== -1) {
      return 'لا يمكن استخدام اسم محجوز للنظام';
    }
    return '';
  }

  function askRestoreDbName(defaultName) {
    var name = prompt('استعادة النسخة باسم ماذا؟\n(حروف إنجليزية / أرقام / _ فقط، 2–64)', defaultName || 'kody2');
    if (name === null) return null;
    var err = validateDbName(name);
    while (err) {
      name = prompt('خطأ: ' + err + '\n\nاستعادة النسخة باسم ماذا؟', name.trim());
      if (name === null) return null;
      err = validateDbName(name);
    }
    return name.trim();
  }

  var btnRestore = document.getElementById('btnRestoreBackup');
  var backupInput = document.getElementById('backupFileInput');
  var pendingRestoreDbName = '';
  if (btnRestore && backupInput) {
    btnRestore.addEventListener('click', function () {
      var dbName = askRestoreDbName('kody2');
      if (!dbName) return;
      pendingRestoreDbName = dbName;
      backupInput.value = '';
      backupInput.click();
    });
    backupInput.addEventListener('change', function () {
      var file = backupInput.files && backupInput.files[0];
      var dbName = pendingRestoreDbName;
      pendingRestoreDbName = '';
      if (!file) return;
      var nameErr = validateDbName(dbName);
      if (nameErr) {
        alert(nameErr);
        backupInput.value = '';
        return;
      }
      if (!confirm('استعادة الملف:\n' + file.name + '\n\nإلى قاعدة البيانات:\n' + dbName + '\n\nسيتم حقن البيانات وتكملة الجداول الناقصة.')) {
        backupInput.value = '';
        return;
      }
      btnRestore.disabled = true;
      btnRestore.innerHTML = '<i class="fas fa-spinner fa-spin ml-1"></i> جاري الاستعادة...';
      var fd = new FormData();
      fd.append('action', 'restore');
      fd.append('db_name', dbName);
      fd.append('backup_file', file);
      fetch('ajax/db_setup.php', { method: 'POST', body: fd, credentials: 'same-origin' })
        .then(function (r) { return r.json(); })
        .then(function (data) {
          alert(data.message || (data.success ? 'تم الاستعادة' : 'فشلت الاستعادة'));
          if (data.success) {
            loadMigrationStatus();
            if (typeof loadPeriods === 'function') loadPeriods();
          }
        })
        .catch(function () { alert('خطأ في رفع أو استعادة الملف'); })
        .finally(function () {
          btnRestore.disabled = false;
          btnRestore.innerHTML = '<i class="fas fa-file-import ml-1"></i> استعادة نسخة احتياطية';
          backupInput.value = '';
        });
    });
  }

  // ─── Periods / multi-DB ───
  function loadPeriods() {
    var sel = document.getElementById('periodDbSelect');
    var label = document.getElementById('periodCurrentLabel');
    if (!sel) return;
    fetch('ajax/period_ops.php?action=list', { credentials: 'same-origin' })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        if (!data.success) {
          if (label) label.textContent = data.message || 'تعذر التحميل';
          return;
        }
        sel.innerHTML = '';
        (data.databases || []).forEach(function (db) {
          var opt = document.createElement('option');
          opt.value = db.name;
          var tag = db.closed_at ? ' [مقفلة]' : '';
          var miss = db.exists === false ? ' (غير موجودة)' : '';
          opt.textContent = (db.label || db.name) + ' — ' + db.name + tag + miss;
          if (db.is_current) opt.selected = true;
          sel.appendChild(opt);
        });
        if (label) {
          label.textContent = 'النشطة الآن: ' + (data.current || '—');
        }
      })
      .catch(function () {
        if (label) label.textContent = 'تعذر الاتصال';
      });
  }

  function postPeriod(action, fields, btn) {
    if (btn) btn.disabled = true;
    var fd = new FormData();
    fd.append('action', action);
    Object.keys(fields || {}).forEach(function (k) { fd.append(k, fields[k]); });
    return fetch('ajax/period_ops.php', { method: 'POST', body: fd, credentials: 'same-origin' })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        alert(data.message || (data.success ? 'تم' : 'فشل'));
        if (data.success && (action === 'switch' || action === 'close')) {
          location.reload();
          return data;
        }
        loadPeriods();
        return data;
      })
      .catch(function () { alert('خطأ في الاتصال بالخادم'); })
      .finally(function () { if (btn) btn.disabled = false; });
  }

  var btnSwitch = document.getElementById('btnSwitchPeriod');
  if (btnSwitch) {
    loadPeriods();
    btnSwitch.addEventListener('click', function () {
      var sel = document.getElementById('periodDbSelect');
      if (!sel || !sel.value) return;
      if (!confirm('التبديل إلى القاعدة: ' + sel.value + '؟')) return;
      postPeriod('switch', { db_name: sel.value }, btnSwitch);
    });
  }

  var btnClose = document.getElementById('btnClosePeriod');
  if (btnClose) {
    btnClose.addEventListener('click', function () {
      var name = (document.getElementById('closeDbName') || {}).value || '';
      var label = (document.getElementById('closeDbLabel') || {}).value || '';
      name = name.trim();
      if (!name) { alert('أدخل اسم القاعدة الجديدة'); return; }
      if (!confirm('تأكيد قفل المدة الحالية وإنشاء "' + name + '" بالأرصدة الافتتاحية؟\nلن تُنقل حركات الفترة القديمة.')) return;
      if (!confirm('تأكيد نهائي: العملية لا يمكن التراجع عنها بسهولة.')) return;
      postPeriod('close', { db_name: name, label: label }, btnClose);
    });
  }

  var btnCreateDb = document.getElementById('btnCreatePeriodDb');
  if (btnCreateDb) {
    btnCreateDb.addEventListener('click', function () {
      var name = (document.getElementById('newDbName') || {}).value || '';
      var label = (document.getElementById('newDbLabel') || {}).value || '';
      name = name.trim();
      if (!name) { alert('أدخل اسم القاعدة'); return; }
      if (!confirm('إنشاء قاعدة جديدة فارغة باسم ' + name + '؟')) return;
      postPeriod('create', { db_name: name, label: label }, btnCreateDb);
    });
  }
});
</script>

<?php include('includes/footer.php'); ?>
