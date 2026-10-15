<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
require_once __DIR__ . '/includes/sidebar.php';

// Fetch departments safely
$sql = "SELECT * FROM `departments` WHERE COALESCE(isdeleted, 0) != 1 ORDER BY id DESC";
$res = $conn->query($sql);
$deptCount = $res ? $res->num_rows : 0;
$canAdd = Can::check('departments.add');
$canEdit = Can::check('departments.edit');
$canDelete = Can::check('departments.delete');
?>

<div class="content-wrapper k-page-wrapper">
  <?= kody_page_header(
      title: $lang_departlist ?? 'قائمة الأقسام',
      icon: 'fa-sitemap',
      breadcrumbs: [
          'الرئيسية' => 'dashboard.php',
          ($lang_departlist ?? 'الأقسام') => ''
      ],
      actions: $canAdd ? [
          [
              'label' => $lang_add_new ?? 'إضافة قسم جديد',
              'url'   => 'add_department.php',
              'class' => 'k-btn-primary',
              'icon'  => 'fa-plus'
          ]
      ] : [],
      badge: $deptCount . ' قسم'
  ) ?>

  <div class="k-card">
    <div class="k-toolbar">
      <div class="k-toolbar-start">
        <div class="k-search-input-wrap">
          <i class="fas fa-search"></i>
          <input type="text" placeholder="بحث سريع في الأقسام..." data-kody-table-search="#departmentsTable">
        </div>
      </div>
      <div class="k-toolbar-end">
        <span class="text-muted small">إجمالي الأقسام: <strong><?= $deptCount ?></strong></span>
      </div>
    </div>

    <?php if ($deptCount === 0): ?>
      <?= kody_empty_state(
          title: 'لا توجد أقسام مسجلة حالياً',
          desc: 'قم بإضافة الأقسام التنظيمية للشركة لربط الموظفين والعمليات بها بسهولة.',
          icon: 'fa-sitemap',
          cta: $canAdd ? [
              'label' => 'إضافة أول قسم',
              'url' => 'add_department.php',
              'icon' => 'fa-plus'
          ] : null
      ) ?>
    <?php else: ?>
      <div class="k-table-wrap">
        <table class="k-table" id="departmentsTable">
          <thead>
            <tr>
              <th style="width: 60px;">#</th>
              <th><?= $lang_publicname ?? 'اسم القسم' ?></th>
              <th><?= $lang_publicinfo ?? 'الوصف والملاحظات' ?></th>
              <th style="width: 120px;" class="text-center"><?= $lang_publicoperations ?? 'العمليات' ?></th>
            </tr>
          </thead>
          <tbody>
            <?php
            $x = 0;
            while ($row = $res->fetch_assoc()):
                $x++;
                $deptId = (int)$row['id'];
                $deptName = htmlspecialchars($row['name'] ?? '', ENT_QUOTES, 'UTF-8');
                $deptInfo = htmlspecialchars($row['info'] ?? '', ENT_QUOTES, 'UTF-8');
            ?>
              <tr>
                <td><span class="text-muted small font-weight-bold"><?= $x ?></span></td>
                <td>
                  <strong class="text-dark"><?= $deptName ?></strong>
                </td>
                <td>
                  <?= $deptInfo !== '' ? $deptInfo : '<span class="text-muted small">—</span>' ?>
                </td>
                <td class="text-center">
                  <div class="k-actions">
                    <?= $canEdit ? kody_action_btn('edit', 'edit_department.php?id=' . $deptId, ['title' => 'تعديل القسم']) : '' ?>
                    <?= $canDelete ? kody_action_btn('delete', 'do/dodel_department.php?id=' . $deptId, [
                        'title' => 'حذف القسم',
                        'item_name' => $deptName
                    ]) : '' ?>
                  </div>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>