<?php include('includes/header.php') ?>
<?php if(isset($_GET['edit'])){
    $id = $_GET['edit'];
    $chk = $conn->query("SELECT * FROM fat_details where item_id = $id")->num_rows;
    
    // إعادة تحميل البيانات المحدثة
    $rowitm = $conn->query("SELECT * FROM myitems where id = $id")->fetch_assoc();
    if ($rowitm == null) {
        header("location:dashboard.php");
    }
} ?>
<?php include('includes/navbar.php') ?>
<?php include('includes/sidebar.php') ?>

<style>
    .modern-card {
        border-radius: 15px;
        border: none;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08) !important;
        transition: transform 0.3s ease;
        overflow: hidden;
    }
    .modern-card:hover {
        transform: translateY(-5px);
    }
    .card-header-gradient {
        background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
        color: white;
        border: none;
        padding: 1.25rem;
    }
    .edit-gradient {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }
    .section-title {
        position: relative;
        padding-bottom: 5px;
        margin-bottom: 20px;
        font-weight: 700;
        color: #1e293b;
    }
    .section-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        right: 0;
        width: 40px;
        height: 3px;
        background: #3b82f6;
        border-radius: 3px;
    }
    .form-control-modern {
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        padding: 0.6rem 1rem;
        height: auto;
        color: #1e293b;
    }
    .form-control-modern:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    .btn-save {
        border-radius: 12px;
        font-weight: 600;
        letter-spacing: 0.5px;
        padding: 12px 30px;
        box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
    }
    .unit-table th {
        background: #f8fafc;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        border-top: none;
    }
    .unit-row {
        transition: background 0.2s;
    }
    .unit-row:hover {
        background-color: #f1f5f9;
    }
    .img-preview-container {
        position: relative;
        width: 120px;
        height: 120px;
        border-radius: 15px;
        border: 2px dashed #cbd5e1;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-top: 10px;
        overflow: hidden;
    }
    .img-preview-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
</style>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <?php if ($role['add_items'] == 1){?>

                <?php if(!isset($_GET['edit'])){?>
                    <form id="myForm" action="do/doadd_item.php" method="post" enctype="multipart/form-data">
                <?php }else{ ?>
                    <form id="myForm" action="do/doedit_item.php?edit=<?= $id ?>" method="post" enctype="multipart/form-data">
                <?php } ?>

                <!-- Header Title -->
                <div class="mb-4 d-flex align-items-center justify-content-between">
                    <div>
                        <h2 class="font-weight-bold mb-0">
                            <i class="fas <?= isset($_GET['edit']) ? 'fa-edit' : 'fa-plus-circle'; ?> mr-2 text-primary"></i>
                            <?= isset($_GET['edit']) ? "تعديل الصنف [ $rowitm[iname] ]" : "إضافة صنف جديد"; ?>
                        </h2>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb bg-transparent p-0 mt-1" style="font-size: 0.9rem;">
                                <li class="breadcrumb-item"><a href="dashboard.php">الرئيسية</a></li>
                                <li class="breadcrumb-item"><a href="items.php">الأصناف</a></li>
                                <li class="breadcrumb-item active"><?= isset($_GET['edit']) ? "تعديل" : "إضافة"; ?></li>
                            </ol>
                        </nav>
                    </div>
                </div>

                <div class="row">
                    <!-- Basic Info Card -->
                    <div class="col-12">
                        <div class="card modern-card mb-4">
                            <div class="card-body p-4">
                                <h5 class="section-title"><i class="fas fa-info-circle ml-2"></i>البيانات الأساسية</h5>
                                
                                <?php 
                                    $rowlstitm = $conn->query("SELECT max(code) FROM myitems ")->fetch_assoc();
                                    if ($rowlstitm['max(code)'] == null ) {
                                      $itmid = 1;
                                    }elseif(isset($_GET['edit'])){$itmid =  $rowitm['code'];
                                    }else {
                                      $itmid = ($rowlstitm['max(code)']+1);
                                    }
                                ?>

                                <div class="row">
                                    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                                        <div class="form-group">
                                            <label class="text-muted small font-weight-bold" for="code">الكود الرقمي</label>
                                            <input readonly value="<?= $itmid ?>" class="form-control form-control-modern bg-light" type="text" name="code" id="code">
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                                        <div class="form-group">
                                            <label class="text-muted small font-weight-bold" for="barcode">الباركود</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-white border-right-0"><i class="fas fa-barcode"></i></span>
                                                </div>
                                                <input required value="<?= $itmid ?>" class="form-control form-control-modern border-left-0" type="text" name="barcode" id="barcode">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-4 col-md-8 col-sm-12 mb-3">
                                        <div class="form-group">
                                            <label class="text-muted small font-weight-bold" for="iname">اسم الصنف <span class="text-danger">*</span></label>
                                            <input list="inamelist" required class="frst form-control form-control-modern" type="text" name="iname" id="iname" value="<?php if(isset($_GET['edit'])){echo $rowitm['iname'];} ?>" placeholder="أدخل اسم الصنف...">
                                            <datalist id="inamelist">
                                                <?php $resname = $conn->query("SELECT iname from myitems order by iname"); while ($rowname = $resname->fetch_assoc()){?>    
                                                    <option value="<?= $rowname['iname']?>"><?= $rowname['iname'] ?></option>
                                                <?php } ?>
                                            </datalist>
                                        </div>
                                    </div>

                                    <div class="col-lg-4 col-md-12 mb-3">
                                        <div class="form-group">
                                            <label class="text-muted small font-weight-bold" for="name2">الاسم اللاتيني / الثاني</label>
                                            <input class="form-control form-control-modern" type="text" name="name2" id="name2" value="<?php if(isset($_GET['edit'])){echo $rowitm['name2'];} ?>" placeholder="اختياري...">
                                        </div>
                                    </div>

                                    <div class="col-12 mb-3">
                                        <div class="form-group">
                                            <label class="text-muted small font-weight-bold" for="info">وصف الصنف / تفاصيل إضافية</label>
                                            <textarea class="form-control form-control-modern" name="info" id="info" rows="1" placeholder="أدخل أي ملاحظات عن الصنف..."><?php if(isset($_GET['edit'])){echo $rowitm['info'];} ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Layout for Units and Sidebar columns -->
                    <div class="col-lg-9 col-12">
                        <!-- Units Table Card -->
                        <div class="card modern-card mb-4">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h5 class="section-title mb-0"><i class="fas fa-layer-group ml-2"></i>وحدات القياس والأسعار</h5>
                                    <button type="button" id="addUnit" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                        <i class="fas fa-plus-circle ml-1"></i>إضافة وحدة أخرى
                                    </button>
                                </div>

                                <div class="table-responsive">
                                    <table class="table unit-table table-borderless">
                                        <thead>
                                            <tr>
                                                <th style="min-width: 150px;">الوحدة</th>
                                                <th style="width: 100px;">معامل التحويل</th>
                                                <th style="min-width: 140px;">باركود الوحدة</th>
                                                <th>سعر التكلفة</th>
                                                <th>البيع 1</th>
                                                <th>البيع 2</th>
                                                <th>سعر السوق</th>
                                                <th style="width: 50px;"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="unitsContainer">
                                            <?php if (!isset($_GET['edit'])) { ?>
                                                <tr class="urow unit-row border-bottom">
                                                    <td>
                                                        <select name="unit_id[]" class="form-control form-control-modern">
                                                            <?php
                                                            $resunit = $conn->query('SELECT * from myunits');
                                                            while ($rowunit = $resunit->fetch_assoc()) {?>
                                                                <option value="<?= $rowunit['id']?>"><?= $rowunit['uname']?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </td>
                                                    <td><input class="form-control form-control-modern" type="number" readonly name="u_val[]" value="1" step="0.001"></td>
                                                    <td><input class="form-control form-control-modern" type="text" name="unit_barcode[]" value="<?= $itmid ?>"></td>
                                                    <td><input type="number" name="cost_price[]" class="form-control form-control-modern" value="0.00" step="0.001"></td>
                                                    <td><input type="number" name="price1[]" class="form-control form-control-modern" value="0.00" step="0.001"></td>
                                                    <td><input type="number" name="price2[]" class="form-control form-control-modern" value="0.00" step="0.001"></td>
                                                    <td><input type="number" name="market_price[]" class="form-control form-control-modern" value="0.00" step="0.001"></td>
                                                    <td class="align-middle text-center"><button type="button" class="btn btn-link text-danger deleteRow"><i class="fas fa-trash-alt"></i></button></td>
                                                </tr>
                                            <?php } else {
                                                $resunt = $conn->query("SELECT * FROM item_units where item_id = $id");
                                                while ($rowunt = $resunt->fetch_assoc()) {?>
                                                    <tr class="urow unit-row border-bottom">
                                                        <td>
                                                            <select name="unit_id[]" class="form-control form-control-modern">
                                                                <?php
                                                                $resunit = $conn->query('SELECT * from myunits');
                                                                while ($rowunit = $resunit->fetch_assoc()) {?>
                                                                    <option <?php if($rowunit['id'] == $rowunt['unit_id']){echo " selected ";} ?> value="<?= $rowunit['id']?>"><?= $rowunit['uname']?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </td>
                                                        <td><input class="form-control form-control-modern" type="number" name="u_val[]" value="<?= $rowunt['u_val']?>" step="0.001"></td>
                                                        <td><input class="form-control form-control-modern" type="text" name="unit_barcode[]" value="<?= $rowunt['unit_barcode']?>"></td>
                                                        <td><input type="number" name="cost_price[]" class="form-control form-control-modern" value="<?= $rowunt['cost_price']?>" step="0.001"></td>
                                                        <td><input type="number" name="price1[]" class="form-control form-control-modern" value="<?= $rowunt['price1']?>" step="0.001"></td>
                                                        <td><input type="number" name="price2[]" class="form-control form-control-modern" value="<?= $rowunt['price2']?>" step="0.001"></td>
                                                        <td><input type="number" name="price3[]" class="form-control form-control-modern" value="<?= $rowunt['price3']?>" step="0.001"></td>
                                                        <td class="align-middle text-center"><button type="button" class="btn btn-link text-danger deleteRow"><i class="fas fa-trash-alt"></i></button></td>
                                                    </tr>
                                                <?php }
                                            } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-12">
                        <!-- Categorization Card -->
                        <div class="card modern-card mb-4">
                            <div class="card-body p-4">
                                <h5 class="section-title small"><i class="fas fa-tags ml-2"></i>التصنيف</h5>
                                
                                <div class="form-group">
                                    <label class="text-muted small font-weight-bold" for="group1">المجموعة</label>
                                    <select name="group1" id="group1" class="form-control form-control-modern shadow-none">
                                        <option value="">اختر المجموعة...</option>
                                        <?php
                                        $resgroup1 = $conn->query("SELECT * FROM item_group where isdeleted = 0");
                                        while($rowgroup1 = $resgroup1->fetch_assoc()){ ?>  
                                            <option value="<?= $rowgroup1['id']?>" <?php if(isset($_GET['edit'])){if($rowgroup1['id'] == $rowitm['group1'] ){echo "selected";}} ?> ><?= $rowgroup1['gname']?></option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="form-group mt-3">
                                    <label class="text-muted small font-weight-bold" for="group2">التصنيف الفرعي</label>
                                    <select name="group2" id="group2" class="form-control form-control-modern shadow-none">
                                        <option value="">اختر التصنيف...</option>
                                        <?php
                                        $resgroup2 = $conn->query("SELECT * FROM item_group2 where isdeleted = 0");
                                        while($rowgroup2 = $resgroup2->fetch_assoc()){ ?>  
                                            <option value="<?= $rowgroup2['id']?>" <?php if(isset($_GET['edit'])){if($rowgroup2['id'] == $rowitm['group2'] ){echo "selected";}} ?>><?= $rowgroup2['gname']?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Image Card -->
                        <div class="card modern-card mb-4">
                            <div class="card-body p-4 text-center">
                                <h5 class="section-title small text-right"><i class="fas fa-image ml-2"></i>عرض الصنف</h5>
                                
                                <div class="d-flex flex-column align-items-center mt-3">
                                    <div class="img-preview-container bg-light" id="imgPreview">
                                        <?php 
                                            $show_current = false;
                                            if(isset($_GET['edit'])){ 
                                                $img_result = $conn->query("SELECT iname FROM imgs WHERE itemid = $id LIMIT 1");
                                                if($img_result && $img_result->num_rows > 0){
                                                    $img_row = $img_result->fetch_assoc();
                                                    $show_current = true;
                                        ?>
                                            <img src="uploads/<?= $img_row['iname'] ?>" alt="صورة الصنف">
                                        <?php } else { echo '<i class="fas fa-camera-retro fa-2x text-muted"></i>'; }
                                            } else { echo '<i class="fas fa-image fa-2x text-muted"></i>'; } ?>
                                    </div>
                                    
                                    <div class="mt-3 w-100">
                                        <label class="btn btn-outline-secondary btn-sm btn-block rounded-pill" for="img">
                                            <i class="fas fa-upload ml-1"></i> <?= $show_current ? "تغيير الصورة" : "رفع صورة"; ?>
                                        </label>
                                        <input type="file" name="imgs[]" id="img" style="display: none;" onchange="previewImage(this)">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Footer -->
                <div class="card modern-card mb-5">
                    <div class="card-body p-4 bg-light d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            <i class="fas fa-info-circle ml-1"></i> يرجى التأكد من صحة البيانات قبل الحفظ. الحقول المميزة بـ <span class="text-danger">*</span> مطلوبة.
                        </div>
                        <button type="submit" class="btn btn-save <?= isset($_GET['edit']) ? "btn-warning" : "btn-primary"; ?> btn-lg px-5">
                            <i class="fas fa-save ml-2"></i> <?= isset($_GET['edit']) ? "تحديث الصنف" : "حفظ الصنف الجديد"; ?>
                        </button>
                    </div>
                </div>

                </form>

                <?php if(!isset($_GET['edit'])){ ?>    
                    <!-- Bulk Upload Card -->
                    <div class="card modern-card border-left-info shadow-none" style="border-left: 5px solid #17a2b8;">
                        <div class="card-body p-4">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h5 class="font-weight-bold text-info ml-2"><i class="fas fa-file-excel ml-2"></i>استيراد من إكسيل (Bulk Upload)</h5>
                                    <p class="text-muted small mb-0">يمكنك تحميل جميع الأصناف دفعة واحدة باستخدام ملف إكسيل. يجب أن يحتوي الملف على الأعمدة التالية بالترتيب: <b>iname, code, barcode, cost_price, price1, price2, qty</b></p>
                                </div>
                                <div class="col-md-4 mt-3 mt-md-0">
                                    <form action="do/uploaditems.php" method="post" enctype="multipart/form-data" class="d-flex align-items-center">
                                        <div class="custom-file mr-2">
                                            <input type="file" class="custom-file-input" name="file" id="excelFile">
                                            <label class="custom-file-label" for="excelFile">اختر ملف...</label>
                                        </div>
                                        <button class="btn btn-info px-4" type="submit">تحميل</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php }?>

            <?php }else{ echo "<div class='alert alert-danger'>$userErrorMassage</div>"; } ?>
        </div>
    </section>
</div>

<script>
$(document).ready(function() {
    // مراقبة الحقول وتحديث جميع الصفوف بناءً على معامل التحويل
    var fields = ["cost_price", "price1", "price2", "market_price"];

    // إضافة وظيفة مراقبة لجميع الحقول في الصف الأول
    fields.forEach(function(fieldName) {
        $(document).on('input', '.urow:first input[name="' + fieldName + '[]"]', function() {
            updateAllRows(fieldName);
        });
    });

    // مراقبة تغيير قيمة u_val في أي صف
    $(document).on('input', 'input[name="u_val[]"]', function() {
        var currentRow = $(this).closest('.urow');
        var u_val = parseFloat($(this).val()) || 1;

        fields.forEach(function(fieldName) {
            var firstRowValue = parseFloat($('.urow:first input[name="' + fieldName + '[]"]').val()) || 0;
            currentRow.find('input[name="' + fieldName + '[]"]').val((firstRowValue * u_val).toFixed(3));
        });
    });

    // وظيفة لتحديث جميع الصفوف بناءً على الصف الأول
    function updateAllRows(fieldName) {
        var firstRowValue = parseFloat($('.urow:first input[name="' + fieldName + '[]"]').val()) || 0;
        
        $('.urow').each(function(index) {
            if (index === 0) return; // تخطي الصف الأول
            var currentRow = $(this);
            var u_val_current = parseFloat(currentRow.find('input[name="u_val[]"]').val()) || 1;
            currentRow.find('input[name="' + fieldName + '[]"]').val((firstRowValue * u_val_current).toFixed(3));
        });
    }

    // منع الإرسال في حالة تكرار الوحدات
    $("form").on("submit", function(e) {
        let selectedValues = [];
        let duplicateFound = false;

        $('select[name="unit_id[]"]').each(function() {
            let selectedValue = $(this).val();
            if (selectedValue && selectedValues.includes(selectedValue)) {
                duplicateFound = true;
            }
            selectedValues.push(selectedValue);
        });

        if (duplicateFound) {
            e.preventDefault();
            Swal.fire ? Swal.fire("خطأ", "غير مسموح بتكرار الوحدات لنفس الصنف", "error") : alert("غير مسموح بتكرار الوحدات");
        }
    });

    // منع مفتاح Enter من إرسال النموذج تلقائياً
    $(document).on('keydown', 'input', function(event) {
        if (event.key === "Enter") {
            event.preventDefault();
        }
    });

    // تحسين مظهر رفع الملفات
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });
});

// معاينة الصورة المرفوعة
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('#imgPreview').html('<img src="' + e.target.result + '" alt="صورة الصنف">');
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<script src="js/additem.js"></script>

<?php include('includes/footer.php') ?>
