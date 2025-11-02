<?php 
session_start();
if (!isset($_SESSION['login'])) {
    header('location:index.php');
    exit;
}
include('includes/connect.php');

$userid = $_SESSION['userid'];
$up = $conn->query("SELECT * FROM users where id = $userid ");

date_default_timezone_set('Africa/Cairo');

// Get language
$lang = isset($rowstg['lang']) ? $rowstg['lang'] : 'ar';
if ($lang == null || $lang == '') {
    include('language/ar.php');
} else {
    include('language/' . $lang . '.php');
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?= isset($lang_title) ? $lang_title : 'نظام نقاط البيع' ?></title>
    
    <!-- Favicon -->
    <link rel="icon" href="assets/favicon/favicon.png" type="image/png">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    
    <!-- NO Bootstrap here - will be loaded in page -->
    
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
