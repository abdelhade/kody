<?php
if (!isset($action_url)) {
    $action_url = "do/doadd_invoice.php";
}
?>
<!-- Main Content -->
<form action="<?= $action_url ?>" method="post" id="posForm" class="h-100 d-flex flex-column">
    <!-- Hidden Fields -->
    <input type="hidden" name="pro_tybe" value="9">
    <input type="hidden" name="pro_serial" value="0">
    <input type="hidden" name="pro_id" value="1">
    
    <!-- Order Settings (Collapsed by Default on Mobile) -->
    <div class="accordion accordion-flush mobile-settings-accordion" id="orderSettingsAccordion">
        <div class="accordion-item border-0">
            <h2 class="accordion-header" id="headingSettings">
                <button class="accordion-button collapsed py-2 bg-white text-primary fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSettings" aria-expanded="false" aria-controls="collapseSettings">
                    <i class="fas fa-cog me-2"></i> إعدادات الطلب
                </button>
            </h2>
            <div id="collapseSettings" class="accordion-collapse collapse" aria-labelledby="headingSettings" data-bs-parent="#orderSettingsAccordion">
                <div class="accordion-body p-2 bg-light">
                    <!-- نوع الطلب -->
                    <div class="btn-group w-100 mb-2" role="group">
                        <input type="radio" class="btn-check" id="age1" name="age" value="1" checked>
                        <label class="btn btn-outline-primary btn-sm" for="age1">تيك اواي</label>

                        <input type="radio" class="btn-check" id="age2" name="age" value="2" <?= isset($_GET['table']) ? " checked " : "" ?>>
                        <label class="btn btn-outline-primary btn-sm" for="age2">طاولة</label>

                        <input type="radio" class="btn-check" id="age3" name="age" value="3">
                        <label class="btn btn-outline-primary btn-sm" for="age3" onclick="openDeliveryModal()">دليفري</label>
                    </div>

                    <!-- التواريخ -->
                    <div class="row g-1 mb-2">
                        <div class="col-6">
                            <label class="form-label mb-0" style="font-size: 10px;">التاريخ</label>
                            <input type="date" name="pro_date" class="form-control form-control-sm" value="<?= $posdate ?>">
                        </div>
                        <div class="col-6">
                            <label class="form-label mb-0" style="font-size: 10px;">الاستحقاق</label>
                            <input type="date" name="accural_date" class="form-control form-control-sm" value="<?= isset($_GET['edit']) ? $rowed['accural_date'] : date('Y-m-d'); ?>">
                        </div>
                    </div>

                    <div class="row g-1 mb-2">
                        <!-- الطاولة -->
                        <div class="col-12">
                            <button type="button" class="btn btn-outline-primary btn-sm w-100" data-bs-toggle="modal" data-bs-target="#tablesModal">
                                <i class="fas fa-chair me-1"></i> <span id="selected_table_display">اختر طاولة</span>
                            </button>
                            <input type="hidden" id="selected_table_id" name="table_id" value="0">
                            <input type="hidden" id="selected_table_name" name="table_name" value="">
                            <input type="hidden" id="selected_order_id" name="edit" value="0">
                        </div>
                    </div>

                    <!-- الحقول الصغيرة (مخزن، موظف، عميل، صندوق) -->
                    <div class="row g-1">
                        <div class="col-6">
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
                            <select name="emp_id" class="form-select form-select-sm" required>
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
                                <option <?= $selected ?> value="<?= $rowemp['id'] ?>"><?= $rowemp['aname'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-6">
                            <select name="acc2_id" class="form-select form-select-sm" required>
                                <?php
                                $resclient = $conn->query("SELECT * FROM `acc_head` WHERE code like '122%'  AND is_basic = 0 AND isdeleted = 0;");
                                if(isset($_GET['edit'])){$rowed = $conn->query("SELECT * FROM ot_head where id = $id")->fetch_assoc();};
                                $first_client = true;
                                while ($rowclient = $resclient->fetch_assoc()) { 
                                    $selected = '';
                                    if($rowstg['def_pos_client'] == $rowclient['id']){
                                        $selected = "selected";
                                    } elseif(isset($_GET['edit']) && $rowed['acc1'] == $rowclient['id']){
                                        $selected = "selected";
                                    } elseif ($first_client && empty($rowstg['def_pos_client']) && !isset($_GET['edit'])) {
                                        $selected = "selected";
                                    }
                                    $first_client = false;
                                ?>
                                <option <?= $selected ?> value="<?= $rowclient['id'] ?>"><?= $rowclient['aname'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-6">
                            <select name="fund_id" class="form-select form-select-sm" required>
                                <?php
                                if(isset($_GET['edit'])){$rowed = $conn->query("SELECT * FROM ot_head where id = $id")->fetch_assoc();};
                                $resfund = $conn->query("SELECT * FROM `acc_head` WHERE is_fund =1 AND is_basic = 0 AND isdeleted = 0;");
                                $first_fund = true;
                                while ($rowfund = $resfund->fetch_assoc()) { 
                                    $selected = '';
                                    if($rowstg['def_pos_fund'] == $rowfund['id']){
                                        $selected = "selected";
                                    } elseif((isset($_GET['edit'])) && $rowed['acc_fund'] == $rowfund['id']){
                                        $selected = "selected";
                                    } elseif ($first_fund && empty($rowstg['def_pos_fund']) && !isset($_GET['edit'])) {
                                        $selected = "selected";
                                    }
                                    $first_fund = false;
                                ?>
                                <option <?= $selected ?> value="<?= $rowfund['id'] ?>"><?= $rowfund['aname'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- الأصناف المضافة (Cart Area) -->
    <div class="flex-grow-1 overflow-auto bg-white p-2" id="cartContainer">
        <div id="emptyCartMessage" class="text-center text-muted my-5">
            <i class="fas fa-shopping-basket fa-3x mb-3 opacity-25"></i>
            <p>الفاتورة فارغة، اضغط على زر الإضافة لإدراج أصناف</p>
        </div>
        
        <div id="itemData" class="d-flex flex-column gap-2 pb-5 mb-5">
            <?php
            // جلب بيانات الفاتورة لو في وضع التعديل
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
                    $qty = floatval($rowdet['qty_out']) - floatval($rowdet['qty_in']);
                    $price = floatval($rowdet['price']);
                    $subtotal = floatval($rowdet['det_value']);
                    $barcode = $rowdet['barcode'] ?: $rowdet['item_id'];
                    ?>
                    <div class="card item-card-order shadow-sm border-start border-4 border-primary position-relative" data-itemid="<?= $barcode ?>">
                        <div class="card-body p-2">
                            <input type="hidden" value='<?= $rowdet['item_id'] ?>' name="itmname[]">
                            <input type="hidden" class="barcode" value="<?= $barcode ?>">
                            
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="text-truncate fw-bold mb-0" style="font-size: 0.9rem; max-width: 85%;">
                                    <?= $item_name ?>
                                </h6>
                                <button type="button" class="btn btn-link text-danger p-0 delRow">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            
                            <div class="d-flex align-items-center justify-content-between mt-2">
                                <div class="d-flex align-items-center border rounded">
                                    <button type="button" class="btn btn-light btn-sm px-3 border-end minusBtn"><i class="fas fa-minus"></i></button>
                                    <input type="number" class="form-control form-control-sm text-center border-0 quantityInput nozero fw-bold px-1" 
                                           value="<?= $qty ?>" name="itmqty[]" min="1" step="0.1" style="width: 50px;">
                                    <button type="button" class="btn btn-light btn-sm px-3 border-start plusBtn"><i class="fas fa-plus"></i></button>
                                </div>
                                <input type="hidden" name="u_val[]" value="1">
                                
                                <div class="text-end">
                                    <input type="hidden" class="priceInput" value="<?= $price ?>" name="itmprice[]">
                                    <input type="hidden" name="itmdisc[]" value="0">
                                    <input type="hidden" class="subtotal" readonly value="<?= $subtotal ?>" name="itmval[]">
                                    <span class="text-primary fw-bold display-subtotal"><?= number_format($subtotal, 2, '.', '') ?> ج.م</span>
                                </div>
                            </div>
                        </div>
                    </div>
            <?php
                }
            }
            ?>
        </div>
    </div>

    <!-- Payment Area (Fixed Bottom) -->
    <div class="bg-white border-top shadow-lg z-2 p-2 mt-auto" style="position: sticky; bottom: 0; left: 0; right: 0;">
        <div class="d-flex justify-content-between align-items-center mb-2 px-1">
            <span class="text-muted fw-bold">الإجمالي</span>
            <h4 class="text-primary mb-0 fw-bold" id="total_display">0.00 ج.م</h4>
            <input type="hidden" name="headtotal" id="total" value="0">
            <input name="headplus" type="hidden">
            <input type="hidden" name="headnet" id="net_val" value="0">
            <input type="hidden" name="headdisc" id="discount" value="0">
            <textarea name="info" id="info" hidden></textarea>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-danger px-3 py-2" onclick="clearAllItems();" title="مسح">
                <i class="fas fa-trash"></i>
            </button>
            <button type="button" class="btn btn-success flex-grow-1 py-2 fw-bold fs-5 shadow-sm" data-bs-toggle="modal" data-bs-target="#paymentModal">
                <i class="fas fa-check-circle me-2"></i> دفع وطباعة
            </button>
        </div>
    </div>
</form>

<!-- Floating Action Button for Adding Item -->
<button class="btn btn-primary rounded-circle shadow-lg fab-add-item" data-bs-toggle="modal" data-bs-target="#searchItemModal">
    <i class="fas fa-plus"></i>
</button>

<!-- Search Item Modal (نظام المودالز) -->
<div class="modal fade" id="searchItemModal" tabindex="-1" aria-labelledby="searchItemModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-fullscreen-sm-down">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white py-2">
                <div class="input-group w-100 me-2">
                    <span class="input-group-text bg-white border-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" class="form-control border-0" id="mobileSearchInput" placeholder="بحث بالاسم أو الباركود..." autocomplete="off">
                    <button class="btn btn-light" type="button" id="barcodeScanBtn"><i class="fas fa-barcode"></i></button>
                </div>
                <button type="button" class="btn-close btn-close-white ms-0 me-2" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 bg-light">
                <div id="mobileSearchResults" class="list-group list-group-flush">
                    <!-- Results will be loaded here via JS -->
                    <div class="text-center text-muted p-5 mt-4">
                        <i class="fas fa-search fa-3x mb-3 opacity-25"></i>
                        <p>ابدأ البحث لإضافة أصناف</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal الدفع (Payment Modal) -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white py-3">
                <h5 class="modal-title fw-bold" id="paymentModalLabel"><i class="fas fa-wallet me-2"></i> تأكيد الدفع</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light p-3">
                
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body text-center p-3">
                        <p class="text-muted mb-1 fs-6">المطلوب دفعه</p>
                        <h1 class="text-success fw-bold mb-0" id="modal_net_display">0.00</h1>
                    </div>
                </div>

                <div class="mb-3 bg-white p-3 rounded shadow-sm">
                    <label class="form-label fw-bold">المدفوع كاش</label>
                    <div class="input-group input-group-lg">
                        <button class="btn btn-outline-secondary" type="button" onclick="document.getElementById('modal_paid_cash').value = parseFloat(document.getElementById('modal_net_display').innerText); calcRemaining();">كل المبلغ</button>
                        <input class="form-control text-center fw-bold text-primary" type="number" id="modal_paid_cash" value="0.00" step="0.01" min="0">
                        <span class="input-group-text">ج.م</span>
                    </div>
                    
                    <div class="d-flex justify-content-between mt-3 text-muted">
                        <span>المتبقي:</span>
                        <span class="text-danger fw-bold fs-5" id="modal_remaining">0.00</span>
                    </div>
                </div>

                <!-- Fields for pos.js to process correctly -->
                <input type="hidden" id="modal_total" value="0">
                <input type="hidden" id="modal_net" value="0">
                <input type="hidden" id="modal_discperc" value="0">
                <input type="hidden" id="modal_discount" value="0">
                <input type="hidden" id="modal_paid_bank" value="0">
                <input type="hidden" id="payment_fund_id" value="<?= $rowstg['def_pos_fund'] ?? 1 ?>">
                <input type="hidden" id="payment_bank_id" value="0">
                <input type="hidden" id="payment_method" value="cash">

            </div>
            <div class="modal-footer border-0 p-3 bg-white">
                <button type="button" class="btn btn-light py-2 w-100 mb-2" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-success btn-lg w-100 py-3 fw-bold shadow" id="confirmPaymentBtn">
                    <i class="fas fa-check me-2"></i> تأكيد وطباعة الفاتورة
                </button>
            </div>
        </div>
    </div>
</div>
<!-- Modal الطاولات -->
<div class="modal fade" id="tablesModal" tabindex="-1" aria-labelledby="tablesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white py-2">
                <h5 class="modal-title" id="tablesModalLabel">
                    <i class="fas fa-chair me-2"></i> اختر الطاولة
                </h5>
                <button type="button" class="btn-close btn-close-white ms-0 me-2" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light">
                <div id="tables-grid" class="row g-3">
                    <div class="col-12 text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">جاري التحميل...</span>
                        </div>
                        <p class="text-muted mt-2">جاري تحميل الطاولات...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 bg-white">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal إغلاق الشيفت -->
<div class="modal fade" id="closeShiftModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-warning text-dark py-2">
                <h5 class="modal-title"><i class="fas fa-power-off me-2"></i>إغلاق الشيفت</h5>
                <button type="button" class="btn-close ms-0 me-2" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center bg-light">
                <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                <h5>هل أنت متأكد من إغلاق الشيفت؟</h5>
            </div>
            <div class="modal-footer border-0 bg-white justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <a href="z_report.php" class="btn btn-warning fw-bold shadow-sm">الانتقال للإغلاق</a>
            </div>
        </div>
    </div>
</div>

<!-- Modal دليفري -->
<div class="modal fade" id="deliveryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white py-2">
                <h5 class="modal-title"><i class="fas fa-motorcycle me-2"></i>بيانات دليفري</h5>
                <button type="button" class="btn-close btn-close-white ms-0 me-2" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light">
                <div class="mb-3">
                    <label class="form-label fw-bold">رقم العميل</label>
                    <input type="text" class="form-control" id="customer_phone" placeholder="أدخل رقم العميل">
                </div>
                <div id="customer_result"></div>
            </div>
            <div class="modal-footer border-0 bg-white">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-primary" id="saveCustomerBtn" onclick="saveCustomerData()"><i class="fas fa-save me-1"></i>حفظ</button>
                <button type="button" class="btn btn-success" id="confirmOrderBtn" style="display:none;" onclick="confirmDeliveryOrder()"><i class="fas fa-check me-1"></i>تأكيد</button>
            </div>
        </div>
    </div>
</div>

<!-- مكتبة قارئ الباركود للكاميرا -->
<script src="https://unpkg.com/html5-qrcode"></script>
