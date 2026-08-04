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
$(document).on('click', 'a[href]', function(e) {
    const href = $(this).attr('href');
    if (!href || href === '#' || href.startsWith('#') || href.startsWith('javascript')) return;
    if (formSubmitting || allowLeave || !hasInvoiceData()) return;

    e.preventDefault();
    e.stopImmediatePropagation();
    const targetHref = href;

    Swal.fire({
        icon: 'warning',
        title: 'تنبيه',
        text: 'الفاتورة تحتوي على بيانات غير محفوظة، هل تريد المغادرة؟',
        showCancelButton: true,
        confirmButtonText: 'نعم، اخرج',
        cancelButtonText: 'لا، ارجع',
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        reverseButtons: true
    }).then(function(result) {
        if (result.isConfirmed) {
            // شيل الـ listener كلياً قبل ما تروح
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

    // عند تغيير المورد → أعد حساب paid/change
    $(document).on('change', '#mySelectEmp', function() {
        updateTotal();
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
    
    // تحديث فوري عند أي تغيير في الصفوف
    $(document).on('input', '.itmqty, .itmprice, .itmdisc', function() {
        setTimeout(function() {
            updateTotal();
        }, 200);
    });
});

function initializeSelect2() {
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
            const isSale = getParameterByName('q') === 'sale';
            const price = isSale ? data.last_price : data.price1;
            // تحديث الحقول دفعة واحدة
            row.find("#itmprice").val(price);
            row.find("#itmval").val(price);
            row.find("#itmqty").val(1);
            
            // تحديث العناصر بدون استخدام .html() المتكرر
            const updates = {
                '#storeqty': data.itmqty ? parseFloat(data.itmqty.toFixed(2)) : '0',
                '#price1': data.price1 || '0',
                '#market_price': data.market_price || '0',
                '#storemdtime': data.mdtime || '',
                '#cost_price': data.cost_price || '0',
                '#last_price': data.last_price || '0'
            };
            
            Object.entries(updates).forEach(([selector, value]) => {
                $(selector).text(value);
            });
            
            const unitSelect = row.find('select[name="u_val[]"]');
            unitSelect.empty();
            
            if (data.units && data.units.length) {
                const options = data.units.map(unit => 
                    `<option value="${unit.unit_value}">${unit.unit_name}</option>`
                ).join('');
                unitSelect.html(options);
                
                // Unit change event - استخدام off قبل on لمنع التكرار
                unitSelect.off('change').on('change', function() {
                    const selectedUnit = data.units.find(u => u.unit_value == $(this).val());
                    if (selectedUnit) {
                        const newPrice = isSale ? data.last_price * selectedUnit.unit_value : selectedUnit.uprice1;
                        row.find("#itmprice").val(newPrice);
                        row.find("#itmqty").val(1);
                        
                        $('#storeqty').text(parseFloat((data.itmqty/selectedUnit.unit_value).toFixed(2)) + " (" + selectedUnit.unit_value + ")");
                        $('#price1').text(selectedUnit.uprice1);
                        $('#market_price').text(selectedUnit.uprice3);
                        $('#cost_price').text(data.cost_price * selectedUnit.unit_value);
                        $('#last_price').text(data.last_price * selectedUnit.unit_value);
                        
                        calculateItemValue(row);
                        updateTotal();
                    }
                });
            }
        },
        error: function(xhr, status, error) {
            console.error("خطأ في استدعاء البيانات:", error);
        }
    });
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
                    <select name="u_val[]" class="form-control form-control-sm" style="width:100px;">
                        <option value="${unitVal}">${unitName}</option>
                    </select>
                </td>
                <td><input type="number" name="itmqty[]"     value="${qty}"            class="itmqty     form-control form-control-sm" style="width:90px;"  onclick="sT(this)"></td>
                <td><input type="number" name="itmprice[]"  value="${price}"           class="itmprice   form-control form-control-sm" style="width:90px;"  onclick="sT(this)" step="0.001"></td>
                <td><input type="number" name="itmdisc_pct[]" value="0.00"             class="itmdisc_pct form-control form-control-sm" style="width:80px;" step="0.01" min="0" max="100" placeholder="%" onclick="sT(this)"></td>
                <td><input type="number" name="itmdisc[]"   value="${disc}"            class="itmdisc    form-control form-control-sm" style="width:90px;"  onclick="sT(this)" step="0.001"></td>
                ${profitCells}
                <td><input type="number" name="itmval[]"    value="${parseFloat(val.toFixed(3))}"  class="itmval bg-light form-control form-control-sm" style="width:150px;" readonly step="0.001"></td>
                <td><button type="button" class="deleteRow btn btn-danger">X</button></td>
            </tr>`);

            newRow.appendTo("#itmrow");

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

    // تعطيل تغيير القيمة بالسكرول وأسهم الكيبورد في صفوف الفاتورة
    $(document).on('wheel', '#itmrow input[type="number"]', function(e) {
        e.preventDefault();
    });
    $(document).on('keydown', '#itmrow input[type="number"]', function(e) {
        if (e.key === 'ArrowUp' || e.key === 'ArrowDown') {
            e.preventDefault();
        }
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

        function handleFormSubmission() {
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

function updateTotal() {
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
    // ترتيب التنقل داخل صف الفاتورة بالـ Enter: كمية → سعر → خصم → بحث
    const ROW_FIELDS = ['.itmqty', '.itmprice', '.itmdisc_pct', '.itmdisc', '.itmprofit_pct', '.itmsellprice'];

    $(document).off('keydown').on('keydown', function(event) {
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

        if (event.key !== 'Enter') return;
        const $target = $(event.target);
        if (!$target.is('input, select')) return;

        // هل الحقل داخل صف فاتورة في #itmrow؟
        const $row = $target.closest('#itmrow tr');
        if ($row.length) {
            event.preventDefault();
            // منع معالج keydown الآخر (keyboard_navigation.js) من التنقل مرتين
            event.stopImmediatePropagation();

            // حدد فهرس الحقل الحالي
            let currentIdx = -1;
            ROW_FIELDS.forEach((sel, i) => {
                if ($target.is($row.find(sel))) currentIdx = i;
            });

            // انتقل لأول حقل موجود بعد الحالي (يتخطى الأعمدة المخفية مثل الربح/البيع في غير المشتريات)
            let moved = false;
            for (let i = currentIdx + 1; i < ROW_FIELDS.length; i++) {
                const $next = $row.find(ROW_FIELDS[i]);
                if ($next.length) { $next.focus(); $next.select(); moved = true; break; }
            }
            if (!moved) {
                // لا يوجد حقل تالٍ → ارجع لحقل البحث لإضافة صنف جديد
                $('#itemSearchInput').focus().select();
            }
            return;
        }

        // تنقل عادي في باقي الحقول
        event.preventDefault();
        let $next = $target.closest('td').next().find('input, select');
        if ($next.length) {
            $next.focus();
        }
    });
}
