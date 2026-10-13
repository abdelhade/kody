<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
require_once __DIR__ . '/includes/sidebar.php';

// Fetch towns
$restwn = $conn->query("SELECT * FROM towns WHERE COALESCE(isdeleted, 0) != 1 ORDER BY name ASC");
?>

<div class="content-wrapper k-page-wrapper">
  <?= kody_page_header(
      title: 'إضافة عميل جديد',
      icon: 'fa-user-plus',
      breadcrumbs: [
          'الرئيسية' => 'dashboard.php',
          'العملاء' => 'clients.php',
          'إضافة عميل' => ''
      ],
      actions: [
          [
              'label' => 'العودة لقائمة العملاء',
              'url'   => 'clients.php',
              'class' => 'k-btn-secondary',
              'icon'  => 'fa-arrow-right'
          ]
      ]
  ) ?>

  <div class="row justify-content-center">
    <div class="col-lg-10 col-xl-9">
      <div class="k-card">
        <div class="k-card-header">
          <h3 class="k-card-title"><i class="fas fa-id-card text-primary mr-1"></i> البيانات الشخصية ومعلومات الاتصال</h3>
        </div>

        <form action="do/doadd_client.php" method="POST">
          <div class="k-card-body">
            <div class="row">
              <div class="col-md-6">
                <div class="k-form-group">
                  <label class="k-label required">اسم العميل</label>
                  <input type="text" name="name" class="k-input" placeholder="الاسم ثلاثي أو رباعي..." required autofocus>
                </div>
              </div>
              <div class="col-md-6">
                <div class="k-form-group">
                  <label class="k-label">رقم الهاتف</label>
                  <input type="text" name="phone" class="k-input" placeholder="01xxxxxxxxx">
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="k-form-group">
                  <label class="k-label">المدينة / المحافظة</label>
                  <select name="city" class="k-select">
                    <option value="">-- اختر المدينة --</option>
                    <?php if ($restwn): while ($twn = $restwn->fetch_assoc()): ?>
                      <option value="<?= $twn['id'] ?>"><?= htmlspecialchars($twn['name'], ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endwhile; endif; ?>
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="k-form-group">
                  <label class="k-label">العنوان بالتفصيل</label>
                  <input type="text" name="address" class="k-input" placeholder="الشارع، رقم المبنى، علامة مميزة...">
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-3">
                <div class="k-form-group">
                  <label class="k-label">النوع</label>
                  <select name="gender" class="k-select">
                    <option value="0">ذكر</option>
                    <option value="1">أنثى</option>
                  </select>
                </div>
              </div>
              <div class="col-md-3">
                <div class="k-form-group">
                  <label class="k-label">تاريخ الميلاد</label>
                  <input type="date" name="dateofbirth" class="k-input">
                </div>
              </div>
              <div class="col-md-3">
                <div class="k-form-group">
                  <label class="k-label">الطول (سم)</label>
                  <input type="number" step="0.1" name="height" class="k-input" placeholder="مثال: 175">
                </div>
              </div>
              <div class="col-md-3">
                <div class="k-form-group">
                  <label class="k-label">الوزن (كجم)</label>
                  <input type="number" step="0.1" name="weight" class="k-input" placeholder="مثال: 70">
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="k-form-group">
                  <label class="k-label">رقم هاتف بديل / الرفيق</label>
                  <input type="text" name="ref" class="k-input" placeholder="هاتف إضافي أو قريب...">
                </div>
              </div>
              <div class="col-md-6">
                <div class="k-form-group">
                  <label class="k-label">ملاحظات طبية / أمراض مزمنة</label>
                  <input type="text" name="diseses" class="k-input" placeholder="سكر، ضغط، حساسية...">
                </div>
              </div>
            </div>

            <div class="k-form-group">
              <label class="k-label">ملاحظات عامة</label>
              <textarea name="info" rows="3" class="k-textarea" placeholder="أي بيانات أو متطلبات خاصة بالعميل..."></textarea>
            </div>
          </div>

          <div class="k-card-header bg-light d-flex justify-content-end gap-2 p-3">
            <a href="clients.php" class="k-btn k-btn-secondary ml-2">إلغاء</a>
            <button type="submit" class="k-btn k-btn-primary">
              <i class="fas fa-save ml-1"></i> حفظ بيانات العميل
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
