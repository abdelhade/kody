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
    
    <style>
        :root {
            --primary-navy: #2c3e50;
            --primary-violet: #8e44ad;
            --neutral-light: #f8f9fa;
            --neutral-gray: #e9ecef;
            --soft-gray: #dee2e6;
            --text-dark: #2c3e50;
        }

        body {
            background-color: var(--neutral-light);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-dark);
        }

        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .category-card {
            background: white;
            border: 2px solid var(--soft-gray);
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            min-height: 140px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .category-card:hover {
            border-color: var(--primary-navy);
            box-shadow: 0 4px 12px rgba(44, 62, 80, 0.15);
            transform: translateY(-2px);
        }

        .category-card.active {
            border-color: var(--primary-violet);
            background-color: rgba(142, 68, 173, 0.05);
        }

        .category-icon {
            font-size: 2.5rem;
            color: var(--primary-navy);
            margin-bottom: 0.5rem;
        }

        .category-name {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-dark);
            margin: 0;
        }

        .items-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem;
        }

        .item-card {
            background: white;
            border: 1px solid var(--soft-gray);
            border-radius: 8px;
            overflow: hidden;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .item-card:hover {
            border-color: var(--primary-navy);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }

        .item-image {
            height: 150px;
            background: var(--neutral-gray);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .item-details {
            padding: 1rem;
        }

        .item-name {
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--text-dark);
        }

        .item-price {
            background: var(--primary-violet);
            color: white;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-weight: bold;
            display: inline-block;
        }

        .order-section {
            background: white;
            border: 1px solid var(--soft-gray);
            border-radius: 8px;
            height: calc(100vh - 120px);
            display: flex;
            flex-direction: column;
        }

        .order-header {
            background: var(--primary-navy);
            color: white;
            padding: 1rem;
            border-radius: 8px 8px 0 0;
        }

        .order-items {
            flex: 1;
            overflow-y: auto;
            padding: 1rem;
        }

        .order-item {
            background: var(--neutral-light);
            border: 1px solid var(--soft-gray);
            border-radius: 6px;
            padding: 0.8rem;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .order-footer {
            border-top: 1px solid var(--soft-gray);
            padding: 1rem;
            background: var(--neutral-light);
        }

        .btn-navy {
            background-color: var(--primary-navy);
            border-color: var(--primary-navy);
            color: white;
        }

        .btn-navy:hover {
            background-color: #1a252f;
            border-color: #1a252f;
            color: white;
        }

        .btn-violet {
            background-color: var(--primary-violet);
            border-color: var(--primary-violet);
            color: white;
        }

        .btn-violet:hover {
            background-color: #7d3c98;
            border-color: #7d3c98;
            color: white;
        }

        .items-container {
            display: none;
        }

        .items-container.show {
            display: block;
        }

        .no-items-message {
            text-align: center;
            padding: 3rem;
            color: #6c757d;
        }

        @media (max-width: 768px) {
            .categories-grid {
                grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            }
            
            .items-grid {
                grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            }
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: var(--primary-navy);">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="fas fa-tshirt me-2"></i>نظام نقاط البيع - الملابس
            </a>
            
            <div class="navbar-nav ms-auto">
                <a href="do/do_logout.php" class="nav-link">
                    <i class="fas fa-sign-out-alt me-1"></i>تسجيل الخروج
                </a>
            </div>
        </div>
    </nav>

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
                <form action="do/doadd_invoice_clothes.php" method="post" id="posForm">
                    <div class="order-section">
                        <div class="order-header">
                            <h6 class="mb-0">
                                <i class="fas fa-shopping-cart me-2"></i>معلومات الطلب
                            </h6>
                        </div>
                        
                        <div class="order-items">
                            <input type="hidden" name="pro_tybe" value="9">
                            <input type="hidden" name="pro_serial" value="0">
                            <input type="hidden" name="pro_id" value="1">
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">نوع الطلب</label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" id="age1" name="age" value="1" checked>
                                    <label class="btn btn-outline-secondary btn-sm" for="age1">
                                        <i class="fas fa-shopping-bag me-1"></i>بيع مباشر
                                    </label>
                                    
                                    <input type="radio" class="btn-check" id="age2" name="age" value="2">
                                    <label class="btn btn-outline-secondary btn-sm" for="age2">
                                        <i class="fas fa-bookmark me-1"></i>حجز
                                    </label>
                                    
                                    <input type="radio" class="btn-check" id="age3" name="age" value="3">
                                    <label class="btn btn-outline-secondary btn-sm" for="age3">
                                        <i class="fas fa-truck me-1"></i>توصيل
                                    </label>
                                </div>
                            </div>

                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label class="form-label">التاريخ</label>
                                    <input type="date" name="pro_date" class="form-control form-control-sm" value="<?= $posdate ?>">
                                </div>
                                <div class="col-6">
                                    <label class="form-label">تاريخ الاستحقاق</label>
                                    <input type="date" name="accural_date" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>">
                                </div>
                            </div>

                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label class="form-label">المخزن</label>
                                    <select name="store_id" class="form-select form-select-sm" required>
                                        <?php
                                        $resstore = $conn->query("SELECT * FROM `acc_head` WHERE is_stock =1 AND isdeleted = 0;");
                                        $first = true;
                                        while ($rowstore = $resstore->fetch_assoc()) { 
                                            $selected = '';
                                            if($rowstg['def_pos_store'] == $rowstore['id']){
                                                $selected = "selected";
                                            } elseif ($first && empty($rowstg['def_pos_store'])) {
                                                $selected = "selected";
                                            }
                                            $first = false;
                                        ?>
                                        <option <?= $selected ?> value="<?= $rowstore['id'] ?>"><?= $rowstore['aname'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="col-6">
                                    <label class="form-label">الموظف</label>
                                    <select name="emp_id" class="form-select form-select-sm" required>
                                        <?php
                                        $resemp = $conn->query("SELECT * FROM `acc_head` WHERE parent_id = 35 AND is_basic = 0 AND isdeleted = 0;");
                                        $first_emp = true;
                                        while ($rowemp = $resemp->fetch_assoc()) { 
                                            $selected = '';
                                            if($rowstg['def_pos_employee'] == $rowemp['id']){
                                                $selected = "selected";
                                            } elseif ($first_emp && empty($rowstg['def_pos_employee'])) {
                                                $selected = "selected";
                                            }
                                            $first_emp = false;
                                        ?>
                                        <option <?= $selected ?> value="<?= $rowemp['id'] ?>"><?= $rowemp['aname'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label class="form-label">العميل</label>
                                    <select name="acc2_id" class="form-select form-select-sm" required>
                                        <?php
                                        $resclient = $conn->query("SELECT * FROM `acc_head` WHERE code like '122%' AND is_basic = 0 AND isdeleted = 0;");
                                        $first_client = true;
                                        while ($rowclient = $resclient->fetch_assoc()) { 
                                            $selected = '';
                                            if($rowstg['def_pos_client'] == $rowclient['id']){
                                                $selected = "selected";
                                            } elseif ($first_client && empty($rowstg['def_pos_client'])) {
                                                $selected = "selected";
                                            }
                                            $first_client = false;
                                        ?>
                                        <option <?= $selected ?> value="<?= $rowclient['id'] ?>"><?= $rowclient['aname'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="col-6">
                                    <label class="form-label">الصندوق</label>
                                    <select name="fund_id" class="form-select form-select-sm" required>
                                        <?php
                                        $resfund = $conn->query("SELECT * FROM `acc_head` WHERE is_fund =1 AND is_basic = 0 AND isdeleted = 0;");
                                        $first_fund = true;
                                        while ($rowfund = $resfund->fetch_assoc()) { 
                                            $selected = '';
                                            if($rowstg['def_pos_fund'] == $rowfund['id']){
                                                $selected = "selected";
                                            } elseif ($first_fund && empty($rowstg['def_pos_fund'])) {
                                                $selected = "selected";
                                            }
                                            $first_fund = false;
                                        ?>
                                        <option <?= $selected ?> value="<?= $rowfund['id'] ?>"><?= $rowfund['aname'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <h6 class="fw-bold mb-2">الأصناف المُضافة <span class="badge bg-secondary" id="itemCount">0</span></h6>
                                <div id="itemData" style="max-height: 300px; overflow-y: auto;">
                                    <p class="text-muted text-center">لا توجد أصناف مُضافة</p>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">ملاحظات</label>
                                <textarea class="form-control form-control-sm" name="info" rows="2" placeholder="ملاحظات..."></textarea>
                            </div>
                        </div>

                        <div class="order-footer">
                            <div class="row mb-3">
                                <div class="col-6 text-center">
                                    <small class="text-muted">الإجمالي</small>
                                    <h5 class="mb-0" style="color: var(--primary-navy);" id="total_display">0.00 ج.م</h5>
                                    <input type="hidden" name="headtotal" id="total" value="0.00">
                                    <input name="headplus" type="hidden" value="0">
                                </div>
                                <div class="col-6 text-center">
                                    <small class="text-muted">الصافي</small>
                                    <h5 class="mb-0" style="color: var(--primary-violet);" id="net_display">0.00 ج.م</h5>
                                    <input type="hidden" name="headnet" id="net_val" value="0">
                                    <input type="hidden" name="headdisc" id="discount" value="0">
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-violet" data-bs-toggle="modal" data-bs-target="#paymentModal">
                                    <i class="fas fa-money-bill-wave me-1"></i>دفع وحفظ
                                    <div style="font-size: 0.8rem;" id="total_display_btn">0.00 ج.م</div>
                                </button>
                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="clearItems();">
                                    <i class="fas fa-eraser me-1"></i>مسح الكل
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header" style="background-color: var(--primary-navy); color: white;">
                        <h6 class="mb-0">
                            <i class="fas fa-boxes me-2"></i>اختيار الأصناف
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="categories-grid" id="categoriesContainer">
                            <?php
                            $rescategories = $conn->query("SELECT * FROM item_group WHERE isdeleted = 0 ORDER BY gname");
                            if ($rescategories && $rescategories->num_rows > 0) {
                                while ($rowcategory = $rescategories->fetch_assoc()) {
                                    $categoryId = $rowcategory['id'];
                                    $categoryName = htmlspecialchars($rowcategory['gname']);
                                    echo '<div class="category-card" data-category="'.$categoryId.'" onclick="loadCategoryItems('.$categoryId.')">
                                            <div class="category-icon">
                                                <i class="fas fa-folder"></i>
                                            </div>
                                            <div class="category-name">'.$categoryName.'</div>
                                          </div>';
                                }
                            } else {
                                echo '<div class="col-12 text-center text-muted"><p>لا توجد مجموعات متاحة</p></div>';
                            }
                            ?>
                        </div>

                        <div class="items-container" id="itemsContainer">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">الأصناف المتاحة</h6>
                                <button class="btn btn-outline-secondary btn-sm" onclick="hideItems()">
                                    <i class="fas fa-arrow-right me-1"></i>العودة للمجموعات
                                </button>
                            </div>
                            <div class="items-grid" id="itemsGrid">
                            </div>
                        </div>

                        <div class="no-items-message" id="noItemsMessage">
                            <i class="fas fa-box-open fa-3x mb-3" style="color: var(--soft-gray);"></i>
                            <h5>اختر مجموعة لعرض الأصناف</h5>
                            <p class="text-muted">قم بالنقر على إحدى المجموعات أعلاه لعرض الأصناف المتاحة</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="paymentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background-color: var(--primary-navy); color: white;">
                    <h5 class="modal-title">
                        <i class="fas fa-cash-register me-2"></i>الدفع والإجماليات
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="card" style="background-color: var(--neutral-light);">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-4">
                                            <label class="mb-0 fw-bold" style="color: var(--primary-navy);">
                                                <i class="fas fa-coins me-2"></i>الإجمالي
                                            </label>
                                        </div>
                                        <div class="col-8">
                                            <h4 class="mb-0 text-end" style="color: var(--primary-navy);" id="modal_total">0.00 ج.م</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="card border-secondary">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0" style="color: var(--text-dark);">
                                        <i class="fas fa-percentage me-2"></i>الخصم
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <label class="form-label fw-bold">الخصم %</label>
                                            <div class="input-group">
                                                <input class="form-control text-center" type="number" id="modal_discperc" value="0" min="0" max="100" step="0.1">
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label fw-bold">قيمة الخصم</label>
                                            <div class="input-group">
                                                <input class="form-control text-center" type="number" id="modal_discount" value="0" step="0.01">
                                                <span class="input-group-text" style="background-color: var(--primary-navy); color: white;">ج.م</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="card" style="background-color: rgba(142, 68, 173, 0.1); border-color: var(--primary-violet);">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-4">
                                            <label class="mb-0 fw-bold" style="color: var(--primary-violet);">
                                                <i class="fas fa-check-circle me-2"></i>الصافي
                                            </label>
                                        </div>
                                        <div class="col-8">
                                            <h3 class="mb-0 text-end" style="color: var(--primary-violet);" id="modal_net">0.00 ج.م</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">
                                <i class="fas fa-money-bill-wave me-2"></i>المدفوع
                            </label>
                            <div class="input-group input-group-lg">
                                <input class="form-control text-center fw-bold" type="number" id="modal_paid" value="0.00" step="0.01">
                                <span class="input-group-text">ج.م</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">الباقي</label>
                            <div class="input-group input-group-lg">
                                <input class="form-control text-center fw-bold bg-danger text-white" type="text" id="modal_change" value="0.00" readonly>
                                <span class="input-group-text bg-danger text-white">ج.م</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>إلغاء
                    </button>
                    <button type="button" class="btn btn-navy" onclick="submitPOS('save');">
                        <i class="fas fa-save me-1"></i>حفظ الطلب
                    </button>
                    <button type="button" class="btn btn-violet" onclick="submitPOS('cash');">
                        <i class="fas fa-print me-1"></i>حفظ وطباعة
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="assets/libs/bootstrap.bundle.min.js"></script>
    <script src="plugins/sweetalert2/sweetalert2.min.js"></script>
    
    <script>
        let selectedItems = [];

        function loadCategoryItems(categoryId) {
            document.querySelectorAll('.category-card').forEach(card => {
                card.classList.remove('active');
            });
            
            document.querySelector(`[data-category="${categoryId}"]`).classList.add('active');
            
            document.getElementById('itemsGrid').innerHTML = `
                <div class="col-12 text-center py-5">
                    <div class="spinner-border" style="color: var(--primary-navy);" role="status">
                        <span class="visually-hidden">جاري التحميل...</span>
                    </div>
                    <p class="mt-2">جاري تحميل الأصناف...</p>
                </div>
            `;
            
            document.getElementById('itemsContainer').classList.add('show');
            document.getElementById('noItemsMessage').style.display = 'none';
            
            fetch(`ajax/get_category_items.php?category_id=${categoryId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayItems(data.items);
                    } else {
                        document.getElementById('itemsGrid').innerHTML = `
                            <div class="col-12 text-center py-5">
                                <i class="fas fa-exclamation-circle fa-3x mb-3" style="color: var(--soft-gray);"></i>
                                <h5>لا توجد أصناف في هذه المجموعة</h5>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('itemsGrid').innerHTML = `
                        <div class="col-12 text-center py-5 text-danger">
                            <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                            <h5>حدث خطأ في تحميل الأصناف</h5>
                        </div>
                    `;
                });
        }

        function displayItems(items) {
            const itemsGrid = document.getElementById('itemsGrid');
            
            if (items.length === 0) {
                itemsGrid.innerHTML = `
                    <div class="col-12 text-center py-5">
                        <i class="fas fa-box-open fa-3x mb-3" style="color: var(--soft-gray);"></i>
                        <h5>لا توجد أصناف في هذه المجموعة</h5>
                    </div>
                `;
                return;
            }
            
            let html = '';
            items.forEach(item => {
                html += `
                    <div class="item-card" onclick="addItemToOrder(${item.id}, '${item.name}', ${item.price})">
                        <div class="item-image">
                            <i class="fas fa-tshirt fa-3x" style="color: var(--soft-gray);"></i>
                        </div>
                        <div class="item-details">
                            <div class="item-name">${item.name}</div>
                            <div class="item-price">${parseFloat(item.price).toFixed(2)} ج.م</div>
                        </div>
                    </div>
                `;
            });
            
            itemsGrid.innerHTML = html;
        }

        function addItemToOrder(itemId, itemName, itemPrice) {
            const existingItemIndex = selectedItems.findIndex(item => item.id === itemId);
            
            if (existingItemIndex !== -1) {
                selectedItems[existingItemIndex].quantity += 1;
            } else {
                selectedItems.push({
                    id: itemId,
                    name: itemName,
                    price: parseFloat(itemPrice),
                    quantity: 1
                });
            }
            
            updateOrderDisplay();
            
            Swal.fire({
                icon: 'success',
                title: 'تم الإضافة',
                text: `تم إضافة ${itemName} للطلب`,
                timer: 1000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        }

        function updateOrderDisplay() {
            const itemData = document.getElementById('itemData');
            const itemCount = document.getElementById('itemCount');
            
            if (selectedItems.length === 0) {
                itemData.innerHTML = '<p class="text-muted text-center">لا توجد أصناف مُضافة</p>';
                itemCount.textContent = '0';
                updateTotals();
                return;
            }
            
            let html = '';
            selectedItems.forEach((item, index) => {
                const subtotal = item.quantity * item.price;
                html += `
                    <div class="order-item">
                        <div class="flex-grow-1">
                            <div class="fw-bold" style="font-size: 0.9rem;">${item.name}</div>
                            <small class="text-muted">${item.price.toFixed(2)} ج.م × ${item.quantity}</small>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold" style="color: var(--primary-violet);">${subtotal.toFixed(2)} ج.م</div>
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-secondary" onclick="decreaseQuantity(${index})">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <button type="button" class="btn btn-outline-secondary" onclick="increaseQuantity(${index})">
                                    <i class="fas fa-plus"></i>
                                </button>
                                <button type="button" class="btn btn-outline-danger" onclick="removeItem(${index})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        <input type="hidden" name="itmname[]" value="${item.id}">
                        <input type="hidden" name="itmqty[]" value="${item.quantity}">
                        <input type="hidden" name="itmprice[]" value="${item.price}">
                        <input type="hidden" name="itmdisc[]" value="0">
                        <input type="hidden" name="u_val[]" value="1">
                        <input type="hidden" name="itmval[]" value="${subtotal.toFixed(2)}">
                    </div>
                `;
            });
            
            itemData.innerHTML = html;
            itemCount.textContent = selectedItems.length;
            updateTotals();
        }

        function increaseQuantity(index) {
            selectedItems[index].quantity += 1;
            updateOrderDisplay();
        }

        function decreaseQuantity(index) {
            if (selectedItems[index].quantity > 1) {
                selectedItems[index].quantity -= 1;
                updateOrderDisplay();
            }
        }

        function removeItem(index) {
            selectedItems.splice(index, 1);
            updateOrderDisplay();
        }

        function clearItems() {
            selectedItems = [];
            document.getElementById('itemData').innerHTML = '<p class="text-muted text-center">لا توجد أصناف مُضافة</p>';
            document.getElementById('itemCount').textContent = '0';
            updateTotals();
        }

        function updateTotals() {
            let total = 0;
            selectedItems.forEach(item => {
                total += item.quantity * item.price;
            });
            
            const discount = parseFloat(document.getElementById('modal_discount')?.value || 0);
            const net = total - discount;
            
            document.getElementById('total_display').textContent = total.toFixed(2) + ' ج.م';
            document.getElementById('net_display').textContent = net.toFixed(2) + ' ج.م';
            document.getElementById('total_display_btn').textContent = net.toFixed(2) + ' ج.م';
            
            document.getElementById('total').value = total.toFixed(2);
            document.getElementById('net_val').value = net.toFixed(2);
            
            if (document.getElementById('modal_total')) {
                document.getElementById('modal_total').textContent = total.toFixed(2) + ' ج.م';
                document.getElementById('modal_net').textContent = net.toFixed(2) + ' ج.م';
                document.getElementById('modal_paid').value = net.toFixed(2);
                updateChange();
            }
        }

        function hideItems() {
            document.getElementById('itemsContainer').classList.remove('show');
            document.getElementById('noItemsMessage').style.display = 'block';
            
            document.querySelectorAll('.category-card').forEach(card => {
                card.classList.remove('active');
            });
        }

        function updateChange() {
            const net = parseFloat(document.getElementById('modal_net').textContent.replace(' ج.م', '')) || 0;
            const paid = parseFloat(document.getElementById('modal_paid').value) || 0;
            const change = paid - net;
            
            document.getElementById('modal_change').value = change.toFixed(2);
            
            if (change < 0) {
                document.getElementById('modal_change').className = 'form-control text-center fw-bold bg-danger text-white';
            } else {
                document.getElementById('modal_change').className = 'form-control text-center fw-bold bg-success text-white';
            }
        }

        function updateDiscount() {
            const total = parseFloat(document.getElementById('total').value) || 0;
            const discountPercent = parseFloat(document.getElementById('modal_discperc').value) || 0;
            const discountValue = parseFloat(document.getElementById('modal_discount').value) || 0;
            
            let finalDiscount = discountValue;
            
            if (discountPercent > 0) {
                finalDiscount = (total * discountPercent) / 100;
                document.getElementById('modal_discount').value = finalDiscount.toFixed(2);
            }
            
            document.getElementById('discount').value = finalDiscount.toFixed(2);
            updateTotals();
        }

        function submitPOS(action) {
            if (selectedItems.length === 0) {
                alert('يجب إضافة صنف واحد على الأقل');
                return false;
            }
            
            const form = document.getElementById('posForm');
            
            // إضافة نوع العملية
            let submitInput = form.querySelector('input[name="submit"]');
            if (!submitInput) {
                submitInput = document.createElement('input');
                submitInput.type = 'hidden';
                submitInput.name = 'submit';
                form.appendChild(submitInput);
            }
            submitInput.value = action;
            
            // إضافة المبلغ المدفوع
            let paidInput = form.querySelector('input[name="paid"]');
            if (!paidInput) {
                paidInput = document.createElement('input');
                paidInput.type = 'hidden';
                paidInput.name = 'paid';
                form.appendChild(paidInput);
            }
            paidInput.value = document.getElementById('modal_paid').value;
            
            $('#paymentModal').modal('hide');
            
            setTimeout(function() {
                HTMLFormElement.prototype.submit.call(form);
            }, 500);
            
            return true;
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('noItemsMessage').style.display = 'block';
            
            document.getElementById('modal_discperc')?.addEventListener('input', updateDiscount);
            document.getElementById('modal_discount')?.addEventListener('input', updateDiscount);
            document.getElementById('modal_paid')?.addEventListener('input', updateChange);
            
            $('#paymentModal').on('show.bs.modal', function() {
                updateTotals();
            });
        });
    </script>
</body>
</html>