
<?php include('includes/header.php') ?>

<?php if (!isset($_POST['password'])) {
?>


<form action="<?= $_SERVER['PHP_SELF']?>" method="post">
<center>
<h3>كلمة المرور</h3>
<input class="" type="password" name="password" id="">
<button type="submit" class="btn btn-danger ">تم</button>
</center>
</form>

<?php
}else{
   $pass =  $_POST['password'];
   if ($pass != $sittingpass) {
    echo "<h1> Invalid Password </h1>";
    
   }else{
?>    

<?php include('includes/navbar.php') ?>
<?php include('includes/sidebar.php') ?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
        <h2>الاعدادات العامة للنظام</h2>
        <h3>نرجو الحذر الاختيارات .. التعديل حرج في هذه القائمة </h3>

    <form action="do/doedit_settings.php" method="post">
    
<div class="card collapsed-cards">
        <div class="card-header">
        <div class="row">
            <div class="col">
                <h1>
                اعدادات النظام
                </h1>
            </div>
                <div class="col"><button class="form-control btn btn-primary" type="submit">تأكيد</button></div>
                <div class="col"></div>
    </div>    
            
            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>

        </div>
        <div class="card-body">
        <div class="row">
                <div class="form-group">
                            <div class="col "><label for="">اسم الشركة</label></div>
                            <div class="col "><input type="text" class="form-control" name="companyname" value='<?= $rowstg['company_name']?>'></div>
                            </div>
                            

                            <div class="form-group">
                            <div class="col "><label for="">عنوان الشركه</label></div>
                            <div class="col "><input type="text" class="form-control" name="companyadd" value='<?= $rowstg['company_add']?>'></div>
                            </div>

                            <div class="form-group">
                            <div class="col "><label for="">تليفونات الشركه</label></div>
                            <div class="col "><input type="text" class="form-control" name="companytel" value='<?= $rowstg['company_tel']?>'></div>
                            </div>

                            <div class="form-group">
                            <div class="col "><label for="">لغه البرنامج</label></div>
                            <div class="col ">

                            <select class="form-control" name="lang" id="">
                                <option  <?php if ($rowstg['lang'] == "ar") {
                                 echo "selected";
                                } ?> value="ar">العربيه</option>

                                <option <?php if ($rowstg['lang'] == "en") {
                                 echo "selected";
                                } ?> value="en">ُEnglish</option>
                                
                                <option <?php if ($rowstg['lang'] == "fr") {
                                 echo "selected";
                                } ?> value="fr">French</option>
                                
                                <option <?php if ($rowstg['lang'] == "gr") {
                                 echo "selected";
                                } ?> value="gr">german</option>
                                
                                <option <?php if ($rowstg['lang'] == "sp") {
                                 echo "selected";
                                } ?> value="sp">Spanish</option>
                                
                                <option <?php if ($rowstg['lang'] == "trk") {
                                 echo "selected";
                                } ?> value="trk">Turky</option>
                                
                                <option <?php if ($rowstg['lang'] == "ch") {
                                 echo "selected";
                                } ?> value="ch">Chinese</option>
                                
                                <option <?php if ($rowstg['lang'] == "hn") {
                                 echo "selected";
                                } ?> value="hn">Hindi</option>
                                
                                <option <?php if ($rowstg['lang'] == "urd") {
                                 echo "selected";
                                } ?> value="urd">Turky</option>
                                

                            </select>
                        
                        </div>
                            </div>

                            <div class="form-group">
                            <div class="col "><label for="">ياسورد التعديل</label></div>
                            <div class="col "><input type="text" class="form-control" name="edit_pass" value='<?= $rowstg['edit_pass']?>'></div>
                            </div>
                            </div>


                            <div class="row">
                            <div class="col">
                            <div class="form-group">
                            <div class="col "><label for="">__</label></div>
                            <div class="col "><input type="text" class="form-control" name="editpass" value='<?= $rowstg['lic']?>'></div>
                            </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6">
                                الحساب الافتراضي للايجار المستحق
                                <input type="text" name="acc_rent" id="" value="<?= $rowstg['acc_rent'] ?>">
                            </div>    
                        </div>

                        <div class="row">
                            <div class="col-lg-6">
                                الحساب الافتراضي لعميل الكاشير
                                <input type="text" name="def_pos_client" id="" value="<?= $rowstg['def_pos_client'] ?>">
                            </div>    
                        </div>

                        <div class="row">
                            <div class="col-lg-6">
                                الحساب الافتراضي لمخزن الكاشير
                                <input type="text" name="def_pos_store" id="" value="<?= $rowstg['def_pos_store'] ?>">
                            </div>    
                        </div>

                        <div class="row">
                            <div class="col-lg-6">
                                الحساب الافتراضي لموظف الكاشير
                                <input type="text" name="def_pos_employee" id="" value="<?= $rowstg['def_pos_employee'] ?>">
                            </div>    
                        </div>

                        <div class="row">
                            <div class="col-lg-6">
                                الحساب الافتراضي لصندوق الكاشير
                                <input type="text" name="def_pos_fund" id="" value="<?= $rowstg['def_pos_fund'] ?>">
                            </div>    
                        </div>


                        <div class="row">
                            <div class="col-lg4-4">
                                <div class="form-group">
                                    <label for="">لون الخلفية</label>
                                    <input type="color" name="bodycolor" id="" value="<?= $rowstg['bodycolor']?>">
                                </div>
                            </div>
                            <div class="col-lg4-4">
                                <div class="form-group">
                                    <label for="">لون الهيدر</label>
                                    <input type="color" name="nav-background" id="" value="<?= $rowstg['bodycolor']?>">
                                </div>
                            </div>
                            <div class="col-lg4-4">
                                <div class="form-group">
                                    <label for="">لون المفاتيح</label>
                                    <input type="color" name="side-background" id="" value="<?= $rowstg['bodycolor']?>">
                                </div>
                            </div>
                        </div>

                        
            </div>

        
       
</div>

<div class="card collapsed-card">
    <div class="card-header">التحكم بالقوائم
    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
    </div>
    <div class="card-body">
        
<div class="table-responsive col-md-4">
    <table class="table table-hover table-stripped table-warning">
        <thead>
            <tr>
                <th class="col-1">id</th>
                <th class="col-9">الاسم</th>
                <th class="col-2">ظهور</th>
                
            </tr>
        </thead>
        <tbody>
            
            <tr>
                <th></th>
                <th>التأجير</th>
                <th><input type="text" name="showrent" value="<?= $rowstg['showrent']?>"></th>   
            </tr>
        <tr>
                <th></th>
                <th>العيادات</th>
                <th><input type="text" name="showclinc" id="" value = "<?= $rowstg['showclinc']?>"></th>
                
                
               
            </tr>
            
            <tr>
                <th></th>
                <th>hr</th>
                <th><input class ="" type="text" name="showhr" value="<?= $rowstg['showhr']?>"></th>
            </tr>
            <tr>
                <th></th>
                <th>الحضور</th>
                <th><input type="text" name="showatt" value="<?= $rowstg['showatt']?>"></th>
            </tr>
            <tr>
                <th></th>
                <th>المرتبات</th>
                <th><input type="text" name="showpayroll" value="<?= $rowstg['showpayroll']?>"></th>
                
               
            </tr>

            <tr>
                <th></th>
                <th>التأجير</th>
                <th><input type="text" name="showrent" value="<?= $rowstg['showrent']?>"></th>   
            </tr>
        </tbody>
    </table>
</div>
    </div>
</div>







<div class="card-footer">
            
    </div>



    <div class="card">
        <div class="card-header">
            اعدادات النظام
            
        </div>
        <div class="card-body">
  
        <div class="table table-responsive">
            <table>
            <thead>
                <tr>
                    <th>م</th>
                    <th>الحاله</th>
                    <th>اسم الاختيار</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
            </table>
        </div>

        </form>

    </div>




  




</div>
<!-- /.row -->
</section>
<!-- /.content -->
</div>

                            <?php }}?>
<?php include('includes/footer.php') ?>