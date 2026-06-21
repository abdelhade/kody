$(document).ready(function() {
    
    // ==========================================
    // 1. Search & Add Items Modal
    // ==========================================
    const searchInput = $('#mobileSearchInput');
    const searchResults = $('#mobileSearchResults');
    let searchTimeout;

    // Focus input when modal opens
    $('#searchItemModal').on('shown.bs.modal', function () {
        searchInput.focus();
    });

    // Clear search when modal closes
    $('#searchItemModal').on('hidden.bs.modal', function () {
        searchInput.val('');
        searchResults.html(`
            <div class="text-center text-muted p-5 mt-4">
                <i class="fas fa-search fa-3x mb-3 opacity-25"></i>
                <p>ابدأ البحث لإضافة أصناف</p>
            </div>
        `);
    });

    searchInput.on('input', function() {
        const query = $(this).val().trim();
        clearTimeout(searchTimeout);

        if (query.length < 2) {
            searchResults.html(`
                <div class="text-center text-muted p-5 mt-4">
                    <i class="fas fa-search fa-3x mb-3 opacity-25"></i>
                    <p>أدخل حرفين على الأقل للبحث</p>
                </div>
            `);
            return;
        }

        searchTimeout = setTimeout(() => {
            searchResults.html('<div class="text-center p-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 text-muted">جاري البحث...</p></div>');

            $.ajax({
                url: `ajax/load_items_lazy.php?search=${encodeURIComponent(query)}&limit=20`,
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (data.success && data.items.length > 0) {
                        let html = '';
                        data.items.forEach(item => {
                            html += `
                                <div class="search-result-item d-flex justify-content-between align-items-center" 
                                     data-id="${item.id}" 
                                     data-name="${item.iname}" 
                                     data-price="${item.price1 || 0}" 
                                     data-barcode="${item.barcode || ''}">
                                    <div>
                                        <div class="item-title">${item.iname} ${item.name2 ? '<small class="text-muted">/ ' + item.name2 + '</small>' : ''}</div>
                                        <div class="item-barcode"><i class="fas fa-barcode"></i> ${item.barcode || 'بدون باركود'}</div>
                                    </div>
                                    <div class="text-end">
                                        <div class="item-price">${parseFloat(item.price1 || 0).toFixed(2)}</div>
                                        <button class="btn btn-sm btn-primary rounded-pill px-3 mt-1 add-this-item">إضافة <i class="fas fa-plus"></i></button>
                                    </div>
                                </div>
                            `;
                        });
                        searchResults.html(html);
                    } else {
                        searchResults.html(`
                            <div class="text-center text-muted p-5 mt-4">
                                <i class="fas fa-box-open fa-3x mb-3 opacity-25"></i>
                                <p>لا توجد نتائج مطابقة لـ "${query}"</p>
                            </div>
                        `);
                    }
                },
                error: function() {
                    searchResults.html('<div class="text-center text-danger p-4"><i class="fas fa-exclamation-triangle fa-2x mb-2"></i><br>حدث خطأ أثناء البحث</div>');
                }
            });
        }, 400);
    });

    // Handle Item Selection from Modal
    $(document).on('click', '.search-result-item, .add-this-item', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const parent = $(this).closest('.search-result-item');
        const item = {
            id: parent.data('id'),
            name: parent.data('name'),
            price: parseFloat(parent.data('price') || 0),
            barcode: parent.data('barcode')
        };

        addItemToCart(item);
        
        // Show brief success feedback
        const btn = parent.find('.add-this-item');
        const originalHtml = btn.html();
        btn.removeClass('btn-primary').addClass('btn-success').html('<i class="fas fa-check"></i> تم');
        setTimeout(() => {
            btn.removeClass('btn-success').addClass('btn-primary').html(originalHtml);
            $('#searchItemModal').modal('hide');
        }, 400);
    });

    // ==========================================
    // 2. Cart Management
    // ==========================================
    function addItemToCart(item) {
        $('#emptyCartMessage').hide();

        // Check if item already exists in cart
        let existingItem = $(`#itemData .item-card-order[data-itemid="${item.barcode}"]`);
        
        if (existingItem.length > 0) {
            // Increase quantity
            let qtyInput = existingItem.find('.quantityInput');
            let newQty = parseFloat(qtyInput.val()) + 1;
            qtyInput.val(newQty);
            updateItemRow(existingItem);
        } else {
            // Add new row
            const html = `
                <div class="card item-card-order shadow-sm border-start border-4 border-primary position-relative" data-itemid="${item.barcode}">
                    <div class="card-body p-2">
                        <input type="hidden" value='${item.id}' name="itmname[]">
                        <input type="hidden" class="barcode" value="${item.barcode}">
                        
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="text-truncate fw-bold mb-0" style="font-size: 0.9rem; max-width: 85%;">
                                ${item.name}
                            </h6>
                            <button type="button" class="btn btn-link text-danger p-0 delRow">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        
                        <div class="d-flex align-items-center justify-content-between mt-2">
                            <div class="d-flex align-items-center border rounded">
                                <button type="button" class="btn btn-light btn-sm px-3 border-end minusBtn"><i class="fas fa-minus"></i></button>
                                <input type="number" class="form-control form-control-sm text-center border-0 quantityInput nozero fw-bold px-1" 
                                       value="1" name="itmqty[]" min="1" step="0.1" style="width: 50px;">
                                <button type="button" class="btn btn-light btn-sm px-3 border-start plusBtn"><i class="fas fa-plus"></i></button>
                            </div>
                            <input type="hidden" name="u_val[]" value="1">
                            
                            <div class="text-end">
                                <input type="hidden" class="priceInput" value="${item.price.toFixed(2)}" name="itmprice[]">
                                <input type="hidden" name="itmdisc[]" value="0">
                                <input type="hidden" class="subtotal" readonly value="${item.price.toFixed(2)}" name="itmval[]">
                                <span class="text-primary fw-bold display-subtotal">${item.price.toFixed(2)} ج.م</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            $('#itemData').prepend(html);
        }
        
        calculateTotals();
    }

    function updateItemRow(row) {
        let qty = parseFloat(row.find('.quantityInput').val()) || 0;
        let price = parseFloat(row.find('.priceInput').val()) || 0;
        let disc = parseFloat(row.find('input[name="itmdisc[]"]').val()) || 0;
        
        let subtotal = (qty * price) - disc;
        if (subtotal < 0) subtotal = 0;
        
        row.find('.subtotal').val(subtotal.toFixed(2));
        row.find('.display-subtotal').text(subtotal.toFixed(2) + ' ج.م');
        
        calculateTotals();
    }

    function calculateTotals() {
        let total = 0;
        
        $('#itemData .subtotal').each(function() {
            total += parseFloat($(this).val()) || 0;
        });
        
        $('#total').val(total.toFixed(2));
        $('#total_display').text(total.toFixed(2) + ' ج.م');
        
        // Update Payment Modal Net
        let discount = parseFloat($('#modal_discount').val()) || 0;
        let net = total - discount;
        if(net < 0) net = 0;
        
        $('#modal_total').val(total.toFixed(2));
        $('#net_val').val(net.toFixed(2));
        $('#modal_net').val(net.toFixed(2));
        $('#modal_net_display').text(net.toFixed(2));
        $('#modal_paid_cash').val(net.toFixed(2)); // Default paid is full
        
        calcRemaining();

        if ($('.item-card-order').length === 0) {
            $('#emptyCartMessage').show();
        }
    }

    // ==========================================
    // 3. Cart Interactions
    // ==========================================
    $(document).on('click', '.plusBtn', function() {
        let input = $(this).siblings('.quantityInput');
        input.val(parseFloat(input.val()) + 1);
        updateItemRow($(this).closest('.item-card-order'));
    });

    $(document).on('click', '.minusBtn', function() {
        let input = $(this).siblings('.quantityInput');
        let val = parseFloat(input.val());
        if (val > 1) {
            input.val(val - 1);
            updateItemRow($(this).closest('.item-card-order'));
        }
    });

    $(document).on('input', '.quantityInput', function() {
        updateItemRow($(this).closest('.item-card-order'));
    });

    $(document).on('click', '.delRow', function() {
        $(this).closest('.item-card-order').remove();
        calculateTotals();
    });

    window.clearAllItems = function() {
        if (confirm('هل أنت متأكد من مسح جميع الأصناف؟')) {
            $('#itemData').empty();
            calculateTotals();
        }
    };

    // ==========================================
    // 4. Payment & Submit
    // ==========================================
    $('#modal_paid_cash').on('input', function() {
        calcRemaining();
    });

    window.calcRemaining = function() {
        let net = parseFloat($('#modal_net').val()) || 0;
        let paid = parseFloat($('#modal_paid_cash').val()) || 0;
        let remaining = net - paid;
        
        if (remaining < 0) remaining = 0;
        
        $('#modal_remaining').text(remaining.toFixed(2));
    };

    $('#confirmPaymentBtn').on('click', function() {
        if ($('.item-card-order').length === 0) {
            alert('الرجاء إضافة أصناف للفاتورة أولاً');
            $('#paymentModal').modal('hide');
            return;
        }

        // Disable button to prevent double submit
        $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> جاري الحفظ...');
        
        // Submit the form
        $('#posForm').submit();
    });

    // Barcode scanner integration (hardware scanner acts as keyboard)
    let barcodeString = '';
    let barcodeTimer = null;
    
    $(document).on('keypress', function(e) {
        // Only capture if not typing in an input
        if (e.target.tagName !== 'INPUT' && e.target.tagName !== 'TEXTAREA') {
            if (e.key === 'Enter') {
                if (barcodeString.length > 2) {
                    searchAndAddByBarcode(barcodeString);
                }
                barcodeString = '';
                clearTimeout(barcodeTimer);
            } else {
                barcodeString += e.key;
                clearTimeout(barcodeTimer);
                barcodeTimer = setTimeout(function() {
                    barcodeString = '';
                }, 300); // Reset if too slow (not a scanner)
            }
        }
    });

    function searchAndAddByBarcode(barcode) {
        $.ajax({
            url: `ajax/load_items_lazy.php?search=${encodeURIComponent(barcode)}&limit=1&by=barcode`,
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data.success && data.items.length > 0) {
                    const itemData = data.items[0];
                    const item = {
                        id: itemData.id,
                        name: itemData.iname,
                        price: parseFloat(itemData.price1 || 0),
                        barcode: itemData.barcode
                    };
                    addItemToCart(item);
                }
            }
        });
    }

    // Initialize totals on load
    calculateTotals();

    // ==========================================
    // 5. Camera Barcode Scanner Integration
    // ==========================================
    let html5QrcodeScanner = null;

    $('#barcodeScanBtn').on('click', function() {
        if (!$('#scanner-container').length) {
            $('.modal-body', '#searchItemModal').prepend('<div id="scanner-container" class="mb-3"></div>');
        }

        if (html5QrcodeScanner) {
            html5QrcodeScanner.clear();
            html5QrcodeScanner = null;
            $('#scanner-container').empty();
            $(this).removeClass('btn-danger').addClass('btn-light').html('<i class="fas fa-barcode"></i>');
            return;
        }

        $(this).removeClass('btn-light').addClass('btn-danger').html('<i class="fas fa-times"></i>');
        
        // Wait for library to load if needed
        if (typeof Html5QrcodeScanner === 'undefined') {
            alert('يتم تحميل قارئ الباركود، يرجى الانتظار');
            return;
        }

        html5QrcodeScanner = new Html5QrcodeScanner("scanner-container", { fps: 10, qrbox: {width: 250, height: 250} }, false);
        html5QrcodeScanner.render(function(decodedText, decodedResult) {
            $('#mobileSearchInput').val(decodedText);
            
            // Stop scanner
            html5QrcodeScanner.clear();
            html5QrcodeScanner = null;
            $('#scanner-container').empty();
            $('#barcodeScanBtn').removeClass('btn-danger').addClass('btn-light').html('<i class="fas fa-barcode"></i>');
            
            // Search logic
            searchAndAddByBarcode(decodedText);
        }, function(error) {
            // ignore
        });
    });

    // ==========================================
    // 6. Delivery Modal Logic
    // ==========================================
    window.openDeliveryModal = function () {
        $('#deliveryModal').modal('show');
    };

    let searchTimeoutDeliv;
    $('#customer_phone').on('input', function() {
        const phone = $(this).val().trim();
        if (phone.length < 3) {
            $('#customer_result').html('');
            $('#saveCustomerBtn').show();
            $('#confirmOrderBtn').hide();
            return;
        }
        
        clearTimeout(searchTimeoutDeliv);
        searchTimeoutDeliv = setTimeout(() => {
            $('#customer_result').html('<div class="text-center"><div class="spinner-border text-primary"></div></div>');
            $.ajax({
                url: 'ajax/customer_search.php',
                method: 'POST',
                data: { phone: phone },
                dataType: 'json',
                success: function(res) {
                    if (res.success && res.data) {
                        $('#customer_result').html(`
                            <div class="alert alert-success">
                                <strong>العميل:</strong> ${res.data.aname}<br>
                                <strong>العنوان:</strong> ${res.data.address || 'غير مسجل'}<br>
                                <strong>الرصيد:</strong> ${res.data.balance || 0}
                            </div>
                        `);
                        // Select customer
                        $('select[name="acc2_id"]').val(res.data.id);
                        $('#saveCustomerBtn').hide();
                        $('#confirmOrderBtn').show();
                    } else {
                        $('#customer_result').html(`
                            <div class="alert alert-warning">عميل غير موجود. سيتم إضافته كعميل جديد عند الحفظ.</div>
                            <input type="text" class="form-control mb-2" id="new_customer_name" placeholder="اسم العميل">
                            <input type="text" class="form-control" id="new_customer_address" placeholder="عنوان العميل">
                        `);
                        $('#saveCustomerBtn').show();
                        $('#confirmOrderBtn').hide();
                    }
                }
            });
        }, 500);
    });

    window.saveCustomerData = function() {
        const phone = $('#customer_phone').val().trim();
        const name = $('#new_customer_name').val()?.trim();
        const address = $('#new_customer_address').val()?.trim();
        
        if (!phone) {
            alert('الرجاء إدخال رقم الهاتف');
            return;
        }
        
        $('#saveCustomerBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> جاري الحفظ...');
        
        $.ajax({
            url: 'ajax/customer_save.php',
            method: 'POST',
            data: { phone: phone, name: name, address: address },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    // Update client select
                    if ($('select[name="acc2_id"] option[value="'+res.id+'"]').length === 0) {
                        $('select[name="acc2_id"]').append(new Option(res.name, res.id));
                    }
                    $('select[name="acc2_id"]').val(res.id);
                    $('#deliveryModal').modal('hide');
                } else {
                    alert(res.message || 'حدث خطأ');
                }
            },
            complete: function() {
                $('#saveCustomerBtn').prop('disabled', false).html('<i class="fas fa-save me-1"></i>حفظ');
            }
        });
    };

    window.confirmDeliveryOrder = function() {
        $('#deliveryModal').modal('hide');
    };

    // ==========================================
    // 7. Tables Modal Logic
    // ==========================================
    $('#tablesModal').on('show.bs.modal', function() {
        $('#tables-grid').html('<div class="col-12 text-center"><div class="spinner-border text-primary"></div><p>جاري تحميل الطاولات...</p></div>');
        $.ajax({
            url: 'ajax/get_tables.php',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    let html = '';
                    response.tables.forEach(function(table) {
                        const statusClass = table.table_case == 0 ? 'btn-outline-success' : 'btn-outline-danger';
                        const statusIcon = table.table_case == 0 ? '✓ متاح' : '⚠ مشغول';
                        html += `
                            <div class="col-4 col-md-3">
                                <button type="button" class="btn ${statusClass} w-100 py-3 shadow-sm" onclick="selectMobileTable(${table.id}, '${table.tname}', ${table.table_case}, ${table.order_id || 'null'})">
                                    <i class="fas fa-chair mb-2 fs-3"></i><br>
                                    <span class="fw-bold">${table.tname}</span><br>
                                    <small>${statusIcon}</small>
                                </button>
                            </div>
                        `;
                    });
                    $('#tables-grid').html(html);
                } else {
                    $('#tables-grid').html('<p class="text-danger w-100 text-center">خطأ في تحميل الطاولات</p>');
                }
            },
            error: function() {
                $('#tables-grid').html('<p class="text-danger w-100 text-center">خطأ في الاتصال بالخادم</p>');
            }
        });
    });

    window.selectMobileTable = function(tableId, tableName, tableCase, orderId) {
        $('#selected_table_id').val(tableId);
        $('#selected_table_name').val(tableName);
        $('#selected_table_display').html('<i class="fas fa-chair me-1"></i> ' + tableName);
        $('#age2').prop('checked', true); // Select table type
        $('#tablesModal').modal('hide');

        if (tableCase != 0 && orderId) {
            $('#selected_order_id').val(orderId);
            // Request existing order data if necessary
        } else {
            $('#selected_order_id').val('');
            $('#itemData').empty();
            calculateTotals();
        }
    };

});
