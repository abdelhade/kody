<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
require_once __DIR__ . '/includes/sidebar.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id < 1) {
    header('Location: departments.php');
    exit;
}

$stmt = $conn->prepare("SELECT * FROM `departments` WHERE `id` = ? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$rowdprt = $res->fetch_assoc();
$stmt->close();

if (!$rowdprt) {
    header('Location: departments.php');
    exit;
}
?>

<div class="content-wrapper k-page-wrapper">
  <?= kody_page_header(
      title: 'تعديل قسم: ' . htmlspecialchars($rowdprt['name'] ?? '', ENT_QUOTES, 'UTF-8'),
      icon: 'fa-sitemap',
      breadcrumbs: [
          'الرئيسية' => 'dashboard.php',
          'الأقسام' => 'departments.php',
          'تعديل قسم' => ''
      ],
      actions: [
          [
              'label' => 'العودة للقائمة',
              'url'   => 'departments.php',
              'class' => 'k-btn-secondary',
              'icon'  => 'fa-arrow-right'
          ]
      ]
  ) ?>

  <div class="row justify-content-center">
    <div class="col-lg-8 col-md-10">
      <div class="k-card">
        <div class="k-card-header">
          <h3 class="k-card-title"><i class="fas fa-edit text-warning mr-1"></i> تعديل بيانات القسم</h3>
        </div>
        <form role="form" action="do/doedit_department.php?id=<?= $id ?>" method="POST">
          <div class="k-card-body">
            <div class="k-form-group">
              <label class="k-label required"><?= $lang_publicname ?? 'اسم القسم' ?></label>
              <input value="<?= htmlspecialchars($rowdprt['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" name="name" type="text" class="k-input" required autofocus>
            </div>

            <div class="k-form-group">
              <label class="k-label"><?= $lang_publicinfo ?? 'الوصف والملاحظات' ?></label>
              <textarea class="k-textarea" name="info" rows="4"><?= htmlspecialchars(trim((string)($rowdprt['info'] ?? '')), ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>
          </div>

          <div class="k-card-header bg-light d-flex justify-content-end gap-2 p-3">
            <a href="departments.php" class="k-btn k-btn-secondary ml-2">إلغاء</a>
            <button type="submit" class="k-btn k-btn-primary">
              <i class="fas fa-save ml-1"></i> حفظ التعديلات
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>