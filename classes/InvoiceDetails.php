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
                $query = "SELECT fd.*, mi.iname, mi.cost_price AS item_cost,
                                mi.price1 AS item_price1, mi.price2 AS item_price2, mi.price3 AS item_price3,
                                (SELECT iu.price1 FROM item_units iu WHERE iu.item_id = fd.item_id AND ABS(iu.u_val - fd.u_val) < 0.0001 LIMIT 1) AS unit_price1,
                                (SELECT iu.price2 FROM item_units iu WHERE iu.item_id = fd.item_id AND ABS(iu.u_val - fd.u_val) < 0.0001 LIMIT 1) AS unit_price2,
                                (SELECT iu.price3 FROM item_units iu WHERE iu.item_id = fd.item_id AND ABS(iu.u_val - fd.u_val) < 0.0001 LIMIT 1) AS unit_price3
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
        $searchColspan = $showProfit ? 11 : 9;
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
                                        <button type="button" id="addNewElement" class="btn bg-lime-200 btn-sm hadi-white-flash"
                                           data-toggle="modal" data-target="#addItemModal">+</button>
                                        <div class="tooltext">إضافة صنف جديد</div>
                                    </div>
                                </td>
                                <td colspan="<?php echo $searchColspan; ?>" style="position:relative; overflow:visible;">
                                    <div style="display:flex; gap:10px; align-items:center;">
                                        <input type="text"
                                               id="itemSearchInput"
                                               class="form-control form-control-sm frst"
                                               placeholder="ابحث عن صنف..."
                                               autocomplete="off"
                                               style="width:260px;">
                                        <div class="barcode-scan-box">
                                            <i class="fas fa-barcode"></i>
                                            <input type="text"
                                                   id="barcodeSearchInput"
                                                   class="form-control form-control-sm scnd"
                                                   placeholder="امسح الباركود أو اكتبه ثم Enter"
                                                   autocomplete="off">
                                        </div>
                                        <span id="barcodeStatus" class="barcode-status"></span>
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
        <input id="itmprice"      type="number" hidden value="0" step="0.001">
        <input id="itmqty"        type="number" hidden value="1">
        <input id="itmdisc_pct"   type="number" hidden value="0" step="0.01">
        <input id="itmdisc"       type="number" hidden value="0" step="0.001">
        <input id="itmval"        type="number" hidden value="0" step="0.001">
        <input id="itmprofit"     type="number" hidden>
        <input id="itmsprice_stg" type="number" hidden value="0" step="0.001">
        <select id="inputUnitSelect" hidden><option value="">اختر وحدة</option></select>
        <button type="button" id="addRow" hidden>إضافة</button>
        <!-- dropdown البحث - خارج الجدول عشان مش يتقطع بالـ overflow -->
        <div id="searchResults" style="position:fixed; z-index:9999; background:white; border:1px solid #ddd; max-height:250px; overflow-y:auto; display:none; box-shadow:0 4px 12px rgba(0,0,0,0.15);"></div>
<!-- Live Search Script -->
<style>
    #searchResults .search-result-item.search-result-active {
        background: #dbeafe !important;
    }

    .barcode-scan-box {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 0 8px;
        border: 2px solid #4B5694;
        border-radius: 6px;
        background: #f8fafc;
    }
    .barcode-scan-box i {
        color: #4B5694;
        font-size: 1.1rem;
    }
    .barcode-scan-box input {
        width: 240px;
        border: 0;
        background: transparent;
        box-shadow: none;
        font-weight: 600;
        letter-spacing: .5px;
    }
    .barcode-scan-box input:focus {
        border: 0;
        background: transparent;
        box-shadow: none;
        outline: 0;
    }
    .barcode-scan-box.is-error {
        border-color: #dc2626;
        background: #fef2f2;
    }
    .barcode-scan-box.is-ok {
        border-color: #16a34a;
        background: #f0fdf4;
    }
    .barcode-status {
        font-size: .8rem;
        font-weight: 600;
        white-space: nowrap;
    }
</style>
<script>
window.SHOW_PROFIT_COLS = <?php echo $showProfit ? 'true' : 'false'; ?>;

window.currentInvoicePriceList = function() {
    return parseInt($('#invoicePriceList').val(), 10) || 1;
};

window.invoicePriceFor = function(source, listId, isUnit) {
    listId = parseInt(listId, 10) || 1;
    var key = isUnit
        ? ({1: 'uprice1', 2: 'uprice2', 3: 'uprice3', 4: 'uprice4'}[listId] || 'uprice1')
        : ({1: 'price1', 2: 'price2', 3: 'price3', 4: 'price4'}[listId] || 'price1');
    var val = parseFloat(source && source[key]);
    if (!(val > 0) && listId === 3 && source) {
        val = parseFloat(isUnit ? source.uprice3 : (source.market_price || source.price3));
    }
    if (!(val > 0) && source) {
        val = parseFloat(isUnit ? source.uprice1 : source.price1) || 0;
    }
    return val || 0;
};

window.isPurchaseInvoice = function() {
    var t = parseInt($('input[name="pro_tybe"]').val(), 10);
    return t === 4 || t === 10 || t === 12;
};

window.isSalesReturn = function() {
    return parseInt($('input[name="pro_tybe"]').val(), 10) === 11;
};

window.fetchLastSaleBases = function(itemIds, done) {
    var clientId = $('#mySelectEmp').val();
    var ids = (itemIds || []).map(function(id) { return parseInt(id, 10); }).filter(function(id) { return id > 0; });
    if (!clientId || !ids.length) {
        done({});
        return;
    }
    $.getJSON('ajax/last_sale_price.php', { client_id: clientId, item_ids: ids.join(',') })
        .done(function(res) { done((res && res.prices) || {}); })
        .fail(function() { done({}); });
};

window.findInvoiceUnit = function(units, unitVal) {
    var target = parseFloat(unitVal);
    if (!isFinite(target)) return null;
    return (units || []).find(function(u) {
        return Math.abs((parseFloat(u.unit_value) || 0) - target) < 0.0001;
    }) || null;
};

window.formatInvoiceMoney = function(n) {
    var v = parseFloat(n);
    if (!isFinite(v)) return '0';
    return String(parseFloat(v.toFixed(3)));
};

// أسعار بيانات الصنف أسفل الفاتورة حسب الوحدة المختارة
window.applyUnitItemInfo = function(data, unit) {
    if (!data) return;
    var label = data.iname || '';
    if (data.barcode) label += (label ? ' — ' : '') + data.barcode;
    $('#selectedLineName').text(label);
    var itemId = parseInt(data.id, 10);
    if (itemId > 0) {
        $('#selectedItemMovement').attr('href', 'item_summery.php?id=' + itemId);
        $('#selectedItemEdit').attr('href', 'add_item.php?edit=' + itemId);
        $('#selectedItemLinks').removeClass('d-none');
    } else {
        $('#selectedItemLinks').addClass('d-none');
    }
    var factor = unit ? (parseFloat(unit.unit_value) || 1) : 1;
    var sell = unit ? parseFloat(unit.uprice1) : NaN;
    if (!(sell > 0)) sell = (parseFloat(data.price1) || 0) * factor;
    var market = unit ? parseFloat(unit.uprice3) : NaN;
    if (!(market > 0)) market = (parseFloat(data.market_price) || parseFloat(data.price3) || 0) * factor;
    var cost = unit ? parseFloat(unit.ucost) : NaN;
    if (!(cost > 0)) cost = (parseFloat(data.cost_price) || 0) * factor;
    var last = (parseFloat(data.last_price) || 0) * factor;
    var qty = (parseFloat(data.itmqty) || 0) / factor;

    $('#storeqty').text(window.formatInvoiceMoney(qty) + (unit ? ' (' + factor + ')' : ''));
    $('#price1').text(window.formatInvoiceMoney(sell));
    $('#market_price').text(window.formatInvoiceMoney(market));
    $('#storemdtime').text(data.mdtime || '');
    $('#cost_price').text(window.formatInvoiceMoney(cost));
    $('#last_price').text(window.formatInvoiceMoney(last));
};

$(document).ready(function() {
    const searchInput = document.getElementById('itemSearchInput');
    const searchResults = document.getElementById('searchResults');
    const selectedItemId = document.getElementById('selectedItemId');
    const priceInput = document.getElementById('itmprice');
    const barcodeInput = document.getElementById('barcodeSearchInput');

    if (!searchInput || !searchResults || !barcodeInput) return;

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
                            const listPrice = window.isPurchaseInvoice()
                                ? (parseFloat(item.price1) || 0)
                                : window.invoicePriceFor(item, window.currentInvoicePriceList(), false);
                            html += `
                                <div class="search-result-item"
                                     data-id="${item.id}"
                                     data-name="${item.iname}"
                                     data-price="${listPrice}"
                                     data-barcode="${item.barcode}"
                                     style="padding:10px; cursor:pointer; border-bottom:1px solid #eee;">
                                    <strong>${item.iname}</strong>
                                    ${item.name2 ? ' // ' + item.name2 : ''}
                                    <span class="search-result-price" style="float:left; color:#10b981;">${listPrice} ج.م</span>
                                </div>
                            `;
                        });
                        searchResults.innerHTML = html;
                        bindResultItems();
                        if (window.isSalesReturn()) {
                            window.fetchLastSaleBases(data.items.map(function(item) { return item.id; }), function(map) {
                                document.querySelectorAll('#searchResults .search-result-item').forEach(function(el) {
                                    var base = parseFloat(map[el.dataset.id]);
                                    if (!(base > 0)) return;
                                    el.dataset.price = base;
                                    var label = el.querySelector('.search-result-price');
                                    if (label) label.textContent = base + ' ج.م';
                                });
                            });
                        }
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

    // مسح صف الإدخال (مطلوب عند دمج الكمية بدل إضافة صف جديد)
    function clearInputRow() {
        searchInput.value = '';
        selectedItemId.value = '';
        $('#itmprice').val('0');
        $('#itmqty').val('1');
        $('#itmdisc').val('0');
        $('#itmval').val('0');
        $('#itmsprice_stg').val('0');
        $('#inputUnitSelect').empty().append('<option value="">اختر وحدة</option>');
    }

    // البحث عن صف موجود بنفس الصنف ونفس الوحدة
    function findExistingRow(itemId, unitVal) {
        var found = null;
        $('#itmrow tr').each(function() {
            var $row = $(this);
            if (String($row.find('input[name="itmname[]"]').val()) !== String(itemId)) return;
            var rowUnit = parseFloat($row.find('select[name="u_val[]"]').val()) || 1;
            if (Math.abs(rowUnit - unitVal) > 0.0001) return;
            found = $row;
            return false;
        });
        return found;
    }

    // إضافة صف جديد أو زيادة كمية الصف الموجود
    function addRowOrMerge(merge) {
        var itemId  = selectedItemId.value;
        var unitVal = parseFloat($('#inputUnitSelect').val()) || 1;

        if (merge && itemId) {
            var $row = findExistingRow(itemId, unitVal);
            if ($row) {
                var $qty = $row.find('.itmqty');
                $qty.val((parseFloat($qty.val()) || 0) + 1).trigger('input');
                $row.addClass('table-warning');
                setTimeout(function() { $row.removeClass('table-warning'); }, 700);
                clearInputRow();
                return true;
            }
        }

        $('#addRow').click();
        return false;
    }

    // اختيار الوحدة المطابقة للباركود الممسوح (قيم الوحدات عشرية مثل "12.000")
    function selectUnitByValue(unitSelect, unitVal) {
        if (unitVal === null || unitVal === undefined) return false;
        var matched = false;
        unitSelect.find('option').each(function() {
            if (Math.abs((parseFloat(this.value) || 0) - unitVal) < 0.0001) {
                unitSelect.val(this.value).trigger('change');
                matched = true;
                return false;
            }
        });
        return matched;
    }

    // اختيار صنف
    // opts: { unitVal, merge, focusBarcode }
    function selectItem(item, opts) {
        opts = opts || {};
        selectedItem = item;
        searchInput.value = item.name;
        selectedItemId.value = item.id;
        priceInput.value = item.price;
        searchResults.style.display = 'none';
        clearHighlight();

        // جلب بيانات الصنف الكاملة وتحديث الحقول مباشرة
        const isPurchase = window.isPurchaseInvoice();

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
                // في المشتريات: تكلفة الوحدة | في المبيعات: سعر الفئة المختارة على الفاتورة
                const defaultUnitCost = (data.units && data.units.length) ? (parseFloat(data.units[0].ucost) || 0) : 0;
                const listId = window.currentInvoicePriceList();
                const listPrice = (data.units && data.units.length)
                    ? window.invoicePriceFor(data.units[0], listId, true)
                    : window.invoicePriceFor(data, listId, false);
                const price = isPurchase
                    ? (defaultUnitCost || parseFloat(data.cost_price) || 0)
                    : listPrice;
                const defaultUnit = (data.units && data.units.length) ? (parseFloat(data.units[0].unit_value) || 1) : 1;

                function renderSelection(activePrice) {
                    $('#itmprice').val(activePrice);
                    $('#itmqty').val(1);
                    $('#itmdisc').val('0');
                    $('#itmval').val(activePrice);
                    $('#itmsprice_stg').val(listPrice || 0);

                    window.applyUnitItemInfo(data, (data.units && data.units.length) ? data.units[0] : null);

                    const unitSelect = $('#inputUnitSelect');
                    unitSelect.empty();
                    if (data.units && data.units.length) {
                        data.units.forEach(function(unit) {
                            var opt = document.createElement('option');
                            opt.value = unit.unit_value;
                            opt.textContent = unit.unit_name;
                            opt.dataset.ucost = unit.ucost || 0;
                            opt.dataset.uprice1 = unit.uprice1 || 0;
                            opt.dataset.uprice2 = unit.uprice2 || 0;
                            opt.dataset.uprice3 = unit.uprice3 || 0;
                            opt.dataset.uprice4 = unit.uprice4 || 0;
                            unitSelect.append(opt);
                        });

                        unitSelect.off('change').on('change', function() {
                            const selectedUnit = window.findInvoiceUnit(data.units, $(this).val());
                            if (!selectedUnit) return;
                            const unitListPrice = window.invoicePriceFor(selectedUnit, window.currentInvoicePriceList(), true);
                            const unitVal = parseFloat(selectedUnit.unit_value) || 1;
                            const returnBase = parseFloat(window._returnPriceBase) || 0;
                            const newPrice = (window.isSalesReturn() && returnBase > 0)
                                ? (returnBase * unitVal)
                                : (isPurchase ? (parseFloat(selectedUnit.ucost) || 0) : unitListPrice);
                            $('#itmprice').val(newPrice);
                            $('#itmqty').val(1);
                            $('#itmval').val(newPrice);
                            $('#itmsprice_stg').val(unitListPrice || 0);
                            window.applyUnitItemInfo(data, selectedUnit);
                        });
                    } else {
                        unitSelect.append('<option value="">لا توجد وحدات</option>');
                    }

                    selectUnitByValue(unitSelect, opts.unitVal);

                    window._pendingItemUnits = data.units || [];
                    var merged = addRowOrMerge(opts.merge);
                    if (opts.noFocus) {
                        searchInput.value = '';
                        searchInput.focus();
                        return;
                    }
                    setTimeout(function() {
                        if (opts.focusBarcode) {
                            barcodeInput.focus();
                            barcodeInput.select();
                        } else if (!merged) {
                            $('#itmrow tr:last .itmqty').focus().select();
                        }
                    }, 50);
                }

                if (window.isSalesReturn()) {
                    window.fetchLastSaleBases([item.id], function(map) {
                        var base = parseFloat(map[item.id]) || 0;
                        window._returnPriceBase = base;
                        renderSelection(base > 0 ? (base * defaultUnit) : price);
                    });
                } else {
                    window._returnPriceBase = 0;
                    renderSelection(price);
                }
            },
            error: function() {
                addRowOrMerge(opts.merge);
                if (opts.noFocus) {
                    searchInput.value = '';
                    searchInput.focus();
                    return;
                }
                setTimeout(function() {
                    if (opts.focusBarcode) {
                        barcodeInput.focus();
                    } else {
                        $('#itmrow tr:last .itmqty').focus().select();
                    }
                }, 50);
            }
        });
    }

    window.selectInvoiceItem = selectItem;

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

    // ===== الإدخال بالباركود =====
    const barcodeBox    = barcodeInput.closest('.barcode-scan-box');
    const barcodeStatus = document.getElementById('barcodeStatus');
    let barcodeTimeout;
    let barcodeBusy = false;

    function setBarcodeState(state, message) {
        if (barcodeBox) {
            barcodeBox.classList.toggle('is-error', state === 'error');
            barcodeBox.classList.toggle('is-ok', state === 'ok');
        }
        if (barcodeStatus) {
            barcodeStatus.textContent = message || '';
            barcodeStatus.style.color = state === 'error' ? '#dc2626'
                                      : state === 'ok'    ? '#16a34a'
                                      : '#6b7280';
        }
    }

    function resetBarcodeState(delay) {
        setTimeout(function() { setBarcodeState('', ''); }, delay || 1500);
    }

    function processBarcode() {
        clearTimeout(barcodeTimeout);
        const barcode = barcodeInput.value.trim();
        if (!barcode) return;

        // مسح سريع متتالٍ: أجّل الكود الحالي بدل إسقاطه
        if (barcodeBusy) {
            barcodeTimeout = setTimeout(processBarcode, 150);
            return;
        }

        barcodeBusy = true;
        setBarcodeState('', 'جاري البحث...');

        fetch('ajax/lookup_barcode.php?barcode=' + encodeURIComponent(barcode))
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    barcodeInput.value = '';
                    setBarcodeState('ok', '✓ ' + data.item.iname);
                    resetBarcodeState();
                    selectItem(
                        { id: data.item.id, name: data.item.iname, price: data.item.price1, barcode: data.item.barcode },
                        { unitVal: data.item.u_val, merge: true, focusBarcode: true }
                    );
                } else {
                    setBarcodeState('error', 'باركود غير موجود: ' + barcode);
                    barcodeInput.select();
                    resetBarcodeState(2500);
                }
            })
            .catch(() => {
                setBarcodeState('error', 'خطأ في الاتصال');
                resetBarcodeState(2500);
            })
            .finally(() => { barcodeBusy = false; });
    }

    // قارئ الباركود بيكتب بسرعة ثم يرسل Enter - وندعم كمان الكتابة اليدوية بـ debounce
    barcodeInput.addEventListener('input', function() {
        clearTimeout(barcodeTimeout);
        if (!this.value.trim()) {
            setBarcodeState('', '');
            return;
        }
        barcodeTimeout = setTimeout(processBarcode, 400);
    });

    barcodeInput.addEventListener('keydown', function(e) {
        if (e.key !== 'Enter') return;
        e.preventDefault();
        e.stopPropagation();
        processBarcode();
    });

    // F2 للانتقال السريع لحقل الباركود
    document.addEventListener('keydown', function(e) {
        if (e.key !== 'F2') return;
        e.preventDefault();
        barcodeInput.focus();
        barcodeInput.select();
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
        $baseQty = abs($detail['qty_in'] - $detail['qty_out']);
        if ($baseQty <= 0) {
            $baseQty = abs((float) ($detail['doc_qty'] ?? 0));
        }
        $quantity = ($detail['u_val'] > 0) ? $baseQty / $detail['u_val'] : $baseQty;
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
                <input value="<?php echo floatval($quantity); ?>" type="number" step="any"
                       name="itmqty[]" onclick="sT(this)" 
                       class="itmqty form-control form-control-sm" style="width:90px;">
            </td>
            
            <!-- السعر -->
            <td>
                <input type="number" name="itmprice[]" onclick="sT(this)" 
                       class="itmprice form-control form-control-sm" style="width:90px;" 
                       value="<?php echo floatval($price); ?>">
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
                <input value="<?php echo floatval($detail['discount']); ?>" 
                       type="number" name="itmdisc[]" onclick="sT(this)" 
                       class="itmdisc form-control form-control-sm" style="width:90px;">
            </td>

            <?php if ($showProfit): ?>
            <?php
            // الأساس = سعر الشراء (عمود السعر) | سعر البيع = سعر الفئة المختارة أعلى الفاتورة
            $sellList = (int) ($this->data['price_list'] ?? 1);
            if ($sellList < 1 || $sellList > 3) {
                $sellList = 1;
            }
            $uValRow = ($detail['u_val'] > 0) ? (float) $detail['u_val'] : 1;
            $sellPrice = floatval($detail['unit_price' . $sellList] ?? 0);
            if ($sellPrice <= 0) {
                $sellPrice = floatval($detail['item_price' . $sellList] ?? 0) * $uValRow;
            }
            if ($sellPrice <= 0) {
                $sellPrice = $price;
            }
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
                       title="سعر البيع - يُحفظ في سعر الصنف حسب الاختيار أعلى الفاتورة">
            </td>
            <?php endif; ?>

            <!-- القيمة -->
            <td>
                <input readonly value="<?php echo floatval($detail['det_value']); ?>" 
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
                $selected = ($selectedUnitVal && abs((float)$unit['u_val'] - (float)$selectedUnitVal) < 0.0001) ? 'selected' : '';
                $uval = htmlspecialchars((string)$unit['u_val'], ENT_QUOTES, 'UTF-8');
                $ucost = htmlspecialchars((string)($unit['cost_price'] ?? '0'), ENT_QUOTES, 'UTF-8');
                $p1 = htmlspecialchars((string)($unit['price1'] ?? '0'), ENT_QUOTES, 'UTF-8');
                $p2 = htmlspecialchars((string)($unit['price2'] ?? '0'), ENT_QUOTES, 'UTF-8');
                $p3 = htmlspecialchars((string)($unit['price3'] ?? '0'), ENT_QUOTES, 'UTF-8');
                $p4 = htmlspecialchars((string)($unit['price4'] ?? '0'), ENT_QUOTES, 'UTF-8');
                echo "<option value='{$uval}' data-ucost='{$ucost}' data-uprice1='{$p1}' data-uprice2='{$p2}' data-uprice3='{$p3}' data-uprice4='{$p4}' {$selected}>{$this->sanitizeInput($unit['uname'])}</option>";
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
                                <input id="itmqty" value="1" type="number" step="any"
                                       onclick="sT(this)" class="itmqty form-control form-control-sm nozero" 
                                       style="width:90px;">
                            </td>
                            
                            <!-- السعر -->
                            <td>
                                <input id="itmprice" value="0" type="number"
                                       onclick="sT(this)" class="itmprice form-control form-control-sm nozero" 
                                       style="width:90px;" step="0.001">
                            </td>
                            
                            <!-- الخصم -->
                            <td>
                                <input id="itmdisc" value="0" type="number"
                                       onclick="sT(this)" class="itmdisc form-control form-control-sm nozero" 
                                       style="width:120px;" step="0.001">
                            </td>
                            
                            <!-- القيمة -->
                            <td>
                                <input readonly id="itmval" value="0" type="number"
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