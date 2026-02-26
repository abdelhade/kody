<?php include('includes/header.php') ?>
<?php 
if(isset($_GET['edit'])){
    $id = $_GET['edit'];
    $rowitm = $conn->query("SELECT * FROM myitems where id = $id")->fetch_assoc();
    if ($rowitm == null) {
        header("location:dashboard.php");
    }
} 
?>
<?php include('includes/navbar.php') ?>
<?php include('includes/sidebar.php') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <?php if ($role['add_items'] == 1){?>

                <?php if(!isset($_GET['edit'])){?>
                    <form action="do/doadd_item.php" method="post" enctype="multipart/form-data">
                <?php }else{ ?>
                    <form action="do/doedit_item.php?edit=<?= $id ?>" method="post" enctype="multipart/form-data">
                <?php } ?>

                <div class="card">
                    <div class="card-header bg-primary">
                        <h3><?= isset($_GET['edit']) ? "تعديل صنف" : "إضافة صنف"; ?></h3>
                    </div>

                    <div class="card-body">
                        
                        <?php 
                            $rowlstitm = $conn->query("SELECT max(code) FROM myitems ")->fetch_assoc();
                            if ($rowlstitm['max(code)'] == null ) {
                              $itmid = 1;
                            }elseif(isset($_GET['edit'])){
                                $itmid =  $rowitm['code'];
                            }else {
                              $itmid = ($rowlstitm['max(code)']+1);
                            }
                        ?>

                        <div class="row">
                            <div class="col-md-2">
                                <label>الكود</label>
                                <input readonly value="<?= $itmid ?>" class="form-control" type="text" name="code">
                            </div>
                            
                            <div class="col-md-2">
                                <label>الباركود</label>
                                <input required value="<?= $itmid ?>" class="form-control" type="text" name="barcode">
                            </div>

                            <div class="col-md-4">
                                <label>اسم الصنف <span class="text-danger">*</span></label>
                                <input required class="form-control" type="text" name="iname" value="<?php if(isset($_GET['edit'])){echo htmlspecialchars($rowitm['iname']);} ?>">
                            </div>

                            <div class="col-md-4">
                                <label>الاسم الثاني</label>
                                <input class="form-control" type="text" name="name2" value="<?php if(isset($_GET['edit'])){echo htmlspecialchars($rowitm['name2']);} ?>">
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-4">
                                <label>المجموعة</label>
                                <select name="group1" class="form-control">
                                    <option value="">اختر</option>
                                    <?php
                                    $resgroup1 = $conn->query("SELECT * FROM item_group where isdeleted = 0");
                                    while($rowgroup1 = $resgroup1->fetch_assoc()){ ?>  
                                        <option value="<?= $rowgroup1['id']?>" <?php if(isset($_GET['edit']) && $rowgroup1['id'] == $rowitm['group1']){echo "selected";} ?>><?= htmlspecialchars($rowgroup1['gname'])?></option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label>التصنيف</label>
                                <select name="group2" class="form-control">
                                    <option value="">اختر</option>
                                    <?php
                                    $resgroup2 = $conn->query("SELECT * FROM item_group2 where isdeleted = 0");
                                    while($rowgroup2 = $resgroup2->fetch_assoc()){ ?>  
                                        <option value="<?= $rowgroup2['id']?>" <?php if(isset($_GET['edit']) && $rowgroup2['id'] == $rowitm['group2']){echo "selected";} ?>><?= htmlspecialchars($rowgroup2['gname'])?></option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label>صورة</label>
                                <input type="file" name="imgs[]" class="form-control">
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <label>ملاحظات</label>
                                <textarea class="form-control" name="info" rows="2"><?php if(isset($_GET['edit'])){echo htmlspecialchars($rowitm['info']);} ?></textarea>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-between mb-2">
                            <h5>الوحدات والأسعار</h5>
                            <button type="button" id="addUnit" class="btn btn-sm btn-primary">+ إضافة</button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 150px;">الوحدة</th>
                                        <th style="width: 100px;">المعامل</th>
                                        <th style="width: 120px;">الباركود</th>
                                        <th style="width: 100px;">التكلفة</th>
                                        <th style="width: 100px;">البيع 1</th>
                                        <th style="width: 100px;">البيع 2</th>
                                        <th style="width: 100px;">السوق</th>
                                        <th style="width: 60px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="unitsContainer">
                                    <?php if (!isset($_GET['edit'])) { ?>
                                        <tr class="urow">
                                            <td>
                                                <select name="unit_id[]" class="form-control form-control-sm">
                                                    <?php
                                                    $resunit = $conn->query('SELECT * from myunits');
                                                    while ($rowunit = $resunit->fetch_assoc()) {?>
                                                        <option value="<?= $rowunit['id']?>"><?= htmlspecialchars($rowunit['uname'])?></option>
                                                    <?php } ?>
                                                </select>
                                            </td>
                                            <td><input class="form-control form-control-sm" type="number" readonly name="u_val[]" value="1" step="0.001"></td>
                                            <td><input class="form-control form-control-sm" type="text" name="unit_barcode[]" value="<?= $itmid ?>"></td>
                                            <td><input type="number" name="cost_price[]" class="form-control form-control-sm" value="0" step="0.001"></td>
                                            <td><input type="number" name="price1[]" class="form-control form-control-sm" value="0" step="0.001"></td>
                                            <td><input type="number" name="price2[]" class="form-control form-control-sm" value="0" step="0.001"></td>
                                            <td><input type="number" name="market_price[]" class="form-control form-control-sm" value="0" step="0.001"></td>
                                            <td class="text-center"><button type="button" class="btn btn-sm btn-danger deleteRow">×</button></td>
                                        </tr>
                                <?php } else {
                                    $resunt = $conn->query("SELECT * FROM item_units where item_id = $id");
                                    while ($rowunt = $resunt->fetch_assoc()) {?>
                                        <tr class="urow">
                                            <td>
                                                <select name="unit_id[]" class="form-control form-control-sm">
                                                    <?php
                                                    $resunit = $conn->query('SELECT * from myunits');
                                                    while ($rowunit = $resunit->fetch_assoc()) {?>
                                                        <option <?php if($rowunit['id'] == $rowunt['unit_id']){echo "selected";} ?> value="<?= $rowunit['id']?>"><?= htmlspecialchars($rowunit['uname'])?></option>
                                                    <?php } ?>
                                                </select>
                                            </td>
                                            <td><input class="form-control form-control-sm" type="number" name="u_val[]" value="<?= $rowunt['u_val']?>" step="0.001"></td>
                                            <td><input class="form-control form-control-sm" type="text" name="unit_barcode[]" value="<?= htmlspecialchars($rowunt['unit_barcode'])?>"></td>
                                            <td><input type="number" name="cost_price[]" class="form-control form-control-sm" value="<?= $rowunt['cost_price']?>" step="0.001"></td>
                                            <td><input type="number" name="price1[]" class="form-control form-control-sm" value="<?= $rowunt['price1']?>" step="0.001"></td>
                                            <td><input type="number" name="price2[]" class="form-control form-control-sm" value="<?= $rowunt['price2']?>" step="0.001"></td>
                                            <td><input type="number" name="price3[]" class="form-control form-control-sm" value="<?= $rowunt['price3']?>" step="0.001"></td>
                                            <td class="text-center"><button type="button" class="btn btn-sm btn-danger deleteRow">×</button></td>
                                        </tr>
                                    <?php }
                                } ?>
                                </tbody>
                            </table>
                        </div>

                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-<?= isset($_GET['edit']) ? 'warning' : 'primary'; ?>">
                            <i class="fas fa-save"></i> <?= isset($_GET['edit']) ? "تحديث" : "حفظ"; ?>
                        </button>
                        <a href="myitems.php" class="btn btn-secondary">إلغاء</a>
                    </div>
                </div>

                </form>

                <?php if(!isset($_GET['edit'])){ ?>    
                    <div class="card mt-3">
                        <div class="card-header bg-info">
                            <h5>استيراد من إكسيل</h5>
                        </div>
                        <div class="card-body">
                            <form action="do/uploaditems.php" method="post" enctype="multipart/form-data" class="form-inline">
                                <input type="file" class="form-control mr-2" name="file" required style="flex: 1;">
                                <button class="btn btn-info" type="submit">تحميل</button>
                            </form>
                        </div>
                    </div>
                <?php }?>

            <?php }else{ 
                echo '<div class="alert alert-danger">ليس لديك صلاحية</div>'; 
            } ?>
        </div>
    </section>
</div>

<script>
$(document).ready(function() {
    var fields = ["cost_price", "price1", "price2", "market_price"];

    fields.forEach(function(fieldName) {
        $(document).on('input', '.urow:first input[name="' + fieldName + '[]"]', function() {
            var firstRowValue = parseFloat($(this).val()) || 0;
            $('.urow').each(function(index) {
                if (index === 0) return;
                var u_val = parseFloat($(this).find('input[name="u_val[]"]').val()) || 1;
                $(this).find('input[name="' + fieldName + '[]"]').val((firstRowValue * u_val).toFixed(3));
            });
        });
    });

    $(document).on('input', 'input[name="u_val[]"]', function() {
        var currentRow = $(this).closest('.urow');
        var u_val = parseFloat($(this).val()) || 1;
        fields.forEach(function(fieldName) {
            var firstRowValue = parseFloat($('.urow:first input[name="' + fieldName + '[]"]').val()) || 0;
            currentRow.find('input[name="' + fieldName + '[]"]').val((firstRowValue * u_val).toFixed(3));
        });
    });

    $("form").on("submit", function(e) {
        let selectedValues = [];
        let duplicateFound = false;
        $('select[name="unit_id[]"]').each(function() {
            let val = $(this).val();
            if (val && selectedValues.includes(val)) duplicateFound = true;
            selectedValues.push(val);
        });
        if (duplicateFound) {
            e.preventDefault();
            alert("غير مسموح بتكرار الوحدات");
        }
    });
});
</script>

<script src="js/additem.js"></script>
<?php include('includes/footer.php') ?>
