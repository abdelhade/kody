<?php 
error_log('[Settings] doedit_settings.php accessed - Method: ' . $_SERVER['REQUEST_METHOD']);
error_log('[Settings] POST data: ' . print_r($_POST, true));

include('../includes/connect.php');

// التحقق من طريقة الطلب
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request method");
}

// التحقق من تسجيل الدخول
if (!isset($_SESSION['login']) || !isset($_SESSION['userid'])) {
    header('location:../index.php');
    exit();
}

// تنظيف وتأمين البيانات المدخلة
$companyname = trim($_POST['companyname'] ?? '');
$companyadd = trim($_POST['companyadd'] ?? '');
$companytel = trim($_POST['companytel'] ?? '');
$edit_pass = trim($_POST['edit_pass'] ?? '');
$lang = trim($_POST['lang'] ?? 'ar');
$showhr = (int)($_POST['showhr'] ?? 0);
$showatt = (int)($_POST['showatt'] ?? 0);
$showclinc = (int)($_POST['showclinc'] ?? 0);
$showrent = (int)($_POST['showrent'] ?? 0);
$bodycolor = trim($_POST['bodycolor'] ?? 'solarized_white');
if (!in_array($bodycolor, ['solarized_white', 'monokai', 'tokyo_night'])) {
    $bodycolor = 'solarized_white';
}
$showpayroll = (int)($_POST['showpayroll'] ?? 0);
$showpulse = (int)($_POST['showpulse'] ?? 0);
$acc_rent = (int)($_POST['acc_rent'] ?? 0);
$def_pos_client = (int)($_POST['def_pos_client'] ?? 0);
$def_pos_store = (int)($_POST['def_pos_store'] ?? 0);
$def_pos_employee = (int)($_POST['def_pos_employee'] ?? 0);
$def_pos_fund = (int)($_POST['def_pos_fund'] ?? 0);
$pos_type = trim($_POST['pos_type'] ?? 'barcode');
$pos_has_password = isset($_POST['pos_has_password']) ? 1 : 0;
$emp_commission = floatval($_POST['emp_commission'] ?? 0);
$user_commission = floatval($_POST['user_commission'] ?? 0);
$receipt_show_logo = isset($_POST['receipt_show_logo']) ? 1 : 0;
$receipt_font_size = (int)($_POST['receipt_font_size'] ?? 14);
$receipt_paper_width = trim($_POST['receipt_paper_width'] ?? '78mm');
$receipt_footer_text = trim($_POST['receipt_footer_text'] ?? '❤ perfect place to grow');
$receipt_show_client = isset($_POST['receipt_show_client']) ? 1 : 0;
$receipt_header_text = trim($_POST['receipt_header_text'] ?? '');
$receipt_notes_text  = trim($_POST['receipt_notes_text']  ?? '');

// رفع اللوجو
$company_logo = '';
if (isset($_FILES['company_logo']) && $_FILES['company_logo']['error'] === UPLOAD_ERR_OK) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];
    $file_type = mime_content_type($_FILES['company_logo']['tmp_name']);
    if (in_array($file_type, $allowed_types)) {
        $ext = pathinfo($_FILES['company_logo']['name'], PATHINFO_EXTENSION);
        $new_filename = 'company_logo_' . time() . '.' . strtolower($ext);
        $upload_dir = __DIR__ . '/../assets/logo/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        if (move_uploaded_file($_FILES['company_logo']['tmp_name'], $upload_dir . $new_filename)) {
            $company_logo = $new_filename;
        }
    }
}

// التحقق من صحة البيانات المطلوبة
if (empty($companyname)) {
    die("Error: Company name is required");
}

// إضافة عمود showpulse إذا لم يكن موجوداً
$col_pulse_check = $conn->query("SHOW COLUMNS FROM settings LIKE 'showpulse'");
if ($col_pulse_check && $col_pulse_check->num_rows === 0) {
    try {
        $conn->query("ALTER TABLE `settings` ADD COLUMN `showpulse` TINYINT(1) NOT NULL DEFAULT 1");
    } catch (mysqli_sql_exception $e) {
        if (stripos($e->getMessage(), 'Duplicate column') === false) {
            die('Error adding showpulse column: ' . $e->getMessage());
        }
    }
}

// إضافة أعمدة العمولة إذا لم تكن موجودة (تحديث قاعدة البيانات)
$col_check = $conn->query("SHOW COLUMNS FROM settings LIKE 'emp_commission'");
if ($col_check && $col_check->num_rows === 0) {
    try {
        $conn->query("ALTER TABLE `settings`
            ADD COLUMN `emp_commission` DOUBLE NOT NULL DEFAULT 0,
            ADD COLUMN `user_commission` DOUBLE NOT NULL DEFAULT 0");
    } catch (mysqli_sql_exception $e) {
        if (stripos($e->getMessage(), 'Duplicate column') === false) {
            die('Error adding commission columns: ' . $e->getMessage());
        }
    }
}

// إضافة أعمدة الطباعة إذا لم تكن موجودة (تحديث قاعدة البيانات)
$col_print_check = $conn->query("SHOW COLUMNS FROM settings LIKE 'receipt_show_logo'");
if ($col_print_check && $col_print_check->num_rows === 0) {
    try {
        $conn->query("ALTER TABLE `settings`
            ADD COLUMN `receipt_show_logo` TINYINT(1) NOT NULL DEFAULT 1,
            ADD COLUMN `receipt_font_size` INT NOT NULL DEFAULT 14,
            ADD COLUMN `receipt_paper_width` VARCHAR(20) NOT NULL DEFAULT '78mm',
            ADD COLUMN `receipt_footer_text` VARCHAR(255) NOT NULL DEFAULT '❤ perfect place to grow',
            ADD COLUMN `receipt_show_client` TINYINT(1) NOT NULL DEFAULT 1");
    } catch (mysqli_sql_exception $e) {
        if (stripos($e->getMessage(), 'Duplicate column') === false) {
            die('Error adding print columns: ' . $e->getMessage());
        }
    }
}

// إضافة عمود اللوجو إذا لم يكن موجوداً
$col_logo_check = $conn->query("SHOW COLUMNS FROM settings LIKE 'company_logo'");
if ($col_logo_check && $col_logo_check->num_rows === 0) {
    try {
        $conn->query("ALTER TABLE `settings` ADD COLUMN `company_logo` VARCHAR(255) NOT NULL DEFAULT ''");
    } catch (mysqli_sql_exception $e) {
        if (stripos($e->getMessage(), 'Duplicate column') === false) {
            die('Error adding logo column: ' . $e->getMessage());
        }
    }
}

// إضافة أعمدة النصوص الإضافية للفاتورة إذا لم تكن موجودة
$col_extra_check = $conn->query("SHOW COLUMNS FROM settings LIKE 'receipt_header_text'");
if ($col_extra_check && $col_extra_check->num_rows === 0) {
    try {
        $conn->query("ALTER TABLE `settings`
            ADD COLUMN `receipt_header_text` TEXT NOT NULL,
            ADD COLUMN `receipt_notes_text`  TEXT NOT NULL");
    } catch (mysqli_sql_exception $e) {
        if (stripos($e->getMessage(), 'Duplicate column') === false) {
            die('Error adding extra text columns: ' . $e->getMessage());
        }
    }
}

// استخدام prepared statement لتحديث الإعدادات
$sql = "UPDATE settings 
SET company_name = ?, 
    company_add = ?, 
    company_tel = ?, 
    edit_pass = ?, 
    lang = ?, 
    acc_rent = ?, 
    showhr = ?, 
    showatt = ?, 
    showpayroll = ?, 
    bodycolor = ?, 
    showrent = ?, 
    showclinc = ?, 
    def_pos_client = ?, 
    def_pos_store = ?, 
    def_pos_employee = ?, 
    def_pos_fund = ?,
    pos_type = ?,
    pos_has_password = ?,
    showpulse = ?,
    emp_commission = ?,
    user_commission = ?,
    receipt_show_logo = ?,
    receipt_font_size = ?,
    receipt_paper_width = ?,
    receipt_footer_text = ?,
    receipt_show_client = ?,
    receipt_header_text = ?,
    receipt_notes_text  = ?
    " . ($company_logo !== '' ? ", company_logo = ?" : "") . "
WHERE 1";

$stmt = $conn->prepare($sql);

if ($company_logo !== '') {
    $stmt->bind_param("sssssiiiisiiiiiisiiddiississs",
        $companyname, $companyadd, $companytel, $edit_pass, $lang,
        $acc_rent, $showhr, $showatt, $showpayroll, $bodycolor,
        $showrent, $showclinc, $def_pos_client, $def_pos_store,
        $def_pos_employee, $def_pos_fund, $pos_type, $pos_has_password,
        $showpulse, $emp_commission, $user_commission,
        $receipt_show_logo, $receipt_font_size, $receipt_paper_width,
        $receipt_footer_text, $receipt_show_client,
        $receipt_header_text, $receipt_notes_text, $company_logo
    );
} else {
    $stmt->bind_param("sssssiiiisiiiiiisiiddiississ",
        $companyname, $companyadd, $companytel, $edit_pass, $lang,
        $acc_rent, $showhr, $showatt, $showpayroll, $bodycolor,
        $showrent, $showclinc, $def_pos_client, $def_pos_store,
        $def_pos_employee, $def_pos_fund, $pos_type, $pos_has_password,
        $showpulse, $emp_commission, $user_commission,
        $receipt_show_logo, $receipt_font_size, $receipt_paper_width,
        $receipt_footer_text, $receipt_show_client,
        $receipt_header_text, $receipt_notes_text
    );
}

if ($stmt->execute()) {
    header('location:../dashboard.php');
} else {
    echo "Error updating settings: " . $conn->error;
}

$stmt->close();
?>
