<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
require_once __DIR__ . '/includes/sidebar.php';

// Safe count
$rescount = $conn->query("SELECT COUNT(*) AS total FROM clients");
$clientCount = $rescount ? (int)($rescount->fetch_assoc()['total'] ?? 0) : 0;

// Fetch clients
$sqlcl = "SELECT c.*, t.name AS city_name 
          FROM clients c 
          LEFT JOIN towns t ON c.city = t.id 
          ORDER BY c.id DESC";
$rescl = $conn->query($sqlcl);
?>

<div class="content-wrapper k-page-wrapper">
  <?= kody_page_header(
      title: 'قائمة العملاء',
      icon: 'fa-user-friends',
      breadcrumbs: [
          'الرئيسية' => 'dashboard.php',
          'العملاء' => ''
      ],
      actions: [
          [
              'label' => 'إضافة عميل جديد',
              'url'   => 'add_client.php',
              'class' => 'k-btn-primary',
              'icon'  => 'fa-user-plus'
          ]
      ],
      badge: $clientCount . ' عميل'
  ) ?>

  <div class="k-card">
    <div class="k-toolbar">
      <div class="k-toolbar-start">
        <div class="k-search-input-wrap">
          <i class="fas fa-search"></i>
          <input type="text" placeholder="بحث باسم العميل أو الهاتف..." data-kody-table-search="#clientsTable">
        </div>
      </div>
      <div class="k-toolbar-end">
        <span class="text-muted small">إجمالي العملاء: <strong><?= $clientCount ?></strong></span>
      </div>
    </div>

    <?php if ($clientCount === 0): ?>
      <?= kody_empty_state(
          title: 'لا يوجد عملاء مسجلون حتى الآن',
          desc: 'قم بإضافة بيانات العملاء الجدد للبدء في إصدار الفواتير والحجوزات ومتابعة الحسابات.',
          icon: 'fa-user-friends',
          cta: [
              'label' => 'إضافة أول عميل',
              'url' => 'add_client.php',
              'icon' => 'fa-user-plus'
          ]
      ) ?>
    <?php else: ?>
      <div class="k-table-wrap">
        <table class="k-table" id="clientsTable">
          <thead>
            <tr>
              <th style="width: 50px;">#</th>
              <th>الاسم</th>
              <th>رقم الهاتف</th>
              <th>المدينة / العنوان</th>
              <th>تاريخ الميلاد</th>
              <th>الشكوى / الملاحظات</th>
              <th style="width: 130px;" class="text-center">العمليات</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $x = 0;
            while ($rowcl = $rescl->fetch_assoc()):
                $x++;
                $cid = (int)$rowcl['id'];
                $cname = htmlspecialchars($rowcl['name'] ?? '', ENT_QUOTES, 'UTF-8');
                $cphone = htmlspecialchars($rowcl['phone'] ?? '', ENT_QUOTES, 'UTF-8');
                $city = htmlspecialchars($rowcl['city_name'] ?? $rowcl['city'] ?? '', ENT_QUOTES, 'UTF-8');
                $address = htmlspecialchars($rowcl['address'] ?? '', ENT_QUOTES, 'UTF-8');
                $dob = htmlspecialchars($rowcl['dateofbirth'] ?? '', ENT_QUOTES, 'UTF-8');
                $complaint = htmlspecialchars($rowcl['diseses'] ?? $rowcl['info'] ?? '', ENT_QUOTES, 'UTF-8');
            ?>
              <tr>
                <td><span class="text-muted small font-weight-bold"><?= $x ?></span></td>
                <td>
                  <a href="clprofile.php?id=<?= $cid ?>" class="font-weight-bold text-primary text-decoration-none" title="عرض ملف العميل">
                    <i class="fas fa-user-circle ml-1 text-muted"></i> <?= $cname ?>
                  </a>
                </td>
                <td>
                  <?= $cphone !== '' ? '<span dir="ltr">' . $cphone . '</span>' : '<span class="text-muted small">—</span>' ?>
                </td>
                <td>
                  <?php if ($city !== '' || $address !== ''): ?>
                    <span class="text-dark"><?= $city ?></span>
                    <?php if ($address !== ''): ?>
                      <small class="text-muted d-block"><?= $address ?></small>
                    <?php endif; ?>
                  <?php else: ?>
                    <span class="text-muted small">—</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?= $dob !== '' ? $dob : '<span class="text-muted small">—</span>' ?>
                </td>
                <td>
                  <?= $complaint !== '' ? $complaint : '<span class="text-muted small">—</span>' ?>
                </td>
                <td class="text-center">
                  <div class="k-actions">
                    <?= kody_action_btn('view', 'clprofile.php?id=' . $cid, ['title' => 'ملف العميل']) ?>
                    <?= kody_action_btn('edit', 'edit_client.php?id=' . $cid, ['title' => 'تعديل البيانات']) ?>
                    <?= kody_action_btn('delete', 'do/dodel_client.php?id=' . $cid, [
                        'title' => 'حذف العميل',
                        'item_name' => $cname
                    ]) ?>
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
