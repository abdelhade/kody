<?php
// إعدادات قاعدة البيانات
if ($_SERVER['HTTP_HOST'] === 'localhost' || strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false) {
    // إعدادات localhost
    $dbhost = 'localhost';
    $dbuser = 'root';
    $dbpass = '';
    $dbname = 'focus';
} else {
    // إعدادات الهوست
    $dbhost = '127.0.0.1';
    $dbuser = 'u173148011_focua';
    $dbpass = 'AbAbAb@1234';
    $dbname = 'u173148011_focus';
}

$conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

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