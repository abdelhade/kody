<?php include('includes/header.php') ?>
<?php include('includes/navbar.php') ?>
<?php include('includes/sidebar.php') ?>
<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
<?php if($role['show_main_cards'] == 1){include('elements/main/main_cards.php');} ?>
<?php if($role['show_main_elements'] == 1){include('elements/main/main_element.php');} ?>
<?php if($role['show_main_tables'] == 1){include('elements/main/main_tables.php');} ?>
<?php if(($role['show_main_hr'] ?? 1) == 1){include('elements/main/main_hr.php');} ?>
    
      </div>                  
    </section>
  </div>
<?php include('includes/footer.php') ?>