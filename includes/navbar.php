<nav class="main-header navbar navbar-expand-lg border-bottom py-2" style="background-color: #ffffff;">
  <div class="container-fluid d-flex justify-content-between align-items-center">
    
    <ul class="navbar-nav align-items-center gap-2 mb-0 me-auto">
      <li class="nav-item">
        <a class="nav-link text-primary fs-5" data-widget="pushmenu" href="#" role="button">
          <i class="fas fa-bars"></i>
        </a>
      </li>

      <li class="nav-item d-none d-sm-inline-block">
        <a href="index.php" class="nav-link active fs-5" data-bs-toggle="tooltip" data-bs-placement="bottom" title="<?= htmlspecialchars($lang_sidemain, ENT_QUOTES, 'UTF-8') ?>" aria-label="<?= htmlspecialchars($lang_sidemain, ENT_QUOTES, 'UTF-8') ?>">
          <i class="fas fa-home"></i>
        </a>
      </li>

      <?php if($role['show_users'] == 1){ ?>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="users.php" class="nav-link fs-5" data-bs-toggle="tooltip" data-bs-placement="bottom" title="المستخدمين" aria-label="المستخدمين">
          <i class="fas fa-users"></i>
        </a>
      </li>
      <?php } ?>

      <li class="nav-item d-none d-sm-inline-block">
        <a href="setting.php" class="nav-link fs-5" data-bs-toggle="tooltip" data-bs-placement="bottom" title="إعدادات النظام" aria-label="إعدادات النظام">
          <i class="fas fa-cog"></i>
        </a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="about.php" class="nav-link fs-5" data-bs-toggle="tooltip" data-bs-placement="bottom" title="بيانات الشركة" aria-label="بيانات الشركة">
          <i class="fas fa-building"></i>
        </a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="roadmap.php" class="nav-link fs-5" data-bs-toggle="tooltip" data-bs-placement="bottom" title="خطة العمل" aria-label="خطة العمل">
          <i class="fas fa-map"></i>
        </a>
      </li>
      
    </ul>

    <div class="d-flex align-items-center gap-3 ms-auto">

      <?php
      $activeDbName = $dbname ?? ($_SESSION['active_dbname'] ?? '');
      $activePeriodLabel = $activeDbName;
      $registryFile = __DIR__ . '/../config/db_registry.json';
      if ($activeDbName && is_readable($registryFile)) {
          $regNav = json_decode((string) file_get_contents($registryFile), true);
          if (is_array($regNav) && !empty($regNav['databases'])) {
              foreach ($regNav['databases'] as $dbNav) {
                  if (($dbNav['name'] ?? '') === $activeDbName) {
                      $activePeriodLabel = $dbNav['label'] ?: $activeDbName;
                      break;
                  }
              }
          }
      }
      if ($activeDbName):
      ?>
      <a href="setting.php" class="btn btn-light rounded-circle p-2 shadow-sm border d-none d-md-inline-flex align-items-center justify-content-center"
         data-bs-toggle="tooltip" data-bs-placement="bottom"
         title="<?= htmlspecialchars($activePeriodLabel . ' (' . $activeDbName . ')', ENT_QUOTES, 'UTF-8') ?>"
         aria-label="<?= htmlspecialchars($activePeriodLabel, ENT_QUOTES, 'UTF-8') ?>">
        <i class="fas fa-calendar-alt text-primary"></i>
      </a>
      <?php endif; ?>
      
      <button id="exportDB" class="btn btn-light rounded-circle p-2 shadow-sm border" data-bs-toggle="tooltip" data-bs-placement="bottom" title="حفظ نسخة احتياطية" aria-label="حفظ نسخة احتياطية">
        <i class="fas fa-database text-primary"></i>
      </button>

      <button id="fullscreenBtn" class="btn btn-light rounded-circle p-2 shadow-sm border" data-bs-toggle="tooltip" data-bs-placement="bottom" title="وضع ملء الشاشة">
        <i class="fas fa-expand text-muted"></i>
      </button>

   

      <a href="do/do_logout.php" class="logout-link d-flex align-items-center px-2 py-1 rounded-pill" data-bs-toggle="tooltip" data-bs-placement="bottom" title="تسجيل الخروج">
        <i class="fas fa-sign-out-alt"></i>
      </a>
    </div>
  </div>
</nav>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('fullscreenBtn');
    
    // إضافة وظيفة لتغيير الأيقونة حسب حالة Fullscreen
    const updateFullscreenIcon = () => {
        if (!document.fullscreenElement) {
            btn.innerHTML = '<i class="fas fa-expand text-muted"></i>';
        } else {
            btn.innerHTML = '<i class="fas fa-compress text-primary"></i>';
        }
    };

    btn.addEventListener('click', () => {
      if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen().then(updateFullscreenIcon).catch(() => {});
      } else {
        document.exitFullscreen().then(updateFullscreenIcon).catch(() => {});
      }
    });

    // Handle full-screen change events outside of button click
    document.addEventListener('fullscreenchange', updateFullscreenIcon);
    
    // Initialize tooltips (if you are using Bootstrap's JS for tooltips)
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl)
    })
  });
</script>