<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
require_once __DIR__ . '/includes/sidebar.php';

require_once __DIR__ . '/includes/RolesSeeder.php';

$isRootAdmin = ((int)($_SESSION['userid'] ?? 0) === 1) || ((int)($userid ?? 0) === 1);
$canShowUsers = $isRootAdmin || (isset($role) && is_array($role) && ((int)($role['show_users'] ?? 0) === 1));

// Fetch presets and existing roles
$presets = RolesSeeder::getPresets();
$existingRoles = RolesSeeder::getExistingRoles($conn);

// Fetch roles with user counts
$sql = "SELECT r.id, r.rollname, r.info,
            (SELECT COUNT(*) FROM users u WHERE u.userrole = r.id AND COALESCE(u.isdeleted, 0) != 1) AS users_count
        FROM usr_pwrs r 
        ORDER BY r.id ASC";
$res = $conn->query($sql);
$rolesCount = $res ? $res->num_rows : 0;
?>

<div class="content-wrapper k-page-wrapper">
  <?php if (!$canShowUsers): ?>
    <div class="alert alert-danger text-center p-4 m-4">
      <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
      <h4>صلاحية غير كافية</h4>
      <p class="mb-0"><?= $userErrorMassage ?? 'ليس لديك صلاحية لإدارة الأدوار والصلاحيات.' ?></p>
    </div>
  <?php else: ?>
    <?= kody_page_header(
        title: 'أدوار وصلاحيات المستخدمين',
        icon: 'fa-user-shield',
        breadcrumbs: [
            'الرئيسية' => 'dashboard.php',
            'المستخدمين' => 'users.php',
            'الأدوار' => ''
        ],
        actions: [
            [
                'label' => 'قائمة المستخدمين',
                'url'   => 'users.php',
                'class' => 'k-btn-secondary',
                'icon'  => 'fa-users'
            ],
            [
                'label' => 'توليد أدوار جاهزة (Seeder)',
                'url'   => '#rolesSeederModal',
                'class' => 'k-btn-success',
                'icon'  => 'fa-seedling',
                'attr'  => 'data-toggle="modal" data-target="#rolesSeederModal"'
            ],
            [
                'label' => 'إضافة دور جديد',
                'url'   => 'add_role.php',
                'class' => 'k-btn-primary',
                'icon'  => 'fa-plus'
            ]
        ],
        badge: $rolesCount . ' دور'
    ) ?>

    <div class="k-card">
      <div class="k-toolbar">
        <div class="k-toolbar-start">
          <div class="k-search-input-wrap">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="بحث في أسماء الأدوار..." data-kody-table-search="#rolesTable">
          </div>
        </div>
        <div class="k-toolbar-end">
          <span class="text-muted small">إجمالي الأدوار: <strong><?= $rolesCount ?></strong></span>
        </div>
      </div>

      <?php if ($rolesCount === 0): ?>
        <?= kody_empty_state(
            title: 'لا توجد أدوار مسجلة حالياً',
            desc: 'قم بإضافة الأدوار لتعيين الصلاحيات المختلفة لموظفي ومستخدمي النظام، أو قم بتوليد الأدوار القياسية الجاهزة بنقرة واحدة.',
            icon: 'fa-user-shield',
            cta: [
                'label' => 'توليد أدوار جاهزة (Seeder)',
                'url' => '#rolesSeederModal',
                'icon' => 'fa-seedling',
                'attr' => 'data-toggle="modal" data-target="#rolesSeederModal"'
            ]
        ) ?>
      <?php else: ?>
        <div class="k-table-wrap">
          <table class="k-table" id="rolesTable">
            <thead>
              <tr>
                <th style="width: 50px;">#</th>
                <th>اسم الدور</th>
                <th>الوصف والملاحظات</th>
                <th style="width: 140px;" class="text-center">المستخدمين المرتبطين</th>
                <th style="width: 120px;" class="text-center">العمليات</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $x = 0;
              while ($r = $res->fetch_assoc()):
                  $x++;
                  $rid = (int)$r['id'];
                  $rname = htmlspecialchars($r['rollname'] ?? '', ENT_QUOTES, 'UTF-8');
                  $rinfo = htmlspecialchars($r['info'] ?? '', ENT_QUOTES, 'UTF-8');
                  $ucnt = (int)($r['users_count'] ?? 0);
                  $editUrl = 'edit_role.php?id=' . md5((string)$rid) . '&no=' . $rid . '&name=' . urlencode($r['rollname'] ?? '');
              ?>
                <tr>
                  <td><span class="text-muted small font-weight-bold"><?= $x ?></span></td>
                  <td>
                    <strong class="text-dark"><?= $rname ?></strong>
                    <?php if ($rid === 1): ?>
                      <span class="badge badge-light border text-muted mr-1 small">دور أساسي</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?= $rinfo !== '' ? $rinfo : '<span class="text-muted small">—</span>' ?>
                  </td>
                  <td class="text-center">
                    <span class="k-badge <?= $ucnt > 0 ? 'k-badge-info' : 'k-badge-secondary' ?>">
                      <i class="fas fa-users ml-1"></i> <?= $ucnt ?> مستخدم
                    </span>
                  </td>
                  <td class="text-center">
                    <div class="k-actions">
                      <?= kody_action_btn('edit', $editUrl, ['title' => 'تعديل الصلاحيات']) ?>
                      <?php if ($rid !== 1 && $ucnt === 0): ?>
                        <?= kody_action_btn('delete', 'do/dodel_role.php?id=' . $rid, [
                            'title' => 'حذف الدور',
                            'item_name' => $rname
                        ]) ?>
                      <?php elseif ($rid !== 1): ?>
                        <span class="k-btn-action text-muted" style="cursor: not-allowed; opacity: 0.5;" title="لا يمكن حذف دور مرتبط بمستخدمين">
                          <i class="fas fa-lock"></i>
                        </span>
                      <?php endif; ?>
                    </div>
                  </td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  <?php endif; ?>
</div>

<!-- Modal توليد الأدوار الجاهزة (Roles Seeder Modal) -->
<div class="modal fade" id="rolesSeederModal" tabindex="-1" role="dialog" aria-labelledby="rolesSeederModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content shadow-lg border-0" style="border-radius: 12px; overflow: hidden;">
      <div class="modal-header bg-white border-bottom px-4 py-3 d-flex justify-content-between align-items-center">
        <h5 class="modal-title font-weight-bold text-dark d-flex align-items-center mb-0" id="rolesSeederModalLabel">
          <span class="d-inline-flex align-items-center justify-content-center rounded-circle mr-2" style="width: 38px; height: 38px; background: rgba(16, 185, 129, 0.12); color: #10b981;">
            <i class="fas fa-seedling"></i>
          </span>
          توليد وزراعة الأدوار القياسية الجاهزة
        </h5>
        <button type="button" class="close text-muted m-0 p-0" data-dismiss="modal" aria-label="Close" style="font-size: 1.5rem; outline: none;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form id="rolesSeederModalForm">
        <div class="modal-body p-4" style="max-height: 70vh; overflow-y: auto;">
          <div class="alert alert-light border p-3 rounded mb-3 d-flex align-items-center">
            <i class="fas fa-info-circle fa-2x text-primary mr-3"></i>
            <div class="small">
              قم باختيار الأدوار المراد زراعتها في النظام. كل دور مجهز تلقائياً بكافة الصلاحيات الموصى بها للوظيفة (محاسب، مدير مالي، موارد بشرية، كاشير، أمين مستودع... إلخ).
            </div>
          </div>

          <div class="d-flex flex-wrap justify-content-between align-items-center bg-light p-3 rounded mb-3">
            <div class="custom-control custom-switch mb-2 mb-md-0">
              <input type="checkbox" class="custom-control-input" id="modalOverwrite" name="overwrite" value="1">
              <label class="custom-control-label font-weight-bold text-dark small" for="modalOverwrite">
                تحديث صلاحيات الدور إذا كان موجوداً مسبقاً (Overwrite)
              </label>
            </div>
            <div>
              <button type="button" class="btn btn-outline-secondary btn-sm py-1 px-2" id="modalSelectAll">
                <i class="fas fa-check-double ml-1"></i> تحديد الكل
              </button>
              <button type="button" class="btn btn-outline-secondary btn-sm py-1 px-2 mr-1" id="modalDeselectAll">
                <i class="fas fa-times ml-1"></i> إلغاء التحديد
              </button>
            </div>
          </div>

          <div class="row">
            <?php foreach ($presets as $k => $preset): 
              $isEx = isset($existingRoles[$preset['rollname']]);
            ?>
              <div class="col-md-6 mb-3">
                <div class="card h-100 border p-3" style="border-radius: 8px; border-color: <?= $preset['color'] ?>25 !important; background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
                  <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="custom-control custom-checkbox">
                      <input type="checkbox" class="custom-control-input modal-role-cb" id="mrole_<?= $k ?>" name="roles[]" value="<?= $k ?>" <?= !$isEx ? 'checked' : '' ?>>
                      <label class="custom-control-label font-weight-bold text-dark" for="mrole_<?= $k ?>" style="cursor: pointer;">
                        <?= htmlspecialchars($preset['rollname'], ENT_QUOTES, 'UTF-8') ?>
                      </label>
                    </div>
                    <span class="badge" style="background-color: <?= $preset['color'] ?>15; color: <?= $preset['color'] ?>; font-size: 0.75rem;">
                      <i class="fas <?= $preset['icon'] ?> ml-1"></i> <?= htmlspecialchars($preset['category'], ENT_QUOTES, 'UTF-8') ?>
                    </span>
                  </div>
                  <p class="text-muted small mb-2" style="line-height: 1.5; font-size: 0.8rem;">
                    <?= htmlspecialchars($preset['info'], ENT_QUOTES, 'UTF-8') ?>
                  </p>
                  <div class="mt-auto pt-2 border-top d-flex justify-content-between align-items-center">
                    <span class="text-muted" style="font-size: 0.75rem;">الحالة:</span>
                    <?php if ($isEx): ?>
                      <span class="badge badge-warning text-dark small"><i class="fas fa-check-circle ml-1"></i> موجود مسبقاً</span>
                    <?php else: ?>
                      <span class="badge badge-success small"><i class="fas fa-plus-circle ml-1"></i> جاهز للإضافة</span>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>

          <div id="modalAlertBox" class="mt-2" style="display: none;"></div>
        </div>

        <div class="modal-footer bg-light px-4 py-3 d-flex justify-content-between">
          <a href="seed_roles.php" class="btn btn-outline-info btn-sm">
            <i class="fas fa-external-link-alt ml-1"></i> فتح في صفحة مستقلة
          </a>
          <div>
            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">إلغاء</button>
            <button type="submit" class="btn btn-success btn-sm px-3" id="modalSubmitBtn">
              <i class="fas fa-bolt ml-1"></i> توليد الأدوار المختارة
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var modalForm = document.getElementById('rolesSeederModalForm');
    if (!modalForm) return;

    var btnSelAll = document.getElementById('modalSelectAll');
    var btnDeselAll = document.getElementById('modalDeselectAll');
    var btnSubmit = document.getElementById('modalSubmitBtn');
    var alertBox = document.getElementById('modalAlertBox');

    btnSelAll.addEventListener('click', function() {
        document.querySelectorAll('.modal-role-cb').forEach(function(cb) { cb.checked = true; });
    });

    btnDeselAll.addEventListener('click', function() {
        document.querySelectorAll('.modal-role-cb').forEach(function(cb) { cb.checked = false; });
    });

    modalForm.addEventListener('submit', function(e) {
        e.preventDefault();

        var selected = document.querySelectorAll('.modal-role-cb:checked');
        if (selected.length === 0) {
            if (window.Swal) {
                Swal.fire({ icon: 'warning', title: 'تنبيه', text: 'يرجى اختيار دور واحد على الأقل للتوليد.' });
            } else {
                alert('يرجى اختيار دور واحد على الأقل للتوليد.');
            }
            return;
        }

        var formData = new FormData(modalForm);
        formData.append('action', 'seed');

        btnSubmit.disabled = true;
        btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin ml-1"></i> جاري التوليد...';
        alertBox.style.display = 'none';

        fetch('ajax/seed_roles.php', {
            method: 'POST',
            body: formData
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = '<i class="fas fa-bolt ml-1"></i> توليد الأدوار المختارة';

            if (data.success) {
                if (window.toastr) {
                    toastr.success(data.message, 'اكتملت العملية بنجاح');
                }
                if (window.Swal) {
                    Swal.fire({
                        icon: 'success',
                        title: 'تم التوليد بنجاح',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(function() {
                        window.location.reload();
                    });
                } else {
                    alertBox.className = 'alert alert-success p-2 small';
                    alertBox.innerHTML = '<i class="fas fa-check-circle ml-1"></i> ' + data.message;
                    alertBox.style.display = 'block';
                    setTimeout(function() { window.location.reload(); }, 1500);
                }
            } else {
                alertBox.className = 'alert alert-danger p-2 small';
                alertBox.innerHTML = '<i class="fas fa-exclamation-triangle ml-1"></i> ' + (data.message || 'حدث خطأ أثناء التوليد');
                alertBox.style.display = 'block';
            }
        })
        .catch(function(err) {
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = '<i class="fas fa-bolt ml-1"></i> توليد الأدوار المختارة';
            alertBox.className = 'alert alert-danger p-2 small';
            alertBox.innerHTML = '<i class="fas fa-times ml-1"></i> خطأ في الاتصال: ' + err;
            alertBox.style.display = 'block';
        });
    });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
