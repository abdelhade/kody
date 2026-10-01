function applyClientPriceList(selectEl) {
    if (!selectEl || !selectEl.options || selectEl.selectedIndex < 0) return;
    var opt = selectEl.options[selectEl.selectedIndex];
    if (!opt) return;
    var list = parseInt(opt.getAttribute('data-price-list'), 10);
    if (!list) return;
    var priceSel = document.getElementById('invoicePriceList');
    if (!priceSel || String(priceSel.value) === String(list)) return;
    priceSel.value = String(list);
    if (window.jQuery) {
        window.jQuery(priceSel).trigger('change');
    }
}

if (window.jQuery) {
    window.jQuery(document).on('change', 'select[name="acc2_id"]', function() {
        if (this.id === 'mySelectEmp') return;
        applyClientPriceList(this);
    });
    window.jQuery(function() {
        if (/[?&](e|edit|edit_id)=/.test(location.search)) return;
        var sel = document.querySelector('select[name="acc2_id"]');
        if (sel && sel.id !== 'mySelectEmp') {
            applyClientPriceList(sel);
        }
    });
}

function selectedInvoicePriceList() {
    var el = document.getElementById('invoicePriceList');
    return el ? (parseInt(el.value, 10) || 1) : 1;
}

function priceFromValues(p1, p2, p3, listId) {
    p1 = parseFloat(p1) || 0;
    p2 = parseFloat(p2) || 0;
    p3 = parseFloat(p3) || 0;
    listId = parseInt(listId, 10) || 1;
    if (listId === 2) return p2 > 0 ? p2 : p1;
    if (listId >= 3) return p3 > 0 ? p3 : p1;
    return p1;
}

function priceFromItem(item) {
    if (!item) return 0;
    var p3 = item.price3;
    if (!(parseFloat(p3) > 0) && item.market_price) {
        p3 = item.market_price;
    }
    var base = item.price1 != null ? item.price1 : item.price;
    var picked = priceFromValues(base, item.price2, p3, selectedInvoicePriceList());
    if (picked > 0) return picked;
    return parseFloat(item.price) || 0;
}
