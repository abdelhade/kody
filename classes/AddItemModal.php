<?php

require_once 'InvoiceElementBase.php';

/**
 * نافذة إضافة صنف من الفاتورة — نفس حقول صفحة إضافة الصنف.
 */
class AddItemModal extends InvoiceElementBase
{
    private $units = [];
    private $groups1 = [];
    private $groups2 = [];
    private $nextBarcode = '';
    private $nextCode = '1';
    private $unitBarcodeAdd = '0';

    public function __construct($invoiceType, $isEditMode = false, $data = null, $conn = null)
    {
        parent::__construct($invoiceType, $isEditMode, $data, $conn);
        $this->loadSelectOptions();
        $this->loadNextCodes();
    }

    private function loadSelectOptions()
    {
        if (!$this->conn) return;

        try {
            $result = $this->executeSecureQuery('SELECT * FROM myunits ORDER BY uname');
            $this->units = $result->fetch_all(MYSQLI_ASSOC);

            $result = $this->executeSecureQuery('SELECT * FROM item_group WHERE isdeleted = 0 ORDER BY gname');
            $this->groups1 = $result->fetch_all(MYSQLI_ASSOC);

            $result = $this->executeSecureQuery('SELECT * FROM item_group2 WHERE isdeleted = 0 ORDER BY gname');
            $this->groups2 = $result->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            error_log('Error loading select options for AddItemModal: ' . $e->getMessage());
        }
    }

    private function loadNextCodes()
    {
        if (!$this->conn) {
            return;
        }

        require_once __DIR__ . '/../includes/item_unit_sync.php';
        try {
            $this->nextBarcode = kody_next_barcode($this->conn);
            $this->unitBarcodeAdd = kody_unit_barcode_addend($this->conn);
            $codeRow = $this->conn->query('SELECT MAX(code) AS max_code FROM myitems');
            $assoc = $codeRow ? $codeRow->fetch_assoc() : null;
            $this->nextCode = ($assoc && $assoc['max_code'] !== null)
                ? (string) ((int) $assoc['max_code'] + 1)
                : '1';
        } catch (Throwable $e) {
            error_log('Error loading next barcode for AddItemModal: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $cssVer = is_file(__DIR__ . '/../dist/css/add_item.css')
            ? (string) filemtime(__DIR__ . '/../dist/css/add_item.css')
            : '1';

        ob_start();
        ?>
        <link rel="stylesheet" href="dist/css/add_item.css?v=<?= htmlspecialchars($cssVer, ENT_QUOTES, 'UTF-8') ?>">
        <div class="modal fade" id="addItemModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header py-2">
                        <h5 class="modal-title mb-0">
                            <i class="fas fa-plus-circle text-primary ml-1"></i> إضافة صنف
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="إغلاق">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-2">
                        <?php if (!$this->checkPermissions()): ?>
                            <div class="alert alert-danger mb-0">ليس لديك صلاحية لإضافة الأصناف</div>
                        <?php else: ?>
                            <?php $this->renderForm(); ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
        if ($this->checkPermissions()) {
            $this->renderScript();
        }
        return ob_get_clean();
    }

    private function renderForm()
    {
        $unitOptions = '';
        $nextCode = htmlspecialchars($this->nextCode, ENT_QUOTES, 'UTF-8');
        $nextBarcode = htmlspecialchars($this->nextBarcode, ENT_QUOTES, 'UTF-8');
        foreach ($this->units as $unit) {
            $unitOptions .= '<option value="' . (int) $unit['id'] . '">'
                . htmlspecialchars((string) $unit['uname'], ENT_QUOTES, 'UTF-8')
                . '</option>';
        }
        ?>
        <style>
            #addItemModal .item-page-wrap { max-width: none; padding: 0; }
            #addItemModal .modern-card { box-shadow: none; }
        </style>
        <div class="add-item-page">
            <div class="item-page-wrap">
                <div id="addItemModalMsg"></div>
                <form id="invoiceAddItemForm" enctype="multipart/form-data" autocomplete="off">
                    <input type="hidden" name="return_json" value="1">
                    <div class="card modern-card compact-card">
                        <div class="card-body">
                            <div class="item-fields-inline">
                                <div class="ifld ifld-code">
                                    <label>كود</label>
                                    <input readonly class="form-control form-control-sm bg-light text-center" type="text" name="code" id="invItemCode" value="<?= $nextCode ?>">
                                </div>
                                <div class="ifld ifld-barcode">
                                    <label for="invItemBarcode">باركود</label>
                                    <input id="invItemBarcode" readonly class="form-control form-control-sm bg-light text-center" type="text" name="barcode" value="<?= $nextBarcode ?>">
                                </div>
                                <div class="ifld ifld-name">
                                    <label for="invItemName">الاسم<span class="text-danger">*</span></label>
                                    <input id="invItemName" required class="form-control form-control-sm" type="text" name="iname" placeholder="اسم الصنف">
                                </div>
                                <div class="ifld ifld-name2">
                                    <label for="invItemName2">ثاني</label>
                                    <input id="invItemName2" class="form-control form-control-sm" type="text" name="name2">
                                </div>
                                <div class="ifld ifld-group">
                                    <label for="invItemGroup1">مجموعة</label>
                                    <select id="invItemGroup1" name="group1" class="form-control form-control-sm">
                                        <option value="">—</option>
                                        <?php foreach ($this->groups1 as $group): ?>
                                            <option value="<?= (int) $group['id'] ?>"><?= htmlspecialchars((string) $group['gname'], ENT_QUOTES, 'UTF-8') ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="ifld ifld-group">
                                    <label for="invItemGroup2">تصنيف</label>
                                    <select id="invItemGroup2" name="group2" class="form-control form-control-sm">
                                        <option value="">—</option>
                                        <?php foreach ($this->groups2 as $group): ?>
                                            <option value="<?= (int) $group['id'] ?>"><?= htmlspecialchars((string) $group['gname'], ENT_QUOTES, 'UTF-8') ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="ifld ifld-info">
                                    <label for="invItemInfo">ملاحظات</label>
                                    <input id="invItemInfo" class="form-control form-control-sm" type="text" name="info">
                                </div>
                                <div class="ifld ifld-img" id="invItemImagePanel">
                                    <div class="item-thumb" id="invItemImagePreview">
                                        <span class="thumb-empty" id="invItemImagePlaceholder"><i class="fas fa-image"></i></span>
                                        <img src="" alt="" id="invItemPreviewImg" class="d-none">
                                    </div>
                                    <label for="invItemImgs" class="btn-img-pick" title="اختر صورة"><i class="fas fa-camera"></i></label>
                                    <input type="file" name="imgs[]" class="d-none" id="invItemImgs" accept="image/*,.jpg,.jpeg,.png,.gif,.webp">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card modern-card units-card mt-2">
                        <div class="card-header card-header-compact d-flex align-items-center justify-content-between">
                            <span class="card-title-compact"><i class="fas fa-layer-group ml-1"></i> الوحدات والأسعار</span>
                            <button type="button" id="invAddUnit" class="btn btn-xs btn-primary py-0">
                                <i class="fas fa-plus"></i> وحدة
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <div class="units-table-wrap">
                                <table class="table table-sm table-bordered mb-0 units-table text-center">
                                    <colgroup>
                                        <col class="c-unit"><col class="c-val"><col class="c-bc">
                                        <col class="c-price"><col class="c-price"><col class="c-price"><col class="c-price">
                                        <col class="c-del">
                                    </colgroup>
                                    <thead>
                                        <tr>
                                            <th>وحدة</th>
                                            <th>المعامل</th>
                                            <th>باركود</th>
                                            <th>التكلفة</th>
                                            <th>قطاعي</th>
                                            <th>جملة</th>
                                            <th>السوق</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody id="invUnitsContainer">
                                        <tr class="urow">
                                            <td>
                                                <input type="hidden" name="iu_id[]" value="0">
                                                <select name="unit_id[]" class="form-control form-control-sm"><?= $unitOptions ?></select>
                                            </td>
                                            <td>
                                                <input class="form-control form-control-sm text-center" type="number" name="u_val[]" value="1" step="0.001" readonly>
                                            </td>
                                            <td>
                                                <input class="form-control form-control-sm unit-barcode-input" type="text" name="unit_barcode[]" value="<?= $nextBarcode ?>">
                                            </td>
                                            <td><input type="number" name="cost_price[]" class="form-control form-control-sm" value="0" step="0.001" min="0"></td>
                                            <td><input type="number" name="price1[]" class="form-control form-control-sm" value="0" step="0.001" min="0"></td>
                                            <td><input type="number" name="price2[]" class="form-control form-control-sm" value="0" step="0.001" min="0"></td>
                                            <td><input type="number" name="market_price[]" class="form-control form-control-sm" value="0" step="0.001" min="0"></td>
                                            <td class="text-center align-middle p-0">
                                                <button type="button" class="btn btn-link btn-del-row inv-delete-unit" title="حذف"><i class="fas fa-times text-danger"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <p class="units-tip mb-0"><i class="fas fa-info-circle ml-1"></i> الباركود والأسعار يُحسبان تلقائياً<?php if ($this->unitBarcodeAdd !== '' && $this->unitBarcodeAdd !== '0'): ?>، وباركود الوحدة الثانية = باركود الأولى + <?= htmlspecialchars($this->unitBarcodeAdd, ENT_QUOTES, 'UTF-8') ?><?php endif; ?></p>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="d-flex justify-content-between align-items-center mt-2">
            <button type="button" class="btn btn-outline-secondary btn-sm" data-dismiss="modal">إلغاء</button>
            <button type="button" class="btn btn-primary btn-sm" id="invSaveItemBtn">
                <i class="fas fa-save ml-1"></i> حفظ
            </button>
        </div>
        <?php
    }

    private function renderScript()
    {
        ?>
        <script>
        (function bindInvoiceAddItem() {
            if (!window.jQuery) {
                setTimeout(bindInvoiceAddItem, 40);
                return;
            }
            var $ = window.jQuery;
            var priceFields = ['cost_price', 'price1', 'price2', 'market_price'];
            var $modal = $('#addItemModal');
            var lastBarcode = ($('#invItemBarcode').val() || '').trim();
            var focusSearchAfterClose = false;
            var unitBarcodeAdd = <?= json_encode($this->unitBarcodeAdd, JSON_UNESCAPED_UNICODE) ?>;

            function addBarcodeNumbers(barcode, addend) {
                barcode = String(barcode || '').replace(/\D/g, '');
                addend = String(addend || '').replace(/\D/g, '');
                if (!barcode || !addend || /^0+$/.test(addend)) return '';
                var i = barcode.length - 1;
                var j = addend.length - 1;
                var carry = 0;
                var out = '';
                while (i >= 0 || j >= 0 || carry > 0) {
                    var sum = carry;
                    if (i >= 0) sum += barcode.charCodeAt(i) - 48;
                    if (j >= 0) sum += addend.charCodeAt(j) - 48;
                    out = String(sum % 10) + out;
                    carry = Math.floor(sum / 10);
                    i--;
                    j--;
                }
                return out.replace(/^0+(?=\d)/, '');
            }

            function refreshSecondUnitBarcode() {
                var $input = $rows().not(':first').first().find('.unit-barcode-input');
                if (!$input.length) return;
                var base = ($rows().first().find('.unit-barcode-input').val() || '').trim();
                if (!/^\d+$/.test(base)) base = ($('#invItemBarcode').val() || '').trim();
                var next = addBarcodeNumbers(base, unitBarcodeAdd);
                if (next === '' || next.length > 20) return;
                var current = ($input.val() || '').trim();
                var linked = $input.data('linked');
                if (current === '' || current === linked) {
                    $input.val(next);
                    $input.data('linked', next);
                }
            }

            function $rows() {
                return $modal.find('#invUnitsContainer .urow');
            }

            function showMsg(html) {
                $modal.find('#addItemModalMsg').html(html);
            }

            function resetUnits() {
                var $all = $rows();
                $all.not(':first').remove();
                var $first = $rows().first();
                $first.find('input[name="u_val[]"]').val('1').prop('readonly', true);
                $first.find('input[name="unit_barcode[]"]').val($('#invItemBarcode').val() || '');
                priceFields.forEach(function(name) {
                    $first.find('input[name="' + name + '[]"]').val('0');
                });
            }

            function loadNextBarcode() {
                fetch('ajax/load_items_lazy.php?action=next_barcode')
                    .then(function(r) { return r.json(); })
                    .then(function(d) {
                        if (!d) return;
                        if (d.code) $('#invItemCode').val(d.code);
                        if (d.barcode) {
                            $('#invItemBarcode').val(d.barcode);
                            $rows().first().find('.unit-barcode-input').val(d.barcode);
                            lastBarcode = String(d.barcode);
                        }
                    })
                    .catch(function() {});
            }

            function prepareFreshItem() {
                showMsg('');
                var form = document.getElementById('invoiceAddItemForm');
                if (form) form.reset();
                resetUnits();
                lastBarcode = ($('#invItemBarcode').val() || '').trim();
                $('#invItemPreviewImg').attr('src', '').addClass('d-none');
                $('#invItemImagePlaceholder').removeClass('d-none');
                loadNextBarcode();
            }

            function syncPricesFromBase() {
                var $base = $rows().first();
                $rows().each(function(index) {
                    if (index === 0) return;
                    var factor = parseFloat($(this).find('input[name="u_val[]"]').val()) || 1;
                    var row = $(this);
                    priceFields.forEach(function(name) {
                        var base = parseFloat($base.find('input[name="' + name + '[]"]').val()) || 0;
                        row.find('input[name="' + name + '[]"]').val((base * factor).toFixed(3));
                    });
                });
            }

            $modal.on('show.bs.modal', function() {
                prepareFreshItem();
            });

            $modal.on('shown.bs.modal', function() {
                var name = document.getElementById('invItemName');
                if (name) name.focus();
            });

            $modal.on('input', '#invItemBarcode', function() {
                var value = this.value;
                var $unit = $rows().first().find('.unit-barcode-input');
                if ($unit.val() === '' || $unit.val() === lastBarcode) {
                    $unit.val(value);
                }
                lastBarcode = value;
                refreshSecondUnitBarcode();
            });

            $modal.on('change', '#invItemImgs', function() {
                if (!this.files || !this.files[0]) return;
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#invItemPreviewImg').attr('src', e.target.result).removeClass('d-none');
                    $('#invItemImagePlaceholder').addClass('d-none');
                };
                reader.readAsDataURL(this.files[0]);
            });

            $modal.on('input', '#invUnitsContainer .urow:first input[name="cost_price[]"], #invUnitsContainer .urow:first input[name="price1[]"], #invUnitsContainer .urow:first input[name="price2[]"], #invUnitsContainer .urow:first input[name="market_price[]"]', syncPricesFromBase);

            $modal.on('input', '#invUnitsContainer input[name="u_val[]"]', function() {
                var row = $(this).closest('.urow');
                var factor = parseFloat($(this).val()) || 1;
                var $base = $rows().first();
                priceFields.forEach(function(name) {
                    var base = parseFloat($base.find('input[name="' + name + '[]"]').val()) || 0;
                    row.find('input[name="' + name + '[]"]').val((base * factor).toFixed(3));
                });
            });

            $modal.on('click', '#invAddUnit', function() {
                var $first = $rows().first();
                if (!$first.length) return;
                var clone = $first.clone();
                var factor = 6;
                var used = {};
                $modal.find('select[name="unit_id[]"]').each(function() {
                    used[$(this).val()] = true;
                });
                clone.find('input[name="iu_id[]"]').val('0');
                clone.find('input[name="u_val[]"]').val(String(factor)).prop('readonly', false);
                clone.find('.unit-barcode-input').val('').removeData('linked');
                var $select = clone.find('select[name="unit_id[]"]');
                $select.find('option').each(function() {
                    if (!used[$(this).val()]) {
                        $select.val($(this).val());
                        return false;
                    }
                });
                priceFields.forEach(function(name) {
                    var base = parseFloat($first.find('input[name="' + name + '[]"]').val()) || 0;
                    clone.find('input[name="' + name + '[]"]').val((base * factor).toFixed(3));
                });
                $rows().last().after(clone);
                refreshSecondUnitBarcode();
            });

            $modal.on('click', '.inv-delete-unit', function() {
                if ($rows().length > 1) {
                    $(this).closest('.urow').remove();
                }
            });

            $modal.on('wheel', '#invUnitsContainer input[type="number"]', function(e) {
                e.preventDefault();
            });

            function onItemSaved(res) {
                focusSearchAfterClose = true;
                try {
                    prepareFreshItem();
                } catch (err) {
                    console.error('reset add-item form failed:', err);
                }
                try {
                    $modal.modal('hide');
                } catch (err) {
                    console.error('hide add-item modal failed:', err);
                }
                try {
                    if (typeof window.selectInvoiceItem === 'function') {
                        window.selectInvoiceItem({
                            id: res.id,
                            name: res.iname,
                            price: res.price || 0,
                            barcode: res.barcode || ''
                        }, { noFocus: true });
                    } else {
                        $('#itemSearchInput').val(res.iname);
                        $('#selectedItemId').val(res.id);
                        $('#itmprice').val(res.price || 0);
                        $('#addRow').click();
                    }
                } catch (err) {
                    console.error('add saved item to invoice failed:', err);
                }
            }

            $('#invoiceAddItemForm').on('submit', function(e) {
                e.preventDefault();
                $('#invSaveItemBtn').trigger('click');
            });

            $('#invSaveItemBtn').on('click', function() {
                var $btn = $(this);
                var form = document.getElementById('invoiceAddItemForm');
                var iname = ($('#invItemName').val() || '').trim();
                if (!iname) {
                    showMsg('<div class="alert alert-danger py-1 mb-1">الاسم مطلوب</div>');
                    return;
                }
                var units = [];
                var factors = [];
                var bad = false;
                $modal.find('select[name="unit_id[]"]').each(function() {
                    var val = $(this).val();
                    if (!val || units.indexOf(val) !== -1) bad = true;
                    units.push(val);
                });
                $modal.find('input[name="u_val[]"]').each(function() {
                    var coeff = parseFloat($(this).val());
                    if (!isFinite(coeff) || coeff <= 0) bad = true;
                    var key = isFinite(coeff) ? coeff.toFixed(3) : '';
                    if (factors.indexOf(key) !== -1) bad = true;
                    factors.push(key);
                });
                if (bad || units.length === 0) {
                    showMsg('<div class="alert alert-danger py-1 mb-1">راجع الوحدات: لا تكرار، ومعامل أكبر من صفر، ووحدة أساسية بمعامل 1</div>');
                    return;
                }
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin ml-1"></i> جاري الحفظ...');
                fetch('do/doadd_item.php', { method: 'POST', body: new FormData(form) })
                    .then(function(r) { return r.text(); })
                    .then(function(text) {
                        var res = null;
                        var start = text.indexOf('{');
                        try {
                            res = JSON.parse(start >= 0 ? text.slice(start) : text);
                        } catch (err) {
                            console.error('doadd_item response:', text);
                            showMsg('<div class="alert alert-danger py-1 mb-1">رد غير صالح من الخادم</div>');
                            return;
                        }
                        if (!res || !res.success) {
                            showMsg('<div class="alert alert-danger py-1 mb-1">' + ((res && (res.error === 'database_error' ? res.message : res.error)) || 'حدث خطأ') + '</div>');
                            return;
                        }
                        onItemSaved(res);
                    }, function() {
                        showMsg('<div class="alert alert-danger py-1 mb-1">خطأ في الاتصال</div>');
                    })
                    .finally(function() {
                        $btn.prop('disabled', false).html('<i class="fas fa-save ml-1"></i> حفظ');
                    });
            });

            $modal.on('hidden.bs.modal', function() {
                showMsg('');
                if (!focusSearchAfterClose) return;
                focusSearchAfterClose = false;
                setTimeout(function() {
                    var search = document.getElementById('itemSearchInput');
                    if (search) search.focus();
                }, 0);
            });
        })();
        </script>
        <?php
    }

    private function checkPermissions()
    {
        global $role;
        return isset($role['add_items']) && $role['add_items'] == 1;
    }

    public function validate()
    {
        $errors = [];

        if (empty($_POST['iname'])) {
            $errors[] = 'اسم الصنف مطلوب';
        }
        if (empty($_POST['unit_id']) || !is_array($_POST['unit_id'])) {
            $errors[] = 'يجب إضافة وحدة واحدة على الأقل';
        }

        return $errors;
    }
}
