<?php
if (!isset($action_url)) {
    $action_url = "do/doadd_invoice.php";
}
?>
<!-- Main Content -->
<form action="<?= $action_url ?>" method="post" id="posForm" class="h-100 d-flex flex-column m-0">
    <div class="container-fluid p-1 flex-grow-1 d-flex flex-column h-100" style="overflow: hidden;">
        <div class="row g-1 m-0 flex-grow-1 h-100" style="overflow: hidden;">
            <!-- القسم الأيمن - الفاتورة وإدخال الباركود -->
            <div class="col-lg-8 d-flex flex-column">
                <div class="card shadow-sm h-100 d-flex flex-column border-0 rounded-3">
                    
                    <!-- شريط الإدخال والبحث العلوي -->
                    <div class="card-header bg-white border-bottom p-2 d-flex gap-2">
                        <!-- Hidden Fields -->
                        <input type="hidden" name="pro_tybe" value="9">
                        <input type="hidden" name="pro_serial" value="0">
                        <input type="hidden" name="pro_id" value="1">
                        <input type="hidden" name="age" value="1"> <!-- تيك اواي افتراضي -->

                        <div class="flex-grow-1 position-relative">
                            <input type="text" class="form-control form-control-lg frst fw-bold"
                                placeholder="امسح الباركود هنا (Alt+B)" id="barcodeInput"
                                style="border: 2px solid #0d6efd; font-size: 1.5rem; background: #f8fbff; text-align: center; border-radius: 10px;" autofocus autocomplete="off">
                            <i class="fas fa-barcode position-absolute top-50 start-0 translate-middle-y ms-3 text-primary fs-3"></i>
                        </div>
                        
                        <div style="width: 250px;">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-light"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control scnd" id="searchInput"
                                    placeholder="بحث باسم الصنف (Alt+S)" autocomplete="off">
                            </div>
                        </div>
                    </div>

                    <!-- جدول الفاتورة (كبير وواضح) -->
                    <div class="card-body p-0 d-flex flex-column" style="flex: 1 1 0; min-height: 0; overflow: hidden;">
                        <div class="table-responsive flex-grow-1" style="overflow-y: auto;" id="itemDataContainer">
                            <table class="table table-hover table-striped table-bordered mb-0 align-middle supermarket-table">
                                <thead class="table-dark sticky-top">
                                    <tr>
                                        <th width="5%" class="text-center">#</th>
                                        <th width="35%">الصنف</th>
                                        <th width="15%" class="text-center">الكمية</th>
                                        <th width="15%" class="text-center">السعر</th>
                                        <th width="20%" class="text-center">الإجمالي</th>
                                        <th width="10%" class="text-center">حذف</th>
                                    </tr>
                                </thead>
                                <tbody id="itemData">
                                    <!-- الأصناف تضاف هنا -->
                                    <?php
                                    if (isset($_GET['edit'])){
                                        $id = intval($_GET['edit']);
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
                                            $u_val = floatval($rowdet['u_val']) ?: 1;
                                    ?>
                                        <tr class="item-card-order" data-itemid="<?= htmlspecialchars($barcode) ?>">
                                            <td class="text-center fw-bold text-muted"><?= $x ?></td>
                                            <td>
                                                <input type="hidden" value='<?= $rowdet['item_id'] ?>' name="itmname[]">
                                                <input type="hidden" class="barcode" value="<?= htmlspecialchars($barcode) ?>">
                                                <input type="hidden" name="u_val[]" value="<?= $u_val ?>">
                                                <span class="fw-bold fs-6"><?= htmlspecialchars($item_name) ?></span>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control form-control-lg text-center quantityInput fw-bold text-primary" 
                                                       value="<?= $qty ?>" name="itmqty[]" min="0.01" step="0.01">
                                            </td>
                                            <td>
                                                <input type="number" class="form-control text-center priceInput fw-bold" 
                                                       value="<?= number_format($price, 2, '.', '') ?>" name="itmprice[]" step="0.01" readonly>
                                            </td>
                                            <td>
                                                <input type="hidden" name="itmdisc[]" value="0">
                                                <input type="text" class="form-control text-center subtotal fw-bold bg-light text-danger fs-5 border-0" 
                                                       readonly value="<?= number_format($subtotal, 2, '.', '') ?>" name="itmval[]">
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-danger btn-lg delRow"><i class="fas fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    <?php
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- شريط الإجماليات والدفع السريع -->
                    <div class="card-footer bg-white border-top p-2">
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                <div class="bg-light p-2 rounded border">
                                    <small class="text-muted d-block fw-bold mb-1">الكمية الإجمالية</small>
                                    <h4 class="mb-0 text-dark" id="total_qty_display">0</h4>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="bg-success bg-opacity-10 p-2 rounded border border-success d-flex justify-content-between align-items-center h-100">
                                    <div>
                                        <small class="text-success fw-bold d-block mb-1">الإجمالي المطلوب</small>
                                        <h2 class="mb-0 text-success fw-bolder" id="net_display" style="font-size: 2.5rem;">0.00</h2>
                                    </div>
                                    <span class="fs-4 text-success fw-bold">ج.م</span>
                                    
                                    <input type="hidden" name="headtotal" id="total" value="0.00">
                                    <input type="hidden" name="headnet" id="net_val" value="0.00">
                                    <input type="hidden" name="headdisc" id="discount" value="0">
                                    <input name="headplus" type="hidden">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex flex-column gap-2 h-100 justify-content-center">
                                    <button type="button" class="btn btn-success btn-lg py-3 fw-bold fs-4" data-bs-toggle="modal" data-bs-target="#paymentModal">
                                        <i class="fas fa-money-bill-wave me-2"></i> دفع (F12)
                                    </button>
                                    <button type="button" class="btn btn-outline-danger" onclick="clearAllItems();">
                                        <i class="fas fa-trash me-2"></i> مسح الفاتورة
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- القسم الأيسر - الإعدادات والمعلومات -->
            <div class="col-lg-4 d-flex flex-column">
                <!-- الإعدادات الأساسية (تخفى في السوبر ماركت وتكون افتراضية، لكن نظهرها كمعلومات) -->
                <div class="card shadow-sm mb-2 rounded-3 border-0">
                    <div class="card-header bg-dark text-white p-2">
                        <h6 class="mb-0"><i class="fas fa-cogs me-2"></i>بيانات الفاتورة الأساسية</h6>
                    </div>
                    <div class="card-body p-2 bg-light">
                        <div class="row g-2">
                            <!-- المخزن -->
                            <div class="col-6">
                                <label class="small text-muted fw-bold">المخزن</label>
                                <select name="store_id" class="form-select form-select-sm fw-bold" required>
                                    <?php
                                    $resstore = $conn->query("SELECT * FROM `acc_head` WHERE is_stock =1 AND isdeleted = 0;");
                                    $first = true;
                                    while ($rowstore = $resstore->fetch_assoc()) { 
                                        $selected = '';
                                        if($rowstg['def_pos_store'] == $rowstore['id']){ $selected = "selected"; } 
                                        elseif ($first && empty($rowstg['def_pos_store'])) { $selected = "selected"; }
                                        $first = false;
                                    ?>
                                    <option <?= $selected ?> value="<?= $rowstore['id'] ?>"><?= $rowstore['aname'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <!-- الموظف -->
                            <div class="col-6">
                                <label class="small text-muted fw-bold">البائع</label>
                                <select name="emp_id" class="form-select form-select-sm fw-bold" required>
                                    <?php
                                    $resemp = $conn->query("SELECT * FROM `acc_head` WHERE parent_id = 35 AND is_basic = 0 AND isdeleted = 0;");
                                    $first_emp = true;
                                    while ($rowemp = $resemp->fetch_assoc()) { 
                                        $selected = '';
                                        if($rowstg['def_pos_employee'] == $rowemp['id']){ $selected = "selected"; } 
                                        elseif(isset($_GET['edit']) && $rowed['emp_id'] == $rowemp['id']){ $selected = "selected"; } 
                                        elseif ($first_emp && empty($rowstg['def_pos_employee']) && !isset($_GET['edit'])) { $selected = "selected"; }
                                        $first_emp = false;
                                    ?>
                                    <option <?= $selected ?> value="<?= $rowemp['id'] ?>"><?= $rowemp['aname'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <!-- العميل -->
                            <div class="col-6">
                                <label class="small text-muted fw-bold">العميل</label>
                                <select name="acc2_id" class="form-select form-select-sm fw-bold" required>
                                    <?php
                                    $resclient = $conn->query("SELECT * FROM `acc_head` WHERE code like '122%'  AND is_basic = 0 AND isdeleted = 0;");
                                    if(isset($_GET['edit'])){$rowed = $conn->query("SELECT * FROM ot_head where id = $id")->fetch_assoc();};
                                    $first_client = true;
                                    while ($rowclient = $resclient->fetch_assoc()) { 
                                        $selected = '';
                                        if($rowstg['def_pos_client'] == $rowclient['id']){ $selected = "selected"; } 
                                        elseif(isset($_GET['edit']) && $rowed['acc1'] == $rowclient['id']){ $selected = "selected"; } 
                                        elseif ($first_client && empty($rowstg['def_pos_client']) && !isset($_GET['edit'])) { $selected = "selected"; }
                                        $first_client = false;
                                    ?>
                                    <option <?= $selected ?> value="<?= $rowclient['id'] ?>"><?= $rowclient['aname'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <!-- الصندوق -->
                            <div class="col-6">
                                <label class="small text-muted fw-bold">الصندوق</label>
                                <select name="fund_id" class="form-select form-select-sm fw-bold" required>
                                    <?php
                                    if(isset($_GET['edit'])){$rowed = $conn->query("SELECT * FROM ot_head where id = $id")->fetch_assoc();};
                                    $resfund = $conn->query("SELECT * FROM `acc_head` WHERE is_fund =1 AND is_basic = 0 AND isdeleted = 0;");
                                    $first_fund = true;
                                    while ($rowfund = $resfund->fetch_assoc()) { 
                                        $selected = '';
                                        if($rowstg['def_pos_fund'] == $rowfund['id']){ $selected = "selected"; } 
                                        elseif((isset($_GET['edit'])) && $rowed['acc_fund'] == $rowfund['id']){ $selected = "selected"; } 
                                        elseif ($first_fund && empty($rowstg['def_pos_fund']) && !isset($_GET['edit'])) { $selected = "selected"; }
                                        $first_fund = false;
                                    ?>
                                    <option <?= $selected ?> value="<?= $rowfund['id'] ?>"><?= $rowfund['aname'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <!-- التواريخ مخفية -->
                        <input type="hidden" name="pro_date" value="<?= $posdate ?>">
                        <input type="hidden" name="accural_date" value="<?php echo isset($_GET['edit']) ? $rowed['accural_date'] : date('Y-m-d'); ?>">
                        <input type="hidden" name="table_id" value="0">
                    </div>
                </div>

                <!-- قائمة الأصناف السريعة (اختياري) -->
                <div class="card shadow-sm flex-grow-1 border-0 rounded-3 d-flex flex-column">
                    <div class="card-header bg-primary text-white p-2 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0"><i class="fas fa-tags me-2"></i>الأصناف السريعة المضافة حديثاً</h6>
                        <span class="badge bg-light text-primary">AJAX</span>
                    </div>
                    <div class="card-body p-0 d-flex flex-column flex-grow-1">
                        <!-- هنا يمكن عرض أزرار للأقسام الرئيسية التي يكثر استخدامها، ولكن سنضع رسالة للتركيز على الباركود -->
                        <div class="p-4 text-center text-muted d-flex flex-column align-items-center justify-content-center h-100 opacity-50">
                            <i class="fas fa-barcode fa-4x mb-3 text-secondary"></i>
                            <h5>نظام السوبر ماركت</h5>
                            <p class="mb-0">يرجى استخدام قارئ الباركود للإضافة السريعة.</p>
                            <small class="mt-2 text-primary fw-bold">يدعم باركود الميزان وتعدد الوحدات</small>
                        </div>
                        
                        <!-- نافذة نتائج البحث الحي ستظهر هنا فوق المحتوى أو كـ Dropdown -->
                        <div id="searchResults" class="position-absolute w-100 bg-white border rounded shadow-lg z-3" style="display:none; max-height: 400px; overflow-y: auto; top: 0;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Modal الدفع (نسخ من الأساسي مع تعديلات طفيفة للحجم) -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-success text-white py-3">
                <h4 class="modal-title fw-bold">
                   <i class="fas fa-cash-register me-2"></i> شاشة الدفع
                </h4>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light p-4">
                <div class="row g-4">
                    <!-- الإجمالي والصافي -->
                    <div class="col-12">
                        <div class="card border-0 shadow-sm bg-success bg-opacity-10">
                            <div class="card-body py-4 text-center">
                                <h5 class="text-success fw-bold mb-2">المبلغ المطلوب للدفع</h5>
                                <h1 class="display-3 fw-bolder text-success mb-0" id="modal_net_large">0.00 ج.م</h1>
                            </div>
                        </div>
                    </div>

                    <!-- المدفوع -->
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-4">
                                <h5 class="fw-bold text-dark mb-3">المبلغ المدفوع (نقدياً)</h5>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-primary text-white"><i class="fas fa-money-bill-wave"></i></span>
                                    <input class="form-control text-center fw-bolder fs-2" type="number" 
                                           id="modal_paid_cash" placeholder="0.00" step="0.01" min="0" style="color: #0d6efd;">
                                    <span class="input-group-text fw-bold">ج.م</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- الباقي -->
                    <div class="col-12">
                        <div class="card border-0 shadow-sm bg-danger bg-opacity-10">
                            <div class="card-body p-3 text-center">
                                <h6 class="text-danger fw-bold mb-1">الباقي للعميل (الصرف)</h6>
                                <h2 class="fw-bolder text-danger mb-0" id="modal_change">0.00 ج.م</h2>
                            </div>
                        </div>
                    </div>
                    
                    <!-- حقول مخفية للدفع (الصندوق والبنك) -->
                    <div class="d-none">
                        <select id="payment_fund_id">
                            <?php
                            $resfund = $conn->query("SELECT * FROM `acc_head` WHERE is_fund = 1 AND is_basic = 0 AND isdeleted = 0 LIMIT 1");
                            if($rowfund = $resfund->fetch_assoc()) { 
                                echo "<option value='{$rowfund['id']}' selected></option>";
                            }
                            ?>
                        </select>
                        <input type="number" id="modal_paid_bank" value="0">
                        <select id="payment_bank_id"><option value="" selected></option></select>
                        <input type="number" id="modal_discperc" value="0">
                        <input type="number" id="modal_discount" value="0">
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-white border-top p-3 d-flex justify-content-between">
                <button type="button" class="btn btn-secondary btn-lg px-4" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-success btn-lg px-5 fw-bold" onclick="submitSupermarketPOS('print');">
                    <i class="fas fa-print me-2"></i> حفظ وطباعة (Enter)
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal إغلاق الشيفت -->
<div class="modal fade" id="closeShiftModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title"><i class="fas fa-power-off me-2"></i>إغلاق الشيفت</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                <h5>هل أنت متأكد من إغلاق الشيفت؟</h5>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <a href="z_report.php" class="btn btn-warning fw-bold">الانتقال للإغلاق</a>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>if (typeof jQuery === 'undefined') { document.write('<script src="plugins/jquery/jquery.min.js"><\/script>'); }</script>
<script src="assets/libs/bootstrap.bundle.min.js"></script>
<script src="js/pos_config_loader.js?v=<?= time() ?>"></script>
<script src="js/pos_supermarket.js?v=<?= time() ?>"></script>
