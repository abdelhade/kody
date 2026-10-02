let counter = 1;
let isProcessing = false; // منع التنفيذ المتعدد

// ========== تحذير مغادرة الصفحة لو في بيانات ==========
let formSubmitting = false;
let allowLeave = false;

function hasInvoiceData() {
    if ($('#itmrow tr').length > 0) return true;
    if ((parseFloat($('#headtotal').val()) || 0) > 0) return true;
    return false;
}

// منع مغادرة الصفحة بالـ browser native (للـ refresh/close)
function beforeUnloadHandler(e) {
    if (!formSubmitting && !allowLeave && hasInvoiceData()) {
        e.preventDefault();
        e.returnValue = '';
    }
}
window.addEventListener('beforeunload', beforeUnloadHandler);

// اعتراض الروابط داخل الصفحة (navbar/sidebar)
let _leaveConfirmOpen = false;
$(document).on('click', 'a[href]', function(e) {
    const href = $(this).attr('href');
    if (!href || href === '#' || href.startsWith('#') || href.startsWith('javascript')) return;
    if (formSubmitting || allowLeave || !hasInvoiceData()) return;
    if (_leaveConfirmOpen) { e.preventDefault(); return; }

    e.preventDefault();
    e.stopImmediatePropagation();
    // تحويل الـ href لـ absolute URL عشان نضمن الانتقال الصح
    const anchor = document.createElement('a');
    anchor.href = href;
    const targetHref = anchor.href;
    _leaveConfirmOpen = true;

    Swal.fire({
        type: 'warning',
        title: 'تنبيه',
        text: 'الفاتورة تحتوي على بيانات غير محفوظة، هل تريد المغادرة؟',
        showCancelButton: true,
        confirmButtonText: 'نعم، اخرج',
        cancelButtonText: 'لا، ارجع',
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        reverseButtons: true
    }).then(function(result) {
        _leaveConfirmOpen = false;
        // v8: result.value === true عند الضغط على confirm
        // v9+: result.isConfirmed === true
        if (result.value === true || result.isConfirmed === true) {
            window.removeEventListener('beforeunload', beforeUnloadHandler);
            allowLeave = true;
            window.location.href = targetHref;
        }
    });
});
// ========== نهاية تحذير المغادرة ==========

$(document).ready(function() {
    initializeSelect2();
    handleItemSelectionChange();
    handleRowAddition();
    handleInputChanges();
    handleRowDeletion();
    handleFormSubmission();
    handleKeyboardShortcuts();
    paintInvoiceLineWarnings();

    function applyClientPriceList(selectEl) {
        if (!selectEl || !selectEl.options || selectEl.selectedIndex < 0) return;
        const opt = selectEl.options[selectEl.selectedIndex];
        if (!opt) return;
        const list = parseInt(opt.getAttribute('data-price-list'), 10);
        if (!list) return;
        const priceSel = document.getElementById('invoicePriceList');
        if (!priceSel || String(priceSel.value) === String(list)) return;
        priceSel.value = String(list);
        $(priceSel).trigger('change');
    }

    // عند تغيير العميل/المورد → طبّق فئته السعرية، ثم أعد حساب paid/change، وفي المردود أعد التسعير
    $(document).on('change', '#mySelectEmp', function() {
        applyClientPriceList(this);
        updateTotal();
        repriceSalesReturnLines();
    });

    if (!/[?&](e|edit_id)=/.test(location.search)) {
        applyClientPriceList(document.getElementById('mySelectEmp'));
    }

    // تغيير الفئة السعرية يعيد تسعير أصناف فاتورة المبيعات
    $(document).on('change', '#invoicePriceList', function() {
        if (typeof window.isPurchaseInvoice === 'function' && window.isPurchaseInvoice()) {
            return;
        }
        if (typeof window.isSalesReturn === 'function' && window.isSalesReturn()) {
            repriceSalesReturnLines();
            return;
        }
        const listId = parseInt(this.value, 10) || 1;
        $('#itmrow tr').each(function() {
            const $row = $(this);
            const itemId = $row.find('input[name="itmname[]"]').val();
            if (!itemId) return;
            const unitVal = $row.find('select[name="u_val[]"]').val();
            $.getJSON('get/get_iteminfo.php', { id: itemId }, function(data) {
                if (!data || data.error) return;
                const unit = (typeof window.findInvoiceUnit === 'function')
                    ? window.findInvoiceUnit(data.units, unitVal)
                    : (data.units || []).find(function(u) {
                        return Math.abs((parseFloat(u.unit_value) || 0) - (parseFloat(unitVal) || 0)) < 0.0001;
                    });
                const price = unit
                    ? window.invoicePriceFor(unit, listId, true)
                    : window.invoicePriceFor(data, listId, false);
                const qty = parseFloat($row.find('.itmqty').val()) || 0;
                const pct = parseFloat($row.find('.itmdisc_pct').val()) || 0;
                $row.find('.itmprice').val(price);
                $row.find('.itmdisc').val(((qty * price * pct) / 100).toFixed(3));
                $row.find('.itmprice').trigger('input');
            });
        });
    });

    // دالة مساعدة لحساب وتحديث الباقي
    function updateChange() {
        const paid = parseFloat($('#paid').val()) || 0;
        const net  = parseFloat($('#headnet').val()) || 0;
        $('#change').val(parseFloat((net - paid).toFixed(2)));
    }
    
    // تحديث المدفوع عند تغيير الخصم أو الإضافات
    $(document).on('input change', '#headdisc, #headplus, #headtotal, #headnet', function() {
        if ($(this).attr('id') === 'headdisc' || $(this).attr('id') === 'headplus') {
            updateTotal(); // updateTotal هتتكفل بـ paid و change حسب المورد
        }
    });

    // عند تغيير المدفوع يدوياً → حساب الباقي (فقط لو مش مورد افتراضي)
    $(document).on('input', '#paid', function() {
        const supplierSel = document.getElementById('mySelectEmp');
        const isDefault = supplierSel &&
                          supplierSel.options.length > 0 &&
                          supplierSel.value === supplierSel.options[0].value;
        if (isDefault) {
            // المورد الافتراضي → أعد المدفوع للصافي
            const net = parseFloat($('#headnet').val()) || 0;
            $(this).val(net.toFixed(2));
            $('#change').val('0.00');
        } else {
            updateChange();
        }
    });

    // عند تغيير نسبة الخصم الإجمالية
    $(document).on('input', '#headdisc_pct', function() {
        const pct = parseFloat($(this).val()) || 0;
        const total = parseFloat($('#headtotal').val()) || 0;
        $('#headdisc').val(parseFloat((total * pct / 100).toFixed(2)));
        updateTotal();
    });
    
    // تحديث المدفوع عند تحميل الصفحة
    setTimeout(function() {
        updateTotal();
    }, 500);
    
    // الضغط على سطر الصنف يعرض بياناته أسفل الفاتورة
    $(document).on('click', '#itmrow tr', function(e) {
        if ($(e.target).closest('.deleteRow').length) return;
        showInvoiceLineInfo($(this));
    });

    // تغيير وحدة السطر يحدّث سعر السطر وبيانات الصنف أسفل الفاتورة
    $(document).on('change', '#itmrow select[name="u_val[]"]', function() {
        const $select = $(this);
        const $row = $select.closest('tr');
        const itemId = $row.find('input[name="itmname[]"]').val();
        if (!itemId) return;

        $.getJSON('get/get_iteminfo.php', { id: itemId }, function(data) {
            if (!data || data.error || typeof window.findInvoiceUnit !== 'function') return;
            const unit = window.findInvoiceUnit(data.units, $select.val());
            if (!unit) return;

            window.applyUnitItemInfo(data, unit);

            const purchase = (typeof window.isPurchaseInvoice === 'function') && window.isPurchaseInvoice();
            const listId = (typeof window.currentInvoicePriceList === 'function') ? window.currentInvoicePriceList() : 1;
            const unitListPrice = (typeof window.invoicePriceFor === 'function')
                ? window.invoicePriceFor(unit, listId, true)
                : (parseFloat(unit.uprice1) || 0);
            let newPrice = purchase ? (parseFloat(unit.ucost) || 0) : unitListPrice;
            if (!(newPrice > 0)) {
                const base = purchase
                    ? (parseFloat(data.cost_price) || parseFloat(data.last_price) || 0)
                    : (parseFloat(data.price1) || 0);
                newPrice = base * (parseFloat(unit.unit_value) || 1);
            }
            const qty = parseFloat($row.find('.itmqty').val()) || 0;
            const pct = parseFloat($row.find('.itmdisc_pct').val()) || 0;

            $row.find('.itmprice').val(newPrice);
            if (purchase) {
                $row.find('.itmsellprice').val(unitListPrice || 0);
            }
            $row.find('.itmdisc').val(((qty * newPrice * pct) / 100).toFixed(3));
            $row.find('.itmprice').trigger('input');
        });
    });

    // تحديث فوري عند أي تغيير في الصفوف
    $(document).on('input', '.itmqty, .itmprice, .itmdisc', function() {
        setTimeout(function() {
            updateTotal();
        }, 200);
    });
});

function showInvoiceLineInfo($row) {
    const itemId = $row.find('input[name="itmname[]"]').val();
    if (!itemId) return;

    $('#itmrow tr').removeClass('item-row-active');
    $row.addClass('item-row-active');

    const unitVal = $row.find('select[name="u_val[]"]').val();
    const fallbackName = $.trim($row.find('td').eq(1).find('p').text());
    if (fallbackName) $('#selectedLineName').text(fallbackName);

    $.getJSON('get/get_iteminfo.php', { id: itemId }, function(data) {
        if (!data || data.error || typeof window.applyUnitItemInfo !== 'function') return;
        const unit = (typeof window.findInvoiceUnit === 'function')
            ? window.findInvoiceUnit(data.units, unitVal)
            : null;
        window.applyUnitItemInfo(data, unit || ((data.units && data.units.length) ? data.units[0] : null));
    });
}

function setInvoiceRowPrice($row, price) {
    const qty = parseFloat($row.find('.itmqty').val()) || 0;
    const pct = parseFloat($row.find('.itmdisc_pct').val()) || 0;
    const shown = (typeof window.formatInvoiceMoney === 'function')
        ? window.formatInvoiceMoney(price)
        : price;
    $row.find('.itmprice').val(shown);
    $row.find('.itmdisc').val(((qty * price * pct) / 100).toFixed(3));
    $row.find('.itmprice').trigger('input');
}

function repriceSalesReturnLines() {
    if (typeof window.isSalesReturn !== 'function' || !window.isSalesReturn()) return;
    if (typeof window.fetchLastSaleBases !== 'function') return;

    const rows = [];
    $('#itmrow tr').each(function() {
        const id = $(this).find('input[name="itmname[]"]').val();
        if (id) rows.push({ row: $(this), id: id });
    });
    if (!rows.length) return;

    window.fetchLastSaleBases(rows.map(function(r) { return r.id; }), function(map) {
        const listId = (typeof window.currentInvoicePriceList === 'function') ? window.currentInvoicePriceList() : 1;
        rows.forEach(function(entry) {
            const unitVal = parseFloat(entry.row.find('select[name="u_val[]"]').val()) || 1;
            const base = parseFloat(map[entry.id]);
            if (base > 0) {
                setInvoiceRowPrice(entry.row, base * unitVal);
                return;
            }
            $.getJSON('get/get_iteminfo.php', { id: entry.id }, function(data) {
                if (!data || data.error) return;
                const unit = (typeof window.findInvoiceUnit === 'function')
                    ? window.findInvoiceUnit(data.units, unitVal)
                    : null;
                const price = unit
                    ? window.invoicePriceFor(unit, listId, true)
                    : window.invoicePriceFor(data, listId, false);
                setInvoiceRowPrice(entry.row, price);
            });
        });
    });
}

function normalizeInvoiceSearch(value) {
    return String(value || '')
        .replace(/[\u064B-\u0652\u0670\u0640]/g, '')
        .replace(/[أإآٱ]/g, 'ا')
        .replace(/ة/g, 'ه')
        .replace(/ى/g, 'ي')
        .replace(/\s+/g, ' ')
        .trim()
        .toLowerCase();
}

function invoiceSelectMatcher(params, data) {
    const term = normalizeInvoiceSearch(params.term);
    if (!term) return data;
    if (!data || typeof data.text === 'undefined') return null;
    return normalizeInvoiceSearch(data.text).indexOf(term) > -1 ? data : null;
}

function initializeSelect2() {
    $('#mySelectEmp, #invoiceStore').each(function() {
        const $el = $(this);
        if (!$el.length || !$.fn.select2) return;
        if ($el.hasClass('select2-hidden-accessible')) {
            $el.select2('destroy');
        }
        $el.select2({
            dir: 'rtl',
            width: '100%',
            theme: 'bootstrap4',
            dropdownParent: $(document.body),
            minimumResultsForSearch: 0,
            matcher: invoiceSelectMatcher,
            language: {
                noResults: function() { return 'لا توجد نتائج'; },
                searching: function() { return 'جاري البحث...'; }
            }
        });
        $el.off('select2:open.invoiceSearch').on('select2:open.invoiceSearch', function() {
            window.setTimeout(function() {
                const field = document.querySelector('.select2-dropdown .select2-search__field');
                if (!field) return;
                field.placeholder = 'ابحث بالاسم...';
                field.focus();
            }, 0);
        });
    });

    if (!$('#mySelectitm').length || !$.fn.select2) {
        return;
    }
    if ($('#mySelectitm').hasClass('select2-hidden-accessible')) {
        $('#mySelectitm').select2('destroy');
    }
    $('#mySelectitm').select2({
        placeholder: "اختر صنف",
        ajax: {
            url: 'js/ajax/sales_myitems.php',
            dataType: 'json',
            delay: 250, // زيادة التأخير لتقليل الطلبات
            data: (params) => ({ search: params.term }),
            processResults: (data) => ({ results: data }),
            cache: true
        },
        minimumInputLength: 1 // لا تبحث إلا بعد كتابة حرف واحد
    });
}

function handleItemSelectionChange() {
    // استخدام event delegation مرة واحدة فقط
    $(document).off('change', 'select.mySelectitm').on('change', 'select.mySelectitm', function() {
        if (isProcessing) return;
        isProcessing = true;
        
        const row = $(this).closest('tr');
        fetchItemInfo($(this).val(), row);
        
        setTimeout(() => { isProcessing = false; }, 300);
    });
}

function fetchItemInfo(itemId, row) {
    $.ajax({
        url: 'get/get_iteminfo.php?id=' + itemId,
        method: 'GET',
        dataType: 'json',
        cache: true, // تفعيل الكاش
        success: function(data) {
            const purchase = (typeof window.isPurchaseInvoice === 'function')
                ? window.isPurchaseInvoice()
                : getParameterByName('q') === 'purchase';
            const listId = (typeof window.currentInvoicePriceList === 'function') ? window.currentInvoicePriceList() : 1;
            const price = purchase
                ? (parseFloat(data.cost_price) || parseFloat(data.last_price) || 0)
                : ((typeof window.invoicePriceFor === 'function') ? window.invoicePriceFor(data, listId, false) : (parseFloat(data.price1) || 0));
            // تحديث الحقول دفعة واحدة
            row.find("#itmprice").val(price);
            row.find("#itmval").val(price);
            row.find("#itmqty").val(1);
            
            if (typeof window.applyUnitItemInfo === 'function') {
                window.applyUnitItemInfo(data, (data.units && data.units.length) ? data.units[0] : null);
            }
            
            const unitSelect = row.find('select[name="u_val[]"]');
            unitSelect.empty();
            
            if (data.units && data.units.length) {
                const options = data.units.map(unit => 
                    `<option value="${unit.unit_value}">${unit.unit_name}</option>`
                ).join('');
                unitSelect.html(options);
            }
        },
        error: function(xhr, status, error) {
            console.error("خطأ في استدعاء البيانات:", error);
        }
    });
}
        
function fillRowUnitSelect($select, fallbackVal, fallbackName) {
    const units = Array.isArray(window._pendingItemUnits) ? window._pendingItemUnits : [];
    const selectedVal = $('#inputUnitSelect').val();
    const selectEl = $select[0];
    if (!selectEl) return;

    function addUnitOption(name, value, attrs) {
        if (value === undefined || value === null || String(value) === '') return;
        const opt = new Option(name || '-', String(value));
        if (attrs) {
            Object.keys(attrs).forEach(function(key) {
                opt.setAttribute(key, attrs[key] == null ? 0 : attrs[key]);
            });
        }
        selectEl.appendChild(opt);
    }

    if (units.length) {
        units.forEach(function(unit) {
            addUnitOption(unit.unit_name, unit.unit_value, {
                'data-ucost': unit.ucost,
                'data-uprice1': unit.uprice1,
                'data-uprice2': unit.uprice2,
                'data-uprice3': unit.uprice3,
                'data-uprice4': unit.uprice4
            });
        });
    } else {
        $('#inputUnitSelect option').each(function() {
            if (this.value === '') return;
            addUnitOption(this.text, this.value, {
                'data-ucost': this.getAttribute('data-ucost'),
                'data-uprice1': this.getAttribute('data-uprice1'),
                'data-uprice2': this.getAttribute('data-uprice2'),
                'data-uprice3': this.getAttribute('data-uprice3'),
                'data-uprice4': this.getAttribute('data-uprice4')
            });
        });
    }

    if (!selectEl.options.length) {
        addUnitOption(fallbackName || '-', fallbackVal || 1, null);
    }

    const target = parseFloat(selectedVal);
    let matched = false;
    Array.prototype.forEach.call(selectEl.options, function(opt) {
        if (!matched && Math.abs((parseFloat(opt.value) || 0) - target) < 0.0001) {
            opt.selected = true;
            matched = true;
        }
    });
    if (!matched) selectEl.selectedIndex = 0;
    window._pendingItemUnits = null;
}

        function handleRowAddition() {
            $(document).off("click", "#addRow").on("click", "#addRow", function(e) {
                e.preventDefault();
                const itemId   = $("#selectedItemId").val();
                const itemName = $("#itemSearchInput").val().trim();
                if (itemId) {
                    addNewRow(itemId, itemName || '---');
                }
                // بدون alert - الإضافة بتحصل فقط لو في صنف محدد
            });
        }

        function addNewRow(itemId, itemName) {
            const qty       = parseFloat($("#itmqty").val())        || 1;
            const price     = parseFloat($("#itmprice").val())       || 0;  // سعر الشراء
            const disc      = parseFloat($("#itmdisc").val())        || 0;
            const sprice    = parseFloat($("#itmsprice_stg").val())  || price; // سعر البيع الافتراضي
            const val       = (qty * price) - disc;
            const unitVal   = parseFloat($("#inputUnitSelect").val()) || 1;
            const unitName  = $("#inputUnitSelect option:selected").text() || '-';
            // نسبة الربح على أساس سعر الشراء (price)
            const profitPct = price > 0 ? parseFloat(((sprice - price) / price * 100).toFixed(1)) : 0;

            // عمودا نسبة الربح وسعر البيع يظهران في فاتورة المشتريات فقط
            const profitCells = window.SHOW_PROFIT_COLS ? `
                <td>
                    <input type="number" class="itmprofit_pct form-control form-control-sm" value="${profitPct}" style="width:70px; background:#f0fdf4; color:#16a34a; font-weight:600;" step="0.1" onclick="sT(this)" title="غيّر نسبة الربح لتحديث سعر البيع">
                </td>
                <td>
                    <input type="number" name="itmsellprice[]" class="itmsellprice form-control form-control-sm" value="${sprice}" style="width:90px;" step="0.001" onclick="sT(this)" title="يُحفظ في سعر الصنف (price1)">
                </td>` : '';

            const newRow = $(`<tr>
                <td class="col-1">${counter++}</td>
                <td class="col-lg-5">
                    <p>${itemName}</p>
                    <input type="number" name="itmname[]" hidden value="${itemId}">
                </td>
                <td>
                    <select name="u_val[]" class="form-control form-control-sm" style="width:100px;"></select>
                </td>
                <td><input type="number" name="itmqty[]"     value="${qty}"            class="itmqty     form-control form-control-sm" style="width:90px;"  onclick="sT(this)"></td>
                <td><input type="number" name="itmprice[]"  value="${price}"           class="itmprice   form-control form-control-sm" style="width:90px;"  onclick="sT(this)" step="0.001"></td>
                <td><input type="number" name="itmdisc_pct[]" value="0.00"             class="itmdisc_pct form-control form-control-sm" style="width:80px;" step="0.01" min="0" max="100" placeholder="%" onclick="sT(this)"></td>
                <td><input type="number" name="itmdisc[]"   value="${disc}"            class="itmdisc    form-control form-control-sm" style="width:90px;"  onclick="sT(this)" step="0.001"></td>
                ${profitCells}
                <td><input type="number" name="itmval[]"    value="${parseFloat(val.toFixed(3))}"  class="itmval bg-light form-control form-control-sm" style="width:150px;" readonly step="0.001"></td>
                <td><button type="button" class="deleteRow btn btn-danger">X</button></td>
            </tr>`);

            fillRowUnitSelect(newRow.find('select[name="u_val[]"]'), unitVal, unitName);

            newRow.appendTo("#itmrow");

            const qtyField = newRow.find('.itmqty').get(0);
            if (qtyField) {
                qtyField.focus();
                setTimeout(function() { try { qtyField.select(); } catch (ex) {} }, 0);
            }

            // مسح حقول الإدخال
            $("#itemSearchInput").val('');
            $("#selectedItemId").val('');
            $("#itmprice").val('0');
            $("#itmqty").val('1');
            $("#itmdisc").val('0');
            $("#itmval").val('0');
            $("#itmsprice_stg").val('0');
            $("#inputUnitSelect").empty().append('<option value="">اختر وحدة</option>');
            updateTotal();
        }

function handleInputChanges() {
    let timeout;

    const ALL_ROW_INPUTS = '.itmqty, .itmprice, .itmdisc, .itmdisc_pct, .itmsellprice, .itmprofit_pct';

    // تعطيل تغيير القيمة بعجلة الماوس؛ أسهم الكيبورد تتنقل بين الحقول
    $(document).on('wheel', '#itmrow input[type="number"]', function(e) {
        e.preventDefault();
    });

    // تغيير وحدة صف الفاتورة يحدّث سعر الصف حسب الوحدة المختارة
    $(document).off('change.rowunit').on('change.rowunit', '#itmrow select[name="u_val[]"]', function() {
        const $sel = $(this);
        const row = $sel.closest('tr');
        const unit = unitFromOption($sel.find('option:selected'));
        if (unit) {
            applyUnitToRow(row, unit);
            return;
        }
        const itemId = row.find('input[name="itmname[]"]').val();
        const unitVal = parseFloat($sel.val()) || 0;
        if (!itemId) return;
        $.getJSON('get/get_iteminfo.php', { id: itemId }, function(data) {
            if (!data || data.error || !data.units) return;
            const found = data.units.find(function(u) {
                return Math.abs((parseFloat(u.unit_value) || 0) - unitVal) < 0.0001;
            });
            if (found) applyUnitToRow(row, found);
        });
    });

    // معالج واحد فقط (namespace) لتفادي تكرار الـ handlers
    $(document).off('input.rowcalc').on('input.rowcalc', ALL_ROW_INPUTS, function() {
        const row   = $(this).closest('tr');
        const $this = $(this);

        // "ر. ربح %" → سعر البيع = سعر الشراء × (1 + النسبة/100)  | لا يغيّر سعر الشراء ولا الإجمالي
        if ($this.hasClass('itmprofit_pct')) {
            const price = parseFloat(row.find('.itmprice').val()) || 0;
            const pct   = parseFloat($this.val()) || 0;
            row.find('.itmsellprice').val(parseFloat((price * (1 + pct / 100)).toFixed(3)));
            return;
        }

        // "س. بيع" → نسبة الربح = (البيع - الشراء) / الشراء × 100  | لا يغيّر سعر الشراء ولا الإجمالي
        if ($this.hasClass('itmsellprice')) {
            calcProfitPct(row);
            paintInvoiceLineWarnings();
            return;
        }

        // باقي الحقول (كمية/سعر/خصم) تؤثر على القيمة والإجمالي → debounce
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            if ($this.hasClass('itmdisc_pct')) {
                calcDiscFromPct(row);
            } else if ($this.hasClass('itmdisc')) {
                calcPctFromDisc(row);
            } else if ($this.hasClass('itmprice')) {
                // تغيّر سعر الشراء → أعد حساب نسبة الربح من سعر البيع الحالي
                calcProfitPct(row);
            }
            calculateItemValue(row);
            updateTotal();
        }, 150);
    });
}

function unitFromOption($opt) {
    if (!$opt.length) return null;
    if ($opt.attr('data-uprice1') === undefined && $opt.attr('data-ucost') === undefined) return null;
    return {
        ucost: $opt.attr('data-ucost'),
        uprice1: $opt.attr('data-uprice1'),
        uprice2: $opt.attr('data-uprice2'),
        uprice3: $opt.attr('data-uprice3'),
        uprice4: $opt.attr('data-uprice4'),
        unit_value: $opt.val()
    };
}

function applyUnitToRow(row, unit) {
    const purchase = typeof window.isPurchaseInvoice === 'function' && window.isPurchaseInvoice();
    const listId = typeof window.currentInvoicePriceList === 'function' ? window.currentInvoicePriceList() : 1;
    const listPrice = typeof window.invoicePriceFor === 'function'
        ? window.invoicePriceFor(unit, listId, true)
        : (parseFloat(unit.uprice1) || 0);
    const unitVal = parseFloat(unit.unit_value) || 1;

    function writePrice(price) {
        row.find('.itmprice').val(price);
        if (row.find('.itmsellprice').length) {
            row.find('.itmsellprice').val(listPrice || price);
            calcProfitPct(row);
        }
        if (row.find('.itmdisc_pct').length) {
            calcDiscFromPct(row);
        }
        calculateItemValue(row);
        updateTotal();
    }

    if (!purchase && typeof window.isSalesReturn === 'function' && window.isSalesReturn() && typeof window.fetchLastSaleBases === 'function') {
        const itemId = row.find('input[name="itmname[]"]').val();
        window.fetchLastSaleBases([itemId], function(map) {
            const base = parseFloat(map[itemId]) || 0;
            writePrice(base > 0 ? base * unitVal : listPrice);
        });
        return;
    }

    writePrice(purchase ? (parseFloat(unit.ucost) || 0) : listPrice);
}

// نسبة الربح على أساس سعر الشراء (عمود السعر)
function calcProfitPct(row) {
    const price = parseFloat(row.find('.itmprice').val())     || 0; // سعر الشراء
    const sell  = parseFloat(row.find('.itmsellprice').val()) || 0; // سعر البيع
    if (price > 0) {
        row.find('.itmprofit_pct').val(parseFloat(((sell - price) / price * 100).toFixed(1)));
    }
}

        function calculateItemValue(row) {
            const itmQty = parseFloat(row.find('.itmqty').val()) || 0;
            const itmPrice = parseFloat(row.find('.itmprice').val()) || 0;
            const itmDisc = parseFloat(row.find('.itmdisc').val()) || 0;
            const itmVal = (itmQty * itmPrice) - itmDisc;
            row.find('.itmval').val(parseFloat(itmVal.toFixed(3)));
        }

        function calcDiscFromPct(row) {
            const qty   = parseFloat(row.find('.itmqty').val())   || 0;
            const price = parseFloat(row.find('.itmprice').val()) || 0;
            const pct   = parseFloat(row.find('.itmdisc_pct').val()) || 0;
            const disc  = (qty * price * pct) / 100;
            row.find('.itmdisc').val(parseFloat(disc.toFixed(3)));
        }

        function calcPctFromDisc(row) {
            const qty   = parseFloat(row.find('.itmqty').val())   || 0;
            const price = parseFloat(row.find('.itmprice').val()) || 0;
            const disc  = parseFloat(row.find('.itmdisc').val())  || 0;
            const base  = qty * price;
            const pct   = base > 0 ? (disc / base) * 100 : 0;
            row.find('.itmdisc_pct').val(parseFloat(pct.toFixed(2)));
        }

        function handleRowDeletion() {
            $("#itmrow").on("click", ".deleteRow", function() {
                $(this).closest("tr").remove();
                updateTotal();
            });
        }

        function newInvoiceRequestUrl() {
            const params = new URLSearchParams(window.location.search);
            const q = params.get('q') || 'sale';
            return 'sales.php?q=' + encodeURIComponent(q);
        }

        function openSavedInvoicePrint(orderId) {
            const cashier = document.getElementById('printCashierField');
            const useCashier = cashier && cashier.value === '1';
            const url = useCashier
                ? ('print/receipt.php?id=' + orderId + '&src=invoice')
                : ('print/print_sales.php?id=' + orderId);
            window.open(url, '_blank');
        }

        function handleInvoiceFinish() {
            let pendingSubmit = 'save';
            $(document).on('click', '#submit, #submit2', function() {
                pendingSubmit = this.value || 'save';
            });

            $('#myForm2').on('submit', function(event) {
                const form = this;
                const lines = paintInvoiceLineWarnings();
                if (lines.length) {
                    event.preventDefault();
                    const html = lines.map(function(text) {
                        return '<div style="text-align:right;margin:4px 0;">' + $('<div>').text(text).html() + '</div>';
                    }).join('');
                    Swal.fire({
                        type: 'error',
                        title: 'لا يمكن حفظ الفاتورة',
                        html: html,
                        confirmButtonText: 'حسناً'
                    });
                    const $first = $('#itmrow .invoice-warn-qty, #itmrow .invoice-warn-price').first();
                    if ($first.length) $first.focus().select();
                    return;
                }

                const action = form.getAttribute('action') || '';
                if (action.indexOf('doadd_invoice.php') === -1) return;

                event.preventDefault();
                formSubmitting = true;
                allowLeave = true;

                const $buttons = $('#submit, #submit2');
                $buttons.prop('disabled', true);

                const formData = new FormData(form);
                formData.set('submit', pendingSubmit);
                formData.set('ajax_save', '1');

                $.ajax({
                    url: action,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'text'
                }).done(function(raw) {
                    let response = null;
                    try {
                        response = JSON.parse(raw);
                    } catch (e) {
                        response = null;
                    }
                    if (!response || !response.success) {
                        formSubmitting = false;
                        allowLeave = false;
                        $buttons.prop('disabled', false);
                        Swal.fire({
                            type: 'error',
                            title: 'خطأ',
                            text: (response && response.message) ? response.message : (raw || 'فشل الحفظ')
                        });
                        return;
                    }
                    if (pendingSubmit === 'print' && response.order_id) {
                        openSavedInvoicePrint(response.order_id);
                    }
                    Swal.fire({
                        type: 'success',
                        title: 'تم بنجاح',
                        text: response.message || 'تم الحفظ بنجاح',
                        confirmButtonText: 'حسناً',
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    }).then(function(result) {
                        if (result.value === true || result.isConfirmed === true) {
                            window.location.href = newInvoiceRequestUrl();
                        }
                    });
                }).fail(function() {
                    formSubmitting = false;
                    allowLeave = false;
                    $buttons.prop('disabled', false);
                    Swal.fire({
                        type: 'error',
                        title: 'خطأ',
                        text: 'فشل الاتصال بالخادم'
                    });
                });
            });
        }

        function handleFormSubmission() {
            handleInvoiceFinish();
            $('#addItemForm').on('submit', function(event) {
                event.preventDefault();
                const formData = new FormData(this);
                $.ajax({
                    url: 'js/ajax/doadd_item.php',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        $('#msgitem').html(response);
                        refreshSelect();
                    },
                    error: function(xhr) {
                        console.error('خطأ:', xhr.status);
                    }
                });
            });
        }

        function refreshSelect() {
            $.get('js/ajax/refresh_select.php', function(data) {
                $('#mySelectitm').html(data);
            });
        }

        function getParameterByName(name, url = window.location.href) {
            const regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)');
            const results = regex.exec(url);
            return results ? (results[2] ? decodeURIComponent(results[2].replace(/\+/g, ' ')) : '') : null;
        }

function rowSalePrice($row) {
    const purchase = typeof window.isPurchaseInvoice === 'function' && window.isPurchaseInvoice();
    if (purchase) {
        const $sell = $row.find('.itmsellprice');
        if (!$sell.length) return null;
        const sell = parseFloat($sell.val());
        return isFinite(sell) ? sell : 0;
    }
    const price = parseFloat($row.find('.itmprice').val());
    return isFinite(price) ? price : 0;
}

function paintInvoiceLineWarnings() {
    const lines = [];
    const purchase = typeof window.isPurchaseInvoice === 'function' && window.isPurchaseInvoice();

    $('#itmrow tr').each(function() {
        const $row = $(this);
        if (!$row.find('input[name="itmname[]"]').val()) return;

        const name = $.trim($row.find('td').eq(1).find('p').text()) || 'صنف';
        const qty = parseFloat($row.find('.itmqty').val());
        const negativeQty = isFinite(qty) && qty < 0;
        $row.find('.itmqty')
            .toggleClass('invoice-warn-qty', negativeQty)
            .attr('title', negativeQty ? 'الكمية سالبة' : '');

        const sale = rowSalePrice($row);
        const $saleInput = purchase ? $row.find('.itmsellprice') : $row.find('.itmprice');
        const zeroSale = sale !== null && sale === 0;
        $saleInput
            .toggleClass('invoice-warn-price', zeroSale)
            .attr('title', zeroSale ? 'البيع صفر' : '');

        if (negativeQty) lines.push(name + ': الكمية سالبة');
        if (zeroSale) lines.push(name + ': البيع صفر');
    });

    let $box = $('#invoiceLineWarnBox');
    if (!$box.length && $('#fatTable').length) {
        const $wrap = $('#fatTable').closest('.itemtable, .table-responsive');
        ($wrap.length ? $wrap : $('#fatTable')).before('<div id="invoiceLineWarnBox"></div>');
        $box = $('#invoiceLineWarnBox');
    }
    if (!$box.length) return lines;

    if (!lines.length) {
        $box.hide().empty();
        return lines;
    }

    const html = lines.map(function(text) {
        return '<div>' + $('<div>').text(text).html() + '</div>';
    }).join('');
    $box.html('<strong>لا يمكن الحفظ</strong>' + html).show();
    return lines;
}

function updateTotal() {
    paintInvoiceLineWarnings();
    let total = 0;
    let totalQty = 0;
    $('#itmrow .itmval').each(function() {
        total += parseFloat(this.value) || 0;
    });
    $('#itmrow .itmqty').each(function() {
        totalQty += parseFloat(this.value) || 0;
    });
    
    const headtotal = total;
    const headdisc = parseFloat($("#headdisc").val()) || 0;
    const headplus = parseFloat($("#headplus").val()) || 0;
    const headnet = headtotal - headdisc + headplus;
    
    $('#headtotal').val(parseFloat(headtotal.toFixed(2)));
    $("#headnet").val(parseFloat(headnet.toFixed(2)));
    $('#headqty').val(parseFloat(totalQty.toFixed(2)));
    
    if (headtotal > 0) {
        $('#headdisc_pct').val(parseFloat(((headdisc / headtotal) * 100).toFixed(2)));
    } else {
        $('#headdisc_pct').val('0');
    }
    
    // هل المورد المختار هو المورد الافتراضي (أول option في القائمة)؟
    const supplierSel = document.getElementById('mySelectEmp');
    const isDefaultSupplier = supplierSel &&
                              supplierSel.options.length > 0 &&
                              supplierSel.value === supplierSel.options[0].value;

    const isEditMode = window.location.href.indexOf('edit_id') !== -1;
    const paidValue  = parseFloat(headnet.toFixed(2));

    if (isDefaultSupplier) {
        // المورد الافتراضي → paid = net دايماً، change = 0
        $("#paid").val(paidValue);
        $("#change").val('0.00');
    } else {
        // مورد تاني → لا تكتب فوق المدفوع لو اتعدّل يدوياً
        const currentPaid = parseFloat($("#paid").val()) || 0;
        const currentNet  = parseFloat($("#headnet").val()) || 0;
        if (!isEditMode && (currentPaid === currentNet || currentNet === 0)) {
            $("#paid").val(paidValue);
        }
        const finalPaid = parseFloat($("#paid").val()) || 0;
        $("#change").val(parseFloat((headnet - finalPaid).toFixed(2)));
    }
}



// تم نقل معالجة Enter إلى handleKeyboardShortcuts
        

function handleKeyboardShortcuts() {
    // ترتيب الحقول من اليمين لليسار في الصفحة العربية
    const ROW_FIELDS = ['select[name="u_val[]"]', '.itmqty', '.itmprice', '.itmdisc_pct', '.itmdisc', '.itmprofit_pct', '.itmsellprice'];

    function rowFields($row) {
        const fields = [];
        ROW_FIELDS.forEach(function(sel) {
            const el = $row.find(sel).get(0);
            if (el && !el.disabled && !el.readOnly) fields.push(el);
        });
        return fields;
    }

    function focusField(el) {
        if (!el) return;
        const row = el.closest('#itmrow tr');
        if (row) {
            $('#itmrow tr').removeClass('item-row-active');
            row.classList.add('item-row-active');
        }
        el.focus();
        if (typeof el.select === 'function') {
            setTimeout(function() { try { el.select(); } catch (ex) {} }, 0);
        }
        if (typeof el.scrollIntoView === 'function') {
            el.scrollIntoView({ block: 'nearest', inline: 'nearest' });
        }
    }

    $(document).off('keydown.invoiceNav').on('keydown.invoiceNav', function(event) {
        if (event.key === 'F11') {
            event.preventDefault();
            $('#submit2').click();
            return;
        }
        if (event.key === 'F12') {
            event.preventDefault();
            $('#submit').click();
            return;
        }

        const $target = $(event.target);
        if ($target.is('button, textarea')) return;
        if ($target.hasClass('select2-search__field') || $target.closest('.select2-dropdown, .select2-container').length) return;
        if (document.querySelector('.modal.show')) return;

        const $row = $target.closest('#itmrow tr');
        if (!$row.length) return;

        const key = event.key;
        const moveKeys = { ArrowUp: 1, ArrowDown: 1, ArrowLeft: 1, ArrowRight: 1, Enter: 1 };
        if (!moveKeys[key]) return;

        const fields = rowFields($row);
        const idx = fields.indexOf(event.target);
        if (idx < 0) return;

        // أسهم القائمة تغيّر الوحدة؛ يمين ويسار وEnter ينقلون بين الحقول
        if ($target.is('select') && (key === 'ArrowUp' || key === 'ArrowDown')) return;

        event.preventDefault();
        event.stopImmediatePropagation();

        if (key === 'ArrowDown' || key === 'ArrowUp') {
            const $destRow = key === 'ArrowDown' ? $row.next('tr') : $row.prev('tr');
            if (!$destRow.length) {
                if (key === 'ArrowUp') focusField(document.getElementById('itemSearchInput'));
                return;
            }
            const destFields = rowFields($destRow);
            focusField(destFields[idx] || destFields[0]);
            return;
        }

        if (key === 'ArrowLeft' || key === 'ArrowRight' || key === 'Enter') {
            // ArrowLeft و Enter يتقدمان لليسار (الحقل التالي). ArrowRight يرجع لليمين.
            const forward = key !== 'ArrowRight';
            const nextIdx = idx + (forward ? 1 : -1);
            if (fields[nextIdx]) {
                focusField(fields[nextIdx]);
                return;
            }
            const $destRow = forward ? $row.next('tr') : $row.prev('tr');
            if ($destRow.length) {
                const destFields = rowFields($destRow);
                focusField(forward ? destFields[0] : destFields[destFields.length - 1]);
                return;
            }
            if (forward) focusField(document.getElementById('itemSearchInput'));
        }
    });
}
