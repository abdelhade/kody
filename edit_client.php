<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
require_once __DIR__ . '/includes/sidebar.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id < 1) {
    header('Location: clients.php');
    exit;
}

$stmt = $conn->prepare("SELECT * FROM clients WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$rowcl = $res->fetch_assoc();
$stmt->close();

if (!$rowcl) {
    header('Location: clients.php');
    exit;
}

// Fetch towns
$restwn = $conn->query("SELECT * FROM towns WHERE COALESCE(isdeleted, 0) != 1 ORDER BY name ASC");
$clientName = htmlspecialchars($rowcl['name'] ?? '', ENT_QUOTES, 'UTF-8');
?>

<div class="content-wrapper k-page-wrapper">
  <?= kody_page_header(
      title: 'تعديل بيانات العميل: ' . $clientName,
      icon: 'fa-user-edit',
      breadcrumbs: [
          'الرئيسية' => 'dashboard.php',
          'العملاء' => 'clients.php',
          'تعديل بيانات عميل' => ''
      ],
      actions: [
          [
              'label' => 'ملف العميل',
              'url'   => 'clprofile.php?id=' . $id,
              'class' => 'k-btn-info',
              'icon'  => 'fa-id-card'
          ],
          [
              'label' => 'العودة للقائمة',
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
          <h3 class="k-card-title"><i class="fas fa-edit text-warning mr-1"></i> تعديل بيانات العميل</h3>
        </div>

        <form action="do/doedit_client.php?id=<?= $id ?>" method="POST">
          <div class="k-card-body">
            <div class="row">
              <div class="col-md-6">
                <div class="k-form-group">
                  <label class="k-label required">اسم العميل</label>
                  <input type="text" name="name" value="<?= $clientName ?>" class="k-input" required autofocus>
                </div>
              </div>
              <div class="col-md-6">
                <div class="k-form-group">
                  <label class="k-label">رقم الهاتف</label>
                  <input type="text" name="phone" value="<?= htmlspecialchars($rowcl['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="k-input">
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
                      <?php $sel = ((string)$rowcl['city'] === (string)$twn['id']) ? 'selected' : ''; ?>
                      <option value="<?= $twn['id'] ?>" <?= $sel ?>><?= htmlspecialchars($twn['name'], ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endwhile; endif; ?>
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="k-form-group">
                  <label class="k-label">العنوان بالتفصيل</label>
                  <input type="text" name="address" value="<?= htmlspecialchars($rowcl['address'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="k-input">
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-3">
                <div class="k-form-group">
                  <label class="k-label">النوع</label>
                  <select name="gender" class="k-select">
                    <option value="0" <?= ((int)($rowcl['gender'] ?? 0) === 0) ? 'selected' : '' ?>>ذكر</option>
                    <option value="1" <?= ((int)($rowcl['gender'] ?? 0) === 1) ? 'selected' : '' ?>>أنثى</option>
                  </select>
                </div>
              </div>
              <div class="col-md-3">
                <div class="k-form-group">
                  <label class="k-label">تاريخ الميلاد</label>
                  <input type="date" name="dateofbirth" value="<?= htmlspecialchars($rowcl['dateofbirth'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="k-input">
                </div>
              </div>
              <div class="col-md-3">
                <div class="k-form-group">
                  <label class="k-label">الطول (سم)</label>
                  <input type="number" step="0.1" name="height" value="<?= htmlspecialchars($rowcl['height'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="k-input">
                </div>
              </div>
              <div class="col-md-3">
                <div class="k-form-group">
                  <label class="k-label">الوزن (كجم)</label>
                  <input type="number" step="0.1" name="weight" value="<?= htmlspecialchars($rowcl['weight'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="k-input">
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="k-form-group">
                  <label class="k-label">رقم هاتف بديل / الرفيق</label>
                  <input type="text" name="ref" value="<?= htmlspecialchars($rowcl['ref'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="k-input">
                </div>
              </div>
              <div class="col-md-6">
                <div class="k-form-group">
                  <label class="k-label">ملاحظات طبية / أمراض مزمنة</label>
                  <input type="text" name="diseses" value="<?= htmlspecialchars($rowcl['diseses'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="k-input">
                </div>
              </div>
            </div>

            <div class="k-form-group">
              <label class="k-label">ملاحظات عامة</label>
              <textarea name="info" rows="3" class="k-textarea"><?= htmlspecialchars($rowcl['info'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>
          </div>

          <div class="k-card-header bg-light d-flex justify-content-end gap-2 p-3">
            <a href="clients.php" class="k-btn k-btn-secondary ml-2">إلغاء</a>
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
