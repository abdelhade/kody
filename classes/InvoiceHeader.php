<?php

require_once 'InvoiceElementBase.php';

/**
 * فئة رأس الفاتورة - تطبيق polymorphism
 * Invoice Header class - implementing polymorphism
 */
class InvoiceHeader extends InvoiceElementBase
{
    private $accounts = [];
    private $stores = [];
    private $employees = [];
    private $priceLists = [];

    public function __construct($invoiceType, $isEditMode = false, $data = null, $conn = null)
    {
        parent::__construct($invoiceType, $isEditMode, $data, $conn);
        $this->loadSelectOptions();
    }

    /**
     * هل تم تفعيل خيار البيع والشراء لجميع الحسابات (عملاء وموردين)؟
     */
    private function isAllowAllAccounts()
    {
        global $rowstg;
        return !empty($rowstg['allow_all_accounts']);
    }

    /**
     * تحميل خيارات القوائم المنسدلة
     */
    private function loadSelectOptions()
    {
        if (!$this->conn) return;

        try {
            // تحميل العملاء/الموردين
            if ($this->isAllowAllAccounts()) {
                $query = "SELECT * FROM acc_head WHERE (code LIKE '122%' OR code LIKE '211%') AND is_basic = 0 AND isdeleted = 0 ORDER BY aname ASC";
                $result = $this->executeSecureQuery($query);
                $this->accounts = $result->fetch_all(MYSQLI_ASSOC);
            } else {
                $accountCode = $this->getAccountCode();
                if ($accountCode) {
                    $query = "SELECT * FROM acc_head WHERE code LIKE ? AND is_basic = 0 AND isdeleted = 0 ORDER BY id";
                    $result = $this->executeSecureQuery($query, [$accountCode . '%'], 's');
                    $this->accounts = $result->fetch_all(MYSQLI_ASSOC);
                }
            }

            // تحميل المخازن
            $query = "SELECT * FROM acc_head WHERE is_stock = 1";
            $result = $this->executeSecureQuery($query);
            $this->stores = $result->fetch_all(MYSQLI_ASSOC);

            // تحميل الموظفين
            $query = "SELECT * FROM acc_head WHERE parent_id = 35 AND is_basic = 0";
            $result = $this->executeSecureQuery($query);
            $this->employees = $result->fetch_all(MYSQLI_ASSOC);

            require_once __DIR__ . '/InvoiceProcessor.php';
            $this->priceLists = InvoiceProcessor::priceLists($this->conn);

        } catch (Exception $e) {
            error_log("Error loading select options: " . $e->getMessage());
        }
    }

    /**
     * عرض رأس الفاتورة
     */
    public function render()
    {
        require_once __DIR__ . '/InvoiceProcessor.php';
        $clientLabel = $this->getClientLabel();
        $addAccountLink = $this->getAddAccountLink();
        $isStoreTransfer = ((int) $this->invoiceType === InvoiceProcessor::INVOICE_TYPES['STORE_TRANSFER']);

        ob_start();
        ?>
        <div class="row frst-row bg--200">
            <?php if ($isStoreTransfer): ?>
            <!-- من مخزن (مصدر) -->
            <div class="col-md-2">
                <label for="">من مخزن</label>
                <select name="store_id" id="invoiceStore" class="select2 form-control form-control-sm">
                    <?php $this->renderStoreOptions(); ?>
                </select>
            </div>
            <!-- إلى مخزن (هدف) — يُحفظ في acc2_id ثم يُحوَّل إلى acc1 في القيد -->
            <div class="col-md-2">
                <label for="">إلى مخزن</label>
                <select class="select2 form-control form-control-sm" name="acc2_id" id="mySelectEmp">
                    <?php $this->renderDestStoreOptions(); ?>
                </select>
                <small id="storeTransferWarn" class="text-danger font-weight-bold d-none">لا يمكن التحويل لنفس المخزن</small>
            </div>
            <?php else: ?>
            <!-- العميل/المورد -->
            <div class="col-lg-2">
                <div class="tool">
                    <label for="">
                        <?php if ($this->isAllowAllAccounts()): ?>
                            العميل / المورد
                            <button type="button" class="btn bg-lime-200 btn-sm ml-1"
                                data-toggle="modal"
                                data-target="#addClientInlineModal" title="إضافة عميل جديد">+ عميل</button>
                            <button type="button" class="btn bg-lime-200 btn-sm"
                                data-toggle="modal"
                                data-target="#addSupplierInlineModal" title="إضافة مورد جديد">+ مورد</button>
                        <?php else: ?>
                            <?php echo $clientLabel; ?>
                            <button type="button" class="btn bg-lime-200 btn-sm"
                                data-toggle="modal"
                                data-target="<?php echo ($this->getClientType() === 'supplier') ? '#addSupplierInlineModal' : '#addClientInlineModal'; ?>">+</button>
                        <?php endif; ?>
                    </label>
                    <div class="tooltext">إضافة جديد</div>
                    <small id="partyBalance" class="font-weight-bold" style="white-space:nowrap; direction:rtl;"
                           data-sign="<?php echo ($this->getClientType() === 'supplier') ? -1 : 1; ?>"
                           data-effect="<?php echo $this->getBalanceEffect(); ?>"
                           data-exclude-op="<?php echo ($this->isEditMode && $this->data) ? (int) $this->data['id'] : 0; ?>"></small>
                </div>
                <select class="select2 form-control form-control-sm" name="acc2_id" id="mySelectEmp">
                    <?php $this->renderAccountOptions(); ?>
                </select>
            </div>

            <!-- المخزن -->
            <div class="col-md-1">
                <label for="">المخزن</label>
                <select name="store_id" id="invoiceStore" class="select2 form-control form-control-sm">
                    <?php $this->renderStoreOptions(); ?>
                </select>
            </div>
            <?php endif; ?>

            <!-- الموظف -->
            <div class="col-md-1">
                <label for="">الموظف</label>
                <select class="form-control form-control-sm" name="emp_id">
                    <?php $this->renderEmployeeOptions(); ?>
                </select>
            </div>

            <!-- الفئة السعرية -->
            <?php if (in_array((int)$this->invoiceType, [3, 11, 14], true)): ?>
            <div class="col-md-1">
                <label for="invoicePriceList">الفئة السعرية</label>
                <select class="form-control form-control-sm" name="price_list" id="invoicePriceList">
                    <?php $this->renderPriceListOptions(); ?>
                </select>
            </div>
            <?php elseif ((int)$this->invoiceType === 4): ?>
            <div class="col-md-2">
                <label for="invoicePriceList">سعر البيع</label>
                <select class="form-control form-control-sm" name="price_list" id="invoicePriceList"
                        title="سعر البيع المعروض في عمود س. بيع ويُحفظ في الصنف">
                    <?php $this->renderPriceListOptions(3, true); ?>
                </select>
            </div>
            <?php endif; ?>

            <!-- التاريخ -->
            <div class="col-md-1">
                <label for="">التاريخ</label>
                <input type="date" class="form-control bg-secondary" name="pro_date" id="pro_date" 
                       value="<?php echo $this->getProDate(); ?>">
            </div>

            <!-- تاريخ الاستحقاق -->
            <div class="col-md-1">
                <label for="">تاريخ الاستحقاق</label>
                <input type="date" class="form-control" name="accural_date" 
                       value="<?php echo $this->getAccuralDate(); ?>">
            </div>

            <!-- رقم الفاتورة -->
            <div class="col-md-1">
                <label for="">رقم الفاتورة</label>
                <input name="pro_id" type="text" class="form-control form-control-sm" 
                       value="<?php echo $this->getProId(); ?>" readonly>
            </div>

            <!-- S.N -->
            <div class="col-md-1">
                <label for="">S.N</label>
                <input type="text" name="pro_serial" class="form-control form-control-sm" 
                       value="<?php echo $this->getProSerial(); ?>">
            </div>
        </div>

        <!-- Modal إضافة مورد -->
        <div class="modal fade" id="addSupplierInlineModal" tabindex="-1" role="dialog" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header bg-success text-white py-2">
                <h5 class="modal-title mb-0"><i class="fas fa-user-plus ml-2"></i> إضافة مورد جديد</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
              </div>
              <div class="modal-body">
                <div id="addSupplierInlineMsg"></div>
                  <input type="hidden" data-field="parent_code" value="211">
                  <div class="form-group">
                    <label class="small">اسم المورد <span class="text-danger">*</span></label>
                    <input type="text" data-field="aname" class="form-control form-control-sm" placeholder="اسم المورد">
                  </div>
                  <div class="row">
                    <div class="col-md-6 form-group">
                      <label class="small">رقم الهاتف</label>
                      <input type="text" data-field="phone" class="form-control form-control-sm" placeholder="اختياري">
                    </div>
                    <div class="col-md-6 form-group">
                      <label class="small">العنوان</label>
                      <input type="text" data-field="address" class="form-control form-control-sm" placeholder="اختياري">
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="small">ملاحظات</label>
                    <input type="text" data-field="info" class="form-control form-control-sm" placeholder="اختياري">
                  </div>
              </div>
              <div class="modal-footer py-2">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-success btn-sm" id="saveSupplierInlineBtn">
                  <i class="fas fa-save ml-1"></i> حفظ المورد
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Modal إضافة عميل -->
        <div class="modal fade" id="addClientInlineModal" tabindex="-1" role="dialog" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header bg-primary text-white py-2">
                <h5 class="modal-title mb-0"><i class="fas fa-user-plus ml-2"></i> إضافة عميل جديد</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
              </div>
              <div class="modal-body">
                <div id="addClientInlineMsg"></div>
                  <input type="hidden" data-field="parent_code" value="122">
                  <div class="form-group">
                    <label class="small">اسم العميل <span class="text-danger">*</span></label>
                    <input type="text" data-field="aname" class="form-control form-control-sm" placeholder="اسم العميل">
                  </div>
                  <div class="row">
                    <div class="col-md-6 form-group">
                      <label class="small">رقم الهاتف</label>
                      <input type="text" data-field="phone" class="form-control form-control-sm" placeholder="اختياري">
                    </div>
                    <div class="col-md-6 form-group">
                      <label class="small">العنوان</label>
                      <input type="text" data-field="address" class="form-control form-control-sm" placeholder="اختياري">
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="small">ملاحظات</label>
                    <input type="text" data-field="info" class="form-control form-control-sm" placeholder="اختياري">
                  </div>
                  <div class="form-group mb-0">
                    <label class="small">الفئة السعرية</label>
                    <select data-field="price_list" class="form-control form-control-sm">
                      <?php
                      foreach ($this->priceLists as $list) {
                          $listId = (int) ($list['id'] ?? 0);
                          if ($listId < 1 || $listId > 3) {
                              continue;
                          }
                          $sel = $listId === 1 ? 'selected' : '';
                          echo '<option value="' . $listId . '" ' . $sel . '>' . $this->sanitizeInput($list['pname']) . '</option>';
                      }
                      ?>
                    </select>
                  </div>
              </div>
              <div class="modal-footer py-2">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-primary btn-sm" id="saveClientInlineBtn">
                  <i class="fas fa-save ml-1"></i> حفظ العميل
                </button>
              </div>
            </div>
          </div>
        </div>

        <script>
        $(document).ready(function() {
            function saveAccountInline(formSel, btnId, msgId, modalId) {
                $('#' + btnId).off('click').on('click', function() {
                    const $btn = $(this);
                    // نجيب الـ aname من الـ modal مباشرة (مش من form عشان nested form ممنوعة)
                    const $modal = $(modalId);
                    const aname = $modal.find('[data-field="aname"]').val();
                    if (!aname || !aname.trim()) {
                        $('#' + msgId).html('<div class="alert alert-danger py-1 mb-1">الاسم مطلوب</div>');
                        return;
                    }
                    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin ml-1"></i> جاري الحفظ...');
                    
                    // بناء البيانات يدوياً بدون form
                    const formData = new FormData();
                    $modal.find('[data-field]').each(function() {
                        formData.append($(this).data('field'), $(this).val() || '');
                    });

                    fetch('ajax/modal_add_account.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(r => r.json())
                    .then(res => {
                        if (res.success) {
                            $('#' + msgId).html('<div class="alert alert-success py-1 mb-1">✓ تم الحفظ: <strong>' + res.aname + '</strong></div>');
                            // إضافة الخيار لقائمة المورد/العميل واختياره
                            const $sel = $('#mySelectEmp');
                            const opt = new Option(res.aname, res.id, true, true);
                            if (res.price_list) {
                                opt.setAttribute('data-price-list', String(res.price_list));
                            }
                            $sel.append(opt).trigger('change');
                            // مسح الحقول
                            $modal.find('[data-field]').not('[type="hidden"]').not('select').val('');
                            $modal.find('select[data-field="price_list"]').val('1');
                            setTimeout(() => $(modalId).modal('hide'), 700);
                        } else {
                            $('#' + msgId).html('<div class="alert alert-danger py-1 mb-1">' + (res.error || 'حدث خطأ') + '</div>');
                        }
                    })
                    .catch(() => {
                        $('#' + msgId).html('<div class="alert alert-danger py-1 mb-1">خطأ في الاتصال</div>');
                    })
                    .finally(() => {
                        $btn.prop('disabled', false).html('<i class="fas fa-save ml-1"></i> حفظ');
                    });
                });
            }

            // رصيد الطرف قبل وبعد الفاتورة
            const $pb = $('#partyBalance');
            let partyBalanceBefore = null;
            let partyBalanceReq = 0;
            const fmtBal = v => String(parseFloat((+v).toFixed(2)));

            window.updatePartyBalance = function() {
                if (!$pb.length || partyBalanceBefore === null) return;
                const net  = parseFloat($('#headnet').val()) || 0;
                const paid = parseFloat($('#paid').val()) || 0;
                const effect = parseInt($pb.data('effect'), 10);
                const after = partyBalanceBefore + (net - paid) * (isNaN(effect) ? 1 : effect);
                $pb.text('قبل:' + fmtBal(partyBalanceBefore) + ' بعد:' + fmtBal(after));
            };

            function loadPartyBalance() {
                const accId = parseInt($('#mySelectEmp').val(), 10) || 0;
                partyBalanceBefore = null;
                $pb.text('');
                if (!accId) return;

                let sign = parseInt($pb.data('sign'), 10) || 1;
                const $opt = $('#mySelectEmp').find('option:selected');
                if ($opt.length && $opt.attr('data-is-supplier') !== undefined) {
                    sign = ($opt.attr('data-is-supplier') === '1') ? -1 : 1;
                }

                const reqId = ++partyBalanceReq;
                $.getJSON('ajax/account_balance.php', { id: accId, exclude_op: $pb.data('exclude-op') || 0 })
                    .done(function(res) {
                        if (reqId !== partyBalanceReq || !res || !res.success) return;
                        partyBalanceBefore = res.balance * sign;
                        window.updatePartyBalance();
                    });
            }

            $('#mySelectEmp').on('change', loadPartyBalance);
            $(document).on('keyup input change', '#paid, #headnet, #headdisc, #headdisc_pct, #headplus, .itmqty, .itmprice, .itmdisc, .itmdisc_pct', function() {
                setTimeout(window.updatePartyBalance, 0);
            });
            loadPartyBalance();

            // تحويل مخزني: منع نفس المخزن من/إلى
            function checkStoreTransferSame() {
                var $warn = $('#storeTransferWarn');
                if (!$warn.length) return true;
                var from = parseInt($('#invoiceStore').val(), 10) || 0;
                var to = parseInt($('#mySelectEmp').val(), 10) || 0;
                var same = from > 0 && to > 0 && from === to;
                $warn.toggleClass('d-none', !same);
                return !same;
            }
            $('#invoiceStore, #mySelectEmp').on('change', checkStoreTransferSame);
            checkStoreTransferSame();
            $('form').on('submit', function(e) {
                if (!checkStoreTransferSame()) {
                    e.preventDefault();
                    alert('لا يمكن التحويل من وإلى نفس المخزن');
                }
            });

            saveAccountInline(null, 'saveSupplierInlineBtn', 'addSupplierInlineMsg', '#addSupplierInlineModal');
            saveAccountInline(null, 'saveClientInlineBtn',   'addClientInlineMsg',   '#addClientInlineModal');

            $('#addSupplierInlineModal, #addClientInlineModal').on('hidden.bs.modal', function() {
                $(this).find('[id$="Msg"]').html('');
            });
        });
        </script>
        <?php
        return ob_get_clean();
    }

    /**
     * التحقق من صحة البيانات
     */
    public function validate()
    {
        $errors = [];
        $isStoreTransfer = ((int) $this->invoiceType === InvoiceProcessor::INVOICE_TYPES['STORE_TRANSFER']);

        if ($isStoreTransfer) {
            if (empty($_POST['store_id'])) {
                $errors[] = 'يجب اختيار المخزن المصدر';
            }
            if (empty($_POST['acc2_id'])) {
                $errors[] = 'يجب اختيار المخزن الهدف';
            }
            if (!empty($_POST['store_id']) && !empty($_POST['acc2_id'])
                && (int) $_POST['store_id'] === (int) $_POST['acc2_id']) {
                $errors[] = 'لا يمكن التحويل من وإلى نفس المخزن';
            }
        } else {
            // التحقق من وجود العميل/المورد
            if (empty($_POST['acc2_id'])) {
                $errors[] = 'يجب اختيار ' . ($this->getClientType() == 'supplier' ? 'المورد' : 'العميل');
            }

            // التحقق من وجود المخزن
            if (empty($_POST['store_id'])) {
                $errors[] = 'يجب اختيار المخزن';
            }
        }

        // التحقق من التاريخ
        if (empty($_POST['pro_date'])) {
            $errors[] = 'يجب إدخال التاريخ';
        }

        return $errors;
    }

    /**
     * الحصول على تسمية العميل/المورد
     */
    private function getClientLabel()
    {
        $clientType = $this->getClientType();
        return $clientType == 'supplier' ? 'المورد' : 'العميل';
    }

    /**
     * الحصول على رابط إضافة عميل/مورد جديد
     */
    private function getAddAccountLink()
    {
        $accountCode = $this->getAccountCode();
        return "add_account.php?parent_id=" . $accountCode;
    }

    /**
     * حساب الطرف في الفاتورة المحفوظة.
     * مردود المشتريات يخزن المورد في acc1، والتحويل المخزني يخزن الهدف في acc1،
     * وباقي الفواتير تخزن الطرف في acc2.
     */
    private function getPartyAccountId()
    {
        $type = (int) $this->invoiceType;
        if ($type === 10 || $type === InvoiceProcessor::INVOICE_TYPES['STORE_TRANSFER']) {
            return $this->data['acc1'] ?? null;
        }
        return $this->data['acc2'] ?? null;
    }

    /** خيارات المخزن الهدف للتحويل المخزني */
    private function renderDestStoreOptions()
    {
        $partyId = $this->isEditMode ? $this->getPartyAccountId() : null;
        foreach ($this->stores as $store) {
            $selected = '';
            if ($partyId !== null && (int) $partyId === (int) $store['id']) {
                $selected = 'selected';
            }
            echo "<option value='{$store['id']}' {$selected}>{$this->sanitizeInput($store['aname'])}</option>";
        }
    }

    /**
     * أثر الفاتورة على رصيد الطرف: الفاتورة تزوّد المستحق، المردود ينقصه، الأوامر والعروض لا تؤثر
     */
    private function getBalanceEffect()
    {
        switch ((int) $this->invoiceType) {
            case 3:
            case 4:
                return 1;
            case 10:
            case 11:
                return -1;
            default:
                return 0;
        }
    }

    /**
     * عرض خيارات العملاء/الموردين
     */
    private function renderAccountOptions()
    {
        $customers = [];
        $suppliers = [];
        foreach ($this->accounts as $account) {
            if (strpos((string)$account['code'], '211') === 0) {
                $suppliers[] = $account;
            } else {
                $customers[] = $account;
            }
        }

        if (!empty($customers) && !empty($suppliers)) {
            $isSupplierType = ($this->getClientType() === 'supplier');
            $firstGroup  = $isSupplierType ? ['label' => 'الموردين', 'items' => $suppliers] : ['label' => 'العملاء', 'items' => $customers];
            $secondGroup = $isSupplierType ? ['label' => 'العملاء', 'items' => $customers] : ['label' => 'الموردين', 'items' => $suppliers];

            echo '<optgroup label="' . $firstGroup['label'] . '">';
            foreach ($firstGroup['items'] as $acc) {
                $this->renderSingleAccountOption($acc);
            }
            echo '</optgroup>';

            echo '<optgroup label="' . $secondGroup['label'] . '">';
            foreach ($secondGroup['items'] as $acc) {
                $this->renderSingleAccountOption($acc);
            }
            echo '</optgroup>';
        } else {
            foreach ($this->accounts as $account) {
                $this->renderSingleAccountOption($account);
            }
        }
    }

    /**
     * افتراضي المستخدم في الفاتورة الجديدة: [يتشال من القائمة؟, selected]
     */
    private function userDefaultOption(string $key, $id, string $fallback = ''): array
    {
        if ($this->isEditMode || !$this->conn) {
            return [false, $fallback];
        }
        if (UserDefaults::hides($this->conn, $key, $id)) {
            return [true, ''];
        }
        if (UserDefaults::get($this->conn, $key) > 0) {
            return [false, trim(UserDefaults::selected($this->conn, $key, $id))];
        }
        return [false, $fallback];
    }

    private function renderSingleAccountOption($account)
    {
        $selected = '';
        if ($this->isEditMode && $this->data && $this->getPartyAccountId() == $account['id']) {
            $selected = 'selected';
        }
        [$hide, $selected] = $this->userDefaultOption(UserDefaults::partyKey((int) $this->invoiceType), $account['id'], $selected);
        if ($hide) {
            return;
        }
        $isSup = (strpos((string)$account['code'], '211') === 0);
        $priceAttr = '';
        if (!$isSup) {
            $clientPrice = max(1, (int) ($account['price_list'] ?? 1));
            $priceAttr = " data-price-list=\"{$clientPrice}\"";
        }
        $supAttr = ' data-is-supplier="' . ($isSup ? '1' : '0') . '"';
        $cashAttr = !empty($account['cash_only']) ? ' data-cash="1"' : '';
        $aname = $this->sanitizeInput($account['aname']);
        echo "<option value='{$account['id']}' {$selected}{$priceAttr}{$supAttr}{$cashAttr}>{$aname}</option>";
    }

    /**
     * عرض خيارات المخازن
     */
    private function renderStoreOptions()
    {
        foreach ($this->stores as $store) {
            $selected = $this->getDefaultSelected('def_store', $store['id']);
            if ($this->isEditMode && $this->data && $this->data['store_id'] == $store['id']) {
                $selected = 'selected';
            }
            [$hide, $selected] = $this->userDefaultOption('store', $store['id'], $selected);
            if ($hide) {
                continue;
            }
            echo "<option value='{$store['id']}' {$selected}>{$this->sanitizeInput($store['aname'])}</option>";
        }
    }

    /**
     * عرض خيارات الموظفين
     */
    private function renderEmployeeOptions()
    {
        foreach ($this->employees as $employee) {
            $selected = $this->getDefaultSelected('def_emp', $employee['id']);
            if ($this->isEditMode && $this->data && $this->data['emp_id'] == $employee['id']) {
                $selected = 'selected';
            }
            [$hide, $selected] = $this->userDefaultOption('emp', $employee['id'], $selected);
            if ($hide) {
                continue;
            }
            echo "<option value='{$employee['id']}' {$selected}>{$this->sanitizeInput($employee['aname'])}</option>";
        }
    }

    /**
     * عرض الفئات السعرية (قطاعي / جملة / السوق)
     */
    private function renderPriceListOptions($maxId = 0, $numbered = false)
    {
        $selectedId = 1;
        if ($this->isEditMode && $this->data && !empty($this->data['price_list'])) {
            $selectedId = (int) $this->data['price_list'];
        }
        if ($maxId > 0 && $selectedId > $maxId) {
            $selectedId = 1;
        }
        $lists = $this->priceLists;
        if (!$lists) {
            $lists = [
                ['id' => 1, 'pname' => 'قطاعي'],
                ['id' => 2, 'pname' => 'جملة'],
                ['id' => 3, 'pname' => 'السوق'],
            ];
        }
        $userList = UserDefaults::hasPriceList((int) $this->invoiceType);
        foreach ($lists as $list) {
            $id = (int) $list['id'];
            if ($maxId > 0 && $id > $maxId) {
                continue;
            }
            $selected = ($id === $selectedId) ? 'selected' : '';
            if ($userList) {
                [$hide, $selected] = $this->userDefaultOption('price_list', $id, $selected);
                if ($hide) {
                    continue;
                }
            }
            $label = $this->sanitizeInput($list['pname']);
            if ($numbered) {
                $label = "سعر البيع {$id} - {$label}";
            }
            echo "<option value='{$id}' {$selected}>{$label}</option>";
        }
    }

    /**
     * الحصول على القيمة الافتراضية المحددة
     */
    private function getDefaultSelected($optionName, $currentId)
    {
        try {
            $query = "SELECT cur_value FROM myoptions WHERE oname = ?";
            $result = $this->executeSecureQuery($query, [$optionName], 's');
            $row = $result->fetch_assoc();
            return ($row && $row['cur_value'] == $currentId) ? 'selected' : '';
        } catch (Exception $e) {
            return '';
        }
    }

    /**
     * الحصول على تاريخ الفاتورة
     */
    private function getProDate()
    {
        if ($this->isEditMode && $this->data) {
            return $this->data['pro_date'];
        }
        return date('Y-m-d');
    }

    /**
     * الحصول على تاريخ الاستحقاق
     */
    private function getAccuralDate()
    {
        if ($this->isEditMode && $this->data) {
            return $this->data['accural_date'];
        }
        return '';
    }

    /**
     * الحصول على رقم الفاتورة
     */
    private function getProId()
    {
        if ($this->isEditMode && $this->data) {
            return $this->data['pro_id'];
        }
        return $this->generateNewProId();
    }

    /**
     * الحصول على الرقم التسلسلي
     */
    private function getProSerial()
    {
        if ($this->isEditMode && $this->data) {
            return $this->data['pro_serial'];
        }
        return '';
    }

    /**
     * توليد رقم فاتورة جديد
     */
    private function generateNewProId()
    {
        try {
            $query = "SELECT MAX(CAST(pro_id AS UNSIGNED)) as max_id FROM ot_head WHERE pro_tybe = ?";
            $result = $this->executeSecureQuery($query, [$this->invoiceType], 'i');
            $row = $result->fetch_assoc();
            return $row ? ($row['max_id'] + 1) : 1;
        } catch (Exception $e) {
            return 1;
        }
    }
}