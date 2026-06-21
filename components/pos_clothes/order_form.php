<?php
// جلب المخزن الافتراضي
$default_store_id = 1;

if (!empty($rowstg['def_pos_store'])) {
    $default_store_id = $rowstg['def_pos_store'];
} else {
    $resstore = $conn->query("SELECT id FROM `acc_head` WHERE is_stock = 1 AND isdeleted = 0 LIMIT 1;");
    if ($resstore && $resstore->num_rows > 0) {
        $rowstore = $resstore->fetch_assoc();
        $default_store_id = $rowstore['id'];
    }
}
?>

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
            
            <div class="mb-2">
                <div class="input-group input-group-sm" style="border: 2px solid var(--primary-violet); border-radius: 4px; overflow: hidden;">
                    <span class="input-group-text fw-bold" style="background-color: var(--primary-violet); color: white; border: none;">
                        <i class="fas fa-barcode me-1"></i>باركود
                    </span>
                    <input type="text" class="frst form-control" id="barcodeSearch" placeholder="امسح أو اكتب الباركود..." autocomplete="off" style="border: none;">
                </div>
            </div>
            
            <div class="mb-2">
                <div class="btn-group w-100" role="group">
                    <input type="radio" class="btn-check" id="age1" name="age" value="1" checked>
                    <label class="btn btn-outline-secondary btn-sm" for="age1">
                        <i class="fas fa-shopping-bag me-1"></i>بيع
                    </label>
                    
                    <input type="radio" class="btn-check" id="age2" name="age" value="2">
                    <label class="btn btn-outline-secondary btn-sm" for="age2">
                        <i class="fas fa-bookmark me-1"></i>حجز
                    </label>
                    
                    <input type="radio" class="btn-check" id="age3" name="age" value="3">
                    <label class="btn btn-outline-secondary btn-sm" for="age3">
                        <i class="fas fa-truck me-1"></i>توصيل
                    </label>

                    <input type="radio" class="btn-check" id="age4" name="age" value="4">
                    <label class="btn btn-outline-danger btn-sm" for="age4">
                        <i class="fas fa-undo me-1"></i>مردود
                    </label>
                </div>
            </div>

            <div class="row g-1 mb-2">
                <div class="col-4">
                    <label class="form-label" style="font-size: 0.75rem;">التاريخ</label>
                    <input type="date" name="pro_date" class="form-control form-control-sm" value="<?= $posdate ?>" style="font-size: 0.75rem; padding: 0.25rem 0.4rem;">
                </div>
                <div class="col-4">
                    <label class="form-label" style="font-size: 0.75rem;">الموظف</label>
                    <select name="emp_id" class="form-select form-select-sm select2-select" required style="font-size: 0.75rem; padding: 0.25rem 0.4rem;">
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
                <div class="col-4">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label mb-0" style="font-size: 0.75rem;">العميل</label>
                        <button type="button" class="btn btn-sm btn-primary py-0 px-1" style="font-size: 0.7rem; line-height: 1; min-height: auto;" data-bs-toggle="modal" data-bs-target="#addClientModal" title="إضافة عميل جديد">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    <select name="acc2_id" id="clientSelect" class="form-select form-select-sm select2-select" required style="font-size: 0.75rem; padding: 0.25rem 0.4rem;">
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
                <input type="hidden" name="accural_date" value="<?= date('Y-m-d') ?>">
            </div>

            <input type="hidden" name="store_id" value="<?= $default_store_id ?>">
        </div>

        <div class="order-items-list">
            <h6 class="fw-bold mb-2" style="font-size: 0.85rem;">الأصناف <span class="badge bg-secondary" id="itemCount">0</span></h6>
            <div id="itemData">
                <p class="text-muted text-center" style="font-size: 0.8rem;">لا توجد أصناف</p>
            </div>
        </div>

        <div class="order-footer">
            <div class="mb-2">
                <textarea class="form-control form-control-sm" name="info" rows="1" placeholder="ملاحظات..." style="font-size: 0.75rem;"></textarea>
            </div>

            <div class="row mb-2">
                <div class="col-6 text-center">
                    <small class="text-muted" style="font-size: 0.7rem;">الإجمالي</small>
                    <h6 class="mb-0" style="color: var(--primary-navy); font-size: 1rem;" id="total_display">0.00 ج.م</h6>
                    <input type="hidden" name="headtotal" id="total" value="0.00">
                    <input name="headplus" type="hidden" value="0">
                </div>
                <div class="col-6 text-center">
                    <small class="text-muted" style="font-size: 0.7rem;">الصافي</small>
                    <h6 class="mb-0" style="color: var(--primary-violet); font-size: 1rem;" id="net_display">0.00 ج.م</h6>
                    <input type="hidden" name="headnet" id="net_val" value="0">
                    <input type="hidden" name="headdisc" id="discount" value="0">
                </div>
            </div>

            <div class="d-grid gap-1">
                <button type="button" class="btn btn-violet btn-sm" data-bs-toggle="modal" data-bs-target="#paymentModal">
                    <i class="fas fa-money-bill-wave me-1"></i>دفع وحفظ
                    <div style="font-size: 0.75rem;" id="total_display_btn">0.00 ج.م</div>
                </button>
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="clearItems();" style="font-size: 0.75rem; padding: 0.25rem;">
                    <i class="fas fa-eraser me-1"></i>مسح الكل
                </button>
            </div>
        </div>
    </div>
</form>

<!-- مودال إضافة عميل جديد -->
<div class="modal fade" id="addClientModal" tabindex="-1" aria-labelledby="addClientModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: var(--primary-navy); color: white;">
                <h5 class="modal-title" id="addClientModalLabel">
                    <i class="fas fa-user-plus me-2"></i>إضافة عميل جديد
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="ajaxAddClientForm">
                    <div class="mb-3">
                        <label for="ajax_client_name" class="form-label fw-bold" style="font-size: 0.85rem;">اسم العميل <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" id="ajax_client_name" name="name" required placeholder="مثال: محمد أحمد" style="font-size: 0.85rem;">
                    </div>
                    <div class="mb-3">
                        <label for="ajax_client_phone" class="form-label fw-bold" style="font-size: 0.85rem;">الهاتف</label>
                        <input type="text" class="form-control form-control-sm" id="ajax_client_phone" name="phone" placeholder="مثال: 010xxxxxxxx" style="font-size: 0.85rem;">
                    </div>
                    <div class="mb-3">
                        <label for="ajax_client_address" class="form-label fw-bold" style="font-size: 0.85rem;">العنوان</label>
                        <input type="text" class="form-control form-control-sm" id="ajax_client_address" name="address" placeholder="مثال: القاهرة، مصر" style="font-size: 0.85rem;">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-violet btn-sm" id="saveAjaxClientBtn">حفظ العميل</button>
            </div>
        </div>
    </div>
</div>
