<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: var(--primary-navy);">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold d-flex align-items-center" href="index.php">
            <span>نظام نقاط البيع - الملابس</span>
            <span class="badge bg-secondary text-white ms-3" style="font-size: 0.8rem; font-weight: normal; margin-right: 10px;">
                <i class="fas fa-key me-1"></i> وردية رقم: <?= date('Ymd') . '_' . ($_SESSION['userid'] ?? 1) ?>
            </span>
        </a>
        
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-sm btn-outline-light" id="fullscreenBtn" onclick="toggleFullscreen()">
                <i class="fas fa-expand" id="fullscreenIcon"></i>
            </button>
            <button type="button" class="btn btn-warning btn-sm d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#closeShiftModal" title="إغلاق الشيفت">
                <i class="fas fa-power-off"></i>
                <span>إغلاق الشيفت</span>
            </button>
            <a href="do/do_logout.php" class="nav-link text-white d-flex align-items-center">
                <i class="fas fa-sign-out-alt me-1"></i>
            </a>
        </div>
    </div>
</nav>
