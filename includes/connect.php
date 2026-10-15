<?php
// بدء الجلسة
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/load_env.php';
require_once __DIR__ . '/db_name.php';

$dbhost = env('DB_HOST', 'localhost');
$dbuser = env('DB_USER', 'root');
$dbpass = env('DB_PASS', '');
$dbname = kody_preferred_dbname();

mysqli_report(MYSQLI_REPORT_OFF);
$conn = @new mysqli($dbhost, $dbuser, $dbpass);

if ($conn->connect_error) {
    if (basename($_SERVER['PHP_SELF']) !== 'pre_start.php' && strpos($_SERVER['PHP_SELF'], 'ajax/') === false) {
        header("Location: pre_start.php?error=server_down");
        exit;
    } else {
        die("Connection failed: " . $conn->connect_error);
    }
}

$selected = kody_select_existing_db($conn);
if ($selected === null) {
    if (basename($_SERVER['PHP_SELF']) !== 'pre_start.php' && strpos($_SERVER['PHP_SELF'], 'ajax/') === false) {
        header("Location: pre_start.php?reason=db_missing");
        exit;
    } else if (strpos($_SERVER['PHP_SELF'], 'ajax/') !== false) {
        die("Database '$dbname' not found. Please run pre_start.php");
    }
} else {
    $dbname = $selected;
}

// Enable SQL error reporting for debugging
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);


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

    $errorCode = strtoupper(substr(md5($e->getMessage() . time()), 0, 8));

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

    $scriptPath = $_SERVER['PHP_SELF'];
    $depth = substr_count($scriptPath, '/') - 1; 
    
    if (strpos($scriptPath, '/do/') !== false || strpos($scriptPath, '/ajax/') !== false) {
        $basePath = '../';
    } else {
        $basePath = '';
    }
    
    $errorUrl = $basePath . 'sql_error.php?code=' . $errorCode;
    if (!headers_sent()) {
        header('Location: ' . $errorUrl);
        exit;
    }
    echo '<script>location.href=' . json_encode($errorUrl) . ';</script>';
    echo '<noscript><meta http-equiv="refresh" content="0;url=' . htmlspecialchars($errorUrl, ENT_QUOTES, 'UTF-8') . '"></noscript>';
    exit;
});
if (file_exists('simple_logger.php')) {
    require_once 'simple_logger.php';
}

// settings

$sqlstg = "SELECT * FROM `settings` WHERE 1";
$resstg = $conn->query($sqlstg);
$rowstg = $resstg->fetch_assoc();


$restwn = $conn->query("SELECT * from towns ");


// الصلاحيات: Can بيحمّل صلاحيات دور المستخدم في الـ session، وبعدين حماية الصفحة من includes/rbac_routes.php
require_once __DIR__ . '/../classes/Can.php';
if ($selected !== null) {
    Can::boot($conn, $dbname);
}
Can::guardRoute();

$edit_pass = $rowstg['edit_pass'];
date_default_timezone_set(env('APP_TIMEZONE', 'Africa/Cairo')); 
$now = new DateTime();

if ((int)$now->format('H') < 4) {
    $now->modify('-1 day');
}

$today = $now->format('Y-m-d');

$user = "";
if (isset($_COOKIE['login'])) {
  $user = $_COOKIE['login'];
}else {
  $user = '';
}

$userErrorMassage = '<div class="alert alert-danger text-center">
    <i class="fas fa-exclamation-triangle"></i> 
    ليس لديك صلاحية للوصول إلى هذه الصفحة
</div>';

if (!function_exists('getUserDefault')) {
    /**
     * Get user specific default or fallback to global myoptions
     * 
     * @param string $optionName 'def_fund', 'def_store', 'def_client', 'def_prod', 'def_emp'
     * @param mysqli $conn
     * @return mixed
     */
    function getUserDefault($optionName, $conn) {
        $allowed = ['def_fund', 'def_store', 'def_client', 'def_prod', 'def_emp'];
        if (in_array($optionName, $allowed) && isset($_SESSION['userid'])) {
            $uid = (int) $_SESSION['userid'];
            $res = $conn->query("SELECT $optionName FROM users WHERE id = $uid");
            if ($res && $row = $res->fetch_assoc()) {
                if (!empty($row[$optionName]) && $row[$optionName] != 0) {
                    return $row[$optionName];
                }
            }
        }
        
        $stmt = $conn->prepare("SELECT cur_value FROM myoptions WHERE oname = ?");
        if ($stmt) {
            $stmt->bind_param("s", $optionName);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                return $row['cur_value'];
            }
        }
        return null;
    }
}