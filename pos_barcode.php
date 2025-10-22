<?php 

include('includes/header.php');

// إضافة طاولات تجريبية إذا لم تكن موجودة
$check_tables = $conn->query("SELECT COUNT(*) as count FROM tables WHERE isdeleted = 0");
if ($check_tables) {
    $tables_count = $check_tables->fetch_assoc()['count'];
    if ($tables_count == 0) {
        for ($i = 1; $i <= 12; $i++) {
            $table_name = "طاولة " . $i;
            $conn->query("INSERT INTO tables (tname, table_case) VALUES ('$table_name', 0)");
        }
    }
}

// جلب البيانات الأساسية
$posdate = date('Y-m-d', strtotime('-4 hours'));
$rowstg = $conn->query("SELECT * FROM settings WHERE id = 1")->fetch_assoc();

if(isset($_GET['edit'])){
    $id = $_GET['edit'];
    $rowed = $conn->query("SELECT * FROM ot_head where id = $id")->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نظام نقاط البيع - POS System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="dist/css/pos.css" rel="stylesheet">
<style>
        :root {
            --bs-primary: #0a7ea4;
            --bs-primary-rgb: 10, 126, 164;
        }
        
        .bg-primary {
            background-color: #0a7ea4 !important;
        }
        
        .btn-primary {
            background-color: #0a7ea4 !important;
            border-color: #0a7ea4 !important;
        }
        
        .btn-primary:hover {
            background-color: #086482 !important;
            border-color: #086482 !important;
        }
        
        .btn-outline-primary {
            color: #0a7ea4 !important;
            border-color: #0a7ea4 !important;
        }
        
        .btn-outline-primary:hover {
            background-color: #0a7ea4 !important;
            border-color: #0a7ea4 !important;
            color: #fff !important;
        }
        
        .text-primary {
            color: #0a7ea4 !important;
        }
        
        .border-primary {
            border-color: #0a7ea4 !important;
        }
        
        .bg-primary.bg-opacity-10 {
            background-color: rgba(10, 126, 164, 0.1) !important;
        }
        
        .navbar-dark .navbar-brand,
        .navbar-dark .nav-link {
            color: #fff !important;
        }
        
        .input-group-text.bg-primary {
            background-color: #0a7ea4 !important;
            border-color: #0a7ea4 !important;
        }
        
        .badge.text-primary {
            color: #0a7ea4 !important;
        }
        
        .card-header.bg-primary {
            background-color: #0a7ea4 !important;
        }
        
        .modal-header.bg-primary {
            background-color: #0a7ea4 !important;
        }
        
        .btn-check:checked + .btn-outline-primary {
            background-color: #0a7ea4 !important;
            border-color: #0a7ea4 !important;
        }
        
        /* تحسينات للشاشات الصغيرة */
        body {
            font-size: 0.85rem;
        }
        
        .navbar {
            padding: 0.25rem 0.5rem;
        }
        
        .navbar-brand {
            font-size: 1rem;
        }
        
        .nav-link {
            font-size: 0.8rem;
            padding: 0.25rem 0.5rem;
        }
        
        .card-header h6 {
            font-size: 0.85rem !important;
        }
        
        .btn-sm {
            font-size: 0.7rem;
            padding: 0.2rem 0.4rem;
        }
        
        .form-control-sm, .form-select-sm {
            font-size: 0.7rem;
            padding: 0.2rem 0.4rem;
        }
        
        .badge {
            font-size: 0.65rem;
        }
        
        h5 {
            font-size: 1rem;
        }
        
        h6 {
            font-size: 0.85rem;
        }
        
        .category-btn {
            font-size: 0.7rem !important;
            padding: 0.25rem 0.5rem !important;
        }
        
        .item-card .card-title {
            font-size: 0.75rem !important;
        }
        
        .item-card .card-text {
            font-size: 0.85rem !important;
        }
        
        .item-image-container {
            height: 100px !important;
        }
        
        .container-fluid {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }
        
        .modal-header {
            padding: 0.5rem 1rem;
        }
        
        .modal-body {
            padding: 1rem;
        }
        
        .modal-title {
            font-size: 1rem;
    }
</style>
</head>
<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="#">
                <i class="fas fa-cash-register me-2"></i>نظام نقاط البيع
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a href="index.php" class="nav-link">
                            <i class="fas fa-home me-1"></i>الرئيسية
                        </a>
    </li>
                    <li class="nav-item">
                        <a href="tables.php" class="nav-link">
                            <i class="fas fa-th-large me-1"></i>الطاولات
                        </a>
    </li>
                    <li class="nav-item">
                        <a href="pos_tables.php" class="nav-link">
                            <i class="fas fa-desktop me-1"></i>POS متكامل
                        </a>
    </li>   
  </ul>

                <ul class="navbar-nav">
                    <li class="nav-item">
                        <button class="btn btn-outline-light btn-sm me-2" id="fullscreenBtn" title="ملء الشاشة">
                            <i class="fas fa-expand"></i>
                        </button>
                    </li>
                    <li class="nav-item">
                        <a href="do/do_logout.php" class="nav-link">
                            <i class="fas fa-sign-out-alt me-1"></i>تسجيل الخروج
                        </a>
                    </li>
                </ul>
            </div>
        </div>
</nav>

    <!-- Main Content -->
    <form action="do/doadd_invoice.php" method="post" id="posForm">
    <div class="container-fluid h-100" style="height: calc(100vh - 60px);">
        <div class="row h-100 g-1">
            <!-- القسم الأيمن - معلومات الطلب -->
            <div class="col-lg-4">
                <div class="card shadow-sm h-100 d-flex flex-column">
                    <div class="card-header bg-primary text-white py-2">
                        <h6 class="mb-0">
                            <i class="fas fa-shopping-cart me-2"></i>معلومات الطلب
                        </h6>
                    </div>
                    <div class="card-body flex-grow-1 overflow-auto d-flex flex-column">
                            <!-- Hidden Fields -->
                            <input type="hidden" name="pro_tybe" value="9">
                            <input type="hidden" name="pro_serial" value="0">
                            <input type="hidden" name="pro_id" value="1">
                            <input type="hidden" id="selected_table_id" name="table_id">
                            <input type="hidden" id="current_order_id" name="order_id">

                            <!-- نوع الطلب -->
                            <div class="mb-2">
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" id="age1" name="age" value="1" checked>
                                    <label class="btn btn-outline-primary btn-sm" for="age1">
                                        <i class="fas fa-shopping-bag me-1"></i>تيك أواي
                                    </label>
                                    
                                    <input type="radio" class="btn-check" id="age2" name="age" value="2" <?php if (isset($_GET['table'])) {echo " checked ";} ?>>
                                    <label class="btn btn-outline-primary btn-sm" for="age2">
                                        <i class="fas fa-chair me-1"></i>طاولة
                                    </label>
                                    
                                    <input type="radio" class="btn-check" id="age3" name="age" value="3">
                                    <label class="btn btn-outline-primary btn-sm" for="age3">
                                        <i class="fas fa-motorcycle me-1"></i>دليفري
                                    </label>
                                </div>
                            </div>

                            <!-- الباركود والبحث -->
                            <div class="row g-1 mb-2">
                                <!-- البحث -->
                                <div class="col-6">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">
                                            <i class="fas fa-search"></i>
                                        </span>
                                        <input type="text" class="scnd form-control" id="searchInput" 
                                               placeholder="ابحث عن الصنف..." title="البحث">
                                    </div>
                                </div>
                                
                                <!-- الباركود -->
                                <div class="col-6">
                                    <input type="text" class="form-control form-control-sm frst" 
                                           placeholder="امسح الباركود..." 
                                           id="barcodeInput" title="قارئ الباركود"
                                           style="border: 2px solid #28a745; background: #f8fff8;">
                                </div>
                            </div>

                            <!-- الحقول الثانوية - في الناحية التانية -->
                            <div class="row g-1 mb-2">
                                <!-- التواريخ -->
                                <div class="col-4">
                                    <input type="date" name="pro_date" class="form-control form-control-sm" 
                                           value="<?= $posdate ?>" title="التاريخ" style="font-size: 0.75rem;">
                                </div>
                                <div class="col-4">
                                    <input type="date" name="accural_date" class="form-control form-control-sm" 
                                           value="<?php echo isset($_GET['edit']) ? $rowed['accural_date'] : date('Y-m-d'); ?>" 
                                           title="تاريخ الاستحقاق" style="font-size: 0.75rem;">
                                </div>

                                <!-- اختيار الطاولة -->
                                <div class="col-4">
                                    <button type="button" class="btn btn-outline-primary btn-sm w-100" 
                                            data-bs-toggle="modal" data-bs-target="#tablesModal"
                                            title="اختر الطاولة" style="font-size: 0.75rem;">
                                        <i class="fas fa-chair me-1"></i>
                                        <span id="selected_table_display">اختر طاولة</span>
                                    </button>
                                    <input type="hidden" id="selected_table_id" name="table_id" value="">
                                    <input type="hidden" id="selected_table_name" name="table_name" value="">
                                    <input type="hidden" id="selected_order_id" name="edit" value="">
                                </div>
                            </div>

                            <!-- الحقول الصغيرة -->
                            <div class="row g-1 mb-2">
                                <!-- المخزن -->
                                <div class="col-3">
                                    <select name="store_id" class="form-select form-select-sm" title="المخزن" style="font-size: 0.75rem;">
                                        <?php
                                        $resstore = $conn->query("SELECT * FROM `acc_head` WHERE is_stock =1 AND isdeleted = 0;");
                                        while ($rowstore = $resstore->fetch_assoc()) { ?>
                                            <option <?php if($rowstg['def_pos_store'] == $rowstore['id']){echo "selected";} ?> 
                                                    value="<?= $rowstore['id'] ?>"><?= $rowstore['aname'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <!-- الموظف -->
                                <div class="col-3">
                                    <select name="emp_id" class="form-select form-select-sm" title="الموظف" style="font-size: 0.75rem;">
                                        <?php
                                        $resemp = $conn->query("SELECT * FROM `acc_head` WHERE parent_id = 35 AND is_basic = 0 AND isdeleted = 0;");
                                        while ($rowemp = $resemp->fetch_assoc()) { ?>
                                            <option <?php if($rowstg['def_pos_employee'] == $rowemp['id']){echo " selected ";} ?> 
                                                    <?php if(isset($_GET['edit']) && $rowed['emp_id'] == $rowemp['id']){echo " selected ";} ?>  
                                                    value="<?= $rowemp['id'] ?>"><?= $rowemp['aname'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <!-- العميل -->
                                <div class="col-3">
                                    <select name="acc2_id" class="form-select form-select-sm" title="العميل" style="font-size: 0.75rem;">
                                        <?php
                                        $resclient = $conn->query("SELECT * FROM `acc_head` WHERE code like '122%'  AND is_basic = 0 AND isdeleted = 0;");
                                        if(isset($_GET['edit'])){$rowed = $conn->query("SELECT * FROM ot_head where id = $id")->fetch_assoc();};
                                        while ($rowclient = $resclient->fetch_assoc()) { ?>
                                            <option <?php if($rowstg['def_pos_client'] == $rowclient['id']){echo " selected ";} ?>
                                                    <?php if(isset($_GET['edit']) && $rowed['acc1'] == $rowclient['id']){echo " selected ";} ?>
                                                    value="<?= $rowclient['id'] ?>"><?= $rowclient['aname'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <!-- الصندوق -->
                                <div class="col-3">
                                    <select name="fund_id" class="form-select form-select-sm" title="الصندوق" style="font-size: 0.75rem;">
                                        <?php
                                        if(isset($_GET['edit'])){$rowed = $conn->query("SELECT * FROM ot_head where id = $id")->fetch_assoc();};
                                        $resfund = $conn->query("SELECT * FROM `acc_head` WHERE is_fund =1 AND is_basic = 0 AND isdeleted = 0;");
                                        while ($rowfund = $resfund->fetch_assoc()) { ?>
                                            <option <?php if($rowstg['def_pos_fund'] == $rowfund['id']){echo " selected ";} ?>
                                                    <?php if((isset($_GET['edit'])) && $rowed['acc_fund'] == $rowfund['id']){echo " selected ";} ?>
                                                    value="<?= $rowfund['id'] ?>"><?= $rowfund['aname'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <!-- الأصناف المُضافة -->
                            <div class="mb-2 flex-grow-1 d-flex flex-column">
                                <div class="card flex-grow-1 d-flex flex-column border-primary">
                                    <div class="card-header bg-gradient bg-primary text-white py-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0" style="font-size: 0.95rem;">
                                                <i class="fas fa-shopping-cart me-2"></i>الأصناف المُضافة
                                            </h6>
                                            <span class="badge bg-white text-primary" id="itemCount">0</span>
                                        </div>
                                    </div>
                                    <div class="card-body p-1 flex-grow-1" style="min-height: 40vh; max-height: 40vh; overflow-y: auto; overflow-x: auto; background: #f8f9fa;" id="itemData">
                                        <?php
                                        if (isset($_GET['edit'])){
    $id = $_GET['edit'];
                                            $sqldet = "SELECT * FROM fat_details where pro_id = $id AND isdeleted  = 0";
                                            $resdet = $conn->query($sqldet);
                                            $x = 0;
                                            while ($rowdet = $resdet->fetch_assoc()) {
                                                $x++;
                                                // Display edit mode items here
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>

                            <!-- قسم الدفع والحسابات -->
                            <div class="card border-primary mt-1">
                                <div class="card-header bg-primary text-white py-1">
                                    <h6 class="mb-0" style="font-size: 0.8rem;">
                                        <i class="fas fa-calculator me-1"></i>الحسابات والدفع
                                    </h6>
                                </div>
                                <div class="card-body p-1">
                                    <!-- الإجمالي والصافي -->
                                    <div class="row g-1 mb-1">
                                        <div class="col-6 text-center">
                                            <small class="text-muted d-block" style="font-size: 0.65rem;">الإجمالي</small>
                                            <h5 class="mb-0 text-primary" id="total_display" style="font-size: 0.9rem;">0.00 ج.م</h5>
                                            <input type="hidden" name="headtotal" id="total" value="0.00">
                                            <input name="headplus" type="hidden">
                                        </div>
                                        <div class="col-6 text-center">
                                            <small class="text-muted d-block" style="font-size: 0.65rem;">الصافي</small>
                                            <h5 class="mb-0 text-success" id="net_display" style="font-size: 0.9rem;">0.00 ج.م</h5>
                                            <input type="hidden" name="headnet" id="net_val" value="0">
                                            <input type="hidden" name="headdisc" id="discount" value="0">
                                        </div>
                                    </div>
                                    
                                    <!-- ملاحظات -->
                                    <div class="mb-1">
                                        <textarea class="form-control form-control-sm" name="info" id="info" rows="1" 
                                                  placeholder="ملاحظات..." style="font-size: 0.7rem; padding: 0.2rem;"></textarea>
                                    </div>
                                    
                                    <!-- أزرار الإجراءات -->
                                    <div class="d-flex gap-1 justify-content-between align-items-center">
                                        <button type="button" class="btn btn-primary flex-grow-1" data-bs-toggle="modal" data-bs-target="#paymentModal" style="font-size: 0.8rem; padding: 0.4rem;">
                                            <i class="fas fa-money-bill-wave me-1"></i>دفع وحفظ
                                            <div style="font-size: 0.7rem; font-weight: bold;" id="total_display_btn">0.00 ج.م</div>
                                        </button>
                                        <a href="tables.php" class="btn btn-outline-primary" style="font-size: 0.7rem; padding: 0.4rem 0.6rem;" title="الطاولات">
                                            <i class="fas fa-th-large"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-danger" style="font-size: 0.7rem; padding: 0.4rem 0.6rem;" onclick="clearAllItems();" title="مسح">
                                            <i class="fas fa-eraser"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                    </div>
                </div>
            </div>

            <!-- القسم الأوسط - الأصناف -->
            <div class="col-lg-8">
                <div class="card shadow-sm h-100 d-flex flex-column">
                    <div class="card-header bg-primary text-white py-2">
                        <h6 class="mb-0">
                            <i class="fas fa-boxes me-2"></i>الأصناف المتاحة
                        </h6>
                    </div>
                    <div class="card-body flex-grow-1 overflow-auto">
                        <!-- التصنيفات -->
                        <div class="mb-2">
                            <div class="d-flex flex-wrap gap-1" id="categoriesContainer">
                                <?php
                                $rescategories = $conn->query("SELECT * FROM item_group WHERE isdeleted = 0 ORDER BY gname");
                                if ($rescategories && $rescategories->num_rows > 0) {
                                    // زر "الكل"
                                    echo '<button class="btn btn-primary btn-sm category-btn active" data-category="all">
                                            <i class="fas fa-th me-1"></i>الكل
                                          </button>';
                                    
                                    while ($rowcategory = $rescategories->fetch_assoc()) {
                                        $categoryId = isset($rowcategory['id']) ? $rowcategory['id'] : '';
                                        $categoryName = isset($rowcategory['gname']) ? htmlspecialchars($rowcategory['gname']) : '';
                                        echo '<button class="btn btn-outline-primary btn-sm category-btn" data-category="'.$categoryId.'">
                                                <i class="fas fa-folder me-1"></i>'.$categoryName.'
                                              </button>';
                                    }
                                } else {
                                    echo '<button class="btn btn-primary btn-sm category-btn active" data-category="all">
                                            <i class="fas fa-th me-1"></i>الكل
                                          </button>';
                                }
                                ?>
                            </div>
                        </div>

                        <!-- شبكة الأصناف -->
                        <div class="row g-2" id="itemsGrid">
                            <?php
                            // استعلام مع join للحصول على الصورة من جدول imgs
                            $sqlitems = "SELECT m.*, i.iname as img_filename
                                        FROM myitems m 
                                        LEFT JOIN imgs i ON i.itemid = m.id 
                                        WHERE m.isdeleted = 0 
                                        GROUP BY m.id
                                        ORDER BY m.iname";
                            $resitems = $conn->query($sqlitems);
                            
                            if ($resitems && $resitems->num_rows > 0) {
                                while ($rowitem = $resitems->fetch_assoc()) {
                                    $itemId = isset($rowitem['id']) ? $rowitem['id'] : '';
                                    $itemName = isset($rowitem['iname']) ? htmlspecialchars($rowitem['iname']) : 'صنف غير محدد';
                                    
                                    // تحديد السعر - جرب price1 أو price
                                    $itemPrice = 0;
                                    if (isset($rowitem['price1']) && !empty($rowitem['price1'])) {
                                        $itemPrice = floatval($rowitem['price1']);
                                    } elseif (isset($rowitem['price']) && !empty($rowitem['price'])) {
                                        $itemPrice = floatval($rowitem['price']);
                                    }
                                    
                                    $itemBarcode = isset($rowitem['barcode']) ? htmlspecialchars($rowitem['barcode']) : '';
                                    $itemCategory = isset($rowitem['group1']) ? $rowitem['group1'] : '';
                                    
                                    // الصورة من جدول imgs
                                    $itemImage = '';
                                    if (isset($rowitem['img_filename']) && !empty($rowitem['img_filename'])) {
                                        $itemImage = 'uploads/' . htmlspecialchars($rowitem['img_filename']);
                                    }
                                    
                                    $itemDesc = isset($rowitem['info']) ? htmlspecialchars($rowitem['info']) : '';
                            ?>
                                <div class="col-lg-3  col-md-4 col-sm-6 item-wrapper" data-category="<?= $itemCategory ?>">
                                    <div class="card item-card itemButton  shadow-sm border-0" 
                                         data-item-id="<?= $itemId ?>" 
                                         data-item-name="<?= $itemName ?>" 
                                         data-item-price="<?= $itemPrice ?>"
                                         data-item-barcode="<?= $itemBarcode ?>"
                                         data-item-desc="<?= $itemDesc ?>"
                                         style="transition: all 0.3s ease;">
                                        <div class="card-body p-2 text-center">
                                            <!-- الصورة -->
                                            <div class="item-image-container mb-2 ratio ratio-1x1 rounded overflow-hidden" style="cursor: pointer; background: #f8f9fa;">
                                                <?php if (!empty($itemImage) && file_exists($itemImage)): ?>
                                                    <img src="<?= $itemImage ?>" 
                                                        
                                                         class="item-image-click object-fit-cover w-100 h-100"
                                                         style="width: 100%; height: 100%;">
                                                <?php else: ?>
                                                    <div class="d-flex align-items-center justify-content-center item-image-click">
                                                        <i class="fas fa-utensils fa-3x text-primary opacity-50"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <!-- اسم الصنف -->
                                            <h6 class="card-title text-truncate mb-1" style="font-size: 0.85rem;" title="<?= $itemName ?>">
                                                <?= $itemName ?>
                                            </h6>
                                            
                                            <!-- السعر -->
                                            <div class="bg-primary bg-opacity-10 rounded px-2 py-1 mb-2">
                                                <p class="card-text fw-bold text-dark mb-0" style="font-size: 1.1rem;">
                                                    <?= number_format($itemPrice, 2) ?> <span class="text-primary">ج.م</span>
                                                </p>
                                            </div>
                                            
                                            <!-- زر التفاصيل -->
                                            <button class="btn btn-outline-primary btn-sm w-100 item-details-btn" 
                                                    style="font-size: 0.75rem;">
                                                <i class="fas fa-info-circle me-1"></i>التفاصيل
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php 
                                }
                            } else {
                                echo '<div class="col-12 text-center text-muted"><p>لا توجد أصناف متاحة</p></div>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </form>

    <!-- Modal الدفع -->
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="paymentModalLabel">
                        <i class="fas fa-cash-register me-2"></i>الدفع والإجماليات
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <!-- الإجمالي -->
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-4">
                                            <label class="mb-0 fw-bold text-primary">
                                                <i class="fas fa-coins me-2"></i>الإجمالي
                                            </label>
                                        </div>
                                        <div class="col-8">
                                            <h4 class="mb-0 text-primary text-end" id="modal_total">0.00 ج.م</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- الخصم -->
                        <div class="col-12">
                            <div class="card border-primary">
                                <div class="card-header bg-primary bg-opacity-10">
                                    <h6 class="mb-0 text-primary">
                                        <i class="fas fa-percentage me-2"></i>الخصم
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <label class="form-label fw-bold">الخصم %</label>
                                            <div class="input-group">
                                                <input class="form-control text-center" 
                                                       type="number" id="modal_discperc" value="0" min="0" max="100" step="0.1">
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label fw-bold">قيمة الخصم</label>
                                            <div class="input-group">
                                                <input class="form-control text-center" 
                                                       type="number" id="modal_discount" value="0" step="0.01">
                                                <span class="input-group-text bg-primary text-white">ج.م</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- الصافي -->
                        <div class="col-12">
                            <div class="card bg-success bg-opacity-10 border-success">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-4">
                                            <label class="mb-0 fw-bold text-success">
                                                <i class="fas fa-check-circle me-2"></i>الصافي
                                            </label>
                                        </div>
                                        <div class="col-8">
                                            <h3 class="mb-0 text-success text-end" id="modal_net">0.00 ج.م</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- المدفوع والباقي -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">
                                <i class="fas fa-money-bill-wave me-2"></i>المدفوع
                            </label>
                            <div class="input-group input-group-lg">
                                <input class="form-control text-center fw-bold" 
                                       type="number" id="modal_paid" value="0.00" step="0.01">
                                <span class="input-group-text">ج.م</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">
                                <i class="fas fa-arrow-left me-2"></i>الباقي
                            </label>
                            <div class="input-group input-group-lg">
                                <input class="form-control text-center fw-bold bg-danger text-white" 
                                       type="text" id="modal_change" value="0.00" readonly>
                                <span class="input-group-text bg-danger text-white">ج.م</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>إلغاء
                    </button>
                    <button type="button" class="btn btn-success" onclick="submitPOS('save');">
                        <i class="fas fa-save me-1"></i>حفظ الطلب
                    </button>
                    <button type="button" class="btn btn-primary" onclick="submitPOS('cash');">
                        <i class="fas fa-print me-1"></i>حفظ وطباعة
                    </button>
                </div>
            </div>
        </div>
</div>

    <!-- Modal الطاولات -->
    <div class="modal fade" id="tablesModal" tabindex="-1" aria-labelledby="tablesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="tablesModalLabel">
                        <i class="fas fa-th-large me-2"></i>اختر الطاولة
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
                <div class="modal-body p-4">
                    <div class="row g-3" id="tablesGrid">
                        <?php
                        $restables = $conn->query("SELECT * FROM tables WHERE isdeleted = 0 ORDER BY tname");
                        if ($restables && $restables->num_rows > 0) {
                            while ($rowtable = $restables->fetch_assoc()) {
                                $tableId = $rowtable['id'];
                                $tableName = htmlspecialchars($rowtable['tname']);
                                $tableCase = $rowtable['table_case'];
                                
                                // تحديد اللون والأيقونة حسب الحالة
                                $statusClass = ($tableCase == 0) ? 'btn-success' : 'btn-danger';
                                $statusIcon = ($tableCase == 0) ? 'fa-check-circle' : 'fa-clock';
                                $statusText = ($tableCase == 0) ? 'متاحة' : 'مشغولة';
                                
                                // البحث عن الطلب النشط للطاولة
                                $orderTotal = 0;
                                $orderId = null;
                                if ($tableCase != 0) {
                                    $orderQuery = $conn->query("SELECT id, net FROM ot_head WHERE table_id = $tableId AND order_status = 'active' ORDER BY id DESC LIMIT 1");
                                    if ($orderQuery && $orderQuery->num_rows > 0) {
                                        $orderData = $orderQuery->fetch_assoc();
                                        $orderId = $orderData['id'];
                                        $orderTotal = floatval($orderData['net']);
                                    }
                                }
                        ?>
                            <div class="col-md-4 col-sm-6">
                                <button type="button" 
                                        class="btn <?= $statusClass ?> w-100 table-select-btn position-relative" 
                                        data-table-id="<?= $tableId ?>" 
                                        data-table-name="<?= $tableName ?>"
                                        data-table-case="<?= $tableCase ?>"
                                        data-order-id="<?= $orderId ?>"
                                        style="min-height: 120px; font-size: 1.1rem;">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <i class="fas fa-utensils fa-2x mb-2"></i>
                                        <h6 class="mb-1"><?= $tableName ?></h6>
                                        <small class="d-flex align-items-center">
                                            <i class="fas <?= $statusIcon ?> me-1"></i>
                                            <?= $statusText ?>
                                        </small>
                                        <?php if ($tableCase != 0 && $orderTotal > 0): ?>
                                            <div class="mt-2 badge bg-white text-dark">
                                                <?= number_format($orderTotal, 2) ?> ج.م
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </button>
                            </div>
                        <?php
                            }
                        } else {
                            echo '<div class="col-12 text-center text-muted">
                                    <i class="fas fa-exclamation-circle fa-3x mb-3"></i>
                                    <p>لا توجد طاولات متاحة</p>
                                  </div>';
                        }
                        ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>إلغاء
                    </button>
                    <button type="button" class="btn btn-primary" onclick="selectNoTable();">
                        <i class="fas fa-shopping-bag me-1"></i>بدون طاولة (تيك أواي)
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal تفاصيل الصنف -->
    <div class="modal fade" id="itemDetailsModal" tabindex="-1" aria-labelledby="itemDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="itemDetailsModalLabel">
                        <i class="fas fa-info-circle me-2"></i>تفاصيل الصنف
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <div id="modal_item_image" style="height: 200px; overflow: hidden; border-radius: 12px; background: #f8f9fa;">
                            <!-- سيتم ملؤها ديناميكياً -->
                        </div>
                    </div>
                    <h4 class="text-center mb-3" id="modal_item_name"></h4>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="text-muted small">الباركود</label>
                            <p class="fw-bold" id="modal_item_barcode">-</p>
                        </div>
                        <div class="col-6">
                            <label class="text-muted small">السعر</label>
                            <p class="fw-bold text-success fs-5" id="modal_item_price">0.00 ج.م</p>
                        </div>
                        <div class="col-12">
                            <label class="text-muted small">الوصف</label>
                            <p id="modal_item_desc">لا يوجد وصف</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>إغلاق
                    </button>
                    <button type="button" class="btn btn-primary" id="modal_add_item">
                        <i class="fas fa-plus me-1"></i>إضافة للطلب
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal اختيار الطاولة -->
    <div class="modal fade" id="tablesModal" tabindex="-1" aria-labelledby="tablesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="tablesModalLabel">
                        <i class="fas fa-chair me-2"></i>اختر الطاولة
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="tables-grid" class="row g-3">
                        <div class="col-12 text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">جاري التحميل...</span>
                            </div>
                            <p class="text-muted mt-2">جاري تحميل الطاولات...</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                </div>
            </div>
        </div>
    </div>

    <!-- زر عائم للطاولات -->
    <a href="tables.php" class="btn btn-primary position-fixed" 
       style="bottom: 20px; right: 20px; z-index: 1000; border-radius: 50px; width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(0,0,0,0.3);" 
       title="عرض الطاولات">
        <i class="fas fa-th-large fa-lg"></i>
    </a>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- <script src="js/pos.js"></script> تم تعطيله لأنه يتعارض مع الكود الجديد -->

<script>
console.log('🚀 POS System JS Loading - Timestamp: <?php echo time(); ?>');

    // Load POS Configuration
    let posConfig = null;
    
    $.ajax({
        url: 'pos_config.json',
        type: 'GET',
        dataType: 'json',
        async: false,
        success: function(config) {
            posConfig = config;
            console.log('✅ POS Config loaded:', config);
        },
        error: function() {
            console.error('❌ Failed to load POS config, using defaults');
            // Default config
            posConfig = {
                scale_barcode: {
                    enabled: true,
                    prefix: "200",
                    barcode_length: 13,
                    item_code_start: 3,
                    item_code_length: 4,
                    weight_start: 7,
                    weight_length: 5,
                    weight_divisor: 1000
                }
            };
        }
    });

    $(document).ready(function() {
        document.getElementById('fullscreenBtn').addEventListener('click', function() {
        if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen();
                this.innerHTML = '<i class="fas fa-compress"></i>';
        } else {
        if (document.exitFullscreen) {
            document.exitFullscreen();
        }
                this.innerHTML = '<i class="fas fa-expand"></i>';
            }
        });

        $('input[name="age"]').change(function() {
            if ($('#age2').is(':checked')) {
                $('#table_name_wrapper').fadeIn(300);
                $('#tablesModal').modal('show');
                loadTables();
            } else {
                $('#table_name_wrapper').fadeOut(300);
            }
        });

        function loadTables() {
            $.ajax({
                url: 'ajax/get_tables.php',
                type: 'GET',
                dataType: 'json',
                success: function(tables) {
                    let html = '';
                    tables.forEach(function(table) {
                        let btnClass = table.table_case == 0 ? 'btn-success' : 'btn-danger';
                        let status = table.table_case == 0 ? 'متاحة' : 'محجوزة';
                        html += `
                            <div class="col-md-3 col-sm-4 col-6">
                                <button class="btn ${btnClass} w-100 table-btn" 
                                        data-table-id="${table.id}" 
                                        data-table-name="${table.tname}"
                                        style="height: 80px;">
                                    <i class="fas fa-chair fa-2x mb-2"></i><br>
                                    ${table.tname}<br>
                                    <small>${status}</small>
                                </button>
                            </div>
                        `;
                    });
                    $('#tables-grid').html(html);
                }
            });
        }

        // Table selection
        $(document).on('click', '.table-btn', function() {
            let tableId = $(this).data('table-id');
            let tableName = $(this).data('table-name');
            
            $('#selected_table_id').val(tableId);
            $('#table_name').val(tableName);
            $('#tablesModal').modal('hide');
        });

        // Category filter functionality
        $(document).on('click', '.category-btn', function(e) {
            e.preventDefault();
            console.log('Category button clicked');
            
            // Remove active class from all buttons
            $('.category-btn').removeClass('active').addClass('btn-outline-primary').removeClass('btn-primary');
            
            // Add active class to clicked button
            $(this).addClass('active').removeClass('btn-outline-primary').addClass('btn-primary');
            
            let categoryId = $(this).data('category');
            console.log('Selected category:', categoryId);
            
            if (categoryId === 'all') {
                // Show all items
                $('.item-wrapper').show();
                console.log('Showing all items');
            } else {
                // Hide all items first
                $('.item-wrapper').hide();
                
                // Show only items from selected category
                let itemsToShow = $('.item-wrapper[data-category="' + categoryId + '"]');
                console.log('Items to show:', itemsToShow.length);
                itemsToShow.show();
            }
        });

        // Barcode input functionality
        $('#barcodeInput').on('keypress', function(e) {
            if (e.which === 13) { // Enter key
                e.preventDefault();
                let barcode = $(this).val().trim();
                if (barcode) {
                    searchItemByBarcode(barcode);
                    $(this).val('');
                }
            }
        });
        
        // Search input
        $('#searchInput').on('keypress', function(e) {
            if (e.which === 13) { // Enter key
                let search = $(this).val().trim();
                if (search) {
                    searchItemByBarcode(search);
                    $(this).val('');
                }
            }
        });

        function searchItemByBarcode(barcode) {
            let qty = 1;
            let searchCode = barcode;
            
            // Check if it's a scale barcode using config
            if (posConfig && posConfig.scale_barcode && posConfig.scale_barcode.enabled) {
                const cfg = posConfig.scale_barcode;
                
                if (barcode.length === cfg.barcode_length && 
                    barcode.substring(0, cfg.prefix.length) === cfg.prefix) {
                    
                    // Extract item code
                    searchCode = barcode.substring(cfg.item_code_start, 
                                                   cfg.item_code_start + cfg.item_code_length);
                    
                    // Extract weight
                    let weightStr = barcode.substring(cfg.weight_start, 
                                                      cfg.weight_start + cfg.weight_length);
                    qty = parseFloat(weightStr) / cfg.weight_divisor;
                    
                    // Remove leading zeros from item code
                    searchCode = parseInt(searchCode).toString();
                    
                    console.log('🔢 Scale Barcode Detected:', {
                        original: barcode,
                        itemCode: searchCode,
                        weight: qty
                    });
                }
            }
            
            $.ajax({
                url: 'ajax/search_item.php',
                type: 'POST',
                data: { barcode: searchCode },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        addItemToOrder(response.item.id, response.item.name, response.item.price, response.item.barcode, qty);
                    } else {
                        alert('الصنف غير موجود');
                    }
                },
                error: function() {
                    alert('خطأ في البحث عن الصنف');
                }
            });
        }

        // Click on item image to add
        $('#itemsGrid').on('click', '.item-image-click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            let card = $(this).closest('.item-card');
            let itemId = card.data('item-id');
            let itemName = card.data('item-name');
            let itemPrice = parseFloat(card.data('item-price')) || 0;
            let itemBarcode = card.data('item-barcode');
            
            addItemToOrder(itemId, itemName, itemPrice, itemBarcode);
        });

        // Item details button
        $('#itemsGrid').on('click', '.item-details-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            let card = $(this).closest('.item-card');
            let itemId = card.data('item-id');
            let itemName = card.data('item-name');
            let itemPrice = card.data('item-price');
            let itemBarcode = card.data('item-barcode');
            let itemDesc = card.data('item-desc') || 'لا يوجد وصف';
            
            // Get image from card
            let imageHtml = card.find('.item-image-container').html();
            
            // Fill modal
            $('#modal_item_name').text(itemName);
            $('#modal_item_barcode').text(itemBarcode || '-');
            $('#modal_item_price').text(itemPrice.toFixed(2) + ' ج.م');
            $('#modal_item_desc').text(itemDesc);
            $('#modal_item_image').html(imageHtml);
            
            // Store data for add button
            $('#modal_add_item').data({
                'id': itemId,
                'name': itemName,
                'price': itemPrice,
                'barcode': itemBarcode
            });
            
            // Show modal
            $('#itemDetailsModal').modal('show');
        });

        // Add item from modal
        $(document).on('click', '#modal_add_item', function() {
            let data = $(this).data();
            let itemPrice = parseFloat(data.price) || 0;
            addItemToOrder(data.id, data.name, itemPrice, data.barcode);
            $('#itemDetailsModal').modal('hide');
        });

        // Item card hover effect
        $('#itemsGrid').on('mouseenter', '.item-card', function() {
            $(this).addClass('shadow').css('transform', 'translateY(-5px)');
        });

        $('#itemsGrid').on('mouseleave', '.item-card', function() {
            $(this).removeClass('shadow').css('transform', 'translateY(0)');
        });

        function addItemToOrder(id, name, price, barcode, qty = 1) {
            // Check if item already exists
            let existingItem = $(`.item-card-order[data-itemid="${barcode}"]`);
            
            if (existingItem.length > 0) {
                // Item exists - increase quantity
                let qtyInput = existingItem.find('.quantityInput');
                let currentQty = parseFloat(qtyInput.val()) || 0;
                let newQty = currentQty + qty; // استخدم الكمية المرسلة
                qtyInput.val(newQty);
                
                // Recalculate subtotal
                let priceInput = existingItem.find('.priceInput');
                let itemPrice = parseFloat(priceInput.val()) || 0;
                let subtotal = newQty * itemPrice;
                existingItem.find('.subtotal').val(subtotal.toFixed(2));
                
                updateTotal();
                $('#barcodeInput').val('').focus();
                return;
            }
            
            // Item doesn't exist - add new
            // استخدم الكمية المرسلة (الافتراضي 1)
            let subtotal = price * qty;
            let itemNumber = $('#itemData .item-card-order').length + 1;
            
            let itemCard = `
                <div class="card mb-1 item-card-order shadow-sm border-start border-3" data-itemid="${barcode}" style="border-color: #0a7ea4 !important; max-width: 100%;">
                    <div class="card-body p-1">
                        <div class="d-flex align-items-center gap-1" style="font-size: 0.75rem;">
                            <!-- رقم -->
                            <span class="badge bg-primary" style="font-size: 0.7rem; min-width: 25px;">#${itemNumber}</span>
                            
                            <!-- اسم الصنف -->
                            <div style="flex: 1; min-width: 0;">
                                <input type="hidden" value='${id}' name="itmname[]">
                                <input type="hidden" class="barcode" value="${barcode}">
                                <div class="text-truncate fw-bold" style="font-size: 0.75rem;" title="${name}">${name}</div>
                            </div>
                            
                            <!-- الكمية -->
                            <div style="width: 65px;">
                                <small class="d-block text-center text-muted" style="font-size: 0.6rem; margin-bottom: 1px;">كمية</small>
                                <input type="number" 
                                       class="form-control form-control-sm text-center quantityInput nozero fw-bold" 
                                       value="${qty}" 
                                       name="itmqty[]"
                                       min="1" 
                                       step="0.1"
                                       style="width: 100%; font-size: 0.75rem; padding: 3px; border: 2px solid #ff6347; height: 26px;"
                                       title="الكمية">
                                <input type="hidden" name="u_val[]" value="1">
                            </div>
                            
                            <!-- السعر -->
                            <div style="width: 55px;">
                                <small class="d-block text-center text-muted" style="font-size: 0.6rem; margin-bottom: 1px;">سعر</small>
                                <input type="number" 
                                       class="form-control form-control-sm text-center priceInput nozero" 
                                       value="${price.toFixed(2)}" 
                                       name="itmprice[]" 
                                       step="0.01"
                                       style="width: 100%; font-size: 0.7rem; padding: 3px; height: 26px;"
                                       title="السعر">
                            </div>
                            
                            <!-- القيمة -->
                            <div style="width: 60px;">
                                <small class="d-block text-center text-muted" style="font-size: 0.6rem; margin-bottom: 1px;">قيمة</small>
                                <input type="hidden" name="itmdisc[]">
                                <input type="text" 
                                       class="form-control form-control-sm text-center subtotal fw-bold" 
                                       readonly 
                                       value="${subtotal.toFixed(2)}" 
                                       name="itmval[]"
                                       style="width: 100%; font-size: 0.7rem; padding: 3px; background: #fff3cd; height: 26px;"
                                       title="القيمة">
                            </div>
                            
                            <!-- زر الحذف -->
                            <button type="button" class="btn btn-danger btn-sm delRow" style="padding: 2px 6px; font-size: 0.7rem;" title="حذف">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            $('#itemData').append(itemCard);
            updateItemCount();
            updateTotal();
            $('#barcodeInput').val('').focus();
        }
        
        function updateItemCount() {
            let count = $('#itemData .item-card-order').length;
            $('#itemCount').text(count);
        }
        
        function clearAllItems() {
            if (confirm('مسح كل الأصناف؟')) {
                $('#itemData').empty();
                $('#discount').val('0');
                $('#modal_discperc').val('0');
                $('#modal_discount').val('0');
                $('#modal_paid').val('0.00');
                $('#modal_change').val('0.00');
                updateItemCount();
                updateTotal();
            }
        }

        // Update total function
        function updateTotal() {
            let total = 0;
            $('.subtotal').each(function() {
                total += parseFloat($(this).val()) || 0;
            });
            $('#total').val(total.toFixed(2));
            $('#total_display').text(total.toFixed(2) + ' ج.م');
            $('#total_display_btn').text(total.toFixed(2) + ' ج.م');
            $('#modal_total').text(total.toFixed(2) + ' ج.م');
            
            let discount = parseFloat($('#discount').val()) || 0;
            let net = total - discount;
            $('#net_val').val(net.toFixed(2));
            $('#net_display').text(net.toFixed(2) + ' ج.م');
            $('#modal_net').text(net.toFixed(2) + ' ج.م');
        }
        
        // ========================================
        // نظام الطاولات - Tables System
        // ========================================
        
        // اختيار طاولة
        $(document).on('click', '.table-select-btn', function() {
            const tableId = $(this).data('table-id');
            const tableName = $(this).data('table-name');
            const tableCase = $(this).data('table-case');
            const orderId = $(this).data('order-id');
            
            // حفظ بيانات الطاولة
            $('#selected_table_id').val(tableId);
            $('#selected_table_name').val(tableName);
            $('#selected_table_display').html('<i class="fas fa-chair me-1"></i>' + tableName);
            
            // تحديد نوع الطلب كطاولة
            $('#age2').prop('checked', true);
            
            // إخفاء الـ modal
            $('#tablesModal').modal('hide');
            
            // إذا الطاولة مشغولة وفيها طلب نشط
            if (tableCase != 0 && orderId) {
                $('#selected_order_id').val(orderId);
                // تحميل الطلب
                loadOrderData(orderId);
            } else {
                // طاولة فاضية - طلب جديد
                clearAllItems();
                console.log('طاولة فاضية: ' + tableName + ' - طلب جديد');
            }
        });
        
        // اختيار "بدون طاولة"
        function selectNoTable() {
            $('#selected_table_id').val('');
            $('#selected_table_name').val('');
            $('#selected_order_id').val('');
            $('#selected_table_display').html('بدون طاولة');
            $('#age1').prop('checked', true); // تيك أواي
            $('#tablesModal').modal('hide');
            clearAllItems();
        }
        
        // تحميل بيانات الطلب النشط
        function loadOrderData(orderId) {
            console.log('تحميل طلب رقم: ' + orderId);
            
            $.ajax({
                url: 'ajax/load_order.php',
                method: 'POST',
                data: { order_id: orderId },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // مسح الأصناف الحالية
                        $('#itemData').empty();
                        
                        // تحميل الأصناف
                        if (response.items && response.items.length > 0) {
                            response.items.forEach(function(item) {
                                addItemToOrder(
                                    item.item_id,
                                    item.item_name,
                                    parseFloat(item.price),
                                    item.item_desc,
                                    parseFloat(item.qty)
                                );
                            });
                        }
                        
                        // تحميل بيانات الطلب
                        if (response.order) {
                            $('#discount').val(response.order.discount || 0);
                            // تحديد الموظف والعميل والصندوق إذا كانت موجودة
                            if (response.order.emp_id) {
                                $('select[name="emp_id"]').val(response.order.emp_id);
                            }
                            if (response.order.acc1) {
                                $('select[name="acc2_id"]').val(response.order.acc1);
                            }
                        }
                        
                        updateItemCount();
                        updateTotal();
                        
                        console.log('تم تحميل الطلب بنجاح');
                    } else {
                        alert('خطأ في تحميل الطلب: ' + (response.error || 'غير معروف'));
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    alert('خطأ في الاتصال بالخادم');
                }
            });
        }

        // Modal discount calculation
        $('#modal_discperc').on('input', function() {
            let total = parseFloat($('#total').val()) || 0;
            let discount = (total * (parseFloat($(this).val()) || 0) / 100).toFixed(2);
            $('#modal_discount').val(discount);
            $('#discount').val(discount);
            let net = (total - discount).toFixed(2);
            $('#modal_net').text(net + ' ج.م');
            $('#net_val').val(net);
            $('#net_display').text(net + ' ج.م');
        });

        $('#modal_discount').on('input', function() {
            let total = parseFloat($('#total').val()) || 0;
            let discount = parseFloat($(this).val()) || 0;
            $('#discount').val(discount);
            let percentage = total > 0 ? ((discount / total) * 100).toFixed(2) : 0;
            $('#modal_discperc').val(percentage);
            let net = (total - discount).toFixed(2);
            $('#modal_net').text(net + ' ج.م');
            $('#net_val').val(net);
            $('#net_display').text(net + ' ج.م');
        });

        // Modal paid amount calculation
        $('#modal_paid').on('input', function() {
            let net = parseFloat($('#net_val').val()) || 0;
            let paid = parseFloat($(this).val()) || 0;
            $('#modal_change').val((paid - net).toFixed(2));
        });

        // Delete row
        $(document).on('click', '.delRow', function() {
            $(this).closest('.item-card-order').remove();
            updateItemCount();
            updateTotal();
        });
        
        // Update quantity and recalculate
        $(document).on('input', '.quantityInput, .priceInput', function() {
            let card = $(this).closest('.item-card-order');
            let qty = parseFloat(card.find('.quantityInput').val()) || 0;
            let price = parseFloat(card.find('.priceInput').val()) || 0;
            let subtotal = qty * price;
            card.find('.subtotal').val(subtotal.toFixed(2));
            updateTotal();
        });

        // Submit POS form
        window.submitPOS = function(action) {
            console.log('submitPOS called with action:', action);
            
            // نسخ قيمة المدفوع من modal للحقل المخفي
            let paidValue = parseFloat($('#modal_paid').val()) || 0;
            console.log('Paid value:', paidValue);
            
            // البحث عن حقل paid المخفي أو إنشاءه
            let paidInput = $('input[name="paid"]');
            if (paidInput.length === 0) {
                paidInput = $('<input>').attr({
                    type: 'hidden',
                    name: 'paid'
                });
                $('#posForm').append(paidInput);
                console.log('Created paid input');
            }
            paidInput.val(paidValue);
            
            // Create hidden input for submit value
            let submitInput = $('<input>').attr({
                type: 'hidden',
                name: 'submit',
                value: action
            });
            
            // Add to form and submit
            $('#posForm').append(submitInput);
            console.log('Added submit input');
            
            // Call validation function
            console.log('About to validate form...');
            if (validatePOSForm()) {
                console.log('Validation passed, submitting form...');
                console.log('Form action:', $('#posForm').attr('action'));
                console.log('Form method:', $('#posForm').attr('method'));
                console.log('Submitting now...');
                $('#posForm').submit();
                console.log('Form submitted!');
            } else {
                console.log('Validation failed, form not submitted');
            }
        };
        
        // Focus on barcode input on load
        $('#barcodeInput').focus();
    }); // End of document.ready
    
    // Form validation function
    function validatePOSForm() {
        console.log('=== validatePOSForm() called ===');
        
        // Debug: Check if #itemData exists
        let itemDataElement = $('#itemData');
        console.log('Step 1: #itemData exists?', itemDataElement.length > 0);
        
        if (itemDataElement.length > 0) {
            let htmlContent = itemDataElement.html();
            console.log('Step 2: #itemData HTML length:', htmlContent.length);
            console.log('Step 3: #itemData HTML preview:', htmlContent.substring(0, 300));
        }
        
        // Check if there are items
        let items = $('#itemData .item-card-order');
        console.log('Step 4: Items with selector "#itemData .item-card-order":', items.length);
        
        if (items.length === 0) {
            console.log('Step 5: No items in #itemData, trying alternative...');
            // Try alternative selector
            let alternativeItems = $('.item-card-order');
            console.log('Step 6: Alternative search (all .item-card-order):', alternativeItems.length);
            
            if (alternativeItems.length > 0) {
                console.log('Step 7: Found items with alternative selector! Returning TRUE');
                return true;
            }
            
            console.log('Step 8: No items found anywhere, showing alert');
            alert('الرجاء إضافة أصناف للفاتورة');
            return false;
        }
        
        console.log('Step 9: Items found in #itemData! Returning TRUE');
        return true;
    }
    
    // Keep old function for backward compatibility
    function dis() {
        return validatePOSForm();
    }
    </script>

<?php include('includes/footer.php');?>