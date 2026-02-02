<?php 



session_start();
if (!isset($_SESSION['login'])) {

  header('location:index.php');
}
include('includes/connect.php');

$userid = $_SESSION['userid'];
$res_up = $conn->query("SELECT * FROM users where id = $userid ");
$up = $res_up->fetch_assoc();

date_default_timezone_set('Africa/Cairo');


?>
<?php
$lang = $rowstg['lang'];
if ($lang == null) {
  include(__DIR__ . '/../language/ar.php');
} else {
  include(__DIR__ . '/../language/' . $lang . '.php');
}
?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?= $lang_title ?></title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="assets/libs/fontawesome.min.css">
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <link rel="icon" href="assets/favicon/favicon.png" type="image/ico">

  <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
  <!-- Theme style -->
  <!-- Ionicons -->
  <link rel="stylesheet" href="dist/css/ionicons.min.css">
  <!-- Tempusdominus Bbootstrap 4 -->
  <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- JQVMap -->
  <link rel="stylesheet" href="plugins/jqvmap/jqvmap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css?v=<?= time() ?>">
  <link rel="stylesheet" href="dist/css/animate.css?v=<?= time() ?>">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="plugins/overlayScrollbars/css/OverlayScrollbars.min.css?v=<?= time() ?>">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css?v=<?= time() ?>">
  <!-- summernote -->
  <link rel="stylesheet" href="plugins/summernote/summernote-bs4.css?v=<?= time() ?>">
  <link href="plugins/hadi/google.css?v=<?= time() ?>" rel="stylesheet">
  <link href="assets/libs/playpen-sans-arabic-local.css?v=<?= time() ?>" rel="stylesheet">
  <link rel="stylesheet" href="dist/css/bootstrap4.2.min.css?v=<?= time() ?>">

  <link rel="stylesheet" href="dist/css/custom.css?v=<?= time() ?>">
  <link rel="stylesheet" href="plugins/select2/css/select2.min.css?v=<?= time() ?>">
  <link rel="stylesheet" href="plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css?v=<?= time() ?>">
  <link href="dist/css/hadianime.css?v=<?= time() ?>" rel="stylesheet">
  <link href="dist/css/horstec.css?v=<?= time() ?>" rel="stylesheet">
  <link href="assets/styles/dashboard.css?v=<?= time() ?>" rel="stylesheet">
  <link href="assets/styles/sidebar-fixes.css?v=<?= time() ?>" rel="stylesheet">
  <link href="css/operations_responsive.css?v=<?= time() ?>" rel="stylesheet">
  
  <!-- Safety Fix for Card Headers -->
  <style>
  /* Force standard AdminLTE card header colors to prevent gray/unreadable overrides */
  .card-primary:not(.modern-card) > .card-header {
    background-color: #007bff !important;
    color: #fff !important;
  }
  .card-warning:not(.modern-card) > .card-header {
    background-color: #ffc107 !important;
    color: #1f2d3d !important;
  }
  .card-success:not(.modern-card) > .card-header {
    background-color: #28a745 !important;
    color: #fff !important;
  }
  .card-info:not(.modern-card) > .card-header {
    background-color: #17a2b8 !important;
    color: #fff !important;
  }
  .card-danger:not(.modern-card) > .card-header {
    background-color: #dc3545 !important;
    color: #fff !important;
  }
  /* Ensure title text is white where needed */
  .card-primary .card-title, .card-success .card-title, .card-info .card-title, .card-danger .card-title {
    color: #fff !important;
  }
  </style>

  <!-- إصلاح طوارئ السايد بار -->
  <style>
  body .wrapper .main-sidebar .nav-sidebar .nav-link:hover {
    background: linear-gradient(135deg, #eff6ff, #dbeafe) !important;
    color: #1e40af !important;
    transform: translateX(3px) scale(1.02) !important;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25) !important;
    border-radius: 12px !important;
    transition: all 0.3s ease !important;
  }
  body .wrapper .main-sidebar .nav-sidebar .nav-link:hover .nav-icon {
    color: #2563eb !important;
    transform: scale(1.15) rotate(5deg) !important;
  }
  </style>
 


<script src="dist/modal/modal.js"></script>
<!-- <script src="assets/js/sidebar-enhancements.js"></script> -->
<!-- <script src="assets/js/sidebar-keep-open.js"></script> -->

<script>console.log('0000000000000000000000000')</script>
  <script src="dist/css/tailwind.js"></script>
  <script>console.log('111111111111111111')</script>



  <style>
    .content-wrapper{
background-color:<?= $rowstg['bodycolor']?>;
}  
.nav-link{
  color:black !important;
  border:1;
}
.content-wrapper{
  background-color: <?= $rowstg['bodycolor'] ?> ;
}

/* Font Awesome Font Faces */
@font-face {
  font-family: "Font Awesome 5 Free";
  font-style: normal;
  font-weight: 900;
  font-display: block;
  src: url("assets/libs/webfonts/fa-solid-900.woff2") format("woff2");
}

@font-face {
  font-family: "Font Awesome 5 Free";
  font-style: normal;
  font-weight: 400;
  font-display: block;
  src: url("assets/libs/webfonts/fa-regular-400.woff2") format("woff2");
}

@font-face {
  font-family: "Font Awesome 5 Brands";
  font-style: normal;
  font-weight: 400;
  font-display: block;
  src: url("assets/libs/webfonts/fa-brands-400.woff2") format("woff2");
}

/* CRITICAL FIX: Force Font Awesome to work */
i.fa, i.fas, i.far, i.fab, i.fal, i.fad,
span.fa, span.fas, span.far, span.fab, span.fal, span.fad,
.fa, .fas, .far, .fab, .fal, .fad {
  font-family: "Font Awesome 5 Free" !important;
  font-weight: 900 !important;
  font-style: normal !important;
  font-variant: normal !important;
  text-rendering: auto !important;
  -webkit-font-smoothing: antialiased !important;
  -moz-osx-font-smoothing: grayscale !important;
  display: inline-block !important;
  direction: ltr !important;
}

.far {
  font-weight: 400 !important;
}

.fab {
  font-family: "Font Awesome 5 Brands" !important;
  font-weight: 400 !important;
}

</style>

  <script src="dist/js/js.js"></script>
</head>
<!-- 
<div class="loader">
<center>
<div class="hazaz">HORSTEC<div class="spinner-grow" role="status">
  <span class="sr-only">Loading...</span>
  </div>
  </div>

<p style="font-size:4vw !important" class="hadi-fade-in2">this may take few seconds</p>


</center>
</div> -->

<body class="hold-transition sidebar-mini sidebar-collapse layout-fixed font-semibold" style="font-family: 'Playpen Sans Arabic', cursive;">
  <div class="wrapper">
