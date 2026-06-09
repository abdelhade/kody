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

      <form action="do/doedit_settings.php" method="post" id="settings-main-form">

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
                  <a class="nav-link py-3 px-4 d-flex align-items-center" id="sidebar-tab" data-toggle="pill" href="#tab-sidebar" role="tab" aria-controls="tab-sidebar" aria-selected="false" style="border-radius: 8px; font-weight: 600; transition: all 0.2s ease;">
                    <i class="fas fa-eye ml-3" style="font-size: 1.1rem; width: 20px;"></i> الشريط الجانبي
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

<?php include('includes/footer.php'); ?>
