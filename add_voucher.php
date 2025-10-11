<?php include('includes/header.php') ?>
<?php include('includes/navbar.php') ?>
<?php include('includes/sidebar.php') ?>
<?php if (isset($_GET['edit'])) {
        $edit_id =  $_GET['edit'];
        $sqledit = "SELECT * FROM ot_head where id = $edit_id";
        $resedit = $conn->query($sqledit);
        $rowedit = $resedit->fetch_assoc();
}?>
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
    <?php
            if ($_GET == null ) {?>

        <div class="card card-danger">
        <div class="card-header">
        <h2>تحذير</h2>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col"><h4>يبدو انك دخلت بطريقة مخالفه للمعايير , برجاء الرجوع لمدير النظام</h4></div>
            </div>
        </div>
        </div>


            <?php }else{ ?>

                <form action="<?php 
                    if(isset($_GET['edit'])){
                        $edit= $_GET['edit'];
                        echo 'do/doedit_voucher.php?id='.$edit;
                    }else{
                        echo 'do/doadd_voucher.php';
                    }
                    ?>" method="post" id="myForm">
    <div class="card card-primary <?= isset($_GET['edit'])?"card-warning":"" ?>">
        <div class="card-header">
       <?php
       if (isset($_GET['t'])) {
            if ($_GET['t'] == "recive") {
            echo "<h3>سند قبض</h3>"; 
            echo '<input type="text" name="tybe" value="1" hidden>';
            $pro_tybe = 1;   
            }if ($_GET['t'] == "payment") {
                echo "<h3>سند دفع</h3>";   
            echo '<input type="text" name="tybe" value="2" hidden>';
            $pro_tybe = 2;
            }
       }
       if (isset($_GET['edit'])) {
        echo "<h3>تعديل</h3>"; 
       }?> 
       
        </div>
        <div class="card-body bg-dark">
            <div class="row">

                <div class="col-lg-2">
                    <div class="form-group">
                        <label for="serial_number">الرقم</label>
                        <input type="text" name="voucher_id" class="form-control" id="f2" value="<?php $rowid= $conn->query("SELECT pro_id FROM ot_head where pro_tybe = $pro_tybe order by id desc limit 1")->fetch_assoc();
                        if(isset($_GET['edit'])){
                            echo $rowedit['pro_id'];
                        }
                        else{
                            if ($rowid > 0) {
                            $pr_id = $rowid['pro_id']+1;
                            echo $pr_id;
                        }else{echo 1;}}
                        ?>">
                    </div>
                </div>



                <div class="col-lg-2">
                    <div class="form-group">
                        <label for="vdate">التاريخ</label>
                        <input type="date" name="vdate" class="form-control" value="<?php 
                        if (isset($_GET['edit'])) {
                            echo $rowedit['pro_date'];
                        }else{
                            echo date('Y-m-d');
                        }
                        ?>">
                    </div>

                    
                    <?php if (isset($_GET['ins'])) {
                    echo '<input type="text" hidden name="ins_id" value="'.$_GET['ins'].'">';
                }?>
                    
                

                </div>
            </div>
            <div class="row">
                <div class="col-lg-2">   
                    <div class="form-group">
                        <label for="val">القيمة</label>
                        <input required value="<?php if(isset($_GET['v'])){echo $_GET['v'];}elseif(isset($_GET['edit'])){echo $rowedit['pro_value'];}?>" aria-required="true" type="number" name="val" class=" frst form-control bg-light" id="value">
                    </div></div>

                <div class="col-lg-4">
                       <div class="form-group">
                        <label for="account">الحساب</label>
                        <select required name="account" class="form-control" <?php if (!isset($_GET['acc'])) { echo 'id="myAccount"';}?> style="height:40px;width:100%">
                        <?php if (!isset($_GET['acc'])) { echo '<option value="">اختر حساب</option>';}?> 
                        <?php
                        if (isset($_GET['acc'])) {
                            $acc = $_GET['acc'];
                        $resacc = $conn->query("SELECT * FROM acc_head where id = $acc");
                        }else{
                        $resacc = $conn->query("SELECT * FROM acc_head where is_basic = 0 AND is_fund != 1");}
                        while ($rowacc = $resacc->fetch_assoc()) {
                        ?>    
                        <option <?php 
                        if(isset($_GET['edit'])){
                        if(($_GET['t']=="payment") & ($rowacc['id'] == $rowedit['acc1'])){echo "selected";}
                        elseif(($_GET['t']=="recive") & ($rowacc['id'] == $rowedit['acc2'])){echo "selected";}
                        }?> value="<?= $rowacc['id'] ?>"><?= $rowacc['code'] ?>_<?= $rowacc['aname'] ?></option>
                    <?php } ?>    
                    </select>
                    </div></div>
            </div>

            
            <div class="row">
                <div class="col-lg-3">
                    <div class="form-group">
                        <label for="">مركز التكلفة</label>
                        <select name="cost_center" id="" class="form-control">
                        <option value="">بدون مركز تكلفة</option>
                        <?php
                        $rescst = $conn->query("SELECT * FROM cost_centers");
                        while ($rowcst = $rescst->fetch_assoc()) {
                        ?>
                        <option <?php
                        if(isset($_GET['edit'])){
                        if($rowcst['id'] == $rowedit['cost_center']){echo "selected";}}?> value="<?= $rowcst['id']?>"><?= $rowcst['cname']?></option>

                        <?php } ?>
                    </select>

                </div>
                     
                </div>
                <div class="col-lg-3">
                       <div class="form-group">
                        <label for="fund_account">حساب الصندوق</label>
                        <select name="fund_account" class="form-control" id="fund_account" >
                        <?php
                        $resacc = $conn->query("SELECT * FROM acc_head where is_basic = 0  AND is_fund = 1");
                        while ($rowacc = $resacc->fetch_assoc()) {
                        ?>    
                        <option 
                        <?php 
                        if(isset($_GET['edit'])){
                        if(($_GET['t']=="payment") & ($rowacc['id'] == $rowedit['acc2'])){echo "selected";}
                        elseif(($_GET['t']=="recive") & ($rowacc['id'] == $rowedit['acc1'])){echo "selected";}
                        }?>
                        value="<?= $rowacc['id'] ?>"><?= $rowacc['code'] ?>_<?= $rowacc['aname'] ?></option>
                    <?php } ?>    
                    </select>
                    </div></div>
            </div>
            <div class="row">
                <div class="col-lg-8"><input placeholder="ملاحظات" type="text" name="info" class="form-control" value="<?= isset($_GET['edit'])? $rowedit['info']:''?>">
            </div>
        </div>
        </div>
        <div class="card-footer">
            <div class="row">
                <div class="col-lg-4"><button type="submit" class="btn btn-primary btn-block" id="submit">(f12)حفظ</button></div>
                <div class="col-lg-4"><button type="reset" class="btn btn-secondary btn-block">اعادة</button></div>
            </div>
        </div>
        </div>
        </form>
<?php } ?>

    </div>
  </section>
</div>
<script>
    
  $(document).ready(function() {
    $('#submit').hide();
    $('#myAccount').select2();
   $('input').focusout(function () {
    $value = $('#value').val();
    $myaccount = $('#myAccount').val();
    if ($value > 0) {
        $('#submit').show()
    }})})


</script>
<?php include('includes/footer.php')?>
