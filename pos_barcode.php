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

// نظام الحماية البسيط
if (isset($rowstg['pos_has_password']) && $rowstg['pos_has_password'] == 1) {
    
    // معالجة تسجيل الدخول
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pos_barcode'])) {
        $barcode = trim($_POST['pos_barcode']);
        
        if (empty($barcode)) {
            $login_error = 'الرجاء إدخال الباركود';
        } else {
            // استخدام prepared statement للأمان
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
                        header('Location: pos_barcode.php');
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
        // عرض شاشة تسجيل الدخول
        include('includes/pos_login_screen.php');
        exit();
    }
}
$check_tables = $conn->query("SELECT COUNT(*) as count FROM tables WHERE isdeleted = 0");
if ($check_tables) {
    $tables_count = $check_tables->fetch_assoc()['count'];
    if ($tables_count == 0) {
        // استخدام prepared statement للأمان
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

// ========================================
// حماية وضع التعديل بكلمة مرور (server-side)
// ========================================
define('EDIT_ORDER_PASSWORD', '1234'); // ← غيّر الباسورد من هنا

$edit_auth_error = '';
if (isset($_GET['edit'])) {
    // وضع التعديل فقط يحتاج باسورد
    $edit_id_check = intval($_GET['edit']);

    // لو جاي POST بالباسورد → تحقق منه
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_order_password'])) {
        if ($_POST['edit_order_password'] === EDIT_ORDER_PASSWORD) {
            // صح → احفظ في session إذن مؤقت لهذا الأوردر
            $_SESSION['edit_order_allowed'] = $edit_id_check;
        } else {
            $edit_auth_error = 'كلمة المرور غير صحيحة!';
        }
    }

    // لو مفيش إذن → اعرض صفحة الباسورد وأوقف التنفيذ
    if (!isset($_SESSION['edit_order_allowed']) || $_SESSION['edit_order_allowed'] != $edit_id_check) {
        // بناء الـ URL الكامل للعودة بعد التحقق
        $return_url = htmlspecialchars($_SERVER['REQUEST_URI']);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>تأكيد كلمة المرور</title>
    <link rel="stylesheet" href="plugins/bootstrap/css/bootstrap.min.css">
    <style>
        body { background: #1a1a2e; display:flex; align-items:center; justify-content:center; min-height:100vh; }
        .pass-card { background:#fff; border-radius:16px; padding:40px; max-width:380px; width:100%; box-shadow:0 20px 60px rgba(0,0,0,0.4); }
        .pass-card h4 { color:#333; margin-bottom:8px; }
        .pass-card p  { color:#777; font-size:14px; margin-bottom:24px; }
        .lock-icon { font-size:48px; margin-bottom:16px; }
    </style>
</head>
<body>
<div class="pass-card text-center">
    <div class="lock-icon">🔐</div>
    <h4>تأكيد كلمة المرور</h4>
    <p>هذه العملية تحتاج إلى كلمة مرور للمتابعة</p>
    <?php if ($edit_auth_error): ?>
        <div class="alert alert-danger py-2"><?= $edit_auth_error ?></div>
    <?php endif; ?>
    <form method="POST" action="<?= $return_url ?>">
        <div class="mb-3 text-start">
            <label class="form-label fw-bold">كلمة المرور</label>
            <input type="password" name="edit_order_password" class="form-control form-control-lg text-center"
                   placeholder="••••••" autofocus autocomplete="off">
        </div>
        <button type="submit" class="btn btn-primary w-100 btn-lg">تأكيد</button>
        <a href="pos_barcode.php" class="btn btn-outline-secondary w-100 mt-2">إلغاء</a>
    </form>
</div>
</body>
</html>
<?php
        exit();
    }
} else {
    // مسح إذن التعديل لما المستخدم يرجع لصفحة POS العادية (بدون edit)
    if (!isset($_GET['add_item'])) {
        unset($_SESSION['edit_order_allowed']);
    }
}
// ========================================
// وضع إضافة صنف: يفتح الطلب بنفس آلية التعديل مع علم add_item_mode
$add_item_mode = isset($_GET['add_item']);
if ($add_item_mode && !isset($_GET['edit'])) {
    // توحيد السلوك مع وضع التعديل حتى تعمل كل أجزاء الواجهة (تحميل الأصناف والبيانات)
    $_GET['edit'] = intval($_GET['add_item']);
}
if(isset($_GET['edit']) || $add_item_mode){
    $id = intval($_GET['edit'] ?? $_GET['add_item']); // تأمين المدخلات
    $stmt = $conn->prepare("SELECT * FROM ot_head WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $rowed = $result->fetch_assoc();
    $stmt->close();
}
// table_id من GET (وضع إضافة صنف)
$table_id_from_get = isset($_GET['table_id']) ? intval($_GET['table_id']) : 0;
$table_name_from_get = '';
if ($table_id_from_get > 0) {
    $tn_stmt = $conn->prepare("SELECT tname FROM tables WHERE id = ? AND isdeleted = 0 LIMIT 1");
    if ($tn_stmt) {
        $tn_stmt->bind_param('i', $table_id_from_get);
        $tn_stmt->execute();
        $tn_row = $tn_stmt->get_result()->fetch_assoc();
        if ($tn_row) $table_name_from_get = $tn_row['tname'];
        $tn_stmt->close();
    }
}

// استخراج بيانات الدفع من الطلب عند التعديل
$edit_paid_cash = 0;
$edit_paid_bank = 0;
$edit_payment_fund_id = 0;
$edit_payment_bank_id = 0;
$edit_change_amount = 0;
if (isset($rowed) && !empty($rowed['payment_notes'])) {
    $payment_notes_data = json_decode($rowed['payment_notes'], true);
    if (is_array($payment_notes_data)) {
        $edit_paid_cash = floatval($payment_notes_data['paid_cash'] ?? 0);
        $edit_paid_bank = floatval($payment_notes_data['paid_bank'] ?? 0);
        $edit_payment_fund_id = intval($payment_notes_data['payment_fund_id'] ?? 0);
        $edit_payment_bank_id = intval($payment_notes_data['payment_bank_id'] ?? 0);
        $edit_change_amount = floatval($payment_notes_data['change_amount'] ?? 0);
    }
}

// استخدام remaining_amount كبديل إذا كان موجوداً
if ($edit_change_amount == 0 && isset($rowed) && isset($rowed['remaining_amount'])) {
    $edit_change_amount = floatval($rowed['remaining_amount']);
}
$success_message = '';
if(isset($_SESSION['success_message'])){
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

// تفضيل الكاشير: الطاولة كنوع طلب افتراضي (يُقرأ هنا لأن الـ navbar يُطبع قبل pos_content.php)
$default_table_pref = isset($_COOKIE['pos_default_table']) && $_COOKIE['pos_default_table'] === '1';
?>

<!-- Assets (CSS & JS) -->
<?php include('includes/pos_assets.php'); ?>

<!-- نظام القفل -->
<?php include('includes/pos_lock_system.php'); ?>

<body class="bg-light">

<!-- Hidden input for Edit Mode -->
<input type="hidden" id="edit_order_id" value="<?= isset($id) ? $id : '' ?>">
<!-- Hidden input for Add Item Mode (فتح الطلب من صفحة الطاولات لإضافة صنف) -->
<input type="hidden" id="add_item_mode" value="<?= $add_item_mode ? '1' : '0' ?>">
<!-- Hidden inputs for Edit Payment Data -->
<input type="hidden" id="edit_paid_cash" value="<?= $edit_paid_cash ?>">
<input type="hidden" id="edit_paid_bank" value="<?= $edit_paid_bank ?>">
<input type="hidden" id="edit_payment_fund_id" value="<?= $edit_payment_fund_id ?>">
<input type="hidden" id="edit_payment_bank_id" value="<?= $edit_payment_bank_id ?>">
<input type="hidden" id="edit_change_amount" value="<?= $edit_change_amount ?>">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand me-5 fw-bold" href="dashboard.php">
            <i class="fas fa-home me-2"></i>
            نظام نقاط البيع
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto align-items-center">
                <li class="nav-item d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-light btn-sm fw-bold text-primary" id="navTablesPanelBtn"
                        onclick="window.PosTablesPanel && PosTablesPanel.open()" title="إدارة الطاولات">
                        <i class="fas fa-th-large me-1"></i>إدارة الطاولات
                    </button>

                    <!-- flex بدل .form-check لأن Bootstrap المحمّل نسخة LTR والصفحة RTL -->
                    <label class="d-flex align-items-center gap-1 mb-0 text-white small"
                        style="cursor: pointer; white-space: nowrap;"
                        title="يجعل نوع الطلب طاولة هو الافتراضي">
                        <input class="form-check-input mt-0" type="checkbox" id="defaultTableChk"
                            <?= $default_table_pref ? 'checked' : '' ?>>
                        افتراضي
                    </label>

                    <span id="navSelectedTable"
                        class="badge bg-warning text-dark d-none d-flex align-items-center gap-2 py-2 px-3"
                        style="font-size: 0.9rem;">
                        <i class="fas fa-chair"></i>
                        <strong id="navSelectedTableName"></strong>
                        <a href="#" id="navClearTable" class="text-dark text-decoration-none" title="إلغاء اختيار الطاولة">
                            <i class="fas fa-times-circle"></i>
                        </a>
                    </span>
                </li>
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
<?php include('includes/pos_success_message.php'); ?>

<!-- Main Content -->
<?php 
$action_url = "do/doadd_invoice.php";
include('includes/pos_content.php');
?>

<?php include('includes/pos_simple_footer.php');?>