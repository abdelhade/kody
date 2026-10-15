<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
require_once __DIR__ . '/includes/sidebar.php';

// Single optimized query with JOINs instead of N+1 per row
$sql = "SELECT e.*, j.name AS job_name, d.name AS dept_name, s.name AS shift_name,
               COALESCE(k.total_kbi, 0) AS total_kbi
        FROM `employees` e
        LEFT JOIN `jops` j ON e.jop = j.id
        LEFT JOIN `departments` d ON e.department = d.id
        LEFT JOIN `shifts` s ON e.shift = s.id
        LEFT JOIN (
            SELECT emp_id, SUM(kbi_sum) AS total_kbi 
            FROM emp_kbis 
            GROUP BY emp_id
        ) k ON k.emp_id = e.id
        WHERE COALESCE(e.isdeleted, 0) != 1 
        ORDER BY e.id DESC";
$res = $conn->query($sql);
$empCount = $res ? $res->num_rows : 0;
$canAdd = Can::check('employees.add');
$canEdit = Can::check('employees.edit');
$canDelete = Can::check('employees.delete');

$pageActions = [];
if ($canAdd) {
    $pageActions[] = [
        'label' => $lang_add_new ?? 'إضافة موظف جديد',
        'url'   => 'add_employee.php',
        'class' => 'k-btn-primary',
        'icon'  => 'fa-user-plus'
    ];
}
if (Can::check('payroll.view')) {
    $pageActions[] = [
        'label' => 'حساب الرواتب',
        'url'   => 'payroll_calcs.php',
        'class' => 'k-btn-secondary',
        'icon'  => 'fa-money-bill-wave'
    ];
}
?>

<div class="content-wrapper k-page-wrapper">
  <?= kody_page_header(
      title: $lang_employeeslist ?? 'قائمة الموظفين',
      icon: 'fa-user-tie',
      breadcrumbs: [
          'الرئيسية' => 'dashboard.php',
          'الموظفين' => ''
      ],
      actions: $pageActions,
      badge: $empCount . ' موظف'
  ) ?>

  <div class="k-card">
    <div class="k-toolbar">
      <div class="k-toolbar-start">
        <div class="k-search-input-wrap">
          <i class="fas fa-search"></i>
          <input type="text" placeholder="بحث باسم الموظف أو الوظيفة أو القسم..." data-kody-table-search="#employeesTable">
        </div>
      </div>
      <div class="k-toolbar-end">
        <span class="text-muted small">الموظفين الحاليين: <strong><?= $empCount ?></strong></span>
      </div>
    </div>

    <?php if ($empCount === 0): ?>
      <?= kody_empty_state(
          title: 'لا يوجد موظفين مسجلين حالياً',
          desc: 'قم بإضافة الموظفين وتعيين الأقسام والوظائف ومواعيد الشيفتات والرواتب لهم.',
          icon: 'fa-user-tie',
          cta: $canAdd ? [
              'label' => 'إضافة موظف جديد',
              'url' => 'add_employee.php',
              'icon' => 'fa-user-plus'
          ] : null
      ) ?>
    <?php else: ?>
      <div class="k-table-wrap">
        <table class="k-table" id="employeesTable">
          <thead>
            <tr>
              <th style="width: 50px;">#</th>
              <th>اسم الموظف</th>
              <th>الوظيفة</th>
              <th>القسم</th>
              <th>رقم الهاتف</th>
              <th>الوردية (الشيفت)</th>
              <th>الراتب الأساسي</th>
              <th>رقم البصمة</th>
              <th>مؤشرات الأداء</th>
              <th style="width: 120px;" class="text-center">العمليات</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $x = 0;
            while ($row = $res->fetch_assoc()):
                $x++;
                $eid = (int)$row['id'];
                $ename = htmlspecialchars($row['name'] ?? '', ENT_QUOTES, 'UTF-8');
                $job = htmlspecialchars($row['job_name'] ?? '—', ENT_QUOTES, 'UTF-8');
                $dept = htmlspecialchars($row['dept_name'] ?? '—', ENT_QUOTES, 'UTF-8');
                $phone = htmlspecialchars($row['number'] ?? '', ENT_QUOTES, 'UTF-8');
                $shift = htmlspecialchars($row['shift_name'] ?? '—', ENT_QUOTES, 'UTF-8');
                $salary = (float)($row['salary'] ?? 0);
                $calcType = ($row['calc_type'] ?? '') === 'daily' ? 'يومي' : 'شهري';
                $basma = htmlspecialchars($row['basma_id'] ?? '—', ENT_QUOTES, 'UTF-8');
                $kbi = (float)($row['total_kbi'] ?? 0);
            ?>
              <tr>
                <td><span class="text-muted small font-weight-bold"><?= $x ?></span></td>
                <td>
                  <a href="emprofile.php?id=<?= $eid ?>" class="font-weight-bold text-primary text-decoration-none" title="عرض ملف الموظف">
                    <i class="fas fa-id-badge ml-1 text-muted"></i> <?= $ename ?>
                  </a>
                </td>
                <td><span class="k-badge k-badge-secondary"><?= $job ?></span></td>
                <td><?= $dept !== '—' ? '<span class="text-dark">' . $dept . '</span>' : '<span class="text-muted small">—</span>' ?></td>
                <td><?= $phone !== '' ? '<span dir="ltr">' . $phone . '</span>' : '<span class="text-muted small">—</span>' ?></td>
                <td><?= $shift !== '—' ? '<span class="k-badge k-badge-info">' . $shift . '</span>' : '<span class="text-muted small">—</span>' ?></td>
                <td>
                  <strong class="text-dark"><?= number_format($salary, 2) ?></strong>
                  <small class="text-muted mr-1">(<?= $calcType ?>)</small>
                </td>
                <td><span class="badge badge-light border text-muted"><?= $basma ?></span></td>
                <td>
                  <?php if ($kbi > 0): ?>
                    <span class="k-badge k-badge-success"><?= number_format($kbi, 1) ?> KBI</span>
                  <?php else: ?>
                    <span class="text-muted small">—</span>
                  <?php endif; ?>
                </td>
                <td class="text-center">
                  <div class="k-actions">
                    <?= kody_action_btn('view', 'emprofile.php?id=' . $eid, ['title' => 'ملف الموظف']) ?>
                    <?= $canEdit ? kody_action_btn('edit', 'edit_employee.php?id=' . $eid, ['title' => 'تعديل بيانات الموظف']) : '' ?>
                    <?= $canDelete ? kody_action_btn('delete', 'do/dodel_employee.php?id=' . $eid, [
                        'title' => 'حذف الموظف',
                        'item_name' => $ename
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