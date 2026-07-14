let selectedItems = [];
let allItems = [];
let searchTimeout;
let barcodeTimeout;
let isSubmitting = false;

// إعدادات الستايل لكل تاب
const TAB_STYLES = {
    '1': { // بيع
        name: 'بيع',
        headerBg: 'linear-gradient(135deg, var(--primary-navy) 0%, #2A3356 100%)',
        barcodeBorder: 'var(--primary-violet)',
        barcodeHeaderBg: 'var(--primary-violet)',
        payBtnBg: 'linear-gradient(135deg, #635BFF 0%, #4A41E1 100%)',
        payBtnShadow: '0 4px 12px rgba(99, 91, 255, 0.3)',
        payBtnHoverBg: 'linear-gradient(135deg, #4A41E1 0%, #3A31D1 100%)',
        orderItemBorder: 'rgba(99, 91, 255, 0.2)',
        footerBg: 'rgba(255,255,255,0.95)',
        totalColor: 'var(--primary-navy)',
        netColor: 'var(--primary-violet)',
        headerIcon: 'fa-shopping-cart',
        headerText: 'معلومات الطلب',
    },
    '2': { // حجز
        name: 'حجز',
        headerBg: 'linear-gradient(135deg, #1a6b3a 0%, #145c31 100%)',
        barcodeBorder: '#198754',
        barcodeHeaderBg: '#198754',
        payBtnBg: 'linear-gradient(135deg, #198754 0%, #145c31 100%)',
        payBtnShadow: '0 4px 12px rgba(25, 135, 84, 0.3)',
        payBtnHoverBg: 'linear-gradient(135deg, #145c31 0%, #0f4a27 100%)',
        orderItemBorder: 'rgba(25, 135, 84, 0.2)',
        footerBg: 'rgba(240,255,248,0.97)',
        totalColor: '#145c31',
        netColor: '#198754',
        headerIcon: 'fa-bookmark',
        headerText: 'معلومات الحجز',
    },
    '3': { // توصيل
        name: 'توصيل',
        headerBg: 'linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%)',
        barcodeBorder: '#0d6efd',
        barcodeHeaderBg: '#0d6efd',
        payBtnBg: 'linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%)',
        payBtnShadow: '0 4px 12px rgba(13, 110, 253, 0.3)',
        payBtnHoverBg: 'linear-gradient(135deg, #0a58ca 0%, #084298 100%)',
        orderItemBorder: 'rgba(13, 110, 253, 0.2)',
        footerBg: 'rgba(240,245,255,0.97)',
        totalColor: '#0a58ca',
        netColor: '#0d6efd',
        headerIcon: 'fa-truck',
        headerText: 'معلومات التوصيل',
    },
    '4': { // مردود
        name: 'مردود',
        headerBg: 'linear-gradient(135deg, #b91c1c 0%, #991b1b 100%)',
        barcodeBorder: '#dc3545',
        barcodeHeaderBg: '#dc3545',
        payBtnBg: 'linear-gradient(135deg, #dc3545 0%, #b91c1c 100%)',
        payBtnShadow: '0 4px 12px rgba(220, 53, 69, 0.3)',
        payBtnHoverBg: 'linear-gradient(135deg, #b91c1c 0%, #991b1b 100%)',
        orderItemBorder: 'rgba(220, 53, 69, 0.2)',
        footerBg: 'rgba(255,242,242,0.97)',
        totalColor: '#991b1b',
        netColor: '#dc3545',
        headerIcon: 'fa-undo',
        headerText: 'معلومات المردود',
    }
};

function applyTabStyle(tabVal) {
    const style = TAB_STYLES[tabVal] || TAB_STYLES['1'];

    // 1. تغيير header الطلب (اللون والأيقونة والنص)
    const orderHeader = document.querySelector('.order-header');
    if (orderHeader) {
        orderHeader.style.background = style.headerBg;
        const icon = orderHeader.querySelector('i');
        if (icon) icon.className = `fas ${style.headerIcon} me-2`;
        const h6 = orderHeader.querySelector('h6');
        if (h6) {
            // النص الموجود بعد الأيقونة مباشرة (TextNode)
            const textNodes = Array.from(h6.childNodes).filter(n => n.nodeType === 3);
            if (textNodes.length > 0) {
                textNodes[0].textContent = style.headerText;
            } else {
                // fallback: استبدال النص بالكامل مع الحفاظ على الأيقونة
                h6.innerHTML = `<i class="fas ${style.headerIcon} me-2"></i>${style.headerText}`;
            }
        }
    }

    // 2. تغيير إطار ولون حقل الباركود
    const barcodeWrapper = document.querySelector('.barcode-wrapper');
    if (barcodeWrapper) {
        barcodeWrapper.style.borderColor = style.barcodeBorder;
        const barcodeSpan = barcodeWrapper.querySelector('.barcode-header-span');
        if (barcodeSpan) barcodeSpan.style.backgroundColor = style.barcodeHeaderBg;
    }

    // 3. تغيير زر الدفع - استخدام inline style لتجنب مشاكل CSS specificity
    const payBtn = document.getElementById('payBtn');
    if (payBtn) {
        payBtn.style.background = style.payBtnBg;
        payBtn.style.boxShadow = style.payBtnShadow;
        payBtn.style.border = 'none';
        payBtn.style.color = 'white';
    }

    // 4. تغيير ألوان إجمالي وصافي
    const totalDisplay = document.getElementById('total_display');
    const netDisplay = document.getElementById('net_display');
    if (totalDisplay) totalDisplay.style.color = style.totalColor;
    if (netDisplay) netDisplay.style.color = style.netColor;

    // 5. تغيير خلفية footer
    const orderFooter = document.querySelector('.order-footer');
    if (orderFooter) orderFooter.style.background = style.footerBg;

    // 6. تغيير لون border الأصناف المختارة
    document.querySelectorAll('.order-item').forEach(el => {
        el.style.borderColor = style.orderItemBorder;
    });

    // 7. حفظ الستايل الحالي ليُطبَّق على الأصناف الجديدة لاحقاً
    window._currentTabStyle = style;
}

// بحث بالباركود
function searchByBarcode() {
    const barcode = document.getElementById('barcodeSearch').value.trim();
    
    if (barcode === '') {
        return;
    }
    
    clearTimeout(barcodeTimeout);
    barcodeTimeout = setTimeout(function() {
        const storeId = document.querySelector('input[name="store_id"]')?.value || 0;
        
        $.ajax({
            url: 'ajax/search_item.php',
            type: 'POST',
            data: { barcode: barcode, store_id: storeId },
            dataType: 'json',
            success: function(data) {
                console.log('Barcode response:', data);
                if (data.success && data.item) {
                    const item = data.item;
                    addItemToOrder(item.id, item.name, item.price, item.balance);
                    document.getElementById('barcodeSearch').value = '';
                    document.getElementById('barcodeSearch').focus();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'غير موجود',
                        text: data.message || 'الصنف غير موجود',
                        timer: 1500,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                    document.getElementById('barcodeSearch').value = '';
                }
            },
            error: function(xhr, status, error) {
                console.error('Barcode Search Error:', error);
                console.error('Response:', xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: 'حدث خطأ في البحث',
                    timer: 1500,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
                document.getElementById('barcodeSearch').value = '';
            }
        });
    }, 300);
}

// تحميل كل الأصناف
function loadAllItems() {
    document.getElementById('itemsGrid').innerHTML = `
        <div class="col-12 text-center py-3">
            <div class="spinner-border spinner-border-sm" style="color: var(--primary-navy);" role="status">
                <span class="visually-hidden">جاري التحميل...</span>
            </div>
        </div>
    `;
    document.getElementById('itemsContainer').classList.add('show');
    document.getElementById('noItemsMessage').style.display = 'none';

    const storeId = document.querySelector('input[name="store_id"]')?.value || 0;
    
    $.ajax({
        url: 'ajax/search_items.php',
        type: 'GET',
        data: { search: '', store_id: storeId },
        dataType: 'json',
        success: function(data) {
            if (data.success && data.items.length > 0) {
                allItems = data.items;
                displayItems(data.items);
            } else {
                document.getElementById('itemsGrid').innerHTML = `
                    <div class="col-12 text-center py-5">
                        <i class="fas fa-box-open fa-3x mb-3" style="color: var(--soft-gray);"></i>
                        <h5>لا توجد أصناف</h5>
                    </div>
                `;
            }
        },
        error: function() {
            document.getElementById('itemsGrid').innerHTML = `
                <div class="col-12 text-center py-5 text-danger">
                    <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                    <h5>حدث خطأ في تحميل الأصناف</h5>
                </div>
            `;
        }
    });
}

// بحث عن الأصناف
function searchItems() {
    const searchTerm = document.getElementById('searchItems').value.trim().toLowerCase();
    
    // الحصول على معرف المجموعة النشطة
    const activeCategoryCard = document.querySelector('.category-card.active');
    const categoryId = activeCategoryCard ? activeCategoryCard.getAttribute('data-category') : null;
    
    if (searchTerm === '') {
        if (categoryId) {
            loadCategoryItems(categoryId);
        } else {
            loadAllItems();
        }
        return;
    }
    
    if (searchTerm.length < 1) {
        return;
    }
    
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(function() {
        document.getElementById('itemsGrid').innerHTML = `
            <div class="col-12 text-center py-3">
                <div class="spinner-border spinner-border-sm" style="color: var(--primary-navy);" role="status">
                    <span class="visually-hidden">جاري البحث...</span>
                </div>
            </div>
        `;
        
        document.getElementById('itemsContainer').classList.add('show');
        document.getElementById('noItemsMessage').style.display = 'none';
        
        const storeId = document.querySelector('input[name="store_id"]')?.value || 0;
        
        $.ajax({
            url: 'ajax/search_items.php',
            type: 'GET',
            data: { search: searchTerm, store_id: storeId, category_id: categoryId || 0 },
            dataType: 'json',
            success: function(data) {
                if (data.success && data.items.length > 0) {
                    allItems = data.items;
                    displayItems(data.items);
                } else {
                    document.getElementById('itemsGrid').innerHTML = `
                        <div class="col-12 text-center py-5">
                            <i class="fas fa-search fa-3x mb-3" style="color: var(--soft-gray);"></i>
                            <h5>لا توجد نتائج للبحث</h5>
                            <p class="text-muted">جرب كلمة بحث أخرى</p>
                        </div>
                    `;
                }
            },
            error: function(xhr, status, error) {
                console.error('Search Error:', error);
                console.error('Status:', status);
                console.error('Response Text:', xhr.responseText);
                console.error('Response Status:', xhr.status);
                
                let errorMsg = 'حدث خطأ في البحث';
                if (xhr.responseText) {
                    errorMsg += '<br><small class="text-muted">' + xhr.responseText.substring(0, 200) + '</small>';
                }
                
                document.getElementById('itemsGrid').innerHTML = `
                    <div class="col-12 text-center py-5 text-danger">
                        <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                        <h5>${errorMsg}</h5>
                    </div>
                `;
            }
        });
    }, 300);
}

// تحميل أصناف المجموعة
function loadCategoryItems(categoryId) {
    document.querySelectorAll('.category-card').forEach(card => {
        card.classList.remove('active');
    });
    
    document.querySelector(`[data-category="${categoryId}"]`).classList.add('active');
    
    // تفريغ مربع البحث لتفادي اللبس عند تغيير المجموعة
    const searchInput = document.getElementById('searchItems');
    if (searchInput) {
        searchInput.value = '';
    }
    
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
    
    const storeId = document.querySelector('input[name="store_id"]')?.value || 0;
    
    fetch(`ajax/get_category_items.php?category_id=${categoryId}&store_id=${storeId}`)
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

// عرض الأصناف
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
            <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-2">
                <div class="item-card" onclick="addItemToOrder(${item.id}, \`${item.name}\`, ${item.price}, ${item.balance})">
                    <div class="item-image">
                        <i class="fas fa-tshirt" style="color: var(--soft-gray);"></i>
                    </div>
                    <div class="item-details">
                        <div class="item-name">${item.name}</div>
                        <div class="item-price">${parseFloat(item.price).toFixed(2)} ج.م</div>
                        <div class="item-balance text-muted" style="font-size: 0.85rem; font-weight: bold; color: var(--primary-violet) !important;">الرصيد: ${item.balance}</div>
                    </div>
                </div>
            </div>
        `;
    });
    
    itemsGrid.innerHTML = html;
}

// إضافة صنف للطلب
function addItemToOrder(itemId, itemName, itemPrice, itemBalance = 0) {
    const existingItemIndex = selectedItems.findIndex(item => item.id === itemId);
    
    if (existingItemIndex !== -1) {
        selectedItems[existingItemIndex].quantity += 1;
    } else {
        selectedItems.push({
            id: itemId,
            name: itemName,
            price: parseFloat(itemPrice),
            quantity: 1,
            balance: parseFloat(itemBalance) || 0
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

// تحديث عرض الطلب
function updateOrderDisplay() {
    const itemData = document.getElementById('itemData');
    const itemCount = document.getElementById('itemCount');
    
    if (selectedItems.length === 0) {
        itemData.innerHTML = '<p class="text-muted text-center" style="font-size: 0.8rem;">لا توجد أصناف</p>';
        itemCount.textContent = '0';
        updateTotals();
        return;
    }
    
    const currentStyle = window._currentTabStyle || TAB_STYLES['1'];

    let html = '';
    selectedItems.forEach((item, index) => {
        const subtotal = item.quantity * item.price;
        html += `
            <div class="order-item p-2 mb-2" style="border-color: ${currentStyle.orderItemBorder};">
                <div class="flex-grow-1">
                    <div class="fw-bold text-dark" style="font-size: 1.8rem;">${item.name}</div>
                    <div class="d-flex align-items-center gap-2 mt-2">
                        <div class="fw-bold" style="font-size: 1.5rem; color: var(--primary-navy);">
                            ${item.price.toFixed(2)} <span class="text-muted mx-1">×</span> <span style="font-size: 1.5rem; color: var(--primary-violet);">${item.quantity}</span>
                        </div>
                        <span class="badge bg-light text-dark border ms-2" style="font-size: 1.5rem; padding: 0.35rem 0.6rem;">الرصيد: ${item.balance}</span>
                    </div>
                </div>
                <div class="text-end d-flex flex-column align-items-end justify-content-between h-100">
                    <div class="fw-bold mb-2" style="color: var(--primary-violet); font-size: 1.4rem;">${subtotal.toFixed(2)}</div>
                    <div class="btn-group btn-group-sm mt-1">
                        <button type="button" class="btn btn-outline-secondary" onclick="decreaseQuantity(${index})" style="padding: 0.35rem 0.8rem; font-size: 1.1rem;">
                            <i class="fas fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="increaseQuantity(${index})" style="padding: 0.35rem 0.8rem; font-size: 1.1rem;">
                            <i class="fas fa-plus"></i>
                        </button>
                        <button type="button" class="btn btn-outline-danger" onclick="removeItem(${index})" style="padding: 0.35rem 0.8rem; font-size: 1.1rem;">
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
    document.getElementById('itemData').innerHTML = '<p class="text-muted text-center" style="font-size: 0.8rem;">لا توجد أصناف</p>';
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
        document.getElementById('modal_change').className = 'form-control form-control-lg text-center fw-bold bg-danger text-white';
    } else {
        document.getElementById('modal_change').className = 'form-control form-control-lg text-center fw-bold bg-success text-white';
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
        Swal.fire({
            icon: 'warning',
            title: 'تنبيه',
            text: 'يجب إضافة صنف واحد على الأقل للطلب',
            confirmButtonText: 'حسناً'
        });
        return false;
    }
    
    const clientId = document.querySelector('select[name="acc2_id"]')?.value;
    if (!clientId) {
        Swal.fire({
            icon: 'warning',
            title: 'تنبيه',
            text: 'يجب اختيار العميل أو إضافة عميل جديد أولاً',
            confirmButtonText: 'حسناً'
        });
        $('#paymentModal').modal('hide');
        return false;
    }
    
    if (isSubmitting) {
        return false;
    }
    isSubmitting = true;
    
    // Disable buttons and show spinner
    const buttons = document.querySelectorAll('#paymentModal .btn-navy, #paymentModal .btn-violet');
    buttons.forEach(btn => {
        btn.disabled = true;
        if (!btn.dataset.originalText) {
            btn.dataset.originalText = btn.innerHTML;
        }
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>جاري الحفظ...';
    });
    
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
    
    // إضافة الصندوق من المودال
    let fundInput = form.querySelector('input[name="fund_id"]');
    if (!fundInput) {
        fundInput = document.createElement('input');
        fundInput.type = 'hidden';
        fundInput.name = 'fund_id';
        form.appendChild(fundInput);
    }
    const fundSelect = document.querySelector('#paymentModal select[name="fund_id"]');
    fundInput.value = fundSelect ? fundSelect.value : '';
    
    $('#paymentModal').modal('hide');
    
    setTimeout(function() {
        HTMLFormElement.prototype.submit.call(form);
    }, 500);
    
    return true;
}

// تبديل وضع الشاشة الكاملة
function toggleFullscreen() {
    if (!document.fullscreenElement && !document.webkitFullscreenElement && 
        !document.mozFullScreenElement && !document.msFullscreenElement) {
        const elem = document.documentElement;
        if (elem.requestFullscreen) {
            elem.requestFullscreen();
        } else if (elem.webkitRequestFullscreen) {
            elem.webkitRequestFullscreen();
        } else if (elem.mozRequestFullScreen) {
            elem.mozRequestFullScreen();
        } else if (elem.msRequestFullscreen) {
            elem.msRequestFullscreen();
        }
    } else {
        if (document.exitFullscreen) {
            document.exitFullscreen();
        } else if (document.webkitExitFullscreen) {
            document.webkitExitFullscreen();
        } else if (document.mozCancelFullScreen) {
            document.mozCancelFullScreen();
        } else if (document.msExitFullscreen) {
            document.msExitFullscreen();
        }
    }
}

function updateFullscreenIcon() {
    const icon = document.getElementById('fullscreenIcon');
    if (document.fullscreenElement || document.webkitFullscreenElement || 
        document.mozFullScreenElement || document.msFullscreenElement) {
        icon.className = 'fas fa-compress';
    } else {
        icon.className = 'fas fa-expand';
    }
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // تفعيل select2 للموظفين وغيرهم
    if (typeof $.fn.select2 !== 'undefined') {
        $('.select2-select').select2({
            theme: 'bootstrap4',
            width: '100%',
            dir: 'rtl'
        });

        // تفعيل select2 للعملاء مع دعم البحث بالاسم أو رقم الهاتف
        function clientMatcher(params, data) {
            if (!params.term || params.term.trim() === '') {
                return data;
            }
            if (!data) {
                return null;
            }
            const term = params.term.trim().toLowerCase();
            const name = (data.text || '').toLowerCase();
            
            let phone = '';
            if (data.element) {
                phone = ($(data.element).data('phone') || '').toString().toLowerCase();
            }

            if (name.indexOf(term) > -1 || phone.indexOf(term) > -1) {
                return data;
            }
            
            // دعم المجموعات (optgroups) في حال وجودها
            if (data.children && data.children.length > 0) {
                const matchedChildren = [];
                for (let i = 0; i < data.children.length; i++) {
                    const matchedChild = clientMatcher(params, data.children[i]);
                    if (matchedChild !== null) {
                        matchedChildren.push(matchedChild);
                    }
                }
                if (matchedChildren.length > 0) {
                    const clonedData = $.extend({}, data, true);
                    clonedData.children = matchedChildren;
                    return clonedData;
                }
            }
            return null;
        }

        $('#clientSelect').select2({
            theme: 'bootstrap4',
            width: '100%',
            dir: 'rtl',
            matcher: clientMatcher,
            placeholder: 'ابحث بالاسم أو الهاتف...',
            allowClear: false,
            language: {
                noResults: function() { return 'لا يوجد عملاء'; },
                searching: function() { return 'جاري البحث...'; }
            }
        });
    }

    // تحميل كل الأصناف عند فتح الصفحة
    loadAllItems();
    
    // focus على حقل الباركود
    document.getElementById('barcodeSearch')?.focus();
    
    // بحث بالباركود
    document.getElementById('barcodeSearch')?.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            searchByBarcode();
        }
    });
    
    // بحث عن الأصناف
    document.getElementById('searchItems')?.addEventListener('input', function(e) {
        searchItems();
    });
    
    document.getElementById('searchItems')?.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            searchItems();
        }
    });
    
    // مودال الدفع
    document.getElementById('modal_discperc')?.addEventListener('input', updateDiscount);
    document.getElementById('modal_discount')?.addEventListener('input', updateDiscount);
    document.getElementById('modal_paid')?.addEventListener('input', updateChange);
    
    $('#paymentModal').on('show.bs.modal', function() {
        updateTotals();
    });

    // تحديد كل المحتوى عند الضغط على أي input داخل مودال الدفع
    $('#paymentModal').on('shown.bs.modal', function() {
        $('#paymentModal input[type="number"], #paymentModal input[type="text"]').off('click.selectAll focus.selectAll').on('click.selectAll focus.selectAll', function() {
            const el = this;
            setTimeout(function() { el.select(); }, 0);
        });
    });
    
    // تحديث أيقونة الشاشة الكاملة
    document.addEventListener('fullscreenchange', updateFullscreenIcon);
    document.addEventListener('webkitfullscreenchange', updateFullscreenIcon);
    document.addEventListener('mozfullscreenchange', updateFullscreenIcon);
    document.addEventListener('MSFullscreenChange', updateFullscreenIcon);

    // تغيير ستايل الصفحة كاملاً حسب نوع التاب المختار
    $('input[name="age"]').on('change', function() {
        applyTabStyle($(this).val());
    });

    // تطبيق الستايل الافتراضي عند تحميل الصفحة (بيع)
    applyTabStyle($('input[name="age"]:checked').val() || '1');

    // حفظ العميل الجديد عبر AJAX
    $('#saveAjaxClientBtn').on('click', function() {
        const name = $('#ajax_client_name').val().trim();
        const phone = $('#ajax_client_phone').val().trim();
        const address = $('#ajax_client_address').val().trim();

        if (name === '') {
            Swal.fire({
                icon: 'warning',
                title: 'تنبيه',
                text: 'يرجى إدخال اسم العميل',
                confirmButtonText: 'حسناً'
            });
            return;
        }

        $.ajax({
            url: 'ajax/add_client_ajax.php',
            type: 'POST',
            data: {
                name: name,
                phone: phone,
                address: address
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'تم بنجاح!',
                        text: response.message || 'تم إضافة العميل الجديد بنجاح',
                        timer: 3000,
                        showConfirmButton: false
                    });

                    // إضافة العميل الجديد لقائمة الاختيار وتحديده مع بيانات الهاتف للبحث
                    const displayPhone = response.phone ? response.phone : '';
                    const displayText = displayPhone ? response.name + ' - ' + displayPhone : response.name;
                    const $newOption = $('<option>', {
                        value: response.id,
                        text: displayText,
                        selected: true
                    }).attr('data-phone', displayPhone);
                    
                    $('#clientSelect').append($newOption).trigger('change');

                    // إغلاق المودال وتصفير الفورم
                    $('#addClientModal').modal('hide');
                    $('#ajaxAddClientForm')[0].reset();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: response.message || 'حدث خطأ أثناء إضافة العميل',
                        confirmButtonText: 'حسناً'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: 'حدث خطأ في الاتصال بالخادم، يرجى المحاولة مرة أخرى.',
                    confirmButtonText: 'حسناً'
                });
            }
        });
    });
});
