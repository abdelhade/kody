<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/classes/Roles.php';

$roleId = (int) ($_GET['id'] ?? 0);
$copyId = (int) ($_GET['copy'] ?? 0);
Can::authorize($roleId > 0 ? 'roles.edit' : 'roles.add');

$roleRow = null;
$sourceId = $roleId > 0 ? $roleId : $copyId;
if ($sourceId > 0) {
    $roleRow = Roles::find($conn, $sourceId);
    if (!$roleRow) {
        echo '<script>location.href = "roles.php?error=" + encodeURIComponent("الدور غير موجود");</script>';
        exit;
    }
}
$isLockedRole = $roleId === 1 || ($roleId > 0 && $roleId === Can::roleId());

$granted = [];
if ($sourceId > 0) {
    $stmt = $conn->prepare('SELECT `permission_id` FROM `role_has_permissions` WHERE `role_id` = ?');
    $stmt->bind_param('i', $sourceId);
    $stmt->execute();
    foreach ($stmt->get_result()->fetch_all() as [$pid]) {
        $granted[(int) $pid] = true;
    }
    $stmt->close();
}

// المجموعات بترتيب الكتالوج: كيانات CRUD (صف بأربع أفعال) + صلاحيات فعل منفردة
$groups = [];
foreach (Roles::allPermissions($conn) as $p) {
    $g = &$groups[$p['group_key']];
    $g['name'] ??= $p['group_name'];
    if ($p['action'] !== null && $p['action'] !== '') {
        $entity = substr($p['name'], 0, strrpos($p['name'], '.'));
        $g['crud'][$entity]['label'] = $p['entity_label'];
        $g['crud'][$entity]['actions'][$p['action']] = $p;
    } else {
        $g['single'][] = $p;
    }
    unset($g);
}

$name = $roleRow['name'] ?? '';
if ($copyId > 0) {
    $name .= ' (نسخة)';
}
$description = $roleRow['description'] ?? '';
$isActive = $roleId > 0 ? (int) $roleRow['is_active'] === 1 : true;
$actions = ['view' => 'عرض', 'add' => 'إضافة', 'edit' => 'تعديل', 'delete' => 'حذف'];
$title = $roleId > 0 ? 'تعديل دور' : 'إضافة دور';
$e = static fn ($v): string => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');

// checked / disabled لكل صلاحية: مقدرش أدّي صلاحية أنا معنديش، وصلاحيات إدارة الأدوار ثابتة في الدور 1 ودوري
$cbAttrs = static function (array $p) use ($granted, $isLockedRole): string {
    $isGranted = isset($granted[$p['id']]);
    if ($isLockedRole && in_array($p['name'], Roles::LOCK_PERMISSIONS, true)) {
        return 'checked disabled';
    }
    if (!$isGranted && !Can::check($p['name'])) {
        return 'disabled';
    }
    return $isGranted ? 'checked' : '';
};

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
}

require_once __DIR__ . '/includes/navbar.php';
require_once __DIR__ . '/includes/sidebar.php';
?>
<style>
  .perm-group { border: 1px solid #e5e7eb; border-radius: 10px; margin-bottom: 14px; background: #fff; }
  .perm-group > .perm-head { display: flex; justify-content: space-between; align-items: center; padding: 10px 14px; background: #f8fafc; border-bottom: 1px solid #e5e7eb; border-radius: 10px 10px 0 0; cursor: pointer; }
  .perm-group > .perm-head h6 { margin: 0; font-weight: 700; }
  .perm-group .perm-count { font-size: .8rem; color: #64748b; }
  .perm-body { padding: 10px 14px; }
  .perm-table { width: 100%; border-collapse: collapse; }
  .perm-table th, .perm-table td { padding: 6px 8px; border-bottom: 1px solid #f1f5f9; text-align: center; }
  .perm-table th:first-child, .perm-table td:first-child { text-align: right; }
  .perm-singles { display: flex; flex-wrap: wrap; gap: 6px 18px; margin-top: 10px; }
  .perm-singles label { margin: 0; cursor: pointer; }
  .perm-table input, .perm-singles input { width: 17px; height: 17px; vertical-align: middle; cursor: pointer; }
  @media (max-width: 600px) {
    .perm-table th, .perm-table td { padding: 6px 3px; font-size: .85rem; }
  }
</style>

<div class="content-wrapper k-page-wrapper">
  <?= kody_page_header(
      title: $title,
      icon: 'fa-user-shield',
      breadcrumbs: ['الرئيسية' => 'dashboard.php', 'الأدوار' => 'roles.php', $title => ''],
      actions: [['label' => 'رجوع للأدوار', 'url' => 'roles.php', 'class' => 'k-btn-secondary', 'icon' => 'fa-arrow-right']]
  ) ?>

  <?php if ($isLockedRole): ?>
    <div class="alert alert-warning mx-3">عرض وتعديل الأدوار ثابتين في الدور ده عشان إدارة الصلاحيات متتقفلش.</div>
  <?php endif; ?>

  <form action="do/dosave_role.php" method="post" id="roleForm" class="px-3 pb-4">
    <input type="hidden" name="csrf_token" value="<?= $e($_SESSION['csrf_token']) ?>">
    <input type="hidden" name="id" value="<?= $roleId ?>">

    <div class="k-card p-3 mb-3">
      <div class="row">
        <div class="col-md-4 form-group">
          <label class="font-weight-bold">اسم الدور</label>
          <input type="text" name="name" class="form-control" maxlength="190" required value="<?= $e($name) ?>">
        </div>
        <div class="col-md-6 form-group">
          <label class="font-weight-bold">الوصف</label>
          <input type="text" name="description" class="form-control" maxlength="255" value="<?= $e($description) ?>">
        </div>
        <div class="col-md-2 form-group d-flex align-items-end">
          <div class="custom-control custom-switch">
            <input type="checkbox" class="custom-control-input" id="isActive" name="is_active" value="1" <?= $isActive ? 'checked' : '' ?>>
            <label class="custom-control-label" for="isActive">مفعّل</label>
          </div>
        </div>
      </div>
      <div class="alert alert-info small mb-0">
        <i class="fas fa-info-circle ml-1"></i>
        تعديل الصلاحيات بيسري على المستخدمين بعد ما يسجّلوا خروج ودخول تاني. اختيار إضافة أو تعديل أو حذف بيختار العرض تلقائياً. الصلاحيات المقفولة انت معندكش، فمتقدرش تدّيها.
      </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-2">
      <div>
        <button type="button" class="btn btn-outline-secondary btn-sm" id="checkAll"><i class="fas fa-check-double ml-1"></i> تحديد الكل</button>
        <button type="button" class="btn btn-outline-secondary btn-sm" id="uncheckAll"><i class="fas fa-times ml-1"></i> إلغاء الكل</button>
      </div>
      <input type="text" class="form-control form-control-sm" style="max-width: 240px" id="permSearch" placeholder="بحث في الصلاحيات...">
    </div>

    <?php foreach ($groups as $key => $g): ?>
      <div class="perm-group" data-group="<?= $e($key) ?>">
        <div class="perm-head">
          <h6><?= $e($g['name']) ?> <span class="perm-count"></span></h6>
          <label class="mb-0 small" onclick="event.stopPropagation()">
            <input type="checkbox" class="group-all"> الكل
          </label>
        </div>
        <div class="perm-body">
          <?php if (!empty($g['crud'])): ?>
            <table class="perm-table">
              <thead>
                <tr>
                  <th></th>
                  <?php foreach ($actions as $label): ?><th><?= $label ?></th><?php endforeach; ?>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($g['crud'] as $entity => $row): ?>
                  <tr class="perm-row" data-entity="<?= $e($entity) ?>" data-search="<?= $e($row['label'] . ' ' . $entity) ?>">
                    <td><?= $e($row['label']) ?></td>
                    <?php foreach ($actions as $action => $label): $p = $row['actions'][$action] ?? null; ?>
                      <td>
                        <?php if ($p): ?>
                          <input type="checkbox" name="perms[]" value="<?= $p['id'] ?>" class="perm-cb" data-action="<?= $action ?>"
                                 title="<?= $e($p['name']) ?>" <?= $cbAttrs($p) ?>>
                        <?php endif; ?>
                      </td>
                    <?php endforeach; ?>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php endif; ?>
          <?php if (!empty($g['single'])): ?>
            <div class="perm-singles">
              <?php foreach ($g['single'] as $p): ?>
                <label class="perm-row" data-search="<?= $e($p['label'] . ' ' . $p['name']) ?>" title="<?= $e($p['name']) ?>">
                  <input type="checkbox" name="perms[]" value="<?= $p['id'] ?>" class="perm-cb" <?= $cbAttrs($p) ?>>
                  <?= $e($p['label']) ?>
                </label>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>

    <div class="text-left">
      <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save ml-1"></i> حفظ الدور</button>
    </div>
  </form>
</div>

<script>
(function () {
  var form = document.getElementById('roleForm');

  function refreshGroup(group) {
    var boxes = group.querySelectorAll('.perm-cb');
    var checked = group.querySelectorAll('.perm-cb:checked').length;
    group.querySelector('.perm-count').textContent = '(' + checked + ' / ' + boxes.length + ')';
    group.querySelector('.group-all').checked = checked === boxes.length && boxes.length > 0;
  }

  function syncRow(cb) {
    var row = cb.closest('tr.perm-row');
    if (!row) return;
    var view = row.querySelector('[data-action="view"]');
    if (!view) return;
    if (cb.dataset.action !== 'view' && cb.checked) {
      view.checked = true;
    }
    if (cb.dataset.action === 'view' && !cb.checked) {
      row.querySelectorAll('.perm-cb:not(:disabled)').forEach(function (other) { other.checked = false; });
    }
  }

  form.addEventListener('change', function (ev) {
    var t = ev.target;
    var group = t.closest('.perm-group');
    if (t.classList.contains('group-all')) {
      group.querySelectorAll('.perm-cb:not(:disabled)').forEach(function (cb) { cb.checked = t.checked; });
    } else if (t.classList.contains('perm-cb')) {
      syncRow(t);
    }
    if (group) refreshGroup(group);
  });

  document.querySelectorAll('.perm-head').forEach(function (head) {
    head.addEventListener('click', function () {
      var body = head.nextElementSibling;
      body.style.display = body.style.display === 'none' ? '' : 'none';
    });
  });

  function setAll(value) {
    form.querySelectorAll('.perm-cb:not(:disabled)').forEach(function (cb) { cb.checked = value; });
    document.querySelectorAll('.perm-group').forEach(refreshGroup);
  }
  document.getElementById('checkAll').addEventListener('click', function () { setAll(true); });
  document.getElementById('uncheckAll').addEventListener('click', function () { setAll(false); });

  document.getElementById('permSearch').addEventListener('input', function () {
    var q = this.value.trim().toLowerCase();
    document.querySelectorAll('.perm-group').forEach(function (group) {
      var any = false;
      group.querySelectorAll('.perm-row').forEach(function (row) {
        var hit = q === '' || row.dataset.search.toLowerCase().indexOf(q) !== -1;
        row.style.display = hit ? '' : 'none';
        any = any || hit;
      });
      group.style.display = any ? '' : 'none';
    });
  });

  document.querySelectorAll('.perm-group').forEach(refreshGroup);
})();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
