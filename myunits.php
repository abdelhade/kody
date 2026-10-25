<?php 
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
require_once __DIR__ . '/includes/sidebar.php';

$resunits = $conn->query("SELECT u.*, (
  SELECT COUNT(DISTINCT iu.item_id)
  FROM item_units iu
  INNER JOIN myitems m ON m.id = iu.item_id AND m.isdeleted = 0
  WHERE iu.unit_id = u.id AND COALESCE(iu.isdeleted, 0) = 0
) AS items_count FROM myunits u ORDER BY u.id ASC");
$unitCount = $resunits ? $resunits->num_rows : 0;
$canAdd = Can::check('units.add');
$canEdit = Can::check('units.edit');
$canDelete = Can::check('units.delete');
?>

<div class="content-wrapper k-page-wrapper">
  <?= kody_page_header(
      title: 'وحدات القياس للأصناف',
      icon: 'fa-balance-scale',
      breadcrumbs: [
          'الرئيسية' => 'dashboard.php',
          'الأصناف' => 'myitems.php',
          'وحدات القياس' => ''
      ],
      actions: [
          [
              'label' => 'قائمة الأصناف',
              'url'   => 'myitems.php',
              'class' => 'k-btn-secondary',
              'icon'  => 'fa-boxes'
          ]
      ],
      badge: $unitCount . ' وحدة'
  ) ?>

  <div class="row">
    <!-- كارت إضافة وحدة جديدة -->
    <?php if ($canAdd) { ?>
    <div class="col-lg-4 col-md-5 mb-3">
      <div class="k-card h-100">
        <div class="k-card-header">
          <h3 class="k-card-title"><i class="fas fa-plus-circle text-primary mr-1"></i> إضافة وحدة جديدة</h3>
        </div>
        <form action="do/doadd_unit.php" method="POST">
          <div class="k-card-body">
            <div class="k-form-group">
              <label class="k-label required">اسم الوحدة</label>
              <input type="text" name="uname" class="k-input" placeholder="مثال: قطعة، كرتونة، كيلو، لتر..." required autofocus>
              <small class="text-muted mt-1 d-block">تُستخدم الوحدة لتعريف كميات الأصناف في الفواتير والتقارير.</small>
            </div>
          </div>
          <div class="k-card-header bg-light d-flex justify-content-end p-3">
            <button type="submit" class="k-btn k-btn-primary w-100">
              <i class="fas fa-save ml-1"></i> إضافة الوحدة
            </button>
          </div>
        </form>
      </div>
    </div>
    <?php } ?>

    <!-- كارت جدول الوحدات -->
    <div class="col-lg-8 col-md-7 mb-3">
      <div class="k-card">
        <div class="k-toolbar">
          <div class="k-toolbar-start">
            <div class="k-search-input-wrap">
              <i class="fas fa-search"></i>
              <input type="text" placeholder="بحث في الوحدات..." data-kody-table-search="#unitsTable">
            </div>
          </div>
          <div class="k-toolbar-end">
            <span class="text-muted small">إجمالي الوحدات: <strong><?= $unitCount ?></strong></span>
          </div>
        </div>

        <?php if ($unitCount === 0): ?>
          <?= kody_empty_state(
              title: 'لا توجد وحدات قياس مسجلة',
              desc: 'أضف أول وحدة قياس من النموذج الجانبي لتتمكن من تحديدها عند إضافة الأصناف.',
              icon: 'fa-balance-scale'
          ) ?>
        <?php else: ?>
          <div class="k-table-wrap">
            <table class="k-table" id="unitsTable">
              <thead>
                <tr>
                  <th style="width: 50px;">#</th>
                  <th>اسم الوحدة</th>
                  <th style="width: 120px;" class="text-center">الأصناف المرتبطة</th>
                  <th style="width: 120px;" class="text-center">العمليات</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $x = 0;
                while ($row = $resunits->fetch_assoc()):
                    $x++;
                    $uid = (int)$row['id'];
                    $uname = htmlspecialchars($row['uname'] ?? '', ENT_QUOTES, 'UTF-8');
                    $icnt = (int)($row['items_count'] ?? 0);
                ?>
                  <tr>
                    <td><span class="text-muted small font-weight-bold"><?= $x ?></span></td>
                    <td>
                      <?php if ($canEdit) { ?>
                      <form action="do/doedit_unit.php?id=<?= $uid ?>" method="POST" class="d-flex align-items-center gap-2 m-0" id="form-unit-<?= $uid ?>">
                        <input type="text" name="uname" value="<?= $uname ?>" class="k-input form-control-sm" style="max-width: 220px;" required>
                        <button type="submit" class="k-btn-action k-btn-action-edit" title="حفظ تعديل الاسم">
                          <i class="fas fa-check"></i>
                        </button>
                      </form>
                      <?php } else { ?>
                      <strong class="text-dark"><?= $uname ?></strong>
                      <?php } ?>
                    </td>
                    <td class="text-center">
                      <span class="k-badge <?= $icnt > 0 ? 'k-badge-info' : 'k-badge-secondary' ?>">
                        <i class="fas fa-box-open ml-1"></i> <?= $icnt ?> صنف
                      </span>
                    </td>
                    <td class="text-center">
                      <div class="k-actions">
                        <?php if ($canDelete) { ?>
                        <?php if ($icnt === 0): ?>
                          <?= kody_action_btn('delete', 'do/dodel_unit.php?id=' . $uid, [
                              'title' => 'حذف الوحدة',
                              'item_name' => $uname
                          ]) ?>
                        <?php else: ?>
                          <span class="k-btn-action text-muted" style="cursor: not-allowed; opacity: 0.5;" title="لا يمكن حذف وحدة مرتبطة بأصناف">
                            <i class="fas fa-lock"></i>
                          </span>
                        <?php endif; ?>
                        <?php } ?>
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
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
