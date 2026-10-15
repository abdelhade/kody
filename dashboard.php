<?php include('includes/header.php') ?>
<?php include('includes/navbar.php') ?>
<?php include('includes/sidebar.php') ?>
<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
<?php if(Can::check('dashboard.cards')){include('elements/main/main_cards.php');} ?>
<?php if(Can::check('dashboard.shortcuts')){include('elements/main/main_element.php');} ?>
<?php if(Can::check('dashboard.tables')){include('elements/main/main_tables.php');} ?>
<?php if(Can::check('dashboard.hr')){include('elements/main/main_hr.php');} ?>
    
      </div>                  
    </section>
  </div>
<?php include('includes/footer.php') ?>
<?php if (!empty($_SESSION['settings_saved_message'])):
    $settingsSavedMessage = (string) $_SESSION['settings_saved_message'];
    unset($_SESSION['settings_saved_message']);
?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof Swal === 'undefined') return;
    Swal.fire({
        type: 'success',
        title: 'تم الحفظ',
        text: <?= json_encode($settingsSavedMessage, JSON_UNESCAPED_UNICODE) ?>,
        confirmButtonText: 'حسناً',
        timer: 4000
    });
});
</script>
<?php endif; ?>