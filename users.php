<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
require_once __DIR__ . '/includes/sidebar.php';

$isRootAdmin = ((int)($_SESSION['userid'] ?? 0) === 1) || ((int)($userid ?? 0) === 1);
$canShowUsers = $isRootAdmin || (isset($role) && is_array($role) && ((int)($role['show_users'] ?? 0) === 1));

// Fetch active users joined with their roles
$sql = "SELECT u.*, r.rollname 
        FROM `users` u 
        LEFT JOIN `usr_pwrs` r ON u.userrole = r.id 
        WHERE COALESCE(u.isdeleted, 0) != 1 
        ORDER BY u.id DESC";
$res = $conn->query($sql);
$userCount = $res ? $res->num_rows : 0;
?>

<div class="content-wrapper k-page-wrapper">
  <?php if (!$canShowUsers): ?>
    <div class="alert alert-danger text-center p-4 m-4">
      <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
      <h4>صلاحية غير كافية</h4>
      <p class="mb-0"><?= $userErrorMassage ?? 'ليس لديك صلاحية لعرض قائمة المستخدمين.' ?></p>
    </div>
  <?php else: ?>
    <?= kody_page_header(
        title: $lang_users ?? 'إدارة المستخدمين',
        icon: 'fa-users',
        breadcrumbs: [
            'الرئيسية' => 'dashboard.php',
            'المستخدمين' => ''
        ],
        actions: [
            [
                'label' => 'أدوار المستخدمين',
                'url'   => 'myroles.php',
                'class' => 'k-btn-secondary',
                'icon'  => 'fa-user-shield'
            ],
            [
                'label' => 'زراعة أدوار جاهزة',
                'url'   => 'seed_roles.php',
                'class' => 'k-btn-success',
                'icon'  => 'fa-seedling'
            ],
            [
                'label' => $lang_add_new ?? 'إضافة مستخدم جديد',
                'url'   => 'add_user.php',
                'class' => 'k-btn-primary',
                'icon'  => 'fa-user-plus'
            ]
        ],
        badge: $userCount . ' مستخدم'
    ) ?>

    <div class="k-card">
      <div class="k-toolbar">
        <div class="k-toolbar-start">
          <div class="k-search-input-wrap">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="بحث باسم المستخدم أو الدور..." data-kody-table-search="#usersTable">
          </div>
        </div>
        <div class="k-toolbar-end">
          <span class="text-muted small">المستخدمين النشطين: <strong><?= $userCount ?></strong></span>
        </div>
      </div>

      <?php if ($userCount === 0): ?>
        <?= kody_empty_state(
            title: 'لا يوجد مستخدمين مسجلين',
            desc: 'يمكنك إضافة مستخدم جديد وتحديد دوره وصلاحياته في النظام.',
            icon: 'fa-users-slash',
            cta: [
                'label' => 'إضافة مستخدم جديد',
                'url' => 'add_user.php',
                'icon' => 'fa-user-plus'
            ]
        ) ?>
      <?php else: ?>
        <div class="k-table-wrap">
          <table class="k-table" id="usersTable">
            <thead>
              <tr>
                <th style="width: 50px;">#</th>
                <th style="width: 60px;">الصورة</th>
                <th><?= $lang_username ?? 'اسم المستخدم' ?></th>
                <th>الدور والصلاحية</th>
                <th>نوع الحساب</th>
                <th style="width: 120px;" class="text-center"><?= $lang_useroperations ?? 'العمليات' ?></th>
              </tr>
            </thead>
            <tbody>
              <?php
              $x = 0;
              while ($row = $res->fetch_assoc()):
                  $x++;
                  $uid = (int)$row['id'];
                  $uname = htmlspecialchars($row['uname'] ?? '', ENT_QUOTES, 'UTF-8');
                  $rname = htmlspecialchars($row['rollname'] ?? 'بدون دور', ENT_QUOTES, 'UTF-8');
                  $isWaiter = ((int)($row['is_waiter'] ?? 0) === 1);
                  $avatar = !empty($row['img']) ? 'uploads/' . htmlspecialchars($row['img'], ENT_QUOTES, 'UTF-8') : '';
              ?>
                <tr>
                  <td><span class="text-muted small font-weight-bold"><?= $x ?></span></td>
                  <td>
                    <?php if ($avatar !== ''): ?>
                      <img src="<?= $avatar ?>" alt="<?= $uname ?>" class="rounded-circle" style="width: 38px; height: 38px; object-fit: cover;" onerror="this.onerror=null;this.src='dist/img/avatar.png';">
                    <?php else: ?>
                      <div class="rounded-circle bg-light text-muted d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="fas fa-user"></i>
                      </div>
                    <?php endif; ?>
                  </td>
                  <td>
                    <strong class="text-dark font-size-15"><?= $uname ?></strong>
                    <?php if ($uid === 1): ?>
                      <span class="badge badge-light border text-muted mr-1 small">الرئيسي</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <span class="k-badge k-badge-primary">
                      <i class="fas fa-shield-alt"></i> <?= $rname ?>
                    </span>
                  </td>
                  <td>
                    <?php if ($isWaiter): ?>
                      <a href="waiter_barcode.php?id=<?= $uid ?>" class="k-badge k-badge-success text-decoration-none" title="اضغط لعرض وطباعة باركود الويتر">
                        <i class="fas fa-user-tie"></i> ويتر <i class="fas fa-barcode mr-1"></i>
                      </a>
                    <?php else: ?>
                      <span class="k-badge k-badge-secondary">
                        <i class="fas fa-desktop"></i> مستخدم نظام
                      </span>
                    <?php endif; ?>
                  </td>
                  <td class="text-center">
                    <div class="k-actions">
                      <?= kody_action_btn('edit', 'edit_user.php?id=' . $uid, ['title' => 'تعديل المستخدم']) ?>
                      <?php if ($uid !== 1 && $uid !== (int)($_SESSION['userid'] ?? 0)): ?>
                        <?= kody_action_btn('delete', 'do/do_deluser.php?id=' . $uid, [
                            'title' => 'حذف المستخدم',
                            'item_name' => $uname
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
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>