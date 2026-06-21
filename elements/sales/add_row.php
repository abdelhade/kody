<div class="row">
      <div class="table-responsive">
        <table class="table table-condensed table-bordered" id="searchTable">
          <tbody>
            <tr>
              <td class="col-1">
                <div class="tool">
                  <a id="addNewElement" class="btn btn-sm hadi-white-flash" style="background: var(--neutral-50); color: var(--primary-dark); border: 1px solid var(--primary-light);" href="add_item.php" target="_blank">+</a>
                  <div class="tooltext">اضافه صنف جديد</div>
                </div>
              </td>
              <td id="itmTd" style="position:relative;">
                <div style="display:flex; gap:4px;">
                  <input type="text"
                         id="itemSearchInput"
                         class="form-control frst"
                         placeholder="ابحث بالاسم..."
                         autocomplete="off"
                         style="flex:1">
                  <input type="text"
                         id="barcodeSearchInput"
                         class="form-control scnd"
                         placeholder="باركود"
                         autocomplete="off"
                         style="width:130px;">
                </div>
                <input type="hidden" name="myitm[]" id="selectedItemId">
                <div id="searchResults" style="position:absolute; z-index:1000; background:white; border:1px solid #ddd; max-height:300px; overflow-y:auto; display:none; width:100%;"></div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

<!-- hidden staging fields - used by addNewRow() in sales.js -->
<input id="itmprice" type="number" hidden value="0.00" step="0.001">
<input id="itmqty"   type="number" hidden value="1.00">
<input id="itmdisc"  type="number" hidden value="0.00" step="0.001">
<input id="itmval"   type="number" hidden value="0.00" step="0.001">
<select id="inputUnitSelect" hidden><option value="">اختر وحدة</option></select>
<button type="button" id="addRow" hidden>إضافة</button>

<script>
(function() {
    const searchInput    = document.getElementById('itemSearchInput');
    const searchResults  = document.getElementById('searchResults');
    const selectedItemId = document.getElementById('selectedItemId');
    const priceInput     = document.getElementById('itmprice');

    let searchTimeout;

    // البحث المباشر
    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        clearTimeout(searchTimeout);

        if (query.length < 2) {
            searchResults.style.display = 'none';
            return;
        }

        searchTimeout = setTimeout(() => {
            searchResults.innerHTML = '<div style="padding:10px;text-align:center;">جاري البحث...</div>';
            searchResults.style.display = 'block';

            fetch(`ajax/load_items_lazy.php?search=${encodeURIComponent(query)}&limit=20`)
                .then(r => r.json())
                .then(data => {
                    if (data.success && data.items.length > 0) {
                        let html = '';
                        data.items.forEach(item => {
                            html += `<div class="search-result-item"
                                         data-id="${item.id}"
                                         data-name="${item.iname}"
                                         data-price="${item.price1}"
                                         data-barcode="${item.barcode}"
                                         style="padding:10px;cursor:pointer;border-bottom:1px solid #eee;">
                                        <strong>${item.iname}</strong>
                                        ${item.name2 ? ' // ' + item.name2 : ''}
                                        <span style="float:left;color:var(--primary-color);">${item.price1} ج.م</span>
                                     </div>`;
                        });
                        searchResults.innerHTML = html;

                        document.querySelectorAll('.search-result-item').forEach(el => {
                            el.addEventListener('click', function() {
                                selectItem({
                                    id:      this.dataset.id,
                                    name:    this.dataset.name,
                                    price:   this.dataset.price,
                                    barcode: this.dataset.barcode
                                });
                            });
                            el.addEventListener('mouseenter', function() { this.style.background = '#f0f9ff'; });
                            el.addEventListener('mouseleave', function() { this.style.background = 'white'; });
                        });
                    } else {
                        searchResults.innerHTML = '<div style="padding:10px;text-align:center;color:#999;">لا توجد نتائج</div>';
                    }
                })
                .catch(() => {
                    searchResults.innerHTML = '<div style="padding:10px;text-align:center;color:#ef4444;">خطأ في البحث</div>';
                });
        }, 300);
    });

    // اختيار صنف → إضافة فورية → فوكس على السعر في الصف الجديد
    function selectItem(item) {
        searchInput.value    = item.name;
        selectedItemId.value = item.id;
        priceInput.value     = item.price;
        searchResults.style.display = 'none';

        // أضف الصف وانتقل للسعر
        document.getElementById('addRow').click();
    }

    // إخفاء النتائج عند الضغط خارجها
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.style.display = 'none';
        }
    });

    searchInput.addEventListener('focus', function() {
        if (this.value && searchResults.children.length > 0) {
            searchResults.style.display = 'block';
        }
    });

    // البحث بالباركود
    const barcodeInput = document.getElementById('barcodeSearchInput');
    barcodeInput.addEventListener('keydown', function(e) {
        if (e.key !== 'Enter') return;
        e.preventDefault();
        const barcode = this.value.trim();
        if (!barcode) return;

        fetch(`ajax/load_items_lazy.php?search=${encodeURIComponent(barcode)}&limit=1&by=barcode`)
            .then(r => r.json())
            .then(data => {
                if (data.success && data.items.length > 0) {
                    const item = data.items[0];
                    selectItem({ id: item.id, name: item.iname, price: item.price1, barcode: item.barcode });
                    barcodeInput.value = '';
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
})();
</script>
