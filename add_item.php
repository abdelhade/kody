<?php include('includes/header.php') ?>
<?php if(isset($_GET['edit'])){
    $id = $_GET['edit'];
    $chk = $conn->query("SELECT * FROM fat_details where item_id = $id")->num_rows;
    
    $rowitm = $conn->query("SELECT * FROM myitems where id = $id")->fetch_assoc();
    if ($rowitm == null) {
        header("location:dashboard.php");
    }
} ?>
<?php include('includes/navbar.php') ?>
<?php include('includes/sidebar.php') ?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">

        <?php if ($role['add_items'] == 1){?>


        <div class="card">
        <?php if(!isset($_GET['edit'])){?>
            <form id="myForm" action="do/doadd_item.php" method="post" enctype="multipart/form-data">
        <?php }elseif(isset($_GET['edit'])){ ?>
        <form id="myForm" action="do/doedit_item.php?edit=<?= $id ?>" method="post" enctype="multipart/form-data">
            <?php } ?>
            <div class="card-header <?php if(isset($_GET['edit'])){ echo 'bg-yellow-400';}else{echo 'bg-blue-400'; }  ?>">
                
                <div class="col ">
                <?php if(!isset($_GET['edit'])){?>
                    <h3>صنف جديد</h3>
                <?php }elseif(isset($_GET['edit'])){ ?>
                    <h3 class="">تعديل الصنف [ <?= $rowitm['iname']?> ]</h3>
                    <?php } ?>
                            </div>
                
                
            </div>
            <?php 
            
            $rowlstitm = $conn->query("SELECT max(code) FROM myitems ")->fetch_assoc();
            if ($rowlstitm['max(code)'] == null ) {
              $itmid = 1;
            }elseif(isset($_GET['edit'])){$itmid =  $rowitm['code'];
            }else {
              $itmid = ($rowlstitm['max(code)']+1);
            }
            ?>


            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
               <div class="form-group ">
                <label for="code">الكود</label>
                <input readonly value="<?= $itmid ?>" class="form-control form-control-sm col-4" type="text" name="code" id="code" >
               </div>
               </div>
            
               <div class="col-md-3">
               <div class="form-group ">
                <label for="barcode">الباركود</label>
                <input required value="<?= $itmid ?>" class="form-control form-control-sm " type="text" name="barcode" id="code" >
               </div>
               </div>
               </div>

               <div class="row">
                <div class="col-md-3">
               <div class="form-group ">
                <label for="iname">اسم الصنف</label>
                <datalist id="inamelist">
                    <?php $resname = $conn->query("SELECT iname from myitems order by iname"); while ($rowname = $resname->fetch_assoc()){?>    
                <option value="<?= $rowname['iname']?>"><?= $rowname['iname'] ?></option>
                <?php } ?>
                </datalist>
                <input list="" required class="frst form-control form-control-sm" type="text" name="iname" id="iname" value="<?php if(isset($_GET['edit'])){echo $rowitm['iname'];} ?>">
               </div>
               </div>
            
               <div class="col-md-3">
               <div class="form-group ">
                <label for="name2">اسم ثاني</label>
                <input class="form-control form-control-sm" type="text" name="name2" id="name2" value="<?php if(isset($_GET['edit'])){echo $rowitm['name2'];} ?>">
               </div>
               </div>
               </div>
               
               <div class="row">
                    <div class="col-md-3">
               <div class="form-group ">
                <label for="info">تفاصيل</label>
                <input class="form-control form-control-sm" type="text" name="info" id="info" value="<?php if(isset($_GET['edit'])){echo $rowitm['info'];} ?>">
               </div>
               </div>
               </div>



<?php if (!isset($_GET['edit'])) {?>
            <div class="bg-light">
               <b>الوحدات</b>
               <p id="addUnit" class="btn btn-primary">اضافه وحده</p>
               <div class="row" >
                    <div class="table-responsive">
                        <table class="table table-responsive">
                            <thead>
                                <tr>
                                    <th class="w-80" style="width:120px">الوحـــــدة</th>
                                    <th class="w-80" style="width:120px">م التحويل</th>
                                    <th class="w-80" style="width:120px">باركود</th>
                                    <th class="w-80" style="width:120px">سعر التكلفه</th>
                                    <th class="w-80" style="width:120px">سعر البيع</th>
                                    <th class="w-80" style="width:120px">سعر البيع 2</th>
                                    <th class="w-80" style="width:120px">سعر السوق</th>
                                    <th class="w-80" style="width:120px">حذف</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="urow">
                                <td><select name="unit_id[]" id="" class="form-control">
                                <?php
                                $resunit = $conn->query('SELECT * from myunits');
                                while ($rowunit = $resunit->fetch_assoc()) {?>
                                <option value="<?= $rowunit['id']?>"><?= $rowunit['uname']?></option>
                                <?php } ?>
                                </select>
                                </td>
                                <td><input class="form-control" type="number" readonly name="u_val[]" id="" value="1" step="0.001"></td>
                                <td><input class="form-control" type="text"  name="unit_barcode[]" id="" value="<?= $itmid ?>"></td>
                                <td><input type="number"  name="cost_price[]" class="form-control form-control-sm" value="<?php if(isset($_GET['edit'])){echo $rowitm['cost_price'];}else{echo "0.00";}?>" step="0.001"></td>
                                <td><input type="number"  name="price1[]" class="form-control form-control-sm" value="<?php if(isset($_GET['edit'])){echo $rowitm['price1'];}else{echo "0.00";}?>" step="0.001"></td>
                                <td><input type="number"  name="price2[]" class="form-control form-control-sm" value="<?php if(isset($_GET['edit'])){echo $rowitm['price2'];}else{echo "0.00";}?>" step="0.001"></td>
                                <td><input type="number"  name="market_price[]" class="form-control form-control-sm" value="<?php if(isset($_GET['edit'])){echo $rowitm['market_price'];}else{echo "0.00";}?>" step="0.001"></td>
                                <th><p class="btn btn-danger deleteRow">X</p></th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php }elseif(isset($_GET['edit'])){
                $resunt = $conn->query("SELECT * FROM item_units where item_id = $id");
                ?>
                     <table>
                     <thead>
                                <tr>
                                    <th class="w-80" style="width:120px">الوحـــــدة</th>
                                    <th class="w-80" style="width:120px">م التحويل</th>
                                    <th class="w-80" style="width:120px">باركود</th>
                                    <th class="w-80" style="width:120px">سعر التكلفه</th>
                                    <th class="w-80" style="width:120px">سعر البيع</th>
                                    <th class="w-80" style="width:120px">سعر البيع 2</th>
                                    <th class="w-80" style="width:120px">سعر السوق</th>
                                    <th class="w-80" style="width:120px">حذف</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php while ($rowunt = $resunt->fetch_assoc()) {?>
                                <tr class="urow">
                                <td><select name="unit_id[]" id="" class="form-control">
                                <?php
                                $resunit = $conn->query('SELECT * from myunits');
                                while ($rowunit = $resunit->fetch_assoc()) {
                                    
                                    ?>
                                <option <?php if($rowunit['id'] == $rowunt['unit_id']){echo " selected ";} ?> value="<?= $rowunit['id']?>"><?= $rowunit['uname']?></option>
                                <?php } ?>
                                </select>
                                </td>
                                <td><input class="form-control" type="number" name="u_val[]" id="" value="<?= $rowunt['u_val']?>" step="0.001"></td>
                                <td><input class="form-control" type="text"  name="unit_barcode[]" id="" value="<?= $rowunt['unit_barcode']?>"></td>
                                <td><input type="text"  name="cost_price[]" class="form-control form-control-sm" value="<?= $rowunt['cost_price']?>" step="0.001"></td>
                                <td><input type="number"  name="price1[]" class="form-control form-control-sm" value="<?= $rowunt['price1']?>" step="0.001"></td>
                                <td><input type="number"  name="price2[]" class="form-control form-control-sm" value="<?= $rowunt['price2']?>" step="0.001"></td>
                                <td><input type="number"  name="price3[]" class="form-control form-control-sm" value="<?= $rowunt['price3']?>" step="0.001"></td>
                                <th><p class="btn btn-danger deleteRow">X</p></th>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                            <?php } ?>








               </div>

            
               <div class="card-body">
              
               <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="group1">المجموعة</label>
                      <select name="group1" id="" class="form-control form-control-sm float-right">
                        <option value="">اختر المجموعة</option>
                      <?php
                      $resgroup1 = $conn->query("SELECT * FROM item_group where isdeleted = 0");
                      while($rowgroup1 = $resgroup1->fetch_assoc()){ ?>  
                      
                      <option value="<?= $rowgroup1['id']?>"  <?php if(isset($_GET['edit'])){if($rowgroup1['id'] == $rowitm['group1'] ){echo "selected";}} ?> ><?= $rowgroup1['gname']?></option>
                      <?php } ?>
                      </select>
                    </div>
                  </div>

                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="group2">التصنيف</label>
                      <select name="group2" id="" class="form-control form-control-sm float-right">
                        <option value="">اختر التصنيف</option>
                      <?php
                      $resgroup2 = $conn->query("SELECT * FROM item_group2 where isdeleted = 0");
                      while($rowgroup2 = $resgroup2->fetch_assoc()){ ?>  
                      
                      <option value="<?= $rowgroup2['id']?>" <?php if(isset($_GET['edit'])){if($rowgroup2['id'] == $rowitm['group2'] ){echo "selected";}} ?>><?= $rowgroup2['gname']?></option>
                      <?php } ?>
                      </select>
                    </div>
                  </div>

                </div>
            </div>
            
            <div class="card-body">   
                <?php if(!isset($_GET['edit'])){ ?>    
               <div class="form-group ">
                <label class="btn btn-secondary" for="img">صور للصنف</label>
                <input type="file" name="imgs[]" id="img" class="" >
            </div>
                    <?php }?>
            </div>
            <div class="card-fotter">
                <div class="row">
                <div class="col">
                        <div class="col"><button type="submit" class="btn <?= isset($_GET['edit']) ? "btn-warning" : "btn-primary"; ?>
                        btn-lg float-right btn-block">حفظ</button></div>
                        </div>
                <div class="col"></div>
                </div>
                </div>

                </form>










            <?php if(!isset($_GET['edit'])){ ?>    

            <div class="card-footer">
            <p>iname ,  code , barcode , cost_price , price1 , price2 , qty</p> 
            <div class="col-md-3">
            <form action="do/uploaditems.php" method="post" enctype="multipart/form-data">
            <label for="file">تحميل ورقةاكسيل</label>   
            <input type="file" name="file">
                <button class="btn btn-secondary" type="submit">تحميل </button>
                </form>
            </div>
            </div>
            <?php }?>
            </div>

            


        <?php }else{ echo $userErrorMassage;} ?>

        </div>
    </section>
</div>


<script>


$(document).ready(function() {

// المصفوفة التي تحتوي على أسماء الحقول التي نريد مراقبتها
var fields = ["cost_price", "price1", "price2", "market_price"];

// إضافة وظيفة مراقبة لجميع الحقول في الصف الأول
fields.forEach(function(fieldName) {
    $('.urow:first input[name="' + fieldName + '[]"]').on('input', function() {
        updateAllRows(fieldName);
    });
});

// مراقبة تغيير قيمة u_val في أي صف
$(document).on('input', 'input[name="u_val[]"]', function() {
    // الوصول إلى الصف الحالي ومعامل التحويل
    var currentRow = $(this).closest('.urow');
    var u_val = parseFloat($(this).val()) || 1;

    // تحديث القيم في الحقول بناءً على الصف الأول
    fields.forEach(function(fieldName) {
        var firstRowValue = parseFloat($('.urow:first input[name="' + fieldName + '[]"]').val()) || 0;
        currentRow.find('input[name="' + fieldName + '[]"]').val((firstRowValue * u_val).toFixed(3));
    });
});

// وظيفة لتحديث جميع الصفوف بناءً على الصف الأول
function updateAllRows(fieldName) {
    $('.urow').each(function(index) {
        if (index === 0) return; // تخطي الصف الأول

        // الحصول على الصف الحالي ومعامل التحويل الخاص به
        var currentRow = $(this);
        var u_val_current = parseFloat(currentRow.find('input[name="u_val[]"]').val()) || 1;

        // الحصول على قيمة الحقل من الصف الأول وتطبيق معامل التحويل
        var firstRowValue = parseFloat($('.urow:first input[name="' + fieldName + '[]"]').val()) || 0;
        currentRow.find('input[name="' + fieldName + '[]"]').val((firstRowValue * u_val_current).toFixed(3));
    });
}
});


</script>
<script>
$("form").on("submit", function(e) {
    // Get all selected values from the dropdowns
    let selectedValues = [];
    let duplicateFound = false;

    // Loop through each dropdown
    $('select[name="unit_id[]"]').each(function() {
        let selectedValue = $(this).val();
        if (selectedValues.includes(selectedValue)) {
            duplicateFound = true;
        }
        selectedValues.push(selectedValue);
    });

    // If duplicate is found, prevent form submission and alert the user
    if (duplicateFound) {
        e.preventDefault();
        alert("غير مسموح بتكرار الوحدات");
    }
});


$(document).ready(function() {
    $(document).on('keydown', function(event) {
        if (event.key === "Enter") {
            event.preventDefault(); // Prevent the default action
            console.log('Enter key press prevented!');
        }
    });
});
</script>



<script src="js/additem.js"></script>

<?php include('includes/footer.php') ?>
