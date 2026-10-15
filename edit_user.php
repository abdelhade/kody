<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
require_once __DIR__ . '/includes/sidebar.php';

// التحقق من المعرف
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: users.php');
    exit;
}

require_once __DIR__ . '/classes/Roles.php';

$canEditUsers = Can::any('users.edit', 'users.change_password');
$canEditPassword = Can::check('users.change_password');
$canEditRole = Can::check('users.edit') && $id !== Can::userId();

// جلب بيانات المستخدم
$stmt = $conn->prepare("SELECT u.*, r.name AS rollname FROM users u LEFT JOIN roles r ON u.userrole = r.id WHERE u.id = ? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res ? $res->fetch_assoc() : null;

$isFound = ($row !== null);
$uname = $isFound ? htmlspecialchars($row['uname'] ?? '', ENT_QUOTES, 'UTF-8') : '';
$rname = $isFound ? htmlspecialchars($row['rollname'] ?? 'بدون دور', ENT_QUOTES, 'UTF-8') : '';
$isWaiter = $isFound && ((int)($row['is_waiter'] ?? 0) === 1);
$avatarUrl = ($isFound && !empty($row['img'])) ? 'uploads/' . htmlspecialchars($row['img'], ENT_QUOTES, 'UTF-8') : 'dist/img/avatar.png';
?>

<div class="content-wrapper k-page-wrapper">
  <?php if (!$canEditUsers): ?>
    <div class="alert alert-danger text-center p-4 m-4">
      <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
      <h4>صلاحية غير كافية</h4>
      <p class="mb-0">ليس لديك الصلاحية المطلوبة لتعديل بيانات المستخدمين.</p>
      <a href="users.php" class="k-btn k-btn-secondary mt-3">
        <i class="fas fa-arrow-right ml-1"></i> العودة لقائمة المستخدمين
      </a>
    </div>
  <?php elseif (!$isFound): ?>
    <div class="k-card text-center p-5">
      <div class="k-empty-icon mb-3">
        <i class="fas fa-user-slash text-danger"></i>
      </div>
      <h3 class="k-empty-title">المستخدم غير موجود</h3>
      <p class="k-empty-desc">تعذر العثور على المستخدم المطلوب، ربما تم حذفه مسبقاً.</p>
      <a href="users.php" class="k-btn k-btn-primary">
        <i class="fas fa-arrow-right ml-1"></i> العودة لقائمة المستخدمين
      </a>
    </div>
  <?php else: ?>

    <?php
    $headerActions = [
        [
            'label' => 'العودة للمستخدمين',
            'url'   => 'users.php',
            'class' => 'k-btn-secondary',
            'icon'  => 'fa-arrow-right'
        ]
    ];
    if ($isWaiter) {
        $headerActions[] = [
            'label' => 'باركود الويتر',
            'url'   => 'waiter_barcode.php?id=' . $id,
            'class' => 'k-btn-success',
            'icon'  => 'fa-barcode'
        ];
    }
    ?>

    <?= kody_page_header(
        title: 'تعديل المستخدم: ' . $uname,
        icon: 'fa-user-edit',
        breadcrumbs: [
            'الرئيسية' => 'dashboard.php',
            'المستخدمين' => 'users.php',
            'تعديل المستخدم' => ''
        ],
        actions: $headerActions,
        badge: '#' . $id
    ) ?>

    <style>
      .edit-user-grid {
        display: grid;
        grid-template-columns: 340px 1fr;
        gap: 1.5rem;
        align-items: start;
      }
      @media (max-width: 991px) {
        .edit-user-grid {
          grid-template-columns: 1fr;
        }
      }

      /* Profile Card Styling */
      .profile-preview-card {
        text-align: center;
        background: #ffffff;
        border: 1px solid var(--k-border);
        border-radius: var(--k-radius-lg);
        overflow: hidden;
        box-shadow: var(--k-shadow-xs);
        transition: var(--k-transition);
      }
      .profile-card-banner {
        background: linear-gradient(135deg, #1e293b 0%, #3b82f6 100%);
        height: 90px;
        position: relative;
      }
      .profile-card-content {
        padding: 0 1.5rem 1.5rem;
        margin-top: -55px;
      }
      .avatar-wrap {
        position: relative;
        display: inline-block;
        margin-bottom: 0.75rem;
      }
      .avatar-img {
        width: 110px;
        height: 110px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #ffffff;
        box-shadow: 0 4px 12px rgba(0,0,0,0.12);
        background: #f8fafc;
        transition: transform 0.25s ease;
      }
      .avatar-img:hover {
        transform: scale(1.03);
      }
      .avatar-badge {
        position: absolute;
        bottom: 4px;
        left: 4px;
        background: var(--k-primary);
        color: white;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid #ffffff;
        font-size: 0.8rem;
      }
      .profile-name {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--k-text-main);
        margin-bottom: 0.25rem;
      }
      .profile-meta-badges {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        flex-wrap: wrap;
        margin-bottom: 1.25rem;
      }

      /* Upload Box */
      .upload-dropzone {
        border: 2px dashed #cbd5e1;
        border-radius: var(--k-radius-md);
        padding: 1.25rem 1rem;
        background: #f8fafc;
        cursor: pointer;
        transition: var(--k-transition);
        margin-bottom: 1rem;
        text-align: center;
      }
      .upload-dropzone:hover, .upload-dropzone.dragover {
        border-color: var(--k-primary);
        background: var(--k-primary-light);
      }
      .upload-dropzone-icon {
        font-size: 1.75rem;
        color: var(--k-primary);
        margin-bottom: 0.5rem;
      }
      .upload-dropzone-text {
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--k-text-main);
        margin-bottom: 0.25rem;
      }
      .upload-dropzone-sub {
        font-size: 0.75rem;
        color: var(--k-text-muted);
      }
      .file-info-badge {
        display: none;
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        color: #065f46;
        padding: 0.5rem 0.75rem;
        border-radius: var(--k-radius-sm);
        font-size: 0.8rem;
        margin-bottom: 0.75rem;
        align-items: center;
        justify-content: space-between;
      }

      /* Sections inside form */
      .form-section-header {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding-bottom: 0.65rem;
        margin-bottom: 1.15rem;
        border-bottom: 1px solid var(--k-border-subtle);
      }
      .form-section-header h4 {
        margin: 0;
        font-size: 1rem;
        font-weight: 700;
        color: var(--k-text-main);
      }
      .form-section-header i {
        font-size: 1.1rem;
      }

      /* Input adornments */
      .input-icon-wrap {
        position: relative;
      }
      .input-icon-wrap .input-icon {
        position: absolute;
        right: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--k-text-light);
        pointer-events: none;
        font-size: 0.95rem;
      }
      .input-icon-wrap .k-input,
      .input-icon-wrap .k-select {
        padding-right: 40px;
      }
      .password-toggle-btn {
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        background: transparent;
        border: none;
        color: var(--k-text-muted);
        cursor: pointer;
        padding: 4px 8px;
        border-radius: var(--k-radius-sm);
        transition: var(--k-transition);
      }
      .password-toggle-btn:hover {
        color: var(--k-primary);
        background: #f1f5f9;
      }

      /* Waiter Card Option */
      .waiter-option-box {
        background: #f8fafc;
        border: 1px solid var(--k-border);
        border-radius: var(--k-radius-md);
        padding: 1rem 1.25rem;
        transition: var(--k-transition);
      }
      .waiter-option-box.is-active {
        background: #f0fdf4;
        border-color: #86efac;
      }

      /* Quick Tips / Meta Info */
      .quick-meta-list {
        list-style: none;
        padding: 0;
        margin: 0;
        text-align: right;
        font-size: 0.825rem;
        color: var(--k-text-muted);
      }
      .quick-meta-list li {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.5rem 0;
        border-bottom: 1px dashed var(--k-border-subtle);
      }
      .quick-meta-list li:last-child {
        border-bottom: none;
      }
    </style>

    <form role="form" enctype="multipart/form-data" action="do/doedit_user.php?id=<?= $id ?>" method="post" autocomplete="off" id="editUserForm">
      <div class="edit-user-grid">

        <!-- العمود الجانبي: معاينة المستخدم ورفع الصورة -->
        <div class="profile-preview-card">
          <div class="profile-card-banner"></div>
          <div class="profile-card-content">
            <div class="avatar-wrap">
              <img src="<?= $avatarUrl ?>" alt="<?= $uname ?>" id="userAvatarPreview" class="avatar-img" onerror="this.onerror=null;this.src='dist/img/avatar.png';">
              <span class="avatar-badge" title="تعديل الصورة">
                <i class="fas fa-camera"></i>
              </span>
            </div>

            <h3 class="profile-name" id="previewUnameText"><?= $uname ?></h3>

            <div class="profile-meta-badges">
              <span class="k-badge k-badge-primary" id="previewRoleBadge">
                <i class="fas fa-shield-alt ml-1"></i> <?= $rname ?>
              </span>
              <span class="k-badge <?= $isWaiter ? 'k-badge-success' : 'k-badge-secondary' ?>" id="previewWaiterBadge">
                <i class="fas <?= $isWaiter ? 'fa-user-tie' : 'fa-desktop' ?> ml-1"></i>
                <?= $isWaiter ? 'ويتر (Waiters)' : 'مستخدم نظام' ?>
              </span>
            </div>

            <!-- رفع وتغيير الصورة -->
            <div class="upload-dropzone" id="uploadDropzone" onclick="document.getElementById('img').click()">
              <input type="file" name="img" id="img" accept="image/*" style="display: none;">
              <div class="upload-dropzone-icon">
                <i class="fas fa-cloud-upload-alt"></i>
              </div>
              <div class="upload-dropzone-text"><?= $lang_image_upload ?? 'اختيار صورة جديدة' ?></div>
              <div class="upload-dropzone-sub">PNG, JPG, WEBP (حجم أقصى 50MB)</div>
            </div>

            <!-- بيانات الملف المختار -->
            <div class="file-info-badge" id="fileInfoBadge">
              <span class="text-truncate mr-1" style="max-width: 180px;" id="selectedFileName"></span>
              <button type="button" class="btn btn-sm btn-link text-danger p-0 ml-1" id="btnCancelFile" title="إلغاء الصورة المختارة">
                <i class="fas fa-times-circle"></i>
              </button>
            </div>

            <!-- معلومات إضافية -->
            <div class="mt-3 pt-3 border-top text-right">
              <ul class="quick-meta-list">
                <li>
                  <span>معرف المستخدم (ID):</span>
                  <span class="badge badge-light border">#<?= $id ?></span>
                </li>
                <li>
                  <span>حالة الحساب:</span>
                  <span class="text-success font-weight-bold"><i class="fas fa-circle fa-xs mr-1"></i> نشط</span>
                </li>
                <?php if ($id === 1): ?>
                <li>
                  <span>نوع الصلاحية:</span>
                  <span class="badge badge-warning text-dark font-weight-bold">المسؤول الرئيسي</span>
                </li>
                <?php endif; ?>
              </ul>
            </div>

            <?php if ($isWaiter): ?>
            <div class="mt-3 pt-2">
              <a href="waiter_barcode.php?id=<?= $id ?>" target="_blank" class="k-btn k-btn-outline w-100 btn-sm">
                <i class="fas fa-barcode text-success ml-1"></i> طباعة باركود الويتر
              </a>
            </div>
            <?php endif; ?>

          </div>
        </div>

        <!-- العمود الرئيسي: بيانات الحساب، الصلاحيات، وكلمة المرور -->
        <div>
          <div class="k-card">
            <div class="k-card-header">
              <h3 class="k-card-title">
                <i class="fas fa-user-cog text-primary ml-1"></i> تفاصيل الحساب والصلاحيات
              </h3>
            </div>
            <div class="k-card-body">

              <!-- القسم الأول: البيانات الأساسية -->
              <div class="form-section-header">
                <i class="fas fa-id-card text-primary"></i>
                <h4>المعلومات الأساسية</h4>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="k-form-group">
                    <label class="k-label required" for="uname"><?= $lang_username ?? 'اسم المستخدم' ?></label>
                    <div class="input-icon-wrap">
                      <i class="fas fa-user input-icon"></i>
                      <input type="text" name="uname" id="uname" class="k-input" value="<?= $uname ?>" required placeholder="اكتب اسم المستخدم">
                    </div>
                    <small class="text-muted mt-1 d-block">اسم المستخدم المستخدم لتسجيل الدخول في النظام.</small>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="k-form-group">
                    <label class="k-label required" for="userrole">دور المستخدم والصلاحية</label>
                    <div class="input-icon-wrap">
                      <i class="fas fa-shield-alt input-icon"></i>
                      <select name="userrole" id="userrole" class="k-select" required <?= $canEditRole ? '' : 'disabled' ?>>
                        <?php foreach (Roles::options($conn) as $opt):
                            $isCurrent = $opt['id'] === (int) $row['userrole'];
                            if (!$isCurrent && Roles::assignError($conn, $opt['id']) !== null) {
                                continue;
                            } ?>
                          <option value="<?= $opt['id'] ?>" <?= $isCurrent ? 'selected' : '' ?>><?= htmlspecialchars($opt['name'], ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <?php if (!$canEditRole): ?>
                      <small class="text-muted mt-1 d-block"><i class="fas fa-lock ml-1"></i> مش مسموح تغيّر الدور هنا<?= $id === Can::userId() ? ' (ده حسابك)' : '' ?>.</small>
                    <?php else: ?>
                      <small class="text-muted mt-1 d-block">يحدد هذا الخيار شاشات النظام والصلاحيات المسموحة لهذا المستخدم.</small>
                    <?php endif; ?>
                  </div>
                </div>
              </div>

              <!-- القسم الثاني: نوع الحساب (الويترز) -->
              <div class="form-section-header mt-4">
                <i class="fas fa-user-tag text-info"></i>
                <h4>إعدادات نوع الحساب</h4>
              </div>

              <div class="waiter-option-box mb-4 <?= $isWaiter ? 'is-active' : '' ?>" id="waiterOptionBox">
                <div class="d-flex align-items-center justify-content-between">
                  <div class="d-flex align-items-center">
                    <label class="k-switch mb-0 ml-3">
                      <input type="checkbox" name="is_waiter" id="is_waiter" value="1" <?= $isWaiter ? 'checked' : '' ?>>
                      <span class="k-switch-slider"></span>
                    </label>
                    <div>
                      <label for="is_waiter" class="mb-0 font-weight-bold cursor-pointer" style="font-size: 1rem; color: var(--k-text-main);">
                        <i class="fas fa-user-tie text-success ml-1"></i> تفعيل حساب ويتر (صالة / مطعم)
                      </label>
                      <div class="text-muted small mt-1">
                        يمكن للويترز الدخول السريع عبر شاشة نقاط البيع (POS) باستخدام مسح الباركود أو الرقم السري الخاص بهم.
                      </div>
                    </div>
                  </div>

                  <span class="k-badge <?= $isWaiter ? 'k-badge-success' : 'k-badge-secondary' ?>" id="waiterStatusTag">
                    <?= $isWaiter ? 'مفعّل' : 'معطّل' ?>
                  </span>
                </div>
              </div>

              <!-- القسم الثالث: الأمان وكلمة المرور -->
              <div class="form-section-header mt-4">
                <i class="fas fa-key text-warning"></i>
                <h4>الأمان وكلمة المرور</h4>
              </div>

              <?php if ($canEditPassword): ?>
                <div class="alert alert-light border mb-3 p-3 text-muted" style="border-radius: var(--k-radius-md); font-size: 0.875rem;">
                  <i class="fas fa-info-circle text-info ml-1"></i>
                  اترك حقول كلمة المرور فارغة إذا كنت لا ترغب بتغييرها. بالنسبة للويترز: يمكنك كتابة رقم الباركود أو الرقم السري هنا.
                </div>

                <div class="row">
                  <div class="col-md-6">
                    <div class="k-form-group">
                      <label class="k-label" for="password">كلمة المرور الجديدة / الباركود</label>
                      <div class="input-icon-wrap">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" name="password" id="password" class="k-input" placeholder="اترك فارغاً للإبقاء على الحالية">
                        <button type="button" class="password-toggle-btn" data-target="password" title="إظهار / إخفاء">
                          <i class="fas fa-eye"></i>
                        </button>
                      </div>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="k-form-group">
                      <label class="k-label" for="confirm_password">تأكيد كلمة المرور</label>
                      <div class="input-icon-wrap">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" name="confirm_password" id="confirm_password" class="k-input" placeholder="أعد كتابة كلمة المرور للتحقق">
                        <button type="button" class="password-toggle-btn" data-target="confirm_password" title="إظهار / إخفاء">
                          <i class="fas fa-eye"></i>
                        </button>
                      </div>
                      <div id="passwordMatchStatus" class="mt-1 small" style="display: none;"></div>
                    </div>
                  </div>
                </div>
              <?php else: ?>
                <div class="alert alert-secondary p-3 mb-3" style="border-radius: var(--k-radius-md);">
                  <i class="fas fa-lock ml-1"></i> ليس لديك صلاحية تعديل كلمة مرور المستخدمين.
                </div>
              <?php endif; ?>

              <!-- أزرار الحفظ والإلغاء -->
              <div class="mt-4 pt-3 border-top d-flex align-items-center justify-content-between flex-wrap gap-2">
                <button type="submit" class="k-btn k-btn-primary k-btn-lg" id="btnSubmitForm">
                  <i class="fas fa-save ml-1"></i> حفظ التغييرات
                </button>

                <a href="users.php" class="k-btn k-btn-secondary">
                  <i class="fas fa-times ml-1"></i> إلغاء والعودة
                </a>
              </div>

            </div>
          </div>
        </div>

      </div>
    </form>

    <script>
      document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('editUserForm');
        const fileInput = document.getElementById('img');
        const avatarPreview = document.getElementById('userAvatarPreview');
        const fileInfoBadge = document.getElementById('fileInfoBadge');
        const selectedFileName = document.getElementById('selectedFileName');
        const btnCancelFile = document.getElementById('btnCancelFile');
        const originalAvatarSrc = avatarPreview.src;

        const unameInput = document.getElementById('uname');
        const previewUnameText = document.getElementById('previewUnameText');

        const userroleSelect = document.getElementById('userrole');
        const previewRoleBadge = document.getElementById('previewRoleBadge');

        const waiterCheckbox = document.getElementById('is_waiter');
        const waiterOptionBox = document.getElementById('waiterOptionBox');
        const waiterStatusTag = document.getElementById('waiterStatusTag');
        const previewWaiterBadge = document.getElementById('previewWaiterBadge');

        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('confirm_password');
        const passwordMatchStatus = document.getElementById('passwordMatchStatus');

        // تحديث اسم المستخدم على المعاينة
        if (unameInput && previewUnameText) {
          unameInput.addEventListener('input', function() {
            previewUnameText.textContent = this.value.trim() || 'اسم المستخدم';
          });
        }

        // تحديث دور المستخدم على المعاينة
        if (userroleSelect && previewRoleBadge) {
          userroleSelect.addEventListener('change', function() {
            const selectedText = this.options[this.selectedIndex].text;
            previewRoleBadge.innerHTML = '<i class="fas fa-shield-alt ml-1"></i> ' + selectedText;
          });
        }

        // تبديل حالة الويتر
        if (waiterCheckbox) {
          waiterCheckbox.addEventListener('change', function() {
            const checked = this.checked;
            if (checked) {
              waiterOptionBox.classList.add('is-active');
              waiterStatusTag.className = 'k-badge k-badge-success';
              waiterStatusTag.textContent = 'مفعّل';
              previewWaiterBadge.className = 'k-badge k-badge-success';
              previewWaiterBadge.innerHTML = '<i class="fas fa-user-tie ml-1"></i> ويتر (Waiters)';
            } else {
              waiterOptionBox.classList.remove('is-active');
              waiterStatusTag.className = 'k-badge k-badge-secondary';
              waiterStatusTag.textContent = 'معطّل';
              previewWaiterBadge.className = 'k-badge k-badge-secondary';
              previewWaiterBadge.innerHTML = '<i class="fas fa-desktop ml-1"></i> مستخدم نظام';
            }
          });
        }

        // معاينة الصورة المختارة فورياً
        if (fileInput) {
          fileInput.addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
              const file = this.files[0];
              const reader = new FileReader();
              reader.onload = function(evt) {
                avatarPreview.src = evt.target.result;
              };
              reader.readAsDataURL(file);

              selectedFileName.textContent = file.name;
              fileInfoBadge.style.display = 'inline-flex';
            }
          });
        }

        // إلغاء الصورة المختارة
        if (btnCancelFile) {
          btnCancelFile.addEventListener('click', function(e) {
            e.stopPropagation();
            if (fileInput) fileInput.value = '';
            avatarPreview.src = originalAvatarSrc;
            fileInfoBadge.style.display = 'none';
          });
        }

        // إظهار / إخفاء كلمة المرور
        document.querySelectorAll('.password-toggle-btn').forEach(btn => {
          btn.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const targetInput = document.getElementById(targetId);
            if (targetInput) {
              const isPassword = targetInput.type === 'password';
              targetInput.type = isPassword ? 'text' : 'password';
              const icon = this.querySelector('i');
              if (icon) {
                icon.className = isPassword ? 'fas fa-eye-slash' : 'fas fa-eye';
              }
            }
          });
        });

        // التحقق من تطابق كلمات المرور
        function checkPasswordMatch() {
          if (!passwordInput || !confirmPasswordInput) return true;

          const p1 = passwordInput.value;
          const p2 = confirmPasswordInput.value;

          if (p1 === '' && p2 === '') {
            passwordMatchStatus.style.display = 'none';
            confirmPasswordInput.style.borderColor = '';
            return true;
          }

          passwordMatchStatus.style.display = 'block';
          if (p1 === p2) {
            passwordMatchStatus.className = 'mt-1 small text-success';
            passwordMatchStatus.innerHTML = '<i class="fas fa-check-circle ml-1"></i> كلمات المرور متطابقة';
            confirmPasswordInput.style.borderColor = 'var(--k-success)';
            return true;
          } else {
            passwordMatchStatus.className = 'mt-1 small text-danger';
            passwordMatchStatus.innerHTML = '<i class="fas fa-times-circle ml-1"></i> كلمات المرور غير متطابقة';
            confirmPasswordInput.style.borderColor = 'var(--k-danger)';
            return false;
          }
        }

        if (passwordInput && confirmPasswordInput) {
          passwordInput.addEventListener('input', checkPasswordMatch);
          confirmPasswordInput.addEventListener('input', checkPasswordMatch);
        }

        // فحص النموذج عند الإرسال
        if (form) {
          form.addEventListener('submit', function(e) {
            if (!checkPasswordMatch()) {
              e.preventDefault();
              confirmPasswordInput.focus();
              if (typeof Swal !== 'undefined') {
                Swal.fire({
                  icon: 'warning',
                  title: 'تنبيه',
                  text: 'كلمات المرور المدخلة غير متطابقة!',
                  confirmButtonText: 'حسناً',
                  confirmButtonColor: '#3b82f6'
                });
              } else {
                alert('كلمات المرور المدخلة غير متطابقة!');
              }
            }
          });
        }
      });
    </script>

  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>