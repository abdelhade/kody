<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<div class="content-wrapper k-page-wrapper">
  <?= kody_page_header(
      title: 'إضافة قسم جديد',
      icon: 'fa-sitemap',
      breadcrumbs: [
          'الرئيسية' => 'dashboard.php',
          'الأقسام' => 'departments.php',
          'إضافة قسم' => ''
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
          <h3 class="k-card-title"><i class="fas fa-plus-circle text-primary mr-1"></i> بيانات القسم</h3>
        </div>
        <form role="form" action="do/doadd_department.php" method="POST">
          <div class="k-card-body">
            <div class="k-form-group">
              <label class="k-label required"><?= $lang_publicname ?? 'اسم القسم' ?></label>
              <input name="name" type="text" class="k-input" placeholder="مثال: قسم الحسابات، قسم المبيعات..." required autofocus>
            </div>

            <div class="k-form-group">
              <label class="k-label"><?= $lang_publicinfo ?? 'الوصف والملاحظات' ?></label>
              <textarea class="k-textarea" name="info" rows="4" placeholder="اكتب تفاصيل أو ملاحظات عن مهام هذا القسم..."></textarea>
            </div>
          </div>

          <div class="k-card-header bg-light d-flex justify-content-end gap-2 p-3">
            <a href="departments.php" class="k-btn k-btn-secondary ml-2">إلغاء</a>
            <button type="submit" class="k-btn k-btn-primary">
              <i class="fas fa-save ml-1"></i> <?= $lang_publicconfirm ?? 'حفظ القسم' ?>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>