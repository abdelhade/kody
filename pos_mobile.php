<?php 
include('includes/pos_simple_header.php');

// معالجة تسجيل الخروج
if (isset($_GET['logout'])) {
    unset($_SESSION['pos_authenticated']);
    unset($_SESSION['pos_user_id']);
    unset($_SESSION['pos_user_name']);
    header('Location: pos_mobile.php');
    exit();
}

// نظام الحماية البسيط
if (isset($rowstg['pos_has_password']) && $rowstg['pos_has_password'] == 1) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pos_barcode'])) {
        $barcode = trim($_POST['pos_barcode']);
        if (empty($barcode)) {
            $login_error = 'الرجاء إدخال الباركود';
        } else {
            $stmt = $conn->prepare("SELECT id, uname, password FROM users WHERE isdeleted = 0");
            $stmt->execute();
            $result = $stmt->get_result();
            $user_found = false;
            
            if ($result && $result->num_rows > 0) {
                while ($user = $result->fetch_assoc()) {
                    $stored_password = $user['password'];
                    $is_valid = false;
                    if (strlen($stored_password) == 32) {
                        $is_valid = (md5($barcode) === $stored_password);
                    }
                    elseif (strpos($stored_password, '$2y$') === 0) {
                        $is_valid = password_verify($barcode, $stored_password);
                    }
                    
                    if ($is_valid) {
                        $_SESSION['pos_authenticated'] = true;
                        $_SESSION['pos_user_id'] = $user['id'];
                        $_SESSION['pos_user_name'] = $user['uname'];
                        $stmt->close();
                        header('Location: pos_mobile.php');
                        exit();
                    }
                }
                $login_error = 'باركود غير صحيح';
            } else {
                $login_error = 'خطأ في قاعدة البيانات';
            }
            $stmt->close();
        }
    }
    if (!isset($_SESSION['pos_authenticated']) || $_SESSION['pos_authenticated'] !== true) {
        include('includes/pos_login_screen.php');
        exit();
    }
}

$check_tables = $conn->query("SELECT COUNT(*) as count FROM tables WHERE isdeleted = 0");
if ($check_tables) {
    $tables_count = $check_tables->fetch_assoc()['count'];
    if ($tables_count == 0) {
        $stmt = $conn->prepare("INSERT INTO tables (tname, table_case) VALUES (?, 0)");
        for ($i = 1; $i <= 12; $i++) {
            $table_name = "طاولة " . $i;
            $stmt->bind_param("s", $table_name);
            $stmt->execute();
        }
        $stmt->close();
    }
}

$posdate = date('Y-m-d', strtotime('-4 hours'));
if(isset($_GET['edit'])){
    $id = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM ot_head WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $rowed = $result->fetch_assoc();
    $stmt->close();
}

$success_message = '';
if(isset($_SESSION['success_message'])){
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}
?>

<!-- Assets (CSS & JS) -->
<?php include('includes/pos_assets.php'); ?>
<link rel="stylesheet" href="css/pos_mobile.css?v=<?= time() ?>">

<!-- نظام القفل -->
<?php include('includes/pos_lock_system.php'); ?>

<body class="bg-light mobile-theme vh-100 overflow-hidden d-flex flex-column">

<!-- Hidden input for Edit Mode -->
<input type="hidden" id="edit_order_id" value="<?= isset($id) ? $id : '' ?>">

<!-- Navbar (Mobile Style) -->
<nav class="navbar navbar-expand navbar-dark bg-primary shadow-sm mobile-navbar">
    <div class="container-fluid px-2">
        <a class="navbar-brand fw-bold fs-6 m-0" href="dashboard.php">
            <i class="fas fa-mobile-alt me-1 text-warning"></i>
            نقطة بيع الموبايل
        </a>

        <div class="d-flex align-items-center ms-auto">
            <div class="text-white me-2 d-none d-sm-block" style="font-size: 0.8rem;">
                <i class="fas fa-user-circle me-1"></i> 
                <?php echo isset($_SESSION['pos_user_name']) ? htmlspecialchars($_SESSION['pos_user_name']) : 'الكاشير'; ?>
            </div>
            
            <button type="button" class="btn btn-outline-warning btn-sm me-1 p-1 px-2" data-bs-toggle="modal"
                data-bs-target="#closeShiftModal" title="إغلاق الشيفت">
                <i class="fas fa-power-off"></i>
            </button>
            <a href="pos_mobile.php?logout=1" class="btn btn-danger btn-sm p-1 px-2">
                <i class="fas fa-sign-out-alt"></i> 
            </a>
        </div>
    </div>
</nav>

<!-- رسالة النجاح -->
<?php include('includes/pos_success_message.php'); ?>

<!-- Main Content -->
<div class="flex-grow-1 d-flex flex-column overflow-hidden position-relative">
<?php 
$action_url = "do/doadd_invoice.php";
include('includes/pos_mobile_content.php');
?>
</div>

<!-- إضافة السكريبت الخاص بنقطة بيع الموبايل -->
<script src="js/pos_mobile.js?v=<?= time() ?>"></script>

<?php include('elements/pos/cofe_widget.php'); ?>
<?php include('includes/pos_simple_footer.php');?>
