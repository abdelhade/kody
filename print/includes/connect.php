
<?php
$dbhost = 'localhost'; // أو عنوان الخادم
$dbuser = 'اسم_المستخدم_للهوست'; // اسم المستخدم من الهوست
$dbpass = 'كلمة_المرور_للهوست'; // كلمة المرور من الهوست
$dbname = 'اسم_قاعدة_البيانات'; // اسم قاعدة البيانات على الهوست


$conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

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


