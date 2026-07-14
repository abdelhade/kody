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

// التحقق من صحة البيانات المطلوبة
if (empty($companyname)) {
    die("Error: Company name is required");
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
    receipt_show_client = ?
WHERE 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssiiiisissiiisiiddiissi", 
    $companyname, $companyadd, $companytel, $edit_pass, $lang,
    $acc_rent, $showhr, $showatt, $showpayroll, $bodycolor,
    $showrent, $showclinc, $def_pos_client, $def_pos_store, 
    $def_pos_employee, $def_pos_fund, $pos_type, $pos_has_password,
    $showpulse, $emp_commission, $user_commission,
    $receipt_show_logo, $receipt_font_size, $receipt_paper_width,
    $receipt_footer_text, $receipt_show_client
);

if ($stmt->execute()) {
    header('location:../dashboard.php');
} else {
    echo "Error updating settings: " . $conn->error;
}

$stmt->close();
?>
