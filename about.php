<?php include('includes/header.php'); ?>
<?php include('includes/navbar.php'); ?>
<?php include('includes/sidebar.php'); ?>

<?php
$companyName = 'KODY POS';

$currentLogo = $rowstg['company_logo'] ?? '';
$logoSrc = '';
if ($currentLogo !== '' && is_file(__DIR__ . '/assets/logo/' . $currentLogo)) {
    $logoSrc = 'assets/logo/' . rawurlencode($currentLogo);
} elseif (is_file(__DIR__ . '/assets/logo/logo.jpg')) {
    $logoSrc = 'assets/logo/logo.jpg';
}

$about = [
    'tagline' => 'إحدى إصدارات URCA_EG',
    'intro' => 'KODY POS إحدى إصدارات URCA_EG، ونظام نقاط بيع يقدّم للعميل خدمة واضحة وسعراً مناسباً وجودة يمكن الاعتماد عليها في الوقت نفسه.',
    'story' => 'بدأت الشركة لتعالج الخدمة غير الاحترافية التي يجدها العميل عند بعض الشركات المنافسة، وتعوّضه بخدمة وسعر وجودة معاً.',
    'vision' => 'أن يحصل كل عميل على تعامل احترافي، من غير أن يضطر للاختيار بين الخدمة الجيدة والسعر المناسب.',
    'mission' => 'تقديم KODY POS بدعم حقيقي، وسعر عادل، وجودة ثابتة، حتى يشعر العميل أن ما دفعه يقابله عمل يُنجز كما ينبغي.',
    'values' => [
        ['icon' => 'fa-headset', 'title' => 'الخدمة', 'text' => 'تعامل مباشر وواضح، بعيد عن الخدمة غير الاحترافية التي تضيّع وقت العميل.'],
        ['icon' => 'fa-tags', 'title' => 'السعر', 'text' => 'سعر يناسب العميل، من غير أن يكون على حساب مستوى العمل.'],
        ['icon' => 'fa-gem', 'title' => 'الجودة', 'text' => 'جودة تبقى مع الخدمة والسعر في وقت واحد، وهذا ما تأسست الشركة من أجله.'],
    ],
    'activities' => [
        ['icon' => 'fa-cash-register', 'title' => 'KODY POS', 'text' => 'إصدار لنقاط البيع ضمن منظومة URCA_EG، موجه لإدارة البيع اليومي ببساطة.'],
        ['icon' => 'fa-globe', 'title' => 'URCA_EG', 'text' => 'الشركة الأم للإصدار، وموقعها urca-eg.com.'],
        ['icon' => 'fa-map-marker-alt', 'title' => 'المكتب الإقليمي', 'text' => 'سمنود، ميدان النحاس، برج زايد، الدور الخامس.'],
    ],
];

$contacts = [
    ['icon' => 'fa-phone-alt', 'label' => 'الهاتف', 'value' => '01005366038', 'href' => 'tel:01005366038'],
    ['icon' => 'fa-map-marker-alt', 'label' => 'المكتب الإقليمي', 'value' => 'سمنود، ميدان النحاس، برج زايد، الدور الخامس'],
    ['icon' => 'fa-globe', 'label' => 'الموقع', 'value' => 'urca-eg.com', 'href' => 'https://urca-eg.com'],
];
?>

<style>
  .about-hero {
    background: linear-gradient(135deg, #1a365d 0%, #2b6cb0 55%, #3182ce 100%);
    border-radius: 16px;
    color: #fff;
    overflow: hidden;
  }
  .about-logo {
    width: 96px;
    height: 96px;
    object-fit: contain;
    background: #fff;
    border-radius: 16px;
    padding: 8px;
  }
  .about-logo-fallback {
    width: 96px;
    height: 96px;
    border-radius: 16px;
    background: rgba(255, 255, 255, 0.15);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
  }
  .about-contact {
    background: #fff;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    height: 100%;
  }
  .about-icon {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    background: #ebf4ff;
    color: #2b6cb0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }
  .about-card {
    border: 0;
    border-radius: 14px;
    box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
    height: 100%;
  }
</style>

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2 align-items-center">
        <div class="col-sm-6">
          <h1 class="m-0 text-dark"><i class="fas fa-building text-primary ml-2"></i> بيانات الشركة</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-left m-0 bg-transparent p-0">
            <li class="breadcrumb-item"><a href="dashboard.php">الرئيسية</a></li>
            <li class="breadcrumb-item active">بيانات الشركة</li>
          </ol>
        </div>
      </div>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid pb-4">

      <div class="about-hero p-4 p-md-5 mb-4">
        <div class="d-flex flex-column flex-md-row align-items-md-center">
          <?php if ($logoSrc !== ''): ?>
            <img class="about-logo mb-3 mb-md-0 ml-md-4" src="<?= htmlspecialchars($logoSrc, ENT_QUOTES, 'UTF-8') ?>" alt="شعار الشركة">
          <?php else: ?>
            <div class="about-logo-fallback mb-3 mb-md-0 ml-md-4"><i class="fas fa-building"></i></div>
          <?php endif; ?>
          <div>
            <h2 class="font-weight-bold mb-2"><?= htmlspecialchars($companyName, ENT_QUOTES, 'UTF-8') ?></h2>
            <p class="mb-0" style="opacity:.92; font-size:1.05rem;"><?= htmlspecialchars($about['tagline'], ENT_QUOTES, 'UTF-8') ?></p>
          </div>
        </div>
      </div>

      <div class="row mb-4">
        <?php foreach ($contacts as $contact): ?>
          <div class="col-md-4 mb-3">
            <div class="about-contact p-3 d-flex align-items-center">
              <span class="about-icon ml-3"><i class="fas <?= htmlspecialchars($contact['icon'], ENT_QUOTES, 'UTF-8') ?>"></i></span>
              <div>
                <div class="text-muted small"><?= htmlspecialchars($contact['label'], ENT_QUOTES, 'UTF-8') ?></div>
                <?php if (!empty($contact['href'])): ?>
                  <a class="font-weight-bold text-dark" href="<?= htmlspecialchars($contact['href'], ENT_QUOTES, 'UTF-8') ?>"<?= strpos($contact['href'], 'http') === 0 ? ' target="_blank" rel="noopener"' : '' ?>><?= htmlspecialchars($contact['value'], ENT_QUOTES, 'UTF-8') ?></a>
                <?php else: ?>
                  <div class="font-weight-bold text-dark"><?= htmlspecialchars($contact['value'], ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="row mb-4">
        <div class="col-lg-7 mb-3">
          <div class="card about-card">
            <div class="card-body p-4">
              <h3 class="h5 font-weight-bold text-primary mb-3"><i class="fas fa-info-circle ml-2"></i> من نحن</h3>
              <p class="mb-3" style="line-height:1.9;"><?= htmlspecialchars($about['intro'], ENT_QUOTES, 'UTF-8') ?></p>
              <p class="mb-0 text-muted" style="line-height:1.9;"><?= htmlspecialchars($about['story'], ENT_QUOTES, 'UTF-8') ?></p>
            </div>
          </div>
        </div>
        <div class="col-lg-5 mb-3">
          <div class="card about-card mb-3">
            <div class="card-body p-4">
              <h3 class="h5 font-weight-bold mb-2"><i class="fas fa-eye text-primary ml-2"></i> الرؤية</h3>
              <p class="mb-0" style="line-height:1.8;"><?= htmlspecialchars($about['vision'], ENT_QUOTES, 'UTF-8') ?></p>
            </div>
          </div>
          <div class="card about-card">
            <div class="card-body p-4">
              <h3 class="h5 font-weight-bold mb-2"><i class="fas fa-bullseye text-primary ml-2"></i> الرسالة</h3>
              <p class="mb-0" style="line-height:1.8;"><?= htmlspecialchars($about['mission'], ENT_QUOTES, 'UTF-8') ?></p>
            </div>
          </div>
        </div>
      </div>

      <h3 class="h5 font-weight-bold mb-3">قيمنا</h3>
      <div class="row mb-4">
        <?php foreach ($about['values'] as $value): ?>
          <div class="col-md-4 mb-3">
            <div class="card about-card">
              <div class="card-body p-4">
                <span class="about-icon mb-3"><i class="fas <?= htmlspecialchars($value['icon'], ENT_QUOTES, 'UTF-8') ?>"></i></span>
                <h4 class="h6 font-weight-bold mt-3"><?= htmlspecialchars($value['title'], ENT_QUOTES, 'UTF-8') ?></h4>
                <p class="text-muted mb-0 small" style="line-height:1.8;"><?= htmlspecialchars($value['text'], ENT_QUOTES, 'UTF-8') ?></p>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <h3 class="h5 font-weight-bold mb-3">نشاط الشركة</h3>
      <div class="row">
        <?php foreach ($about['activities'] as $activity): ?>
          <div class="col-md-4 mb-3">
            <div class="card about-card">
              <div class="card-body p-4">
                <span class="about-icon mb-3"><i class="fas <?= htmlspecialchars($activity['icon'], ENT_QUOTES, 'UTF-8') ?>"></i></span>
                <h4 class="h6 font-weight-bold mt-3"><?= htmlspecialchars($activity['title'], ENT_QUOTES, 'UTF-8') ?></h4>
                <p class="text-muted mb-0" style="line-height:1.8;"><?= htmlspecialchars($activity['text'], ENT_QUOTES, 'UTF-8') ?></p>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

    </div>
  </section>
</div>

<?php include('includes/footer.php'); ?>
