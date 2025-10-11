<?php
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '';
$dbname = 'focus';
$conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// تحميل نظام Logging المبسط
require_once 'simple_logger.php';

// settings

$sqlstg = "SELECT * FROM `settings` WHERE 1";
$resstg = $conn->query($sqlstg);
$rowstg = $resstg->fetch_assoc();


$restwn = $conn->query("SELECT * from towns ");



// user powers
if (isset($_SESSION['usrole'])) {
$user_role_id = $_SESSION['usrole'];
$sqlrole = "SELECT * FROM `usr_pwrs` WHERE id = $user_role_id ";
$resrole = $conn->query($sqlrole);
$role = $resrole->fetch_assoc();}

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