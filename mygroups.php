<?php 
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
require_once __DIR__ . '/includes/sidebar.php';

// Ensure column exists
$col = $conn->query("SHOW COLUMNS FROM item_group LIKE 'supplier_id'");
if ($col && $col->num_rows === 0) {
    $conn->query("ALTER TABLE item_group ADD COLUMN supplier_id INT(11) DEFAULT NULL");
}

$supplierRes = $conn->query("SELECT id, aname FROM acc_head WHERE code LIKE '211%' AND code != '211' AND isdeleted = 0 ORDER BY aname");
$suppliers = $supplierRes ? $supplierRes->fetch_all(MYSQLI_ASSOC) : [];

// Fetch groups with supplier name and items count
$sqlgrb = "SELECT g.*, a.aname AS supplier_name,
               (SELECT COUNT(*) FROM myitems m WHERE m.group1 = g.id AND COALESCE(m.isdeleted, 0) != 1) AS items_count
           FROM item_group g 
           LEFT JOIN acc_head a ON a.id = g.supplier_id 
           WHERE g.isdeleted = 0 
           ORDER BY g.id ASC";
$resgrb = $conn->query($sqlgrb);
$groupCount = $resgrb ? $resgrb->num_rows : 0;
?>

<div class="content-wrapper k-page-wrapper">
  <?= kody_page_header(
      title: 'مجموعات وتصنيفات الأصناف',
      icon: 'fa-layer-group',
      breadcrumbs: [
          'الرئيسية' => 'dashboard.php',
          'الأصناف' => 'myitems.php',
          'المجموعات' => ''
      ],
      actions: [
          [
              'label' => 'إضافة مجموعة جديدة',
              'url'   => '#',
              'class' => 'k-btn-primary',
              'icon'  => 'fa-plus',
              'attr'  => 'data-toggle="modal" data-target="#addGroupModal"'
          ],
          [
              'label' => 'قائمة الأصناف',
              'url'   => 'myitems.php',
              'class' => 'k-btn-secondary',
              'icon'  => 'fa-boxes'
          ]
      ],
      badge: $groupCount . ' مجموعة'
  ) ?>

  <div class="k-card">
    <div class="k-toolbar">
      <div class="k-toolbar-start">
        <div class="k-search-input-wrap">
          <i class="fas fa-search"></i>
          <input type="text" placeholder="بحث في المجموعات أو الموردين..." data-kody-table-search="#groupsTable">
        </div>
      </div>
      <div class="k-toolbar-end">
        <span class="text-muted small">إجمالي المجموعات: <strong><?= $groupCount ?></strong></span>
      </div>
    </div>

    <?php if ($groupCount === 0): ?>
      <?= kody_empty_state(
          title: 'لا توجد مجموعات أصناف مسجلة',
          desc: 'قم بإنشاء المجموعات لتصنيف الأصناف وتسهيل البحث والفلترة في نقاط البيع وتقارير المبيعات.',
          icon: 'fa-layer-group',
          cta: [
              'label' => 'إضافة أول مجموعة',
              'url' => '#',
              'icon' => 'fa-plus'
          ]
      ) ?>
    <?php else: ?>
      <div class="k-table-wrap">
        <table class="k-table" id="groupsTable">
          <thead>
            <tr>
              <th style="width: 50px;">#</th>
              <th>اسم المجموعة</th>
              <th>المورد الافتراضي</th>
              <th style="width: 140px;" class="text-center">الأصناف التابعة</th>
              <th style="width: 120px;" class="text-center">العمليات</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $x = 0;
            while ($rowgrb = $resgrb->fetch_assoc()):
                $x++;
                $gid = (int)$rowgrb['id'];
                $gname = htmlspecialchars($rowgrb['gname'] ?? '', ENT_QUOTES, 'UTF-8');
                $supName = htmlspecialchars($rowgrb['supplier_name'] ?? '', ENT_QUOTES, 'UTF-8');
                $supId = (int)($rowgrb['supplier_id'] ?? 0);
                $icnt = (int)($rowgrb['items_count'] ?? 0);
            ?>
              <tr>
                <td><span class="text-muted small font-weight-bold"><?= $x ?></span></td>
                <td>
                  <strong class="text-dark font-size-15"><?= $gname ?></strong>
                </td>
                <td>
                  <?= $supName !== '' ? '<span class="text-muted"><i class="fas fa-truck-loading ml-1 text-primary"></i> ' . $supName . '</span>' : '<span class="text-muted small">—</span>' ?>
                </td>
                <td class="text-center">
                  <span class="k-badge <?= $icnt > 0 ? 'k-badge-info' : 'k-badge-secondary' ?>">
                    <i class="fas fa-box-open ml-1"></i> <?= $icnt ?> صنف
                  </span>
                </td>
                <td class="text-center">
                  <div class="k-actions">
                    <button type="button" 
                            class="k-btn-action k-btn-action-edit btn-edit-group" 
                            title="تعديل المجموعة"
                            data-id="<?= $gid ?>"
                            data-name="<?= $gname ?>"
                            data-supplier="<?= $supId ?>">
                      <i class="fas fa-edit"></i>
                    </button>
                    <?php if ($icnt === 0): ?>
                      <?= kody_action_btn('delete', 'do/dodel_group.php?id=' . $gid, [
                          'title' => 'حذف المجموعة',
                          'item_name' => $gname
                      ]) ?>
                    <?php else: ?>
                      <span class="k-btn-action text-muted" style="cursor: not-allowed; opacity: 0.5;" title="لا يمكن حذف مجموعة مرتبطة بأصناف">
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
</div>

<!-- Modal: إضافة مجموعة -->
<div class="modal fade" id="addGroupModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
      <form action="do/doadd_group.php" method="POST">
        <div class="modal-header bg-light border-bottom">
          <h5 class="modal-title font-weight-bold text-dark"><i class="fas fa-plus-circle text-primary ml-1"></i> إضافة مجموعة جديدة</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="إغلاق">&times;</button>
        </div>
        <div class="modal-body p-4">
          <div class="k-form-group">
            <label class="k-label required">اسم المجموعة</label>
            <input type="text" name="gname" class="k-input" placeholder="اكتب اسم المجموعة..." required autofocus>
          </div>
          <div class="k-form-group">
            <label class="k-label required">المورد</label>
            <select name="supplier_id" class="k-select" required>
              <option value="">-- اختر المورد --</option>
              <?php foreach ($suppliers as $s): ?>
                <option value="<?= (int)$s['id'] ?>"><?= htmlspecialchars($s['aname'], ENT_QUOTES, 'UTF-8') ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="modal-footer bg-light border-top">
          <button type="button" class="k-btn k-btn-secondary" data-dismiss="modal">إلغاء</button>
          <button type="submit" class="k-btn k-btn-primary"><i class="fas fa-save ml-1"></i> حفظ المجموعة</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal: تعديل مجموعة -->
<div class="modal fade" id="editGroupModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
      <form id="editGroupForm" method="POST" action="">
        <div class="modal-header bg-light border-bottom">
          <h5 class="modal-title font-weight-bold text-dark"><i class="fas fa-edit text-warning ml-1"></i> تعديل بيانات المجموعة</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="إغلاق">&times;</button>
        </div>
        <div class="modal-body p-4">
          <div class="k-form-group">
            <label class="k-label required">اسم المجموعة</label>
            <input type="text" name="gname" id="edit_gname" class="k-input" required>
          </div>
          <div class="k-form-group">
            <label class="k-label required">المورد</label>
            <select name="supplier_id" id="edit_supplier_id" class="k-select" required>
              <option value="">-- اختر المورد --</option>
              <?php foreach ($suppliers as $s): ?>
                <option value="<?= (int)$s['id'] ?>"><?= htmlspecialchars($s['aname'], ENT_QUOTES, 'UTF-8') ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="modal-footer bg-light border-top">
          <button type="button" class="k-btn k-btn-secondary" data-dismiss="modal">إلغاء</button>
          <button type="submit" class="k-btn k-btn-primary"><i class="fas fa-save ml-1"></i> حفظ التعديلات</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php 
$extra_footer_scripts = '
<script>
$(document).ready(function() {
  $(".btn-edit-group").on("click", function() {
    var id = $(this).data("id");
    var name = $(this).data("name");
    var supplier = $(this).data("supplier");

    $("#editGroupForm").attr("action", "do/doedit_group.php?id=" + id);
    $("#edit_gname").val(name);
    $("#edit_supplier_id").val(supplier);
    $("#editGroupModal").modal("show");
  });
});
</script>
';
require_once __DIR__ . '/includes/footer.php'; 
?>
