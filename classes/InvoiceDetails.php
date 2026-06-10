<?php

require_once 'InvoiceElementBase.php';

/**
 * فئة تفاصيل الفاتورة - تطبيق polymorphism
 * Invoice Details class - implementing polymorphism
 */
class InvoiceDetails extends InvoiceElementBase
{
    private $items = [];
    private $existingDetails = [];

    public function __construct($invoiceType, $isEditMode = false, $data = null, $conn = null)
    {
        parent::__construct($invoiceType, $isEditMode, $data, $conn);
        $this->loadItems();
        if ($this->isEditMode) {
            $this->loadExistingDetails();
        }
    }

    /**
     * تحميل الأصناف
     */
    private function loadItems()
    {
        // تم تعطيل تحميل الأصناف - يتم استخدام Live Search بدلاً منه
        // Items are now loaded via AJAX on-demand
        $this->items = [];
        return;
    }

    /**
     * تحميل التفاصيل الموجودة (في حالة التعديل)
     */
    private function loadExistingDetails()
    {
        if (!$this->conn || !$this->data) return;

        try {
            $invoiceId = intval($this->data['id'] ?? 0);
            if ($invoiceId > 0) {
                $query = "SELECT fd.*, mi.iname, mi.cost_price AS item_cost, mi.price1 AS item_price1 
                         FROM fat_details fd 
                         JOIN myitems mi ON fd.item_id = mi.id 
                         WHERE fd.pro_id = ? AND fd.isdeleted = 0";
                $result = $this->executeSecureQuery($query, [$invoiceId], 'i');
                $this->existingDetails = $result->fetch_all(MYSQLI_ASSOC);
            }
        } catch (Exception $e) {
            error_log("Error loading existing details: " . $e->getMessage());
        }
    }

    /**
     * عرض تفاصيل الفاتورة
     */
    public function render()
    {
        // عمودا نسبة الربح وسعر البيع يظهران في فاتورة المشتريات فقط
        $showProfit = ((int) $this->invoiceType === 4);
        $searchColspan = $showProfit ? 9 : 7;
        ob_start();
        ?>
        <div class="row">
            <div class="col">
                <div class="table-responsive itemtable" style="height: 300px">
                    <table id="fatTable" class="table table-hover table-striped table-bordered">
                        <thead class="bg-light">
                            <tr class="bg- border">
                                <th>م</th>
                                <th class="col-5">اسم الصنف</th>
                                <th>الوحدة</th>
                                <th>كمية</th>
                                <th>سعر</th>
                                <th>نسبة %</th>
                                <th>خصم</th>
                                <?php if ($showProfit): ?>
                                <th title="نسبة الربح - قابل للتعديل">ر. ربح %</th>
                                <th title="سعر البيع - قابل للتعديل">س. بيع</th>
                                <?php endif; ?>
                                <th>القيمة</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="itmrow">
                            <?php $this->renderExistingRows(); ?>
                        </tbody>
                        <tfoot id="searchTable" style="position:relative;">
                            <tr>
                                <td class="col-1">
                                    <div class="tool">
                                        <a id="addNewElement" class="btn bg-lime-200 btn-sm hadi-white-flash"
                                           href="add_item.php" target="_blank">+</a>
                                        <div class="tooltext">إضافة صنف جديد</div>
                                    </div>
                                </td>
                                <td colspan="<?php echo $searchColspan; ?>" style="position:relative; overflow:visible;">
                                    <div style="display:flex; gap:6px;">
                                        <input type="text"
                                               id="itemSearchInput"
                                               class="form-control form-control-sm frst"
                                               placeholder="ابحث عن صنف..."
                                               autocomplete="off"
                                               style="width:260px;">
                                        <input type="text"
                                               id="barcodeSearchInput"
                                               class="form-control form-control-sm scnd"
                                               placeholder="باركود"
                                               autocomplete="off"
                                               style="width:130px;">
                                    </div>
                                    <input type="hidden" id="selectedItemId">
                                    <!-- searchResults خارج الجدول - بيتحرك بـ JS -->
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- حقول مخفية يستخدمها addNewRow() - خارج الجدول -->
        <input id="itmprice"      type="number" hidden value="0.00" step="0.001">
        <input id="itmqty"        type="number" hidden value="1.00">
        <input id="itmdisc_pct"   type="number" hidden value="0.00" step="0.01">
        <input id="itmdisc"       type="number" hidden value="0.00" step="0.001">
        <input id="itmval"        type="number" hidden value="0.00" step="0.001">
        <input id="itmprofit"     type="number" hidden>
        <input id="itmsprice_stg" type="number" hidden value="0.00" step="0.001">
        <select id="inputUnitSelect" hidden><option value="">اختر وحدة</option></select>
        <button type="button" id="addRow" hidden>إضافة</button>
        <!-- dropdown البحث - خارج الجدول عشان مش يتقطع بالـ overflow -->
        <div id="searchResults" style="position:fixed; z-index:9999; background:white; border:1px solid #ddd; max-height:250px; overflow-y:auto; display:none; box-shadow:0 4px 12px rgba(0,0,0,0.15);"></div>
<!-- Live Search Script -->
<style>
    #searchResults .search-result-item.search-result-active {
        background: #dbeafe !important;
    }
</style>
<script>
window.SHOW_PROFIT_COLS = <?php echo $showProfit ? 'true' : 'false'; ?>;
$(document).ready(function() {
    const searchInput = document.getElementById('itemSearchInput');
    const searchResults = document.getElementById('searchResults');
    const selectedItemId = document.getElementById('selectedItemId');
    const priceInput = document.getElementById('itmprice');

    if (!searchInput || !searchResults) return;

    // تحديث موضع الـ dropdown تحت الـ input
    function positionDropdown() {
        const rect = searchInput.getBoundingClientRect();
        searchResults.style.top    = (rect.bottom + window.scrollY) + 'px';
        searchResults.style.left   = (rect.left   + window.scrollX) + 'px';
        searchResults.style.width  = rect.width + 'px';
    }

    let searchTimeout;
    let selectedItem = null;
    let highlightIndex = -1;

    function getResultItems() {
        return searchResults.querySelectorAll('.search-result-item');
    }

    function setHighlight(index) {
        const items = getResultItems();
        if (!items.length) {
            highlightIndex = -1;
            return;
        }
        highlightIndex = Math.max(0, Math.min(index, items.length - 1));
        items.forEach(function (el, i) {
            el.classList.toggle('search-result-active', i === highlightIndex);
        });
        items[highlightIndex].scrollIntoView({ block: 'nearest', behavior: 'smooth' });
    }

    function clearHighlight() {
        highlightIndex = -1;
        getResultItems().forEach(function (el) {
            el.classList.remove('search-result-active');
        });
    }

    function bindResultItems() {
        clearHighlight();
        document.querySelectorAll('#searchResults .search-result-item').forEach(function (item, idx) {
            item.addEventListener('click', function(e) {
                e.stopPropagation();
                selectItem({
                    id: this.dataset.id,
                    name: this.dataset.name,
                    price: this.dataset.price,
                    barcode: this.dataset.barcode
                });
            });

            item.addEventListener('mouseenter', function() {
                highlightIndex = idx;
                getResultItems().forEach(function (el, i) {
                    el.classList.toggle('search-result-active', i === idx);
                });
            });
        });
    }

    // البحث المباشر
    searchInput.addEventListener('input', function() {
        const query = this.value.trim();

        clearTimeout(searchTimeout);

        if (query.length < 2) {
            searchResults.style.display = 'none';
            clearHighlight();
            return;
        }

        searchTimeout = setTimeout(() => {
            positionDropdown();
            searchResults.innerHTML = '<div style="padding:10px; text-align:center;">جاري البحث...</div>';
            searchResults.style.display = 'block';
            clearHighlight();

            fetch(`ajax/load_items_lazy.php?search=${encodeURIComponent(query)}&limit=20`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.items.length > 0) {
                        let html = '';
                        data.items.forEach(item => {
                            html += `
                                <div class="search-result-item"
                                     data-id="${item.id}"
                                     data-name="${item.iname}"
                                     data-price="${item.price1}"
                                     data-barcode="${item.barcode}"
                                     style="padding:10px; cursor:pointer; border-bottom:1px solid #eee;">
                                    <strong>${item.iname}</strong>
                                    ${item.name2 ? ' // ' + item.name2 : ''}
                                    <span style="float:left; color:#10b981;">${item.price1} ج.م</span>
                                </div>
                            `;
                        });
                        searchResults.innerHTML = html;
                        bindResultItems();
                    } else {
                        searchResults.innerHTML = '<div style="padding:10px; text-align:center; color:#999;">لا توجد نتائج</div>';
                        clearHighlight();
                    }
                })
                .catch(error => {
                    console.error('Search error:', error);
                    searchResults.innerHTML = '<div style="padding:10px; text-align:center; color:#ef4444;">خطأ في البحث</div>';
                    clearHighlight();
                });
        }, 300);
    });

    searchInput.addEventListener('keydown', function(e) {
        if (searchResults.style.display === 'none') {
            return;
        }
        const items = getResultItems();
        if (!items.length) {
            return;
        }

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            if (highlightIndex < 0) {
                setHighlight(0);
            } else if (highlightIndex < items.length - 1) {
                setHighlight(highlightIndex + 1);
            }
            return;
        }

        if (e.key === 'ArrowUp') {
            e.preventDefault();
            if (highlightIndex <= 0) {
                clearHighlight();
            } else {
                setHighlight(highlightIndex - 1);
            }
            return;
        }

        if (e.key === 'Enter') {
            var idx = highlightIndex >= 0 ? highlightIndex : 0;
            if (items[idx]) {
                e.preventDefault();
                var row = items[idx];
                selectItem({
                    id: row.dataset.id,
                    name: row.dataset.name,
                    price: row.dataset.price,
                    barcode: row.dataset.barcode
                });
            }
            return;
        }

        if (e.key === 'Escape') {
            e.preventDefault();
            searchResults.style.display = 'none';
            clearHighlight();
        }
    });

    // اختيار صنف
    function selectItem(item) {
        selectedItem = item;
        searchInput.value = item.name;
        selectedItemId.value = item.id;
        priceInput.value = item.price;
        searchResults.style.display = 'none';
        clearHighlight();

        // جلب بيانات الصنف الكاملة وتحديث الحقول مباشرة
        const isSale = window.location.href.indexOf('q=sale') !== -1;

        $.ajax({
            url: 'get/get_iteminfo.php?id=' + item.id,
            method: 'GET',
            dataType: 'json',
            cache: true,
            success: function(data) {
                if (data.error) {
                    console.error('get_iteminfo error:', data.error, '| item id:', item.id);
                    return;
                }
                const price = isSale ? (data.last_price || data.price1) : data.price1;

                // تحديث حقول صف الإدخال
                $('#itmprice').val(price || 0);
                $('#itmqty').val(1);
                $('#itmdisc').val('0.00');
                $('#itmval').val(price || 0);
                $('#itmsprice_stg').val(data.price1 || 0);

                // تحديث حقول المعلومات
                $('#storeqty').text(data.itmqty ? parseFloat(data.itmqty).toFixed(2) : '0');
                $('#price1').text(data.price1 || '0');
                $('#market_price').text(data.market_price || '0');
                $('#storemdtime').text(data.mdtime || '');
                $('#cost_price').text(data.cost_price || '0');
                $('#last_price').text(data.last_price || '0');

                // تحديث الوحدات
                const unitSelect = $('#inputUnitSelect');
                unitSelect.empty();
                if (data.units && data.units.length) {
                    data.units.forEach(function(unit) {
                        unitSelect.append('<option value="' + unit.unit_value + '">' + unit.unit_name + '</option>');
                    });

                    unitSelect.off('change').on('change', function() {
                        const selectedUnit = data.units.find(u => u.unit_value == $(this).val());
                        if (selectedUnit) {
                            const newPrice = isSale
                                ? (data.last_price * selectedUnit.unit_value)
                                : selectedUnit.uprice1;
                            $('#itmprice').val(newPrice);
                            $('#itmqty').val(1);
                            $('#itmval').val(newPrice);

                            $('#storeqty').text((data.itmqty / selectedUnit.unit_value).toFixed(2) + ' (' + selectedUnit.unit_value + ')');
                            $('#price1').text(selectedUnit.uprice1);
                            $('#market_price').text(selectedUnit.uprice3);
                            $('#cost_price').text(data.cost_price * selectedUnit.unit_value);
                            $('#last_price').text(data.last_price * selectedUnit.unit_value);
                        }
                    });
                } else {
                    unitSelect.append('<option value="">لا توجد وحدات</option>');
                }

                // أضف الصف فوراً وانتقل لحقل الكمية في الصف الجديد
                $('#addRow').click();
                setTimeout(function() {
                    $('#itmrow tr:last .itmqty').focus().select();
                }, 50);
            },
            error: function() {
                $('#addRow').click();
                setTimeout(function() {
                    $('#itmrow tr:last .itmqty').focus().select();
                }, 50);
            }
        });
    }

    // إخفاء النتائج عند الضغط خارجها
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.style.display = 'none';
        }
    });

    // مسح عند focus
    searchInput.addEventListener('focus', function() {
        if (this.value && searchResults.children.length > 0) {
            searchResults.style.display = 'block';
        }
    });

    // الباركود - يختار الصنف فوراً بمجرد وقف الكتابة (debounce 400ms)
    const barcodeInput = document.getElementById('barcodeSearchInput');
    let barcodeTimeout;

    barcodeInput.addEventListener('input', function() {
        clearTimeout(barcodeTimeout);
        const barcode = this.value.trim();
        if (!barcode) return;

        barcodeTimeout = setTimeout(() => {
            fetch(`ajax/load_items_lazy.php?search=${encodeURIComponent(barcode)}&limit=1&by=barcode`)
                .then(r => r.json())
                .then(data => {
                    if (data.success && data.items.length > 0) {
                        const item = data.items[0];
                        barcodeInput.value = '';
                        selectItem({ id: item.id, name: item.iname, price: item.price1, barcode: item.barcode });
                    } else {
                        barcodeInput.style.borderColor = '#ef4444';
                        setTimeout(() => barcodeInput.style.borderColor = '', 1000);
                    }
                })
                .catch(() => {
                    barcodeInput.style.borderColor = '#ef4444';
                    setTimeout(() => barcodeInput.style.borderColor = '', 1000);
                });
        }, 400);
    });

    // Enter على الباركود كمان يشتغل
    barcodeInput.addEventListener('keydown', function(e) {
        if (e.key !== 'Enter') return;
        e.preventDefault();
        clearTimeout(barcodeTimeout);
        const barcode = this.value.trim();
        if (!barcode) return;

        fetch(`ajax/load_items_lazy.php?search=${encodeURIComponent(barcode)}&limit=1&by=barcode`)
            .then(r => r.json())
            .then(data => {
                if (data.success && data.items.length > 0) {
                    const item = data.items[0];
                    barcodeInput.value = '';
                    selectItem({ id: item.id, name: item.iname, price: item.price1, barcode: item.barcode });
                } else {
                    barcodeInput.style.borderColor = '#ef4444';
                    setTimeout(() => barcodeInput.style.borderColor = '', 1000);
                }
            })
            .catch(() => {
                barcodeInput.style.borderColor = '#ef4444';
                setTimeout(() => barcodeInput.style.borderColor = '', 1000);
            });
    });
}); // end document.ready
</script>
        <?php
        return ob_get_clean();
    }

    /**
     * عرض الصفوف الموجودة (في حالة التعديل)
     */
    private function renderExistingRows()
    {
        if (!$this->isEditMode || empty($this->existingDetails)) {
            return;
        }

        foreach ($this->existingDetails as $index => $detail) {
            $rowNumber = $index + 1;
            $this->renderDetailRow($detail, $rowNumber);
        }
    }

    /**
     * عرض صف تفاصيل واحد
     */
    private function renderDetailRow($detail, $rowNumber)
    {
        $quantity = ($detail['u_val'] > 0) ? abs($detail['qty_in'] - $detail['qty_out']) / $detail['u_val'] : abs($detail['qty_in'] - $detail['qty_out']);
        $price = $detail['price'] * ($detail['u_val'] > 0 ? $detail['u_val'] : 1);
        $showProfit = ((int) $this->invoiceType === 4);
        ?>
        <tr>
            <td class="col-1">
                <?php echo $rowNumber; ?>
                <input type="text" name="det_id[]" hidden value="<?php echo $detail['id']; ?>">
                <input type="text" name="detcrtime[]" hidden value="<?php echo $detail['crtime']; ?>">
            </td>
            
            <!-- الصنف -->
            <td id="itmTd" class="col-lg-5">
                <p><?php echo $this->sanitizeInput($detail['iname']); ?></p>
                <input id="itmprice2" type="number" name="itmname[]" hidden 
                       onclick="sT(this)" value="<?php echo $detail['item_id']; ?>">
            </td>
            
            <!-- الوحدة -->
            <td>
                <?php $this->renderUnitSelect($detail['item_id'], $detail['u_val']); ?>
            </td>
            
            <!-- الكمية -->
            <td>
                <input value="<?php echo $quantity; ?>" type="number" 
                       name="itmqty[]" onclick="sT(this)" 
                       class="itmqty form-control form-control-sm" style="width:90px;">
            </td>
            
            <!-- السعر -->
            <td>
                <input type="number" name="itmprice[]" onclick="sT(this)" 
                       class="itmprice form-control form-control-sm" style="width:90px;" 
                       value="<?php echo $price; ?>">
            </td>

            <!-- نسبة الخصم -->
            <td>
                <?php
                $disc_pct = isset($detail['disc_pct']) ? $detail['disc_pct'] : 0;
                if ($disc_pct == 0 && !empty($detail['price']) && !empty($detail['qty_in']) - !empty($detail['qty_out'])) {
                    $base = $price * $quantity;
                    if ($base > 0) {
                        $disc_pct = round(($detail['discount'] / $base) * 100, 2);
                    }
                }
                ?>
                <input type="number" name="itmdisc_pct[]" value="<?php echo $disc_pct; ?>"
                       class="itmdisc_pct form-control form-control-sm" style="width:80px;"
                       step="0.01" min="0" max="100" placeholder="%" onclick="sT(this)">
            </td>

            <!-- الخصم -->
            <td>
                <input value="<?php echo $detail['discount']; ?>" 
                       type="number" name="itmdisc[]" onclick="sT(this)" 
                       class="itmdisc form-control form-control-sm" style="width:90px;">
            </td>

            <?php if ($showProfit): ?>
            <?php
            // الأساس = سعر الشراء (عمود السعر) | سعر البيع الافتراضي = price1 للصنف
            $sellPrice = floatval($detail['item_price1'] ?? $price);
            $profitPct = ($price > 0) ? round(($sellPrice - $price) / $price * 100, 1) : 0;
            ?>
            <!-- نسبة الربح -->
            <td>
                <input type="number" class="itmprofit_pct form-control form-control-sm"
                       value="<?php echo $profitPct; ?>"
                       style="width:70px; background:#f0fdf4; color:#16a34a; font-weight:600;"
                       step="0.1" onclick="sT(this)" title="نسبة الربح - غيّرها لتحديث سعر البيع">
            </td>

            <!-- سعر البيع -->
            <td>
                <input type="number" name="itmsellprice[]" class="itmsellprice form-control form-control-sm"
                       value="<?php echo $sellPrice; ?>"
                       style="width:90px;" step="0.001" onclick="sT(this)"
                       title="سعر البيع - يُحفظ في سعر الصنف (price1)">
            </td>
            <?php endif; ?>

            <!-- القيمة -->
            <td>
                <input readonly value="<?php echo $detail['det_value']; ?>" 
                       type="number" name="itmval[]" 
                       class="itmval bg-light form-control form-control-sm" style="width:150px;">
            </td>
            
            <td>
                <button type="button" class="deleteRow btn btn-danger">X</button>
            </td>
        </tr>
        <?php
    }

    /**
     * عرض قائمة الوحدات للصنف
     */
    private function renderUnitSelect($itemId, $selectedUnitVal = null)
    {
        if (!$this->conn) {
            echo '<select name="u_val[]" class="form-control form-control-sm" style="width:100px;"><option value="">لا توجد وحدات</option></select>';
            return;
        }

        try {
            $query = "SELECT iu.*, mu.uname 
                     FROM item_units iu 
                     JOIN myunits mu ON iu.unit_id = mu.id 
                     WHERE iu.item_id = ?";
            $result = $this->executeSecureQuery($query, [$itemId], 'i');
            $units = $result->fetch_all(MYSQLI_ASSOC);

            echo '<select name="u_val[]" class="form-control form-control-sm" style="width:100px;">';
            foreach ($units as $unit) {
                $selected = ($selectedUnitVal && $unit['u_val'] == $selectedUnitVal) ? 'selected' : '';
                echo "<option value='{$unit['u_val']}' {$selected}>{$this->sanitizeInput($unit['uname'])}</option>";
            }
            echo '</select>';

        } catch (Exception $e) {
            echo '<select name="u_val[]" class="form-control form-control-sm" style="width:100px;"><option value="">خطأ في التحميل</option></select>';
        }
    }

    /**
     * التحقق من صحة البيانات
     */
    public function validate()
    {
        $errors = [];

        // التحقق من وجود أصناف
        if (empty($_POST['itmname']) || !is_array($_POST['itmname'])) {
            $errors[] = 'يجب إضافة صنف واحد على الأقل';
            return $errors;
        }

        // التحقق من كل صنف
        foreach ($_POST['itmname'] as $index => $itemId) {
            if (empty($itemId)) {
                continue; // تجاهل الصفوف الفارغة
            }

            // التحقق من الكمية
            $quantity = $_POST['itmqty'][$index] ?? 0;
            if ($quantity <= 0) {
                $errors[] = "الكمية غير صحيحة في الصف " . ($index + 1);
            }

            // التحقق من السعر
            $price = $_POST['itmprice'][$index] ?? 0;
            if ($price < 0) {
                $errors[] = "السعر غير صحيح في الصف " . ($index + 1);
            }
        }

        return $errors;
    }

    /**
     * إنشاء صف جديد فارغ لإضافة صنف
     */
    public function renderNewRow()
    {
        ob_start();
        ?>
        <div class="row">
            <div class="table-responsive">
                <table class="table table-condensed table-hover table-striped table-bordered" id="searchTable">
                    <tbody>
                        <tr>
                            <td class="col-1">
                                <div class="tool">
                                    <a id="addNewElement" class="btn bg-lime-200 btn-sm hadi-white-flash" 
                                       href="add_item.php" target="_blank">+</a>
                                    <div class="tooltext">إضافة صنف جديد</div>  
                                </div>
                            </td>
                             
                            <!-- الصنف -->
                            <td id="itmTd" class="col-lg-5">
                                <select style="width:100%" name="myitm[]" id="mySelectitm" 
                                        class="frst mySelectitm form-control">
                                    <option value="">اختر صنف</option>
                                    <?php $this->renderItemOptions(); ?>
                                </select>
                                <input id="itmprice2" type="number" hidden onclick="sT(this)">
                            </td>
                            
                            <!-- الوحدة -->
                            <td>
                                <select id="inputUnitSelect" class="form-control form-control-sm" style="width:100px;">
                                    <option value="">اختر وحدة</option>
                                </select>
                            </td>
                            
                            <!-- الكمية -->
                            <td>
                                <input type="number" hidden>
                                <input id="itmqty" value="1.00" type="number"
                                       onclick="sT(this)" class="itmqty form-control form-control-sm nozero" 
                                       style="width:90px;">
                            </td>
                            
                            <!-- السعر -->
                            <td>
                                <input id="itmprice" value="0.00" type="number"
                                       onclick="sT(this)" class="itmprice form-control form-control-sm nozero" 
                                       style="width:90px;" step="0.001">
                            </td>
                            
                            <!-- الخصم -->
                            <td>
                                <input id="itmdisc" value="0.00" type="number"
                                       onclick="sT(this)" class="itmdisc form-control form-control-sm nozero" 
                                       style="width:120px;" step="0.001">
                            </td>
                            
                            <!-- القيمة -->
                            <td>
                                <input readonly id="itmval" value="0.00" type="number"
                                       class="itmval bg-light form-control form-control-sm nozero" 
                                       style="width:150px;" step="0.001">
                            </td>
                            
                            <td>
                                <input id="itmprofit" name="itmprofit" hidden>
                                <button type="button" id="addRow" class="btn btn-light">إضافة</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * عرض خيارات الأصناف
     */
    private function renderItemOptions()
    {
        foreach ($this->items as $item) {
            $displayName = $item['iname'];
            if (!empty($item['name2'])) {
                $displayName .= ' // ' . $item['name2'];
            }
            echo "<option value='{$item['id']}'>{$this->sanitizeInput($displayName)}</option>";
        }
    }
}