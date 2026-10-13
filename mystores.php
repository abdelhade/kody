<?php 
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
require_once __DIR__ . '/includes/sidebar.php';

// Fetch active stores from acc_head
$sql = "SELECT id, code, aname, phone, address, balance 
        FROM acc_head 
        WHERE is_stock = 1 AND COALESCE(isdeleted, 0) != 1 
        ORDER BY id ASC";
$res = $conn->query($sql);
$storeCount = $res ? $res->num_rows : 0;
?>

<div class="content-wrapper k-page-wrapper">
  <?= kody_page_header(
      title: 'إدارة المخازن والمستودعات',
      icon: 'fa-warehouse',
      breadcrumbs: [
          'الرئيسية' => 'dashboard.php',
          'المخازن' => ''
      ],
      actions: [
          [
              'label' => 'إضافة مخزن جديد',
              'url'   => 'add_account.php?parent=123',
              'class' => 'k-btn-primary',
              'icon'  => 'fa-plus'
          ]
      ],
      badge: $storeCount . ' مخزن'
  ) ?>

  <div class="k-card">
    <div class="k-toolbar">
      <div class="k-toolbar-start">
        <div class="k-search-input-wrap">
          <i class="fas fa-search"></i>
          <input type="text" placeholder="بحث باسم المخزن أو الكود..." data-kody-table-search="#storesTable">
        </div>
      </div>
      <div class="k-toolbar-end">
        <span class="text-muted small">إجمالي المخازن: <strong><?= $storeCount ?></strong></span>
      </div>
    </div>

    <?php if ($storeCount === 0): ?>
      <?= kody_empty_state(
          title: 'لا توجد مخازن مضافة حتى الآن',
          desc: 'قم بإضافة المخازن لربط الأصناف وحركات المخزون والمبيعات بها.',
          icon: 'fa-warehouse',
          cta: [
              'label' => 'إضافة مخزن جديد',
              'url' => 'add_account.php?parent=123',
              'icon' => 'fa-plus'
          ]
      ) ?>
    <?php else: ?>
      <div class="k-table-wrap">
        <table class="k-table" id="storesTable">
          <thead>
            <tr>
              <th style="width: 50px;">#</th>
              <th>كود الحساب</th>
              <th>اسم المخزن</th>
              <th>الهاتف</th>
              <th>العنوان / الموقع</th>
              <th>الرصيد الدفتري</th>
              <th style="width: 120px;" class="text-center">العمليات</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $x = 0;
            while ($row = $res->fetch_assoc()):
                $x++;
                $sid = (int)$row['id'];
                $sname = htmlspecialchars($row['aname'] ?? '', ENT_QUOTES, 'UTF-8');
                $scode = htmlspecialchars($row['code'] ?? '', ENT_QUOTES, 'UTF-8');
                $sphone = htmlspecialchars($row['phone'] ?? '', ENT_QUOTES, 'UTF-8');
                $saddr = htmlspecialchars($row['address'] ?? '', ENT_QUOTES, 'UTF-8');
                $bal = (float)($row['balance'] ?? 0);
            ?>
              <tr>
                <td><span class="text-muted small font-weight-bold"><?= $x ?></span></td>
                <td><span class="k-badge k-badge-secondary font-weight-bold"><?= $scode ?></span></td>
                <td>
                  <strong class="text-dark font-size-15"><?= $sname ?></strong>
                </td>
                <td><?= $sphone !== '' ? $sphone : '<span class="text-muted small">—</span>' ?></td>
                <td><?= $saddr !== '' ? $saddr : '<span class="text-muted small">—</span>' ?></td>
                <td>
                  <span class="font-weight-bold <?= $bal >= 0 ? 'text-success' : 'text-danger' ?>">
                    <?= number_format($bal, 2) ?>
                  </span>
                </td>
                <td class="text-center">
                  <div class="k-actions">
                    <?= kody_action_btn('edit', 'edit_account.php?id=' . $sid, ['title' => 'تعديل بيانات المخزن']) ?>
                    <?php if ($x > 1): ?>
                      <?= kody_action_btn('delete', 'do/dodel_account.php?id=' . $sid, [
                          'title' => 'حذف المخزن',
                          'item_name' => $sname
                      ]) ?>
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

<?php require_once __DIR__ . '/includes/footer.php'; ?>