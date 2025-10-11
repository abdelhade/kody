<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-light font-xs font-light p-0" >
  <!-- Left navbar links -->
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
    </li>
    <li>

    </li>
    <li class="nav-item d-none d-sm-inline-block" >
      <a href="index.php" class="nav-link"><?=$lang_sidemain?></a>
    </li>

 

    <li class="nav-item d-none d-sm-inline-block">
      <a href="chances.php" class="nav-link">CRM</a>
    </li>


    <?php if($role['show_users'] == 1){ ?>
    <li class="nav-item d-none d-sm-inline-block">
      <a href="users.php" class="nav-link">المستخدمين</a>
    </li> 
    <?php } ?>

    
    <li class="nav-item d-none d-sm-inline-block">
      <a href="setting.php" class="nav-link">اعدادات النظام</a>
    </li>

   
    
    <li class="nav-item d-none d-sm-inline-block">
      <a href="about.php" class="nav-link">بيانات الشركة</a>
    </li>

    

    <li class="nav-item d-none d-sm-inline-block">
      <a href="do/do_logout.php" class="nav-link"><?=$lang_navlogout?></a>
    </li>

    <li class="nav-item d-none d-sm-inline-block">
      <a href="roadmap.php" class="nav-link">خطة العمل</a>
    </li>





  </ul>

 
  <li class="nav-item d-none d-sm-inline-block">
      <button href="" class="nav-link" id="exportDB">حفظ نسخه احتياطية</button>
    </li>

    






<button class="btn btn-light float-left"><i class="fas fa-vector-square"></i></button>
  <!-- Right navbar links -->
   <script>
    document.addEventListener('DOMContentLoaded', function() {
  var fullscreenButton = document.querySelector('.btn.btn-light.float-left');
  
  fullscreenButton.addEventListener('click', function() {
    if (!document.fullscreenElement) {
      document.documentElement.requestFullscreen();
    } else {
      if (document.exitFullscreen) {
        document.exitFullscreen();
      }
    }
  });
});
   </script>
  
</nav>
<!-- /.navbar -->
