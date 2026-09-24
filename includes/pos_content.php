<?php
if (!isset($action_url)) {
    $action_url = "do/doadd_invoice.php";
}
?>
<style>
/* POS — blur + تعتيم الخلفية عند فتح المودال */
.modal-backdrop {
    opacity: 1 !important;
    background-color: rgba(15, 23, 42, 0.55) !important;
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
}

.modal-backdrop.fade {
    opacity: 0 !important;
}

.modal-backdrop.show {
    opacity: 1 !important;
}

.modal.show {
    background-color: rgba(15, 23, 42, 0.25) !important;
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
}

body {
    background-color: #f5f7fa !important;
}
.content-wrapper {
    background-color: #f5f7fa !important;
}

#posTablesPanelModal .modal-body {
    overflow: hidden;
}
</style>
<!-- Main Content -->
<form action="<?= $action_url ?>" method="post" id="posForm">
        <div class="container-fluid pos-main-container h-100">
            <div class="row h-100 g-1">
                <!-- القسم الأيمن - معلومات الطلب -->
                <div class="col-lg-4">
                    <div class="card shadow-sm h-100 d-flex flex-column order-info-panel">
                        <div
                            class="card-header bg-primary text-white py-1 d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">
                            معلومات الطلب
                            </h6>
                            <button type="button" id="recentOrdersBtn2" class="btn btn-light btn-sm recent-orders-btn">
                                 عرض الطلبات السابقة
                            </button>
                        </div>
                        <div class="card-body order-info-body flex-grow-1 d-flex flex-column p-1 min-h-0">
                            <div class="order-info-top">
                            <!-- Hidden Fields -->
                            <input type="hidden" name="pro_tybe" value="9">
                            <input type="hidden" name="pro_serial" value="0">
                            <input type="hidden" name="pro_id" value="1">

                            <!-- نوع الطلب -->
                            <?php
                            // يُحسب في pos_barcode.php قبل الـ navbar — هذا احتياط لو ضُمِّن الملف من مكان آخر
                            if (!isset($default_table_pref)) {
                                $default_table_pref = isset($_COOKIE['pos_default_table']) && $_COOKIE['pos_default_table'] === '1';
                            }

                            $order_type_val = $default_table_pref ? 2 : 1;
                            if (isset($_GET['edit']) && isset($rowed['info'])) {
                                $info_text = $rowed['info'];
                                if (strpos($info_text, 'نوع الطلب: دليفري') !== false) {
                                    $order_type_val = 3;
                                } elseif (strpos($info_text, 'طاولة:') !== false || strpos($info_text, 'نوع الطلب: طاولة') !== false) {
                                    $order_type_val = 2;
                                } elseif (strpos($info_text, 'نوع الطلب: تيك أواي') !== false) {
                                    $order_type_val = 1;
                                }
                            }

                            // عند تعديل طلب طاولة: استرجع ربط الطاولة حتى لا يُحفظ الطلب بلا طاولة
                            $preset_table_id = isset($table_id_from_get) && $table_id_from_get > 0 ? $table_id_from_get : 0;
                            $preset_table_name = isset($table_name_from_get) ? $table_name_from_get : '';
                            if ($preset_table_id <= 0 && isset($_GET['edit']) && $order_type_val == 2) {
                                if (!empty($rowed['table_id'])) {
                                    $preset_table_id = intval($rowed['table_id']);
                                    $ptn = $conn->prepare("SELECT tname FROM tables WHERE id = ? AND isdeleted = 0 LIMIT 1");
                                    if ($ptn) {
                                        $ptn->bind_param('i', $preset_table_id);
                                        $ptn->execute();
                                        $ptn_row = $ptn->get_result()->fetch_assoc();
                                        if ($ptn_row) $preset_table_name = $ptn_row['tname'];
                                        $ptn->close();
                                    }
                                } elseif (preg_match('/طاولة:\s*([^\-]+)/u', (string)$rowed['info'], $m)) {
                                    // طلبات قديمة مربوطة باسم الطاولة في info فقط
                                    $legacy_name = trim($m[1]);
                                    $ptn = $conn->prepare("SELECT id, tname FROM tables WHERE tname = ? AND isdeleted = 0 LIMIT 1");
                                    if ($ptn) {
                                        $ptn->bind_param('s', $legacy_name);
                                        $ptn->execute();
                                        $ptn_row = $ptn->get_result()->fetch_assoc();
                                        if ($ptn_row) {
                                            $preset_table_id = intval($ptn_row['id']);
                                            $preset_table_name = $ptn_row['tname'];
                                        }
                                        $ptn->close();
                                    }
                                }
                            }
                            ?>
                            <div class="mb-0">
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" id="age1" name="age" value="1" <?= $order_type_val == 1 ? 'checked' : '' ?>>
                                    <label class="btn btn-outline-primary btn-sm" for="age1">
                                        تيك اواي
                                    </label>

                                    <input type="radio" class="btn-check" id="age2" name="age" value="2"
                                        <?= ($order_type_val == 2 || isset($_GET['table'])) ? 'checked' : '' ?>>
                                    <label class="btn btn-outline-primary btn-sm" for="age2">
                                        طاولة
                                    </label>

                                    <input type="radio" class="btn-check" id="age3" name="age" value="3" <?= $order_type_val == 3 ? 'checked' : '' ?>>
                                    <label class="btn btn-outline-primary btn-sm" for="age3"
                                        onclick="openDeliveryModal()">
                                        دليفري
                                    </label>
                                </div>
                            </div>

                            <!-- الباركود والبحث -->
                            <div class="row g-0 mb-0">
                                <!-- البحث -->
                                <div class="col-6">
                                    <div class="input-group input-group-sm">
                                        <!-- <span class="input-group-text">
                                            <i class="fas fa-search"></i>
                                        </span> -->
                                        <input type="text" class="scnd form-control" id="searchInput"
                                            placeholder="ابحث عن الصنف..."
                                            title="ابحث عن الصنف واضغط Enter | Alt+S للتركيز">
                                    </div>
                                </div>

                                <!-- الباركود -->
                                <div class="col-6">
                                    <input type="text" class="form-control form-control-sm frst"
                                        placeholder="امسح الباركود..." id="barcodeInput"
                                        title="قارئ الباركود | Alt+B للتركيز"
                                        style="border: 2px solid #28a745; background: #f8fff8;">
                                </div>
                            </div>

                            <!-- الحقول الثانوية - في الناحية التانية -->
                            <div class="row g-0 mb-0">
                                <!-- التواريخ -->
                                <div class="col-6">
                                    <input type="date" name="pro_date" class="form-control form-control-sm"
                                        value="<?= $posdate ?>" title="التاريخ" style="font-size: 0.75rem;">
                                </div>
                                <div class="col-6">
                                    <input type="date" name="accural_date" class="form-control form-control-sm"
                                        value="<?php echo isset($_GET['edit']) ? $rowed['accural_date'] : date('Y-m-d'); ?>"
                                        title="تاريخ الاستحقاق" style="font-size: 0.75rem;">
                                </div>

                                <input type="hidden" id="selected_table_id" name="table_id" value="<?= $preset_table_id ?>">
                                <input type="hidden" id="selected_table_name" name="table_name" value="<?= htmlspecialchars($preset_table_name) ?>">
                                <input type="hidden" id="selected_order_id" name="edit" value="0">
                            </div>

                            <!-- الحقول الصغيرة -->
                            <div class="row g-0 mb-0">
                                <!-- المخزن -->
                                <div class="col-3">
                                    <select name="store_id" class="form-select form-select-sm" title="المخزن"
                                        style="font-size: 0.75rem;" required>
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
                                        <option <?= $selected ?> value="<?= $rowstore['id'] ?>">
                                            <?= $rowstore['aname'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <!-- الموظف -->
                                <div class="col-3">
                                    <select name="emp_id" class="form-select form-select-sm" title="الموظف"
                                        style="font-size: 0.75rem;" required>
                                        <?php
                                        $resemp = $conn->query("SELECT * FROM `acc_head` WHERE parent_id = 35 AND is_basic = 0 AND isdeleted = 0;");
                                        $first_emp = true;
                                        while ($rowemp = $resemp->fetch_assoc()) { 
                                            $selected = '';
                                            if($rowstg['def_pos_employee'] == $rowemp['id']){
                                                $selected = "selected";
                                            } elseif(isset($_GET['edit']) && $rowed['emp_id'] == $rowemp['id']){
                                                $selected = "selected";
                                            } elseif ($first_emp && empty($rowstg['def_pos_employee']) && !isset($_GET['edit'])) {
                                                $selected = "selected";
                                            }
                                            $first_emp = false;
                                        ?>
                                        <option <?= $selected ?> value="<?= $rowemp['id'] ?>"><?= $rowemp['aname'] ?>
                                        </option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <!-- العميل -->
                                <div class="col-3">
                                    <select name="acc2_id" class="form-select form-select-sm" title="العميل"
                                        style="font-size: 0.75rem;" required>
                                        <?php
                                        $resclient = $conn->query("SELECT * FROM `acc_head` WHERE code like '122%'  AND is_basic = 0 AND isdeleted = 0;");
                                        if(isset($_GET['edit'])){$rowed = $conn->query("SELECT * FROM ot_head where id = $id")->fetch_assoc();};
                                        $first_client = true;
                                        while ($rowclient = $resclient->fetch_assoc()) { 
                                            $selected = '';
                                            if($rowstg['def_pos_client'] == $rowclient['id']){
                                                $selected = "selected";
                                            } elseif(isset($_GET['edit']) && $rowed['acc2'] == $rowclient['id']){
                                                // العميل في acc2 لفواتير الكاشير (acc1 = الصندوق)
                                                $selected = "selected";
                                            } elseif ($first_client && empty($rowstg['def_pos_client']) && !isset($_GET['edit'])) {
                                                $selected = "selected";
                                            }
                                            $first_client = false;
                                        ?>
                                        <option <?= $selected ?> value="<?= $rowclient['id'] ?>">
                                            <?= $rowclient['aname'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <!-- الصندوق -->
                                <div class="col-3">
                                    <select name="fund_id" class="form-select form-select-sm" title="الصندوق"
                                        style="font-size: 0.75rem;" required>
                                        <?php
                                        if(isset($_GET['edit'])){$rowed = $conn->query("SELECT * FROM ot_head where id = $id")->fetch_assoc();};
                                        $resfund = $conn->query("SELECT * FROM `acc_head` WHERE is_fund =1 AND is_basic = 0 AND isdeleted = 0;");
                                        $first_fund = true;
                                        while ($rowfund = $resfund->fetch_assoc()) { 
                                            $selected = '';
                                            if($rowstg['def_pos_fund'] == $rowfund['id']){
                                                $selected = "selected";
                                            } elseif(isset($_GET['edit']) && (
                                                $rowed['acc_fund'] == $rowfund['id']
                                                || (empty($rowed['acc_fund']) && $rowed['acc1'] == $rowfund['id'])
                                            )){
                                                $selected = "selected";
                                            } elseif ($first_fund && empty($rowstg['def_pos_fund']) && !isset($_GET['edit'])) {
                                                $selected = "selected";
                                            }
                                            $first_fund = false;
                                        ?>
                                        <option <?= $selected ?> value="<?= $rowfund['id'] ?>"><?= $rowfund['aname'] ?>
                                        </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            </div>

                            <!-- الأصناف المُضافة -->
                            <div class="order-items-panel flex-grow-1 d-flex flex-column min-h-0">
                                <div class="card flex-grow-1 d-flex flex-column border-primary mb-0 min-h-0">
                                    <div class="card-header bg-gradient bg-primary text-white py-1">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0" style="font-size: 0.85rem;">
                                                الأصناف المُضافة
                                            </h6>
                                            <span class="badge bg-white text-primary" id="itemCount">0</span>
                                        </div>
                                    </div>
                                    <div class="card-body p-1 flex-grow-1 min-h-0 order-items-scroll"
                                        style="background: #f8f9fa;"
                                        id="itemData">
                                        <?php
                                        if (isset($_GET['edit'])){
                                            $id = $_GET['edit'];
                                            $sqldet = "SELECT fd.*, m.iname as item_name, m.barcode 
                                                      FROM fat_details fd 
                                                      LEFT JOIN myitems m ON m.id = fd.item_id 
                                                      WHERE fd.pro_id = $id AND fd.isdeleted = 0";
                                            $resdet = $conn->query($sqldet);
                                            $x = 0;
                                            while ($rowdet = $resdet->fetch_assoc()) {
                                                $x++;
                                                $item_name = $rowdet['item_name'] ?: 'صنف غير معروف';
                                                // Fix: Use correct column names from database schema
                                                // qty should be qty_out (for sales) or qty_in - qty_out
                                                $qty = floatval($rowdet['qty_out']) - floatval($rowdet['qty_in']);
                                                $price = floatval($rowdet['price']);
                                                // Fix: Use det_value instead of val
                                                $subtotal = floatval($rowdet['det_value']);
                                                $barcode = $rowdet['barcode'] ?: $rowdet['item_id'];
                                                ?>
                                        <div class="card mb-1 item-card-order shadow-sm border-start border-3"
                                            data-itemid="<?= $barcode ?>"
                                            data-fat-id="<?= $rowdet['id'] ?>"
                                            style="border-color: #0a7ea4 !important; max-width: 100%;">
                                            <div class="card-body p-1">
                                                <div class="d-flex align-items-center gap-1"
                                                    style="font-size: 0.75rem;">
                                                    <span class="badge bg-primary"
                                                        style="font-size: 0.7rem; min-width: 25px;">#<?= $x ?></span>

                                                    <div style="flex: 1; min-width: 0;">
                                                        <input type="hidden" value='<?= $rowdet['item_id'] ?>'
                                                            name="itmname[]">
                                                        <input type="hidden" class="barcode" value="<?= $barcode ?>">
                                                        <div class="text-truncate fw-bold" style="font-size: 1rem;"
                                                            title="<?= $item_name ?>"><?= $item_name ?></div>
                                                    </div>

                                                    <div style="width: 65px;">
                                                        <small class="d-block text-center text-muted"
                                                            style="font-size: 0.6rem; margin-bottom: 1px;">كمية</small>
                                                        <input type="number"
                                                            class="form-control form-control-sm text-center quantityInput nozero fw-bold"
                                                            value="<?= $qty ?>" name="itmqty[]" min="1" step="0.1"
                                                            style="width: 100%; font-size: 0.75rem; padding: 3px; border: 2px solid #ff6347; height: 26px;"
                                                            title="الكمية">
                                                        <input type="hidden" name="u_val[]" value="1">
                                                    </div>

                                                    <div style="width: 55px;">
                                                        <small class="d-block text-center text-muted"
                                                            style="font-size: 0.6rem; margin-bottom: 1px;">سعر</small>
                                                        <input type="number"
                                                            class="form-control form-control-sm text-center priceInput nozero"
                                                            value="<?= number_format($price, 2, '.', '') ?>"
                                                            name="itmprice[]" step="0.01"
                                                            style="width: 100%; font-size: 0.7rem; padding: 3px; height: 26px;"
                                                            title="السعر">
                                                    </div>

                                                    <div style="width: 60px;">
                                                        <small class="d-block text-center text-muted"
                                                            style="font-size: 0.6rem; margin-bottom: 1px;">قيمة</small>
                                                        <input type="hidden" name="itmdisc[]" value="0">
                                                        <input type="text"
                                                            class="form-control form-control-sm text-center subtotal fw-bold"
                                                            readonly value="<?= number_format($subtotal, 2, '.', '') ?>"
                                                            name="itmval[]"
                                                            style="width: 100%; font-size: 0.7rem; padding: 3px; background: #fff3cd; height: 26px;"
                                                            title="القيمة">
                                                    </div>

                                                    <button type="button" class="btn btn-danger btn-sm delRow"
                                                        data-fat-id="<?= $rowdet['id'] ?>"
                                                        style="padding: 2px 6px; font-size: 0.7rem;" title="حذف صنف">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>

                        </div>

                            <!-- الحسابات والدفع -->
                            <div class="order-info-footer">
                                <input type="hidden" name="headtotal" id="total" value="0.00">
                                <input name="headplus" type="hidden">
                                <input type="hidden" name="headnet" id="net_val" value="0">
                                <input type="hidden" name="headdisc" id="discount" value="0">
                                <span id="total_display" class="d-none">0.00 ج.م</span>
                                <span id="net_display" class="d-none">0.00 ج.م</span>
                                <div id="selectedTableDisplay" class="badge bg-primary text-white mb-2"
                                    style="font-size: 0.7rem; display: none;">
                                    <i class="fas fa-chair me-1"></i><span id="selectedTableName"></span>
                                </div>
                                <textarea class="form-control form-control-sm mb-2" name="info" id="info" rows="1"
                                    placeholder="ملاحظات..."
                                    style="font-size: 0.75rem; padding: 0.35rem;"><?php echo isset($_GET['edit']) ? htmlspecialchars($rowed['info']) : ''; ?></textarea>
                                <!-- حفظ طلب الطاولة معلّقاً: مخزون فقط بلا قيود حتى السداد -->
                                <button type="button" class="btn btn-warning w-100 fw-bold mb-2 d-none"
                                    id="holdOrderBtn" onclick="submitPOS('hold');">
                                    <i class="fas fa-clock me-1"></i>حفظ معلّق
                                    <span class="fw-bold ms-1" id="hold_total_display">0.00 ج.م</span>
                                </button>
                                <button type="button" class="btn btn-primary w-100 order-pay-btn"
                                    data-bs-toggle="modal" data-bs-target="#paymentModal">
                                    <i class="fas fa-money-bill-wave me-1"></i>دفع وحفظ
                                    <span class="fw-bold ms-1" id="total_display_btn">0.00 ج.م</span>
                                </button>
                            </div>
                    </div>
                </div>

                <!-- القسم الأوسط - الأصناف -->
                <div class="col-lg-8">
                    <div class="card shadow-sm items-section-card d-flex flex-column h-100">
                        <div class="items-section-top">
                            <div class="card-header bg-primary text-white py-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">
                                        <i class="fas fa-boxes me-2"></i>الأصناف المتاحة
                                    </h6>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="input-group" style="width: 280px;">
                                            <input type="text" class="scnd form-control border-start-0" id="itemFilterInput"
                                                placeholder="فلترة الأصناف..." autocomplete="off"
                                                title="اضغط Ctrl+F للتركيز | Escape للمسح"
                                                style="font-size: 0.9rem;">
                                            <button class="btn btn-outline-secondary" type="button" id="clearFilter"
                                                title="مسح الفلتر">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="items-categories-bar">
                                <div class="d-flex flex-wrap gap-1" id="categoriesContainer">
                                    <?php
                                $rescategories = $conn->query("SELECT * FROM item_group WHERE isdeleted = 0 ORDER BY gname");
                                if ($rescategories && $rescategories->num_rows > 0) {
                                    echo '<button type="button" class="btn btn-primary btn-sm category-btn active" data-category="all">
                                            <i class="fas fa-th me-1"></i>الكل
                                          </button>';
                                    
                                    while ($rowcategory = $rescategories->fetch_assoc()) {
                                        $categoryId = isset($rowcategory['id']) ? $rowcategory['id'] : '';
                                        $categoryName = isset($rowcategory['gname']) ? htmlspecialchars($rowcategory['gname']) : '';
                                        echo '<button type="button" class="btn btn-outline-primary btn-sm category-btn" data-category="'.$categoryId.'">
                                                <i class="fas fa-folder me-1"></i>'.$categoryName.'
                                              </button>';
                                    }
                                } else {
                                    echo '<button type="button" class="btn btn-primary btn-sm category-btn active" data-category="all">
                                            <i class="fas fa-th me-1"></i>الكل
                                          </button>';
                                }
                                ?>
                                </div>
                            </div>
                        </div>
                        <div class="card-body items-grid-scroll flex-grow-1 min-h-0 p-2">
                            <!-- شبكة الأصناف -->
                            <div class="row g-3" id="itemsGrid">
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
                                <div class="col-lg-3  col-md-4 col-sm-6 item-wrapper"
                                    data-category="<?= $itemCategory ?>">
                                    <div class="card item-card itemButton  shadow-sm border-0"
                                        data-item-id="<?= $itemId ?>" data-item-name="<?= $itemName ?>"
                                        data-item-price="<?= $itemPrice ?>" data-item-barcode="<?= $itemBarcode ?>"
                                        data-item-desc="<?= $itemDesc ?>" style="cursor: pointer;">
                                        <div class="card-body p-2 text-center">
                                            <!-- الصورة -->
                                            <div class="item-image-container mb-2 ratio ratio-1x1 rounded overflow-hidden"
                                                style="cursor: pointer; background: #f8f9fa;">
                                                <?php if (!empty($itemImage) && file_exists($itemImage)): ?>
                                                <img src="<?= $itemImage ?>"
                                                    class="item-image-click object-fit-cover w-100 h-100"
                                                    style="width: 100%; height: 100%;">
                                                <?php else: ?>
                                                <div
                                                    class="d-flex align-items-center justify-content-center item-image-click">
                                                    <i class="fas fa-box fa-3x text-primary opacity-50"></i>
                                                </div>
                                                <?php endif; ?>
                                            </div>

                                            <!-- اسم الصنف -->
                                            <h6 class="card-title text-truncate mb-1" style="font-size: 1.5rem; font-weight: 600;"
                                                title="<?= $itemName ?>">
                                                <?= $itemName ?>
                                            </h6>

                                            <!-- السعر -->
                                            <div class="bg-primary rounded px-2 py-1 mb-2">
                                                <p class="card-text fw-bold text-white mb-0" style="font-size: 1.1rem;">
                                                    <?= number_format($itemPrice, 2) ?> <span
                                                        class="text-white opacity-75">ج.م</span>
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
        <div class="modal-dialog modal-dialog-centered payment-modal-dialog">
            <div class="modal-content payment-modal-content">
                <div class="modal-header payment-modal-header py-2 px-3">
                    <h6 class="modal-title mb-0" id="paymentModalLabel">الدفع والإجماليات</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body payment-modal-body p-2">
                    <div class="pay-summary-strip">
                        <div class="pay-stat">
                            <span class="pay-stat-lbl">الإجمالي</span>
                            <strong class="pay-stat-val text-primary" id="modal_total">0.00 ج.م</strong>
                        </div>
                        <div class="pay-stat">
                            <span class="pay-stat-lbl">الصافي</span>
                            <strong class="pay-stat-val text-success" id="modal_net">0.00 ج.م</strong>
                        </div>
                        <div class="pay-stat">
                            <span class="pay-stat-lbl">الباقي</span>
                            <strong class="pay-stat-val text-danger" id="modal_change">0.00 ج.م</strong>
                        </div>
                    </div>

                    <div class="row g-1 pay-row">
                        <div class="col-6">
                            <label class="pay-lbl" for="modal_discperc">خصم %</label>
                            <input class="form-control form-control-sm text-center" type="number"
                                id="modal_discperc" value="0" min="0" max="100" step="0.1">
                        </div>
                        <div class="col-6">
                            <label class="pay-lbl" for="modal_discount">ق. الخصم</label>
                            <input class="form-control form-control-sm text-center" type="number"
                                id="modal_discount" value="0" step="0.01">
                        </div>
                    </div>

                    <div class="pay-old-paid" id="old_payment_container" style="display: none;">
                        <span><i class="fas fa-history"></i> مدفوع سابقاً</span>
                        <strong class="text-info" id="old_paid_display">0.00 ج.م</strong>
                    </div>

                    <div class="row g-1 pay-row">
                        <div class="col-6">
                            <label class="pay-lbl" for="payment_fund_id"><i class="fas fa-money-bill"></i> كاش</label>
                            <select class="form-select form-select-sm pay-select" id="payment_fund_id">
                                <?php
                                $resfund = $conn->query("SELECT * FROM `acc_head` WHERE is_fund = 1 AND is_basic = 0 AND isdeleted = 0 ORDER BY aname");
                                while ($rowfund = $resfund->fetch_assoc()) {
                                    $selected = '';
                                    if ($rowstg['def_pos_fund'] == $rowfund['id']) {
                                        $selected = 'selected';
                                    }
                                ?>
                                <option <?= $selected ?> value="<?= $rowfund['id'] ?>"><?= $rowfund['aname'] ?></option>
                                <?php } ?>
                            </select>
                            <div class="input-group input-group-sm mt-1">
                                <input class="form-control text-center fw-bold" type="number"
                                    id="modal_paid_cash" value="0.00" step="0.01" min="0">
                                <span class="input-group-text">ج.م</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="pay-lbl" for="payment_bank_id"><i class="fas fa-credit-card"></i> صرافة</label>
                            <select class="form-select form-select-sm pay-select" id="payment_bank_id">
                                <option value="">-- بنك --</option>
                                <?php
                                $resbank = $conn->query("SELECT * FROM `acc_head` WHERE (parent_id = 124 OR code LIKE '124%') AND is_basic = 0 AND isdeleted = 0 ORDER BY aname");
                                while ($rowbank = $resbank->fetch_assoc()) { ?>
                                <option value="<?= $rowbank['id'] ?>"><?= $rowbank['aname'] ?></option>
                                <?php } ?>
                            </select>
                            <div class="input-group input-group-sm mt-1">
                                <input class="form-control text-center fw-bold" type="number"
                                    id="modal_paid_bank" value="0.00" step="0.01" min="0">
                                <span class="input-group-text">ج.م</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer payment-modal-footer py-2 px-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">إلغاء</button>
                    <?php if (isset($id)): ?>
                    <button type="button" id="paymentSaveBtn" class="btn btn-warning btn-sm text-dark fw-bold" onclick="submitPOS('save');">حفظ التعديل</button>
                    <?php else: ?>
                    <button type="button" id="paymentSaveBtn" class="btn btn-success btn-sm" onclick="submitPOS('save');">حفظ</button>
                    <?php endif; ?>
                    <button type="button" id="paymentPrintBtn" class="btn btn-primary btn-sm" onclick="submitPOS('cash');">حفظ وطباعة</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal الطاولات (full screen) -->
    <?php include('includes/pos_tables_modal.php'); ?>

    <!-- Modal تفاصيل الصنف -->
    <div class="modal fade" id="itemDetailsModal" tabindex="-1" aria-labelledby="itemDetailsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="itemDetailsModalLabel">
                        <i class="fas fa-info-circle me-2"></i>تفاصيل الصنف
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <div id="modal_item_image"
                            style="height: 200px; overflow: hidden; border-radius: 12px; background: #f8f9fa;">
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


    <!-- Modal إغلاق الشيفت -->
    <div class="modal fade" id="closeShiftModal" tabindex="-1" aria-labelledby="closeShiftModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
                <div class="modal-header bg-warning text-dark border-0 py-3">
                    <h5 class="modal-title fw-bold" id="closeShiftModalLabel">
                        <i class="fas fa-power-off me-2"></i>إغلاق الشيفت
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    <div class="text-center mb-3">
                        <i class="fas fa-exclamation-triangle fa-2x text-warning mb-2"></i>
                        <h6 class="fw-bold text-dark">هل أنت متأكد من إغلاق الشيفت؟</h6>
                        <p class="text-muted small">سيتم احتساب مبيعات الوردية الحالية وإغلاقها.</p>
                    </div>

                    <div class="table-responsive mb-3">
                        <table class="table table-bordered table-sm text-center mb-0" style="font-size: 0.85rem;">
                            <thead class="bg-light text-dark">
                                <tr>
                                    <th class="py-2">البيان</th>
                                    <th class="py-2">القيمة</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-start py-2 px-3 fw-bold">عدد الطلبات</td>
                                    <td class="py-2 px-3 fw-bold text-primary" id="tbl_total_orders">0</td>
                                </tr>
                                <tr>
                                    <td class="text-start py-2 px-3">إجمالي المبيعات</td>
                                    <td class="py-2 px-3" id="tbl_total_gross">0.00 ج.م</td>
                                </tr>
                                <tr>
                                    <td class="text-start py-2 px-3">إجمالي الخصومات</td>
                                    <td class="py-2 px-3 text-danger" id="tbl_total_discount">0.00 ج.م</td>
                                </tr>
                                <tr class="table-success fw-bold">
                                    <td class="text-start py-2 px-3">صافي المبيعات</td>
                                    <td class="py-2 px-3 text-success" id="tbl_total_net">0.00 ج.م</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="card bg-light border-0 mb-3" style="border-radius: 8px;">
                        <div class="card-body p-3">
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label small mb-1">مبلغ بداية الشيفت</label>
                                    <input type="text" class="form-control form-control-sm text-center fw-bold bg-white" id="shift_start_cash" readonly value="0.00">
                                </div>
                                <div class="col-6">
                                    <label class="form-label small mb-1">صافي النقدية الواردة</label>
                                    <input type="text" class="form-control form-control-sm text-center fw-bold bg-white" id="shift_cash_received" readonly value="0.00">
                                </div>
                                <div class="col-6">
                                    <label class="form-label small mb-1 text-danger fw-bold">المصروفات (مصروف)</label>
                                    <input type="number" class="form-control form-control-sm text-center fw-bold border-danger" id="shift_expenses" placeholder="0.00" step="0.01" value="0">
                                </div>
                                <div class="col-6">
                                    <label class="form-label small mb-1 text-success fw-bold">الباقي (مستنتج)</label>
                                    <input type="text" class="form-control form-control-sm text-center fw-bold bg-white text-success border-success" id="shift_fund_after" readonly value="0.00">
                                </div>
                                <div class="col-12 mt-2">
                                    <label class="form-label small mb-1">ملاحظات إضافية</label>
                                    <textarea class="form-control form-control-sm" id="shift_notes" rows="2" placeholder="أدخل أي ملاحظات إضافية هنا..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 px-4">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>إلغاء
                    </button>
                    <button type="button" class="btn btn-warning btn-sm px-4 fw-bold" onclick="closeShift()">
                        <i class="fas fa-check-circle me-1"></i>إغلاق الشيفت
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal معلومات الشيفت -->
    <div class="modal fade" id="shiftInfoModal" tabindex="-1" aria-labelledby="shiftInfoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
                <div class="modal-header bg-info text-white border-0 py-3">
                    <h5 class="modal-title fw-bold" id="shiftInfoModalLabel">
                        <i class="fas fa-user-clock me-2"></i>معلومات الشيفت الحالي
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm text-center mb-0" style="font-size: 0.9rem;">
                            <thead class="bg-light text-dark">
                                <tr>
                                    <th class="py-2">البيان</th>
                                    <th class="py-2">القيمة</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-start py-2 px-3 fw-bold">الكاشير</td>
                                    <td class="py-2 px-3 fw-bold text-primary" id="shift_cashier_name">-</td>
                                </tr>
                                <tr>
                                    <td class="text-start py-2 px-3">عدد الطلبات</td>
                                    <td class="py-2 px-3" id="shift_total_orders">0</td>
                                </tr>
                                <tr>
                                    <td class="text-start py-2 px-3">إجمالي المبيعات</td>
                                    <td class="py-2 px-3" id="shift_total_gross">0.00 ج.م</td>
                                </tr>
                                <tr>
                                    <td class="text-start py-2 px-3">إجمالي الخصومات</td>
                                    <td class="py-2 px-3 text-danger" id="shift_total_discount">0.00 ج.م</td>
                                </tr>
                                <tr class="table-success fw-bold">
                                    <td class="text-start py-2 px-3">صافي المبيعات</td>
                                    <td class="py-2 px-3 text-success" id="shift_total_net">0.00 ج.م</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 px-4">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>إغلاق
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal الدليفري -->
    <div class="modal fade" id="deliveryModal" tabindex="-1" aria-labelledby="deliveryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="deliveryModalLabel">
                        <i class="fas fa-motorcycle me-2"></i>بيانات العميل - دليفري
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- مندوب التوصيل -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">الطيار (مندوب التوصيل)</label>
                        <select class="form-select" id="delivery_person_id" required>
                            <option value="">-- اختر الطيار --</option>
                            <?php
                            $resdelivery = $conn->query("SELECT * FROM `acc_head` WHERE code LIKE '126%' AND isdeleted = 0 ORDER BY aname");
                            while ($rowdelivery = $resdelivery->fetch_assoc()) {
                                echo '<option value="' . $rowdelivery['id'] . '">' . htmlspecialchars($rowdelivery['aname']) . ' (' . $rowdelivery['code'] . ')</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">رقم العميل</label>
                        <input type="text" class="form-control" id="customer_phone"
                            placeholder="أدخل رقم العميل ثم اخرج من الحقل للبحث"
                            autocomplete="off">
                        <small class="text-muted">يتم البحث عند الخروج من حقل التليفون</small>
                    </div>

                    <div id="customer_status" class="mb-2"></div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">اسم العميل</label>
                        <input type="text" class="form-control" id="customer_name" placeholder="اسم العميل">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">العنوان</label>
                        <textarea class="form-control" id="customer_address" rows="2"
                            placeholder="عنوان العميل"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>إلغاء
                    </button>
                    <button type="button" class="btn btn-primary" id="saveCustomerBtn" onclick="saveCustomerData()">
                        <i class="fas fa-save me-1"></i>حفظ
                    </button>
                    <button type="button" class="btn btn-success" id="confirmOrderBtn" onclick="confirmDeliveryOrder()">
                        <i class="fas fa-check me-1"></i>تأكيد الطلب
                    </button>
                </div>
            </div>
        </div>
    </div>


    <!-- Scripts - jQuery (CDN for reliability) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="assets/libs/sweetalert2/sweetalert2.min.js"></script>
    <script>
        if (typeof jQuery === 'undefined') { 
            document.write('<script src="plugins/jquery/jquery.min.js"><\/script>'); 
        }
    </script>
    <script src="assets/libs/bootstrap.bundle.min.js"></script>
    
    <!-- إصلاح مشكلة Service Worker -->
    <script>
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.getRegistrations().then(function(registrations) {
            for(let registration of registrations) {
                registration.unregister();
            }
        });
    }
    </script>
    
    <script src="js/pos_config_loader.js?v=<?= time() ?>"></script>
    <script src="js/pos_offline_adapter.js?v=<?= time() ?>"></script>
    <script src="js/pos_barcode.js?v=<?= time() ?>"></script>
    <script src="js/pos_tables_modal.js?v=<?= time() ?>"></script>
    
    <script>
        // تفعيل النظام الأوفلاين فور تحميل الصفحة
        $(document).ready(function() {
            console.log('🚀 Starting POS Offline System...');
            
            // التحقق من حالة الاتصال
            if (!navigator.onLine) {
                console.log('📴 Device is offline - Offline mode activated');
            } else {
                console.log('🌐 Device is online - Offline adapter ready');
            }
        });
    </script>

    <script>
        // دالة طباعة تقرير المبيعات اليومية
        function printDailySalesReport() {
            console.log('Opening daily sales report...');
            window.open('print/daily_sales_receipt.php', '_blank');
        }
        
        // دالة طباعة تقرير مبيعات الشيفت الشخصية
        function printShiftSalesReport() {
            console.log('Opening shift sales report...');
            window.open('print/shift_sales_receipt.php', '_blank');
        }
        
        // كود البحث المباشر
        $(document).ready(function () {
            console.log('Search script loaded');

            // F1 للتركيز على العنصر الذي يحمل كلاس frst
            // F2 للتركيز على العنصر الذي يحمل كلاس scnd
            // F1 للتركيز على العنصر الذي يحمل كلاس frst
            // F2 للتركيز على العنصر الذي يحمل كلاس scnd
            $(document).keydown(function (e) {
                if (e.key === 'F1') {
                    e.preventDefault();
                    $('.frst').focus();
                } else if (e.key === 'F2') {
                    e.preventDefault();
                    $('.scnd').focus();
                }
            });

            // ملاحظة: كود السيرش والفلترة موجود في pos_barcode.js (محسّن للأداء)

            // تحديث معلومات الشيفت على الزر عند تحميل الصفحة
            updateShiftInfoButton();

            let globalStartCash = 0;
            let globalNetSales = 0;

            function calculateShiftRemaining() {
                const expenses = parseFloat($('#shift_expenses').val()) || 0;
                const remaining = globalStartCash + globalNetSales - expenses;
                $('#shift_fund_after').val(remaining.toFixed(2) + ' ج.م');
            }

            window.closeShift = function () {
                const expenses = parseFloat($('#shift_expenses').val()) || 0;
                const notes = $('#shift_notes').val() || '';
                const fundAfter = globalStartCash + globalNetSales - expenses;
                const cash = globalNetSales;

                const form = $('<form>', {
                    method: 'POST',
                    action: 'close_shift.php'
                });

                form.append($('<input>', { type: 'hidden', name: 'expenses', value: expenses }));
                form.append($('<input>', { type: 'hidden', name: 'exp_notes', value: 'مصروفات شيفت' }));
                form.append($('<input>', { type: 'hidden', name: 'cash', value: cash }));
                form.append($('<input>', { type: 'hidden', name: 'fund_after', value: fundAfter }));
                form.append($('<input>', { type: 'hidden', name: 'fund_before', value: globalStartCash }));
                form.append($('<input>', { type: 'hidden', name: 'notes', value: notes }));

                $('body').append(form);
                form.submit();
            };

            $('#shift_expenses').on('input', calculateShiftRemaining);

            $('#closeShiftModal').on('show.bs.modal', function () {
                loadShiftPreview();
            });

            $('#shiftInfoModal').on('show.bs.modal', function () {
                loadShiftInfo();
            });

            function loadShiftPreview() {
                $.ajax({
                    url: 'do/get_shift_preview.php',
                    method: 'GET',
                    success: function(data) {
                        try {
                            var response = (typeof data === 'object') ? data : JSON.parse(data);

                            if (response.success) {
                                globalStartCash = parseFloat(response.data.start_cash) || 0;
                                globalNetSales = parseFloat(response.data.total_net) || 0;

                                $('#tbl_total_orders').text(response.data.total_orders);
                                $('#tbl_total_gross').text(parseFloat(response.data.total_gross).toFixed(2) + ' ج.م');
                                $('#tbl_total_discount').text(parseFloat(response.data.total_discount).toFixed(2) + ' ج.م');
                                $('#tbl_total_net').text(globalNetSales.toFixed(2) + ' ج.م');

                                $('#shift_start_cash').val(globalStartCash.toFixed(2) + ' ج.م');
                                $('#shift_cash_received').val(globalNetSales.toFixed(2) + ' ج.م');

                                calculateShiftRemaining();
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'خطأ',
                                    text: response.error || 'لا يمكن تحميل بيانات الوردية حالياً'
                                });
                            }
                        } catch (e) {
                            console.error('Error parsing shift preview:', e);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', error);
                    }
                });
            }

            function loadShiftInfo() {
                $.ajax({
                    url: 'do/get_shift_preview.php',
                    method: 'GET',
                    success: function(data) {
                        try {
                            var response = (typeof data === 'object') ? data : JSON.parse(data);

                            if (response.success) {
                                $('#shift_cashier_name').text(response.data.cashier_name || 'الكاشير');
                                $('#shift_total_orders').text(response.data.total_orders || 0);
                                $('#shift_total_gross').text(parseFloat(response.data.total_gross || 0).toFixed(2) + ' ج.م');
                                $('#shift_total_discount').text(parseFloat(response.data.total_discount || 0).toFixed(2) + ' ج.م');
                                $('#shift_total_net').text(parseFloat(response.data.total_net || 0).toFixed(2) + ' ج.م');
                            } else {
                                $('#shift_cashier_name').text('خطأ');
                                $('#shift_total_orders').text('0');
                                $('#shift_total_gross').text('0.00 ج.م');
                                $('#shift_total_discount').text('0.00 ج.م');
                                $('#shift_total_net').text('0.00 ج.م');
                            }
                        } catch (e) {
                            console.error('Error parsing shift info:', e);
                            $('#shift_cashier_name').text('خطأ');
                            $('#shift_total_orders').text('0');
                            $('#shift_total_gross').text('0.00 ج.م');
                            $('#shift_total_discount').text('0.00 ج.م');
                            $('#shift_total_net').text('0.00 ج.م');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', error);
                        $('#shift_cashier_name').text('خطأ');
                        $('#shift_total_orders').text('0');
                        $('#shift_total_gross').text('0.00 ج.م');
                        $('#shift_total_discount').text('0.00 ج.م');
                        $('#shift_total_net').text('0.00 ج.م');
                    }
                });
            }

            function updateShiftInfoButton() {
                $.ajax({
                    url: 'do/get_shift_preview.php',
                    method: 'GET',
                    success: function(data) {
                        try {
                            var response = (typeof data === 'object') ? data : JSON.parse(data);

                            if (response.success) {
                                var cashierName = response.data.cashier_name || 'الكاشير';
                                var totalNet = parseFloat(response.data.total_net || 0).toFixed(2);
                                $('#shift_info_display').text(cashierName + ' - ' + totalNet + ' ج.م');
                            } else {
                                $('#shift_info_display').text('غير متاح');
                            }
                        } catch (e) {
                            console.error('Error parsing shift info for button:', e);
                            $('#shift_info_display').text('غير متاح');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', error);
                        $('#shift_info_display').text('غير متاح');
                    }
                });
            }

            // وظائف الدليفري
            window.openDeliveryModal = function () {
                $('#deliveryModal').modal('show');
            };

            // البحث عن العميل عند الخروج من حقل التليفون
            let lastSearchedPhone = '';
            let isSearchingCustomer = false;

            function setCustomerStatus(type, message) {
                if (!message) {
                    $('#customer_status').html('');
                    return;
                }
                const icons = {
                    success: 'fa-check-circle',
                    info: 'fa-user-plus',
                    warning: 'fa-exclamation-triangle',
                    danger: 'fa-times-circle'
                };
                const icon = icons[type] || 'fa-info-circle';
                $('#customer_status').html(
                    '<div class="alert alert-' + type + ' mb-0 py-2">' +
                    '<i class="fas ' + icon + ' me-2"></i>' + message +
                    '</div>'
                );
            }

            $('#customer_phone').on('input', function () {
                $(this).removeClass('border-success border-info border-danger border-warning');
                if ($(this).val().trim() !== lastSearchedPhone) {
                    lastSearchedPhone = '';
                    setCustomerStatus('', '');
                }
            });

            $('#customer_phone').on('blur', function () {
                const phone = $(this).val().trim();
                if (!phone || phone.length < 3) {
                    return;
                }
                if (phone === lastSearchedPhone || isSearchingCustomer) {
                    return;
                }
                searchCustomerDynamic(phone);
            });

            function searchCustomerDynamic(phone) {
                isSearchingCustomer = true;
                lastSearchedPhone = phone;

                $('#customer_phone')
                    .addClass('border-warning')
                    .attr('placeholder', 'جاري البحث...');
                setCustomerStatus('info', 'جاري البحث عن العميل...');

                $.ajax({
                    url: 'do/search_customer.php',
                    method: 'POST',
                    data: { phone: phone },
                    dataType: 'json',
                    timeout: 15000,
                    success: function (response) {
                        $('#customer_phone')
                            .removeClass('border-warning')
                            .attr('placeholder', 'أدخل رقم العميل ثم اخرج من الحقل للبحث');

                        if (!response || typeof response !== 'object') {
                            $('#customer_phone').addClass('border-danger');
                            setCustomerStatus('warning', 'استجابة غير متوقعة — يمكنك إدخال البيانات يدوياً');
                            return;
                        }

                        if (response.found) {
                            $('#customer_phone').addClass('border-success');
                            $('#customer_name').val(response.name || '');
                            $('#customer_address').val(response.address || '');
                            setCustomerStatus('success', 'تم العثور على العميل');
                            $('#saveCustomerBtn').html('<i class="fas fa-save me-1"></i>حفظ التعديل');
                        } else {
                            $('#customer_phone').addClass('border-info');
                            setCustomerStatus('info', 'عميل جديد — يرجى إدخال الاسم والعنوان');
                            $('#saveCustomerBtn').html('<i class="fas fa-save me-1"></i>حفظ');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('Dynamic search AJAX Error:', {
                            status: status,
                            http: xhr.status,
                            error: error,
                            responseText: xhr.responseText
                        });

                        $('#customer_phone')
                            .removeClass('border-warning')
                            .addClass('border-danger')
                            .attr('placeholder', 'أدخل رقم العميل ثم اخرج من الحقل للبحث');

                        // حاول قراءة JSON من رد 500 إن وُجد
                        let msg = 'تعذر البحث — يمكنك إدخال البيانات يدوياً';
                        try {
                            const errBody = xhr.responseJSON || JSON.parse(xhr.responseText || '{}');
                            if (errBody && (errBody.message || errBody.error)) {
                                msg = errBody.message || errBody.error;
                            }
                        } catch (e) { /* ignore */ }

                        if (xhr.status === 500) {
                            msg = 'خطأ في الخادم (500) — ' + msg;
                        } else if (status === 'timeout') {
                            msg = 'انتهت مهلة البحث — يمكنك إدخال البيانات يدوياً';
                        }

                        setCustomerStatus('warning', msg);
                        // اسمح بإعادة المحاولة عند blur التالي
                        lastSearchedPhone = '';
                    },
                    complete: function () {
                        isSearchingCustomer = false;
                    }
                });
            }

            window.searchCustomer = function () {
                const phone = $('#customer_phone').val().trim();
                if (!phone) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'تنبيه',
                        text: 'يرجى إدخال رقم العميل'
                    });
                    return;
                }

                if (phone.length < 3) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'تنبيه',
                        text: 'يرجى إدخال 3 أرقام على الأقل'
                    });
                    return;
                }

                searchCustomerDynamic(phone);
            };

            // دالة مساعدة لتنظيف النماذج (واجهة المودال فقط — بدون مسح الحقول المخفية في الفورم)
            window.clearDeliveryForm = function () {
                $('#customer_phone').val('').removeClass('border-success border-info border-danger border-warning')
                    .attr('placeholder', 'أدخل رقم العميل ثم اخرج من الحقل للبحث');
                $('#customer_name').val('');
                $('#customer_address').val('');
                setCustomerStatus('', '');
                // لا تمسح #delivery_person_id هنا — القيمة متزامنة مع الفورم للحفظ/الطباعة
                $('#saveCustomerBtn').html('<i class="fas fa-save me-1"></i>حفظ').show();
                $('#confirmOrderBtn').show();
                lastSearchedPhone = '';
                isSearchingCustomer = false;
            };

            // بعد التأكيد: نترك بيانات الطيار/العميل في الفورم؛ ننظّف حقول الإدخال فقط عند إعادة الفتح
            $('#deliveryModal').on('hidden.bs.modal', function () {
                // لا نستدعي clearDeliveryForm() بالكامل حتى لا نفقد الطيار قبل الدفع
                $('#customer_phone').removeClass('border-success border-info border-danger border-warning');
                setCustomerStatus('', '');
            });

            $('#deliveryModal').on('show.bs.modal', function () {
                // أعد تعبئة المودال من الفورم لو كانت محفوظة
                const phone = ($('input[name="delivery_customer_phone"]').val() || '').trim();
                const name = ($('input[name="delivery_customer_name"]').val() || '').trim();
                const address = ($('input[name="delivery_customer_address"]').val() || '').trim();
                const driverId = ($('input[name="delivery_person_id"]').val() || '').trim();
                if (phone) $('#customer_phone').val(phone);
                if (name) $('#customer_name').val(name);
                if (address) $('#customer_address').val(address);
                if (driverId) $('#delivery_person_id').val(driverId);
            });

            $('#deliveryModal').on('shown.bs.modal', function () {
                $('#customer_phone').trigger('focus');
            });
            // Listen for changes on the 'age' radio buttons
            $('input[name="age"]').change(function(){
                if ($(this).val() == '2') {
                    if (window.PosTablesPanel) {
                        window.PosTablesPanel.open();
                    }
                } else if ($(this).val() == '3') {
                    openDeliveryModal();
                } else {
                    clearDeliveryFieldsFromForm();
                }

                if ($(this).val() != '2' && window.PosTablesPanel) {
                    $('#selected_table_id').val(0);
                    $('#selected_table_name').val('');
                    window.PosTablesPanel.setTableBadge('');
                    window.PosTablesPanel.clearFinalizeFlag();
                }
            });

            // زر "حفظ معلّق" خاص بطلبات الطاولة فقط
            window.syncHoldButton = function () {
                const isTable = $('input[name="age"]:checked').val() == '2';
                $('#holdOrderBtn').toggleClass('d-none', !isTable);
            };
            $('input[name="age"]').on('change', window.syncHoldButton);
            window.syncHoldButton();

            // تفضيل "الطاولة افتراضياً" — يُحفظ سنة كاملة في الكوكيز
            $('#defaultTableChk').on('change', function () {
                const on = this.checked ? '1' : '0';
                const exp = new Date(Date.now() + 365 * 24 * 60 * 60 * 1000).toUTCString();
                document.cookie = 'pos_default_table=' + on + '; expires=' + exp + '; path=/; SameSite=Lax';
                Swal.fire({
                    icon: 'success',
                    title: this.checked ? 'الطاولة هي الافتراضي' : 'تم إلغاء الافتراضي',
                    timer: 1200,
                    showConfirmButton: false
                });
            });

            // لو الطاولة هي الافتراضي، افتح اللوحة مباشرة ليختار الكاشير طاولته
            <?php if ($default_table_pref && !isset($_GET['edit']) && !isset($_GET['table'])): ?>
            if (!parseInt($('#selected_table_id').val() || 0)) {
                setTimeout(function () {
                    if (window.PosTablesPanel) window.PosTablesPanel.open();
                }, 400);
            }
            <?php endif; ?>

            window.getDeliveryCustomerData = function () {
                let phone = ($('#customer_phone').val() || '').trim();
                if (!phone) {
                    phone = ($('input[name="delivery_customer_phone"]').val() || '').trim();
                }

                let name = ($('#customer_name').val() || '').trim();
                if (!name) {
                    name = ($('input[name="delivery_customer_name"]').val() || '').trim();
                }

                let address = ($('#customer_address').val() || '').trim();
                if (!address) {
                    address = ($('input[name="delivery_customer_address"]').val() || '').trim();
                }

                return { phone, name, address };
            };

            window.syncDeliveryFieldsToForm = function (phone, name, address) {
                const form = document.getElementById('posForm');
                if (!form) return;

                // لو الـ select فاضي (بعد إغلاق المودال) احتفظ بالقيمة المخفية السابقة
                const driverFromSelect = ($('#delivery_person_id').val() || '').trim();
                const driverFromForm = ($('input[name="delivery_person_id"]').val() || '').trim();
                const driverId = driverFromSelect || driverFromForm;

                const fields = {
                    delivery_customer_name: name,
                    delivery_customer_phone: phone,
                    delivery_customer_address: address,
                    delivery_person_id: driverId
                };

                Object.entries(fields).forEach(function ([fieldName, value]) {
                    let input = form.querySelector('input[name="' + fieldName + '"]');
                    if (!input) {
                        input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = fieldName;
                        form.appendChild(input);
                    }
                    input.value = value;
                });
            };

            window.clearDeliveryFieldsFromForm = function () {
                ['delivery_customer_name', 'delivery_customer_phone', 'delivery_customer_address', 'delivery_person_id'].forEach(function (fieldName) {
                    document.querySelectorAll('#posForm input[name="' + fieldName + '"]').forEach(function (input) {
                        input.remove();
                    });
                });
            };

            window.persistDeliveryCustomer = function (phone, name, address, onSuccess) {
                $.ajax({
                    url: 'do/save_customer.php',
                    method: 'POST',
                    data: { phone: phone, name: name, address: address },
                    dataType: 'json',
                    timeout: 15000,
                    success: function (response) {
                        if (response && response.success && typeof onSuccess === 'function') {
                            onSuccess(response);
                        } else if (!response || !response.success) {
                            console.error('Delivery customer save error:', response && (response.error || response.message));
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('Delivery customer AJAX error:', {
                            status: status,
                            http: xhr.status,
                            error: error,
                            responseText: xhr.responseText
                        });
                    }
                });
            };

            window.confirmDeliveryOrder = function () {
                const customer = getDeliveryCustomerData();
                const driverId = ($('#delivery_person_id').val() || '').trim();

                if (!customer.phone || !customer.name || !customer.address) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'تنبيه',
                        text: 'يرجى ملء جميع الحقول'
                    });
                    return;
                }
                if (!driverId) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'تنبيه',
                        text: 'يرجى اختيار الطيار'
                    });
                    return;
                }

                syncDeliveryFieldsToForm(customer.phone, customer.name, customer.address);
                persistDeliveryCustomer(customer.phone, customer.name, customer.address);

                $('#deliveryModal').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'تم بنجاح',
                    text: 'تم تأكيد طلب الدليفري وحفظ بيانات العميل',
                    timer: 2000,
                    showConfirmButton: false
                });
            };

            window.saveCustomerData = function () {
                const customer = getDeliveryCustomerData();
                const phone = customer.phone;
                const name = customer.name;
                const address = customer.address;

                if (!phone || !name || !address) {
                    alert('يرجى ملء جميع الحقول');
                    return;
                }

                $.ajax({
                    url: 'do/save_customer.php',
                    method: 'POST',
                    data: {
                        phone: phone,
                        name: name,
                        address: address
                    },
                    dataType: 'json',
                    timeout: 15000,
                    success: function (response) {
                        if (response && response.success) {
                            syncDeliveryFieldsToForm(phone, name, address);
                            Swal.fire({
                                icon: 'success',
                                title: 'تم بنجاح',
                                text: 'تم حفظ بيانات العميل بنجاح',
                                timer: 2000,
                                showConfirmButton: false
                            });
                            $('#saveCustomerBtn').html('<i class="fas fa-save me-1"></i>حفظ التعديل');
                        } else {
                            var errorMsg = (response && (response.message || response.error))
                                ? (response.message || response.error)
                                : 'حدث خطأ في حفظ البيانات';
                            Swal.fire({
                                icon: 'error',
                                title: 'خطأ',
                                text: errorMsg
                            });
                            console.error('Save error:', response);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('AJAX Error:', {
                            status: status,
                            http: xhr.status,
                            error: error,
                            responseText: xhr.responseText
                        });

                        let errorMsg = 'حدث خطأ في الاتصال';
                        try {
                            const errBody = xhr.responseJSON || JSON.parse(xhr.responseText || '{}');
                            if (errBody && (errBody.message || errBody.error)) {
                                errorMsg = errBody.message || errBody.error;
                            }
                        } catch (e) { /* ignore */ }

                        if (xhr.status === 500) {
                            errorMsg = 'خطأ في الخادم (500): ' + errorMsg;
                        } else if (status === 'timeout') {
                            errorMsg = 'انتهت مهلة الحفظ — حاول مرة أخرى';
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ',
                            text: errorMsg
                        });
                    }
                });
            };
        });
    </script>
    <script>
    // override submitPOS to ensure new logic is used immediately (bypassing cache)
    window.submitPOS = function(action) {
        console.log('✅ submitPOS (Inline Override) called with action:', action);
        
        const form = document.getElementById('posForm');
        if (!form) {
            console.error('❌ Form with id "posForm" not found!');
            Swal.fire({
                icon: 'error',
                title: 'خطأ نظام',
                text: 'حدث خطأ في النظام. يرجى إعادة تحميل الصفحة.'
            });
            return false;
        }
        
        if (typeof validatePOSForm === 'function' && !validatePOSForm()) {
            return false;
        }

        // طلب طاولة بلا طاولة = بيانات ناقصة
        if ($('input[name="age"]:checked').val() == '2' && !(parseInt($('#selected_table_id').val()) || 0)) {
            $('#paymentModal').modal('hide');
            Swal.fire({
                icon: 'warning',
                title: 'اختر الطاولة',
                text: 'لا يمكن حفظ طلب نوع "طاولة" بدون تحديد طاولة',
                confirmButtonText: 'اختيار طاولة'
            }).then(function () {
                if (window.PosTablesPanel) window.PosTablesPanel.open();
            });
            return false;
        }

        if ($('input[name="age"]:checked').val() == '3' && typeof getDeliveryCustomerData === 'function') {
            const customer = getDeliveryCustomerData();
            const driverId = (
                ($('#delivery_person_id').val() || '').trim()
                || ($('input[name="delivery_person_id"]').val() || '').trim()
            );
            if (!customer.phone || !customer.name || !customer.address) {
                Swal.fire({
                    icon: 'warning',
                    title: 'بيانات الدليفري',
                    text: 'يرجى إدخال اسم العميل ورقم الهاتف والعنوان'
                });
                if (typeof openDeliveryModal === 'function') {
                    openDeliveryModal();
                }
                return false;
            }
            if (!driverId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'الطيار',
                    text: 'يرجى اختيار الطيار قبل حفظ الطلب'
                });
                if (typeof openDeliveryModal === 'function') {
                    openDeliveryModal();
                }
                return false;
            }
            if (typeof syncDeliveryFieldsToForm === 'function') {
                syncDeliveryFieldsToForm(customer.phone, customer.name, customer.address);
            }
        }
        
        // جمع بيانات الدفع
        const isHold = (action === 'hold');
        let paidCash = isHold ? 0 : (parseFloat($('#modal_paid_cash').val()) || 0);
        let paidBank = isHold ? 0 : (parseFloat($('#modal_paid_bank').val()) || 0);
        let fundId = isHold ? '' : $('#payment_fund_id').val();
        let bankId = isHold ? '' : $('#payment_bank_id').val();
        let net = parseFloat($('#net_val').val()) || 0;
        
        // في حالة التعديل - اجمع المدفوع القديم + الجديد
        // (الباك اند يحذف سندات الدفع القديمة ويعيد إنشاءها بالمبلغ المرسل)
        let editIdForPayment = $('#edit_order_id').val();
        if (!isHold && editIdForPayment) {
            let savedPaidCash = parseFloat($('#edit_paid_cash').val()) || 0;
            let savedPaidBank = parseFloat($('#edit_paid_bank').val()) || 0;
            paidCash = savedPaidCash + paidCash;
            paidBank = savedPaidBank + paidBank;
            // استرجاع الصندوق/البنك المحفوظ إذا لم يختر المستخدم غيره
            let savedFundId = $('#edit_payment_fund_id').val();
            let savedBankId = $('#edit_payment_bank_id').val();
            if ((!fundId || fundId == '0') && savedFundId && savedFundId != '0') {
                fundId = savedFundId;
            }
            if ((!bankId || bankId == '0' || bankId == '') && savedBankId && savedBankId != '0') {
                bankId = savedBankId;
            }
            console.log('✏️ Edit Mode - Cumulative Payment: old_cash=' + savedPaidCash + ' old_bank=' + savedPaidBank + ' => total_cash=' + paidCash + ', total_bank=' + paidBank);
        }
        
        console.log('=== INLINE PAYMENT DATA DEBUG ===');
        console.log('modal_paid_cash value:', $('#modal_paid_cash').val());
        console.log('modal_paid_bank value:', $('#modal_paid_bank').val());
        console.log('payment_fund_id value:', $('#payment_fund_id').val());
        console.log('payment_bank_id value:', $('#payment_bank_id').val());
        console.log('Processed:', {
            paidCash: paidCash,
            paidBank: paidBank,
            fundId: fundId,
            bankId: bankId,
            net: net
        });
        console.log('==================================');
        
        // التحقق من صحة البيانات
        if (paidCash > 0 && (!fundId || fundId == '0')) {
            Swal.fire({
                icon: 'warning',
                title: 'تنبيه',
                text: 'يجب اختيار الصندوق عند الدفع كاش'
            });
            return false;
        }
        
        if (paidBank > 0 && (!bankId || bankId == '0' || bankId == '')) {
            Swal.fire({
                icon: 'warning',
                title: 'تنبيه',
                text: 'يجب اختيار البنك عند الدفع صرافة'
            });
            return false;
        }
        
        // إضافة حقول الدفع المخفية
        let paidCashInput = form.querySelector('input[name="paid_cash"]');
        if (!paidCashInput) {
            paidCashInput = document.createElement('input');
            paidCashInput.type = 'hidden';
            paidCashInput.name = 'paid_cash';
            form.appendChild(paidCashInput);
        }
        paidCashInput.value = paidCash;

        let paidBankInput = form.querySelector('input[name="paid_bank"]');
        if (!paidBankInput) {
            paidBankInput = document.createElement('input');
            paidBankInput.type = 'hidden';
            paidBankInput.name = 'paid_bank';
            form.appendChild(paidBankInput);
        }
        paidBankInput.value = paidBank;

        let paymentFundInput = form.querySelector('input[name="payment_fund_id"]');
        if (!paymentFundInput) {
            paymentFundInput = document.createElement('input');
            paymentFundInput.type = 'hidden';
            paymentFundInput.name = 'payment_fund_id';
            form.appendChild(paymentFundInput);
        }
        paymentFundInput.value = fundId;

        let paymentBankInput = form.querySelector('input[name="payment_bank_id"]');
        if (!paymentBankInput) {
            paymentBankInput = document.createElement('input');
            paymentBankInput.type = 'hidden';
            paymentBankInput.name = 'payment_bank_id';
            form.appendChild(paymentBankInput);
        }
        paymentBankInput.value = bankId || '';

        // إضافة المدفوع الإجمالي (للتوافق مع الكود القديم)
        let totalPaid = paidCash + paidBank;
        let paidInput = form.querySelector('input[name="paid"]');
        if (!paidInput) {
            paidInput = document.createElement('input');
            paidInput.type = 'hidden';
            paidInput.name = 'paid';
            form.appendChild(paidInput);
        }
        paidInput.value = totalPaid;

        // Check for Edit ID
        let editId = $('#edit_order_id').val();
        if (editId) {
            console.log('✏️ Edit Mode: ID', editId);
            let editIdInput = form.querySelector('input[name="edit_id"]');
            if (!editIdInput) {
                editIdInput = document.createElement('input');
                editIdInput.type = 'hidden';
                editIdInput.name = 'edit_id';
                form.appendChild(editIdInput);
            }
            editIdInput.value = editId;
        }

        const existingSubmits = form.querySelectorAll('input[name="submit"]');
        existingSubmits.forEach(input => input.remove());
        
        const submitInput = document.createElement('input');
        submitInput.type = 'hidden';
        submitInput.name = 'submit';
        submitInput.value = action;
        form.appendChild(submitInput);

        // أزرار محدّدة بالـ id: المحدّد النصي القديم لم يكن يطابق زر الحفظ فبقي مفعّلاً
        // وأتاح ضغطاً مزدوجاً يحفظ الطلب مرتين
        let saveBtn = $('#paymentSaveBtn');
        let printBtn = $('#paymentPrintBtn');
        let holdBtn = $('#holdOrderBtn');

        const spinner = '<i class="fas fa-spinner fa-spin"></i> جاري الحفظ...';
        const saveBtnHtml = saveBtn.html();
        const printBtnHtml = printBtn.html();
        const holdBtnHtml = holdBtn.html();

        const restoreButtons = function () {
            saveBtn.prop('disabled', false).html(saveBtnHtml);
            printBtn.prop('disabled', false).html(printBtnHtml);
            holdBtn.prop('disabled', false).html(holdBtnHtml);
        };

        saveBtn.prop('disabled', true).html(spinner);
        printBtn.prop('disabled', true).html(spinner);
        if (isHold) holdBtn.prop('disabled', true).html(spinner);

        // قبل إخفاء المودال: اعتبر الدفع جارياً حتى لا يُلغى فصل الأصناف بالخطأ
        if (window.PosTablesPanel && typeof PosTablesPanel.markPaymentSaving === 'function') {
            PosTablesPanel.markPaymentSaving();
        }

        $('#paymentModal').modal('hide');

        const tableId = parseInt($('#selected_table_id').val()) || 0;
        const isTableOrder = $('input[name="age"]:checked').val() == '2' && tableId > 0;
        const finalizeInput = form.querySelector('input[name="finalize_order"]');
        if (isHold && finalizeInput) finalizeInput.value = '0';
        const isFinalizing = !isHold && !!(finalizeInput && finalizeInput.value === '1');

        if ((isTableOrder || isFinalizing) && (action === 'save' || action === 'cash' || action === 'hold')) {
            let ajaxInput = form.querySelector('input[name="ajax_save"]');
            if (!ajaxInput) {
                ajaxInput = document.createElement('input');
                ajaxInput.type = 'hidden';
                ajaxInput.name = 'ajax_save';
                form.appendChild(ajaxInput);
            }
            ajaxInput.value = '1';

            const formData = new FormData(form);
            fetch(form.action, { method: 'POST', body: formData })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    restoreButtons();
                    if (data.success) {
                        $(document).trigger('pos:payment-saved');
                        let editIdInput = form.querySelector('input[name="edit_id"]');
                        if (!editIdInput) {
                            editIdInput = document.createElement('input');
                            editIdInput.type = 'hidden';
                            editIdInput.name = 'edit_id';
                            form.appendChild(editIdInput);
                        }

                        if (data.finalized) {
                            // الطلب أُغلق: أفرِغ السياق حتى لا يُعدّل طلب مقفول
                            if (finalizeInput) finalizeInput.value = '0';
                            editIdInput.value = '';
                            $('#edit_order_id, #selected_order_id').val('');
                            $('#selected_table_id').val(0);
                            $('#selected_table_name').val('');
                            $('#age1').prop('checked', true);
                            if (typeof window.syncHoldButton === 'function') window.syncHoldButton();
                            $('#selectedTableDisplay').hide();
                            if (window.PosTablesPanel) PosTablesPanel.setTableBadge('');
                            $('#itemData').empty();
                            if (typeof window.updateItemCount === 'function') window.updateItemCount();
                            if (typeof window.updateTotal === 'function') window.updateTotal();
                        } else {
                            // الطاولة ما زالت مفتوحة: أكمل الإضافة على نفس الطلب
                            $('#edit_order_id').val(data.order_id);
                            editIdInput.value = data.order_id;
                        }

                        Swal.fire({ icon: 'success', title: 'تم', text: data.message, timer: 1500, showConfirmButton: false });
                        if (window.PosTablesPanel) PosTablesPanel.refresh();
                        if (action === 'cash' && data.order_id) {
                            window.open('print/receipt.php?id=' + data.order_id, '_blank');
                        }
                        setTimeout(updateShiftInfoButton, 500);
                    } else {
                        $(document).trigger('pos:payment-failed');
                        Swal.fire({ icon: 'error', title: 'خطأ', text: data.message || 'فشل الحفظ' });
                    }
                })
                .catch(function() {
                    restoreButtons();
                    $(document).trigger('pos:payment-failed');
                    Swal.fire({ icon: 'error', title: 'خطأ', text: 'فشل الاتصال بالخادم' });
                });
            return true;
        }

        form.submit();

        // تحديث معلومات الشيفت بعد الحفظ
        setTimeout(function() {
            updateShiftInfoButton();
        }, 1000);

        return true;
    };
    </script>


    <!-- Modal إغلاق الشيفت -->
    <div class="modal fade" id="shiftPreviewModal" tabindex="-1" aria-labelledby="shiftPreviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="shiftPreviewModalLabel">
                        <i class="fas fa-receipt me-2"></i>ملخص الشيفت الحالي
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="shiftPreview">
                        <div class="text-center py-4">
                            <div class="spinner-border text-success" role="status">
                                <span class="visually-hidden">جاري التحميل...</span>
                            </div>
                            <p class="mt-2 text-muted">جاري تحميل بيانات الشيفت...</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <form action="close_shift.php" method="POST" id="closeShiftForm" class="d-inline">
                        <!-- بيانات المصروفات والعهدة -->
                        <div class="input-group mb-3">
                            <span class="input-group-text">مصروفات</span>
                            <input type="number" class="form-control" name="expenses" value="0" step="0.01">
                        </div>
                         <div class="input-group mb-3">
                            <span class="input-group-text">السبب</span>
                            <input type="text" class="form-control" name="exp_notes" placeholder="سبب المصروفات...">
                        </div>
                        <div class="input-group mb-3">
                            <span class="input-group-text">عهدة تالية</span>
                            <input type="number" class="form-control" name="fund_after" value="0" step="0.01">
                        </div>
                        <div class="input-group mb-3">
                            <span class="input-group-text">الكاش الفعلي</span>
                            <input type="number" class="form-control" name="cash" step="0.01" required placeholder="المبلغ الموجود بالدرج">
                        </div>
                         <div class="input-group mb-3">
                            <span class="input-group-text">ملاحظات</span>
                            <input type="text" class="form-control" name="notes" placeholder="ملاحظات الإغلاق...">
                        </div>
                        <button type="submit" class="btn btn-success fw-bold w-100">
                            <i class="fas fa-check-circle me-1"></i>تأكيد إغلاق الشيفت
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Items Modal -->
    <div class="modal fade" id="orderItemsModal" tabindex="-1" aria-labelledby="orderItemsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white py-2">
                    <h6 class="modal-title mb-0" id="orderItemsModalLabel">
                        <i class="fas fa-minus-circle me-1"></i> حذف صنف من الطلب
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-2" id="orderItemsBody">
                    <div class="text-center py-4">
                        <div class="spinner-border text-danger"></div>
                        <p class="mt-2 text-muted">جاري التحميل...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders Offcanvas -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="recentOrdersModal" aria-labelledby="recentOrdersModalLabel"
        style="width: 80%; max-width: 1200px;">
        <div class="offcanvas-header bg-primary text-white">
            <h5 class="offcanvas-title" id="recentOrdersModalLabel">
                الطلبات الأخيرة (آخر 10 طلبات)
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped table-bordered mb-0">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th>#</th>
                            <th>رقم الفاتورة</th>
                            <th>التاريخ</th>
                            <th>العميل</th>
                            <th>النوع</th>
                            <th>الإجمالي</th>
                            <th>الحالة</th>
                            <th>العمليات</th>
                        </tr>
                    </thead>
                    <tbody id="recentOrdersList">
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">جاري التحميل...</span>
                                </div>
                                <p class="mt-2">جاري تحميل الطلبات...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Recent Orders Button Handler
            $('#recentOrdersBtn2').click(function() {
                var offcanvas = new bootstrap.Offcanvas(document.getElementById('recentOrdersModal'));
                offcanvas.show();
                loadRecentOrders();
            });

            function loadRecentOrders() {
                $('#recentOrdersList').html('<tr><td colspan="8" class="text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">جاري تحميل الطلبات...</p></td></tr>');
                
                $.ajax({
                    url: 'ajax/get_recent_orders.php',
                    method: 'GET',
                    success: function(response) {
                        try {
                            // If response is a string (due to accidental whitespace/BOM), parse it
                            if (typeof response === 'string') {
                                // Try to extract JSON if mixed with HTML
                                const jsonMatch = response.match(/\{[\s\S]*\}/);
                                if (jsonMatch) {
                                    response = JSON.parse(jsonMatch[0]);
                                } else {
                                    response = JSON.parse(response);
                                }
                            }

                            if (response.success && response.orders) {
                                var html = '';
                                if (response.orders.length === 0) {
                                    html = '<tr><td colspan="8" class="text-center py-4">لا توجد طلبات حديثة</td></tr>';
                                } else {
                                    response.orders.forEach(function(order, index) {
                                        var statusBadge = order.status === 'ملغى' ? 'bg-danger' : 'bg-success';
                                        var typeBadge = order.type === 'دليفري' ? 'bg-info text-dark' : (order.type === 'طاولة' ? 'bg-warning text-dark' : 'bg-secondary');
                                        
                                        html += `
                                            <tr>
                                                <td>${index + 1}</td>
                                                <td class="fw-bold">${order.invoice_number}</td>
                                                <td>${order.date}</td>
                                                <td>${order.customer_name}</td>
                                                <td><span class="badge ${typeBadge}">${order.type}</span></td>
                                                <td class="fw-bold text-primary">${parseFloat(order.total).toFixed(2)}</td>
                                                <td><span class="badge ${statusBadge}">${order.status}</span></td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <button type="button" class="btn btn-warning" onclick="editOrderWithPassword(${order.id})" title="تعديل">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-success" onclick="window.location.href='pos_barcode.php?add_item=${order.id}'" title="إضافة صنف">
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-danger" onclick="showOrderItems(${order.id})" title="حذف صنف">
                                                            <i class="fas fa-minus"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-secondary" onclick="reprintOrder(${order.id})" title="طباعة">
                                                            <i class="fas fa-print"></i>
                                                        </button>
                                                        ${order.status !== 'ملغى' ? `
                                                        <button type="button" class="btn btn-dark" onclick="deleteOrder(${order.id})" title="حذف الطلب">
                                                            <i class="fas fa-trash"></i>
                                                        </button>` : ''}
                                                    </div>
                                                </td>
                                            </tr>
                                        `;
                                    });
                                }
                                $('#recentOrdersList').html(html);
                            } else {
                                $('#recentOrdersList').html('<tr><td colspan="8" class="text-center text-danger py-4">فشل تحميل البيانات: ' + (response.error || 'خطأ غير معروف') + '</td></tr>');
                            }
                        } catch (e) {
                            console.error('Error parsing recent orders:', e);
                            $('#recentOrdersList').html('<tr><td colspan="8" class="text-center text-danger py-4">خطأ في معالجة البيانات</td></tr>');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', error);
                        $('#recentOrdersList').html('<tr><td colspan="8" class="text-center text-danger py-4">خطأ في الاتصال بالخادم</td></tr>');
                    }
                });
            }

            // Global functions for actions
            window.reprintOrder = function(orderId) {
                // Use existing print function logic or redirect
                // Usually calling the print endpoint directly
                 window.open('print/receipt.php?order_id=' + orderId, '_blank');
            };

            window.showOrderItems = function(orderId) {
                $('#orderItemsBody').html('<div class="text-center py-4"><div class="spinner-border text-info"></div><p class="mt-2 text-muted">جاري التحميل...</p></div>');
                var modal = new bootstrap.Modal(document.getElementById('orderItemsModal'));
                modal.show();

                $.ajax({
                    url: 'ajax/get_order_items.php',
                    method: 'GET',
                    data: { order_id: orderId },
                    dataType: 'json',
                    success: function(res) {
                        if (!res.success || !res.items.length) {
                            $('#orderItemsBody').html('<div class="alert alert-warning m-2">لا توجد أصناف في هذا الطلب</div>');
                            return;
                        }
                        var html = '<div class="table-responsive"><table class="table table-sm table-bordered mb-0">';
                        html += '<thead class="table-dark"><tr><th>#</th><th>الصنف</th><th>الكمية</th><th>السعر</th><th>الإجمالي</th><th>حذف</th></tr></thead><tbody id="items-tbody-' + orderId + '">';
                        res.items.forEach(function(item, i) {
                            html += `<tr id="item-row-${item.id}">
                                <td class="text-center">${i+1}</td>
                                <td>${item.item_name}</td>
                                <td class="text-center">${parseFloat(item.qty).toFixed(2)}</td>
                                <td class="text-center">${parseFloat(item.price).toFixed(2)}</td>
                                <td class="text-center fw-bold text-primary">${parseFloat(item.det_value).toFixed(2)}</td>
                                <td class="text-center">
                                    <button class="btn btn-danger btn-sm" onclick="deleteOrderItem(${item.id}, ${orderId})" title="حذف الصنف">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>`;
                        });
                        html += '</tbody></table></div>';
                        html += `<div class="d-flex justify-content-between align-items-center px-2 pt-2">
                            <span class="text-muted small">عدد الأصناف: <strong>${res.items.length}</strong></span>
                            <span class="fw-bold text-success">الإجمالي: <span id="items-total-${orderId}">${parseFloat(res.total).toFixed(2)}</span> ج.م</span>
                        </div>`;
                        $('#orderItemsBody').html(html);
                    },
                    error: function() {
                        $('#orderItemsBody').html('<div class="alert alert-danger m-2">خطأ في الاتصال بالخادم</div>');
                    }
                });
            };

            window.deleteOrderItem = function(fatId, orderId) {
                Swal.fire({
                    title: 'حذف الصنف',
                    text: 'هل تريد حذف هذا الصنف من الطلب؟',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'نعم، احذف',
                    cancelButtonText: 'إلغاء'
                }).then(function(result) {
                    if (!result.isConfirmed) return;
                    $.ajax({
                        url: 'ajax/delete_order_item.php',
                        method: 'POST',
                        data: { fat_id: fatId },
                        dataType: 'json',
                        success: function(res) {
                            if (res.success) {
                                $('#item-row-' + fatId).fadeOut(300, function() { $(this).remove(); });
                                if (res.new_total !== undefined) {
                                    $('#items-total-' + orderId).text(parseFloat(res.new_total).toFixed(2));
                                }
                                Swal.fire({ icon: 'success', title: 'تم الحذف', timer: 900, showConfirmButton: false });
                                loadRecentOrders(); // تحديث القائمة الخارجية
                            } else {
                                Swal.fire('خطأ', res.error || 'فشل الحذف', 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('خطأ', 'خطأ في الاتصال بالخادم', 'error');
                        }
                    });
                });
            };

            // ---- Password helpers ----
            const ORDER_PASSWORD = '1234'; // ← غيّر الباسورد من هنا

            function askPassword(title, onSuccess) {
                Swal.fire({
                    title: title,
                    html: `<input type="password" id="swal-order-pass" class="swal2-input" placeholder="أدخل كلمة المرور" autocomplete="off">`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'تأكيد',
                    cancelButtonText: 'إلغاء',
                    confirmButtonColor: '#3085d6',
                    didOpen: () => {
                        document.getElementById('swal-order-pass').focus();
                        // السماح بـ Enter داخل حقل الباسورد
                        document.getElementById('swal-order-pass').addEventListener('keydown', function(e) {
                            if (e.key === 'Enter') Swal.clickConfirm();
                        });
                    },
                    preConfirm: () => {
                        const pass = document.getElementById('swal-order-pass').value;
                        if (pass !== ORDER_PASSWORD) {
                            Swal.showValidationMessage('كلمة المرور غير صحيحة!');
                            return false;
                        }
                        return true;
                    }
                }).then((result) => {
                    if (result.isConfirmed) onSuccess();
                });
            }

            window.editOrderWithPassword = function(orderId) {
                askPassword('🔐 تعديل الطلب', function() {
                    window.location.href = 'pos_barcode.php?edit=' + orderId;
                });
            };

            window.addItemWithPassword = function(orderId) {
                askPassword('🔐 إضافة صنف للطلب', function() {
                    window.location.href = 'pos_barcode.php?add_item=' + orderId;
                });
            };
            // ---- End Password helpers ----

            window.deleteOrder = function(orderId) {
                askPassword('🔐 حذف الطلب', function() {
                Swal.fire({
                    title: 'هل أنت متأكد؟',
                    text: "هل أنت متأكد من حذف هذا الطلب؟ لا يمكن التراجع عن هذا الإجراء.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'نعم، احذفه!',
                    cancelButtonText: 'إلغاء'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'ajax/cancel_order.php',
                            method: 'POST',
                            data: { id: orderId },
                            success: function(response) {
                                try {
                                    if (typeof response === 'string') response = JSON.parse(response);
                                    if (response.success) {
                                        Swal.fire(
                                            'تم الحذف!',
                                            'تم حذف الطلب بنجاح.',
                                            'success'
                                        );
                                        loadRecentOrders(); // Reload list
                                    } else {
                                        Swal.fire(
                                            'خطأ!',
                                            'فشل الحذف: ' + (response.error || 'خطأ غير معروف'),
                                            'error'
                                        );
                                    }
                                } catch (e) {
                                    Swal.fire(
                                        'خطأ!',
                                        'خطأ في استجابة الخادم',
                                        'error'
                                    );
                                }
                            },
                            error: function() {
                                Swal.fire(
                                    'خطأ!',
                                    'خطأ في الاتصال',
                                    'error'
                                );
                            }
                        });
                    }
                });
                }); // end askPassword
            };
        });
    </script>

    <script>
        // وضع إضافة صنف (قادم من صفحة الطاولات): تركيز تلقائي على حقل الباركود
        $(document).ready(function() {
            if ($('#add_item_mode').val() === '1') {
                setTimeout(function() {
                    $('#barcodeInput').trigger('focus');
                }, 500);
            }
        });
    </script>

