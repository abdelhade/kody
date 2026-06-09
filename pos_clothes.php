<?php 
include('includes/pos_simple_header.php');

$posdate = date('Y-m-d', strtotime('-4 hours'));
$rowstg = $conn->query("SELECT * FROM settings WHERE id = 1")->fetch_assoc();

$success_message = '';
if(isset($_SESSION['success_message'])){
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نظام نقاط البيع - الملابس</title>
    
    <link href="assets/libs/bootstrap.min.css" rel="stylesheet">
    <link href="assets/libs/fontawesome.min.css" rel="stylesheet">
    <link href="plugins/sweetalert2/sweetalert2.min.css" rel="stylesheet">
    <link href="plugins/select2/css/select2.min.css" rel="stylesheet">
    <link href="plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css" rel="stylesheet">
    <link href="components/pos_clothes/styles.css" rel="stylesheet">
</head>

<body>
    <?php include('components/pos_clothes/header.php'); ?>

    <?php if(!empty($success_message)): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'تم بنجاح!',
                text: '<?= htmlspecialchars($success_message) ?>',
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false
            });
        });
    </script>
    <?php endif; ?>

    <div class="container-fluid py-3">
        <div class="row g-3">
            <div class="col-lg-4">
                <?php include('components/pos_clothes/order_form.php'); ?>
            </div>

            <div class="col-lg-8">
                <?php include('components/pos_clothes/items_section.php'); ?>
            </div>
        </div>
    </div>

    <?php include('components/pos_clothes/payment_modal.php'); ?>
    <?php include('components/pos_clothes/close_shift_modal.php'); ?>

    <script src="assets/libs/jquery/jquery-3.6.0.min.js"></script>
    <script src="assets/libs/bootstrap.bundle.min.js"></script>
    <script src="plugins/sweetalert2/sweetalert2.min.js"></script>
    <script src="plugins/select2/js/select2.full.min.js"></script>
    <script src="components/pos_clothes/scripts.js"></script>
</body>
<style>
      input:focus {
    background-color: #eef0fa !important;
    border-color: #4B5694 !important;
    box-shadow: 0 0 0 0.2rem rgba(75, 86, 148, 0.25) !important;
    outline: none !important;
    transition: all 0.3s ease !important;
  }
  /* Customizing select2 heights and styles to look like form-control-sm */
  .select2-container .select2-selection--single {
    height: 31px !important;
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
    line-height: 1.5;
  }
  .select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 23px !important;
    padding-right: 0px !important;
    padding-left: 20px !important;
    text-align: right;
  }
  .select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 29px !important;
    left: 3px !important;
    right: auto !important;
  }
</style>
</html>
