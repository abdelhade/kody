<div class="row">
      <div class="table-responsive">
        <table class="table table-condensed table-bordered" id="searchTable">
          <tbody>
            <tr>
              <td class="col-1">
                <div class="tool">
                  <button type="button" id="addNewElement" class="btn btn-sm hadi-white-flash" style="background: var(--neutral-50); color: var(--primary-dark); border: 1px solid var(--primary-light);" data-toggle="modal" data-target="#addItemModal">+</button>
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

<!-- Modal إضافة صنف جديد -->
<div class="modal fade" id="addItemModal" tabindex="-1" role="dialog" aria-labelledby="addItemModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="addItemModalLabel"><i class="fas fa-plus-circle ml-2"></i> إضافة صنف جديد</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="إغلاق">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body p-3">
        <div id="addItemMsg"></div>
        <form id="addItemModalForm" enctype="multipart/form-data">
          <div class="row">
            <div class="col-md-3 form-group">
              <label class="small text-muted">الباركود <span class="text-danger">*</span></label>
              <input type="text" name="barcode" id="modalBarcode" class="form-control form-control-sm" required placeholder="باركود">
            </div>
            <div class="col-md-5 form-group">
              <label class="small text-muted">اسم الصنف <span class="text-danger">*</span></label>
              <input type="text" name="iname" id="modalIname" class="form-control form-control-sm" required placeholder="اسم الصنف">
            </div>
            <div class="col-md-4 form-group">
              <label class="small text-muted">الاسم الثاني</label>
              <input type="text" name="name2" class="form-control form-control-sm" placeholder="اختياري">
            </div>
          </div>
          <div class="row">
            <div class="col-md-4 form-group">
              <label class="small text-muted">المجموعة</label>
              <select name="group1" class="form-control form-control-sm">
                <option value="">— اختر —</option>
                <?php
                $resgroup1m = $conn->query('SELECT * FROM item_group WHERE isdeleted = 0');
                while ($rowgroup1m = $resgroup1m->fetch_assoc()) {
                  echo '<option value="' . (int)$rowgroup1m['id'] . '">' . htmlspecialchars($rowgroup1m['gname'], ENT_QUOTES, 'UTF-8') . '</option>';
                }
                ?>
              </select>
            </div>
            <div class="col-md-4 form-group">
              <label class="small text-muted">التصنيف</label>
              <select name="group2" class="form-control form-control-sm">
                <option value="">— اختر —</option>
                <?php
                $resgroup2m = $conn->query('SELECT * FROM item_group2 WHERE isdeleted = 0');
                while ($rowgroup2m = $resgroup2m->fetch_assoc()) {
                  echo '<option value="' . (int)$rowgroup2m['id'] . '">' . htmlspecialchars($rowgroup2m['gname'], ENT_QUOTES, 'UTF-8') . '</option>';
                }
                ?>
              </select>
            </div>
            <div class="col-md-4 form-group">
              <label class="small text-muted">ملاحظات</label>
              <input type="text" name="info" class="form-control form-control-sm" placeholder="اختياري">
            </div>
          </div>
          <!-- وحدة الأساسية والأسعار -->
          <hr class="mt-1 mb-2">
          <div class="row align-items-end">
            <div class="col-md-2 form-group">
              <label class="small text-muted">الوحدة <span class="text-danger">*</span></label>
              <select name="unit_id[]" class="form-control form-control-sm">
                <?php
                $resunitm = $conn->query('SELECT * FROM myunits');
                while ($rowunitm = $resunitm->fetch_assoc()) {
                  echo '<option value="' . (int)$rowunitm['id'] . '">' . htmlspecialchars($rowunitm['uname'], ENT_QUOTES, 'UTF-8') . '</option>';
                }
                ?>
              </select>
            </div>
            <input type="hidden" name="u_val[]" value="1">
            <input type="hidden" name="code" value="__auto__">
            <div class="col-md-2 form-group">
              <label class="small text-muted">التكلفة</label>
              <input type="number" name="cost_price[]" value="0" step="0.001" min="0" class="form-control form-control-sm">
            </div>
            <div class="col-md-2 form-group">
              <label class="small text-muted">سعر البيع</label>
              <input type="number" name="price1[]" value="0" step="0.001" min="0" class="form-control form-control-sm">
            </div>
            <div class="col-md-2 form-group">
              <label class="small text-muted">جملة</label>
              <input type="number" name="price2[]" value="0" step="0.001" min="0" class="form-control form-control-sm">
            </div>
            <div class="col-md-2 form-group">
              <label class="small text-muted">السوق</label>
              <input type="number" name="market_price[]" value="0" step="0.001" min="0" class="form-control form-control-sm">
            </div>
            <div class="col-md-2 form-group">
              <label class="small text-muted">الباركود الوحدة</label>
              <input type="text" name="unit_barcode[]" id="modalUnitBarcode" class="form-control form-control-sm">
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">إلغاء</button>
        <button type="button" class="btn btn-primary btn-sm" id="saveItemModalBtn">
          <i class="fas fa-save ml-1"></i> حفظ الصنف
        </button>
      </div>
    </div>
  </div>
</div>

<script>
(function() {
  // مزامنة باركود الوحدة مع باركود الصنف
  document.getElementById('modalBarcode').addEventListener('input', function() {
    document.getElementById('modalUnitBarcode').value = this.value;
  });

  // عند فتح المودال: احضر باركود تلقائي
  $('#addItemModal').on('show.bs.modal', function() {
    document.getElementById('addItemMsg').innerHTML = '';
    // احضر أعلى كود وباركود
    fetch('ajax/load_items_lazy.php?action=next_barcode')
      .then(r => r.json())
      .then(data => {
        if (data.barcode) {
          document.getElementById('modalBarcode').value = data.barcode;
          document.getElementById('modalUnitBarcode').value = data.barcode;
        }
      }).catch(() => {});
  });

  // حفظ الصنف
  document.getElementById('saveItemModalBtn').addEventListener('click', function() {
    const $btn = $(this);
    const form = document.getElementById('addItemModalForm');
    const iname = document.getElementById('modalIname').value.trim();
    const barcode = document.getElementById('modalBarcode').value.trim();

    if (!iname || !barcode) {
      document.getElementById('addItemMsg').innerHTML = '<div class="alert alert-danger py-1 mb-2">الاسم والباركود مطلوبان</div>';
      return;
    }

    $btn.prop('disabled', true).text('جاري الحفظ...');
    const formData = new FormData(form);
    formData.set('code', '__auto__');

    fetch('ajax/modal_add_item.php', {
      method: 'POST',
      body: formData
    })
    .then(r => r.json())
    .then(res => {
      if (res.success) {
        document.getElementById('addItemMsg').innerHTML = '<div class="alert alert-success py-1 mb-2">✓ تم حفظ الصنف: <strong>' + res.iname + '</strong></div>';
        form.reset();
        // اختر الصنف الجديد تلقائياً في حقل البحث
        setTimeout(() => {
          document.getElementById('itemSearchInput').value = res.iname;
          document.getElementById('selectedItemId').value = res.id;
          document.getElementById('itmprice').value = res.price || 0;
          $('#addItemModal').modal('hide');
        }, 800);
      } else {
        document.getElementById('addItemMsg').innerHTML = '<div class="alert alert-danger py-1 mb-2">' + (res.error || 'حدث خطأ') + '</div>';
      }
    })
    .catch(() => {
      document.getElementById('addItemMsg').innerHTML = '<div class="alert alert-danger py-1 mb-2">خطأ في الاتصال</div>';
    })
    .finally(() => {
      $btn.prop('disabled', false).html('<i class="fas fa-save ml-1"></i> حفظ الصنف');
    });
  });
})();
</script>
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

    // البحث بالباركود مع التحقق من صحة الباركود
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
                    
                    // التحقق من الصنف المحدد في حقل البحث
                    const selectedItemId = document.getElementById('selectedItemId').value;
                    
                    if (selectedItemId && selectedItemId != item.id) {
                        // الباركود لا يخص الصنف المحدد
                        barcodeInput.style.borderColor = '#ef4444';
                        barcodeInput.style.backgroundColor = '#fee';
                        
                        // عرض رسالة تحذير
                        const msg = document.createElement('div');
                        msg.style.cssText = 'position:absolute; top:100%; left:0; background:#ef4444; color:white; padding:5px 10px; border-radius:4px; font-size:12px; z-index:1001; margin-top:2px;';
                        msg.textContent = 'الباركود يخص صنف آخر: ' + item.iname;
                        barcodeInput.parentElement.style.position = 'relative';
                        barcodeInput.parentElement.appendChild(msg);
                        
                        setTimeout(() => {
                            barcodeInput.style.borderColor = '';
                            barcodeInput.style.backgroundColor = '';
                            msg.remove();
                        }, 3000);
                        
                        barcodeInput.value = '';
                        return;
                    }
                    
                    // الباركود صحيح - اضافة الصنف
                    selectItem({ id: item.id, name: item.iname, price: item.price1, barcode: item.barcode });
                    barcodeInput.value = '';
                } else {
                    // الباركود غير موجود
                    barcodeInput.style.borderColor = '#ef4444';
                    barcodeInput.style.backgroundColor = '#fee';
                    setTimeout(() => {
                        barcodeInput.style.borderColor = '';
                        barcodeInput.style.backgroundColor = '';
                    }, 1000);
                }
            })
            .catch(() => {
                barcodeInput.style.borderColor = '#ef4444';
                setTimeout(() => barcodeInput.style.borderColor = '', 1000);
            });
    });
})();
</script>
