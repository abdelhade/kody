<?php 
include('includes/pos_simple_header.php');

// معالجة تسجيل الخروج
if (isset($_GET['logout'])) {
    unset($_SESSION['pos_authenticated']);
    unset($_SESSION['pos_user_id']);
    unset($_SESSION['pos_user_name']);
    header('Location: pos_barcode.php');
    exit();
}

// جلب الإعدادات
$rowstg = $conn->query("SELECT * FROM settings WHERE id = 1")->fetch_assoc();

// نظام الحماية البسيط
if (isset($rowstg['pos_has_password']) && $rowstg['pos_has_password'] == 1) {
    
    // معالجة تسجيل الدخول
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pos_barcode'])) {
        $barcode = trim($_POST['pos_barcode']);
        
        if (empty($barcode)) {
            $login_error = 'الرجاء إدخال الباركود';
        } else {
            // جلب كل اليوزرز النشطين
            $query = "SELECT id, uname, password FROM users WHERE isdeleted = 0";
            $result = $conn->query($query);
            
            $user_found = false;
            
            if ($result && $result->num_rows > 0) {
                while ($user = $result->fetch_assoc()) {
                    $stored_password = $user['password'];
                    
                    // التحقق من النوعين: MD5 أو bcrypt
                    $is_valid = false;
                    
                    // لو الباسورد MD5 (32 حرف)
                    if (strlen($stored_password) == 32) {
                        $is_valid = (md5($barcode) === $stored_password);
                    }
                    // لو الباسورد bcrypt (يبدأ بـ $2y$)
                    elseif (strpos($stored_password, '$2y$') === 0) {
                        $is_valid = password_verify($barcode, $stored_password);
                    }
                    
                    if ($is_valid) {
                        // ✅ تم العثور على المستخدم
                        $_SESSION['pos_authenticated'] = true;
                        $_SESSION['pos_user_id'] = $user['id'];
                        $_SESSION['pos_user_name'] = $user['uname'];
                        header('Location: pos_barcode.php');
                        exit();
                    }
                }
                
                // لو وصلنا هنا، معناه الباركود غلط
                $login_error = 'باركود غير صحيح';
            } else {
                $login_error = 'خطأ في قاعدة البيانات';
            }
        }
    }
    
    // التحقق من تسجيل الدخول
    if (!isset($_SESSION['pos_authenticated']) || $_SESSION['pos_authenticated'] !== true) {
        // عرض شاشة تسجيل الدخول
        include('includes/pos_login_screen.php');
        exit();
    }
}
// ============================================

// إضافة طاولات تجريبية إذا لم تكن موجودة
$check_tables = $conn->query("SELECT COUNT(*) as count FROM tables WHERE isdeleted = 0");
if ($check_tables) {
    $tables_count = $check_tables->fetch_assoc()['count'];
    if ($tables_count == 0) {
        for ($i = 1; $i <= 12; $i++) {
            $table_name = "طاولة " . $i;
            $conn->query("INSERT INTO tables (tname, table_case) VALUES ('$table_name', 0)");
        }
    }
}

// جلب البيانات الأساسية
$posdate = date('Y-m-d', strtotime('-4 hours'));
$rowstg = $conn->query("SELECT * FROM settings WHERE id = 1")->fetch_assoc();

if(isset($_GET['edit'])){
    $id = $_GET['edit'];
    $rowed = $conn->query("SELECT * FROM ot_head where id = $id")->fetch_assoc();
}

// التحقق من رسالة النجاح
$success_message = '';
if(isset($_SESSION['success_message'])){
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}
?>

    <!-- Duplicate HTML headers removed -->
    <link href="assets/libs/bootstrap.min.css" rel="stylesheet">
    <link href="assets/libs/fontawesome.min.css" rel="stylesheet">
    <link href="dist/css/pos.css" rel="stylesheet">
    <link href="dist/css/pos_barcode.css" rel="stylesheet">
    <link href="dist/css/pos_search.css" rel="stylesheet">
    <link href="dist/css/pos_clean.css" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link href="assets/libs/sweetalert2/sweetalert2-bootstrap-4.css" rel="stylesheet">
    <!-- Load jQuery early for plugins -->
    <script src="assets/libs/jquery/jquery-3.6.0.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="assets/libs/sweetalert2/sweetalert2.min.js"></script>
    
    <?php if(isset($rowstg['pos_has_password']) && $rowstg['pos_has_password'] == 1): ?>
    <!-- نظام القفل البسيط -->
    <script>
        // القفل عند تبديل التاب
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden && sessionStorage.getItem('pos_hidden')) {
                window.location.href = 'pos_barcode.php?logout=1';
            }
            if (document.hidden) {
                sessionStorage.setItem('pos_hidden', '1');
            }
        });
        
        // القفل عند الضغط على أي رابط غير tables.php و pos_barcode.php
        document.addEventListener('click', function(e) {
            const link = e.target.closest('a');
            if (link && link.href && 
                !link.href.includes('tables.php') && 
                !link.href.includes('pos_barcode.php') && 
                link.target !== '_blank') {
                // قفل الجلسة قبل المغادرة
                sessionStorage.setItem('pos_locked', '1');
            }
        });
        
        // فحص عند تحميل الصفحة: لو راجع من صفحة تانية، اقفل
        if (sessionStorage.getItem('pos_locked') === '1') {
            sessionStorage.removeItem('pos_locked');
            window.location.href = 'pos_barcode.php?logout=1';
        }
    </script>
    <?php endif; ?>
    


    <body class="bg-light">
    
    <!-- Hidden input for Edit Mode -->
    <input type="hidden" id="edit_order_id" value="<?= isset($id) ? $id : '' ?>">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="dashboard.php">
                <i class="fas fa-home me-2"></i>
                نظام نقاط البيع

            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">

                </ul>

                <ul class="navbar-nav">
                    <li class="nav-item">
                        <button class="btn btn-outline-light btn-sm me-2" id="fullscreenBtn" title="ملء الشاشة">
                            <i class="fas fa-expand-arrows-alt"></i>
                        </button>


                        <button type="button" class="btn btn-outline-warning btn-sm me-2" data-bs-toggle="modal"
                            data-bs-target="#closeShiftModal" title="إغلاق الشيفت">
                            <i class="fas fa-power-off me-1"></i> إغلاق الشيفت
                        </button>
                    </li>
                    <li class="nav-item">
                        <a href="do/do_logout.php" class="nav-link">
                            <i class="fas fa-sign-out-alt me-1"></i> 
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>


    <!-- رسالة النجاح -->
    <?php if(!empty($success_message)): ?>
    <script src="assets/libs/sweetalert2/sweetalert2.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'تم بنجاح!',
                text: '<?= htmlspecialchars($success_message) ?>',
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false
            });
        });
    </script>
    <?php endif; ?>

    <!-- Main Content -->
    <?php 
    $action_url = "do/doadd_invoice.php";
    include('includes/pos_content.php');
    ?>



    <?php include('includes/pos_simple_footer.php');?>