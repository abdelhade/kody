<?php
/**
 * seed_roles.php - أداة زراعة الأدوار الافتراضية المستقلة (CLI & Web)
 */

if (php_sapi_name() === 'cli') {
    require_once __DIR__ . '/includes/connect.php';
    require_once __DIR__ . '/includes/RolesSeeder.php';

    echo "========================================================\n";
    echo "  Kody ERP - Roles Seeder (زراعة الأدوار الافتراضية)     \n";
    echo "========================================================\n\n";

    $overwrite = in_array('--overwrite', $argv) || in_array('-f', $argv);
    echo "Overwrite existing roles: " . ($overwrite ? "YES" : "NO (skip existing)") . "\n\n";

    $presets = RolesSeeder::getPresets();
    echo "Available Presets (" . count($presets) . "):\n";
    foreach ($presets as $p) {
        echo " - {$p['rollname']} ({$p['key']}) [{$p['category']}]\n";
    }
    echo "\nRunning seeder...\n";

    $res = RolesSeeder::seedMany($conn, [], $overwrite);

    if (!empty($res['created'])) {
        echo "\n[CREATED] Successfully created:\n";
        foreach ($res['created'] as $name) {
            echo "  + {$name}\n";
        }
    }

    if (!empty($res['updated'])) {
        echo "\n[UPDATED] Successfully updated:\n";
        foreach ($res['updated'] as $name) {
            echo "  * {$name}\n";
        }
    }

    if (!empty($res['skipped'])) {
        echo "\n[SKIPPED] Already existing (skipped):\n";
        foreach ($res['skipped'] as $name) {
            echo "  - {$name}\n";
        }
    }

    if (!empty($res['errors'])) {
        echo "\n[ERRORS]:\n";
        foreach ($res['errors'] as $err) {
            echo "  ! {$err}\n";
        }
    }

    echo "\nDone!\n";
    exit(0);
}

// Web Mode
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
require_once __DIR__ . '/includes/sidebar.php';
require_once __DIR__ . '/includes/RolesSeeder.php';

$canShowUsers = isset($role) && is_array($role) && ((int)($role['show_users'] ?? 0) === 1);
$presets = RolesSeeder::getPresets();
$existing = RolesSeeder::getExistingRoles($conn);
?>

<div class="content-wrapper k-page-wrapper">
  <?php if (!$canShowUsers): ?>
    <div class="alert alert-danger text-center p-4 m-4">
      <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
      <h4>صلاحية غير كافية</h4>
      <p class="mb-0">ليس لديك صلاحية لإدارة الأدوار والصلاحيات في النظام.</p>
    </div>
  <?php else: ?>
    <?= kody_page_header(
        title: 'أداة زراعة الأدوار الافتراضية (Roles Seeder)',
        icon: 'fa-seedling',
        breadcrumbs: [
            'الرئيسية' => 'dashboard.php',
            'الأدوار' => 'myroles.php',
            'زراعة الأدوار' => ''
        ],
        actions: [
            [
                'label' => 'العودة للأدوار',
                'url'   => 'myroles.php',
                'class' => 'k-btn-secondary',
                'icon'  => 'fa-arrow-right'
            ]
        ],
        badge: count($presets) . ' دور جاهز'
    ) ?>

    <div class="container-fluid px-0">
      <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
        <div class="card-body p-4">
          <div class="d-flex align-items-center mb-3">
            <div class="p-3 rounded-circle mr-3" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
              <i class="fas fa-magic fa-2x"></i>
            </div>
            <div>
              <h5 class="font-weight-bold mb-1">توليد الأدوار الوظيفية القياسية للنظام</h5>
              <p class="text-muted mb-0 small">
                تمكنك هذه الأداة من إنشاء وتثبيت أدوار مهنية متكاملة (محاسب، مدير مالي، موارد بشرية HR، كاشير، أمين مستودع... إلخ) بصلاحيات مدروسة ومضبوطة مسبقاً.
              </p>
            </div>
          </div>

          <form id="standaloneSeedForm">
            <div class="row align-items-center bg-light p-3 rounded mb-4">
              <div class="col-md-6 mb-2 mb-md-0">
                <div class="custom-control custom-switch">
                  <input type="checkbox" class="custom-control-input" id="saOverwrite" name="overwrite" value="1">
                  <label class="custom-control-label font-weight-bold text-dark" for="saOverwrite">
                    تحديث وإعادة ضبط الصلاحيات إذا كان الدور موجوداً مسبقاً
                  </label>
                </div>
                <small class="text-muted d-block mt-1">عند تفعيل هذا الخيار سيتم تحديث صلاحيات الدور القائم لتطابق التوزيع القياسي.</small>
              </div>
              <div class="col-md-6 text-md-left">
                <button type="button" class="btn btn-outline-secondary btn-sm mr-2" id="btnSelectAll">
                  <i class="fas fa-check-double ml-1"></i> تحديد الكل
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm mr-2" id="btnDeselectAll">
                  <i class="fas fa-times ml-1"></i> إلغاء التحديد
                </button>
                <button type="submit" class="btn btn-success btn-sm px-3" id="btnSubmitSeed">
                  <i class="fas fa-bolt ml-1"></i> بدء التوليد الآن
                </button>
              </div>
            </div>

            <div class="row">
              <?php foreach ($presets as $key => $p): 
                $isExists = isset($existing[$p['rollname']]);
              ?>
                <div class="col-lg-4 col-md-6 mb-4">
                  <div class="card h-100 border transition-all position-relative" style="border-radius: 10px; border-color: <?= $p['color'] ?>30 !important; box-shadow: 0 2px 6px rgba(0,0,0,0.03);">
                    <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center pb-0 pt-3">
                      <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input role-checkbox" id="role_<?= $key ?>" name="roles[]" value="<?= $key ?>" <?= !$isExists ? 'checked' : '' ?>>
                        <label class="custom-control-label font-weight-bold text-dark" for="role_<?= $key ?>" style="cursor: pointer; font-size: 1.05rem;">
                          <?= htmlspecialchars($p['rollname'], ENT_QUOTES, 'UTF-8') ?>
                        </label>
                      </div>
                      <span class="badge" style="background-color: <?= $p['color'] ?>15; color: <?= $p['color'] ?>; font-weight: 600; padding: 5px 10px; border-radius: 20px;">
                        <i class="fas <?= $p['icon'] ?> ml-1"></i> <?= htmlspecialchars($p['category'], ENT_QUOTES, 'UTF-8') ?>
                      </span>
                    </div>
                    <div class="card-body pt-2 pb-3">
                      <p class="text-muted small mb-3" style="line-height: 1.6; min-height: 48px;">
                        <?= htmlspecialchars($p['info'], ENT_QUOTES, 'UTF-8') ?>
                      </p>
                      <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                        <span class="small font-weight-bold">الحالة:</span>
                        <?php if ($isExists): ?>
                          <span class="badge badge-warning text-dark"><i class="fas fa-check-circle ml-1"></i> موجود مسبقاً (ID: <?= $existing[$p['rollname']] ?>)</span>
                        <?php else: ?>
                          <span class="badge badge-success"><i class="fas fa-plus-circle ml-1"></i> جديد (جاهز للإنشاء)</span>
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </form>

          <div id="seedResultAlert" class="mt-4" style="display: none;"></div>
        </div>
      </div>
    </div>
  <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('standaloneSeedForm');
    if (!form) return;

    var btnSelectAll = document.getElementById('btnSelectAll');
    var btnDeselectAll = document.getElementById('btnDeselectAll');
    var btnSubmit = document.getElementById('btnSubmitSeed');
    var resultAlert = document.getElementById('seedResultAlert');

    btnSelectAll.addEventListener('click', function() {
        document.querySelectorAll('.role-checkbox').forEach(function(cb) { cb.checked = true; });
    });

    btnDeselectAll.addEventListener('click', function() {
        document.querySelectorAll('.role-checkbox').forEach(function(cb) { cb.checked = false; });
    });

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        var checkedBoxes = document.querySelectorAll('.role-checkbox:checked');
        if (checkedBoxes.length === 0) {
            if (window.Swal) {
                Swal.fire({ icon: 'warning', title: 'تنبيه', text: 'يرجى اختيار دور واحد على الأقل للتوليد.' });
            } else {
                alert('يرجى اختيار دور واحد على الأقل للتوليد.');
            }
            return;
        }

        var formData = new FormData(form);
        formData.append('action', 'seed');

        btnSubmit.disabled = true;
        btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin ml-1"></i> جاري التوليد...';

        fetch('ajax/seed_roles.php', {
            method: 'POST',
            body: formData
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = '<i class="fas fa-bolt ml-1"></i> بدء التوليد الآن';

            if (data.success) {
                var html = '<div class="alert alert-success p-4 rounded shadow-sm">';
                html += '<h5 class="font-weight-bold mb-2"><i class="fas fa-check-circle ml-2"></i> اكتملت عملية التوليد بنجاح!</h5>';
                html += '<p class="mb-3">' + data.message + '</p>';
                html += '<div class="d-flex"><a href="myroles.php" class="btn btn-success btn-sm"><i class="fas fa-list ml-1"></i> الانتقال لقائمة الأدوار والصلاحيات</a></div>';
                html += '</div>';

                resultAlert.innerHTML = html;
                resultAlert.style.display = 'block';

                if (window.Swal) {
                    Swal.fire({
                        icon: 'success',
                        title: 'تم التوليد بنجاح',
                        text: data.message,
                        confirmButtonText: 'عرض الأدوار'
                    }).then(function(result) {
                        if (result.isConfirmed) {
                            window.location.href = 'myroles.php';
                        }
                    });
                }
            } else {
                resultAlert.innerHTML = '<div class="alert alert-danger p-3"><i class="fas fa-times-circle ml-2"></i> ' + (data.message || 'حدث خطأ أثناء التوليد') + '</div>';
                resultAlert.style.display = 'block';
            }
        })
        .catch(function(err) {
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = '<i class="fas fa-bolt ml-1"></i> بدء التوليد الآن';
            resultAlert.innerHTML = '<div class="alert alert-danger p-3"><i class="fas fa-times-circle ml-2"></i> فشل الاتصال بالخادم: ' + err + '</div>';
            resultAlert.style.display = 'block';
        });
    });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
