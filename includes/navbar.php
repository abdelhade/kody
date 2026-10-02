<nav class="main-header navbar navbar-expand border-bottom py-2" style="background-color: #ffffff;">
  <div class="container-fluid d-flex justify-content-between align-items-center">

    <!-- يمين: زر إخفاء القائمة الجانبية + قائمة الاختصارات -->
    <div class="d-flex align-items-center" style="gap: .5rem;">
      <a class="nav-link text-primary fs-5 px-2" data-widget="pushmenu" href="#" role="button" title="القائمة الجانبية" aria-label="القائمة الجانبية">
        <i class="fas fa-bars"></i>
      </a>

      <div class="dropdown">
        <button class="btn btn-light rounded-circle p-2 shadow-sm border" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="اختصارات" aria-label="اختصارات">
          <i class="fas fa-th text-primary"></i>
        </button>
        <div class="dropdown-menu navbar-drop-right text-right shadow">
          <a class="dropdown-item" href="index.php">
            <i class="fas fa-home ml-2 text-primary"></i><?= htmlspecialchars($lang_sidemain ?? 'الرئيسية', ENT_QUOTES, 'UTF-8') ?>
          </a>
          <?php if (($role['show_users'] ?? 0) == 1) { ?>
          <a class="dropdown-item" href="users.php">
            <i class="fas fa-users ml-2 text-primary"></i>المستخدمين
          </a>
          <?php } ?>
          <a class="dropdown-item" href="setting.php">
            <i class="fas fa-cog ml-2 text-primary"></i>إعدادات النظام
          </a>
          <a class="dropdown-item" href="about.php">
            <i class="fas fa-building ml-2 text-primary"></i>بيانات الشركة
          </a>
          <a class="dropdown-item" href="roadmap.php">
            <i class="fas fa-map ml-2 text-primary"></i>خطة العمل
          </a>
        </div>
      </div>
    </div>

    <!-- شمال: قائمة الأدوات -->
    <div class="dropdown">
      <button class="btn btn-light rounded-circle p-2 shadow-sm border" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="أدوات" aria-label="أدوات">
        <i class="fas fa-ellipsis-h text-muted"></i>
      </button>
      <div class="dropdown-menu navbar-drop-left text-right shadow">
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
        <a class="dropdown-item" href="setting.php" title="<?= htmlspecialchars($activePeriodLabel . ' (' . $activeDbName . ')', ENT_QUOTES, 'UTF-8') ?>">
          <i class="fas fa-calendar-alt ml-2 text-primary"></i><?= htmlspecialchars($activePeriodLabel, ENT_QUOTES, 'UTF-8') ?>
        </a>
        <div class="dropdown-divider"></div>
        <?php endif; ?>

        <button type="button" id="exportDB" class="dropdown-item">
          <i class="fas fa-database ml-2 text-primary"></i>حفظ نسخة احتياطية
        </button>
        <button type="button" id="fullscreenBtn" class="dropdown-item">
          <i class="fas fa-expand ml-2 text-muted"></i><span id="fullscreenLabel">وضع ملء الشاشة</span>
        </button>
        <div class="dropdown-divider"></div>
        <a href="do/do_logout.php" class="dropdown-item text-danger">
          <i class="fas fa-sign-out-alt ml-2"></i>تسجيل الخروج
        </a>
      </div>
    </div>
  </div>
</nav>
<style>
  .main-header .navbar-drop-right {
    left: auto !important;
    right: 0 !important;
  }
  .main-header .navbar-drop-left {
    left: 0 !important;
    right: auto !important;
  }
</style>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('fullscreenBtn');
    const label = document.getElementById('fullscreenLabel');
    if (!btn) return;

    const updateFullscreenIcon = () => {
      if (!document.fullscreenElement) {
        btn.querySelector('i').className = 'fas fa-expand ml-2 text-muted';
        if (label) label.textContent = 'وضع ملء الشاشة';
      } else {
        btn.querySelector('i').className = 'fas fa-compress ml-2 text-primary';
        if (label) label.textContent = 'إنهاء ملء الشاشة';
      }
    };

    btn.addEventListener('click', () => {
      if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen().then(updateFullscreenIcon).catch(() => {});
      } else {
        document.exitFullscreen().then(updateFullscreenIcon).catch(() => {});
      }
    });

    document.addEventListener('fullscreenchange', updateFullscreenIcon);
  });
</script>
