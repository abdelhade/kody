<?php
// بدء الجلسة
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '';
$dbname = 'kody2';

// Check connection
mysqli_report(MYSQLI_REPORT_OFF);
$conn = @new mysqli($dbhost, $dbuser, $dbpass);

if ($conn->connect_error) {
    // If we can't even connect to MySQL server
    if (basename($_SERVER['PHP_SELF']) !== 'pre_start.php' && strpos($_SERVER['PHP_SELF'], 'ajax/') === false) {
        header("Location: pre_start.php?error=server_down");
        exit;
    } else {
        die("Connection failed: " . $conn->connect_error);
    }
}

// Try to select database
if (!$conn->select_db($dbname)) {
    // Database doesn't exist
    if (basename($_SERVER['PHP_SELF']) !== 'pre_start.php' && strpos($_SERVER['PHP_SELF'], 'ajax/') === false) {
        header("Location: pre_start.php?reason=db_missing");
        exit;
    } else if (strpos($_SERVER['PHP_SELF'], 'ajax/') !== false) {
        // For AJAX, return a JSON error if database is missing (optional, but safer)
        // For now, let's just let it continue or die
        die("Database '$dbname' not found. Please run pre_start.php");
    }
}

// Enable SQL error reporting for debugging
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// ======================================================
// Global SQL Error Handler
// أي خطأ SQL في أي صفحة يتم توجيه المستخدم لصفحة الخطأ
// ======================================================
set_exception_handler(function ($e) {
    // تسجيل الخطأ الحقيقي في ملف log (للمطور فقط)
    $logFile = __DIR__ . '/../logs/sql_errors.log';
    $logDir  = dirname($logFile);
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0755, true);
    }
    $logMsg = '[' . date('Y-m-d H:i:s') . '] '
            . get_class($e) . ': ' . $e->getMessage()
            . ' in ' . $e->getFile() . ':' . $e->getLine()
            . PHP_EOL;
    @file_put_contents($logFile, $logMsg, FILE_APPEND);

    // توليد كود مرجعي للخطأ
    $errorCode = strtoupper(substr(md5($e->getMessage() . time()), 0, 8));

    // إذا كان الطلب AJAX أو JSON fetch نرجع JSON بدل redirect
    $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])
               && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
           || (!empty($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)
           || (!empty($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false)
           || (strpos($_SERVER['PHP_SELF'], 'ajax/') !== false);

    if ($isAjax) {
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error'   => 'database_error',
            'message' => 'حدث خطأ في قاعدة البيانات، يرجى التواصل مع الفيندور',
            'code'    => $errorCode,
        ]);
        exit;
    }

    // للصفحات العادية — redirect لصفحة الخطأ
    $basePath = str_repeat('../', substr_count($_SERVER['PHP_SELF'], '/') - 1);
    header('Location: ' . $basePath . 'sql_error.php?code=' . $errorCode);
    exit;
});
// تحميل نظام Logging المبسط (إذا كان موجود)
if (file_exists('simple_logger.php')) {
    require_once 'simple_logger.php';
}

// settings

$sqlstg = "SELECT * FROM `settings` WHERE 1";
$resstg = $conn->query($sqlstg);
$rowstg = $resstg->fetch_assoc();


$restwn = $conn->query("SELECT * from towns ");


// user powers
$role = []; // Initialize as empty array to prevent undefined key warnings
if (isset($_SESSION['usrole'])) {
$user_role_id = $_SESSION['usrole'];
$sqlrole = "SELECT * FROM `usr_pwrs` WHERE id = $user_role_id ";
$resrole = $conn->query($sqlrole);
$role = $resrole->fetch_assoc();
}

$edit_pass = $rowstg['edit_pass'];
date_default_timezone_set('Africa/Cairo'); // ضبط التوقيت المحلي (توقيت مصر)
$now = new DateTime();

if ((int)$now->format('H') < 4) {
    // إذا الساعة أقل من 4 صباحًا، نطرح يوم
    $now->modify('-1 day');
}

$today = $now->format('Y-m-d');

$user = "";
if (isset($_COOKIE['login'])) {
  $user = $_COOKIE['login'];
}else {
  $user = '';
}

// رسالة خطأ الصلاحيات
$userErrorMassage = '<div class="alert alert-danger text-center">
    <i class="fas fa-exclamation-triangle"></i> 
    ليس لديك صلاحية للوصول إلى هذه الصفحة
</div>';