<?php

require_once 'InvoiceElementBase.php';

/**
 * فئة ذيل الفاتورة - تطبيق polymorphism
 * Invoice Footer class - implementing polymorphism
 */
class InvoiceFooter extends InvoiceElementBase
{
    private $funds = [];

    public function __construct($invoiceType, $isEditMode = false, $data = null, $conn = null)
    {
        parent::__construct($invoiceType, $isEditMode, $data, $conn);
        $this->loadFunds();
    }

    /**
     * تحميل الصناديق
     */
    private function loadFunds()
    {
        if (!$this->conn) return;

        try {
            $query = "SELECT * FROM acc_head WHERE is_fund = 1";
            $result = $this->executeSecureQuery($query);
            $this->funds = $result->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            error_log("Error loading funds: " . $e->getMessage());
        }
    }

    /**
     * عرض ذيل الفاتورة
     */
    public function render()
    {
        ob_start();
        $isPurchase = in_array($this->invoiceType, [4, 10, 12]);
        ?>
        <div class="invoice-footer-fixed invoice-footer-modern" id="invoiceFooter">
        <style>
        .invoice-footer-modern {
            padding: 6px 10px 6px;
            background: #ffffff;
            border-top: 1px solid #cbd5e1;
            box-shadow: 0 -4px 18px rgba(15, 23, 42, 0.08);
            font-family: inherit;
        }
        .invoice-footer-modern .footer-grid {
            display: grid;
            grid-template-columns: 1.15fr 1.15fr 1fr;
            gap: 8px;
            margin-bottom: 5px;
        }
        @media (max-width: 991px) {
            .invoice-footer-modern .footer-grid {
                grid-template-columns: 1fr;
            }
        }
        .invoice-footer-modern .footer-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 6px 8px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 108px;
        }
        .invoice-footer-modern .footer-card-totals {
            background: #ffffff;
            border-color: #cbd5e1;
        }
        .invoice-footer-modern .footer-card-payment {
            background: #ffffff;
            border-color: #cbd5e1;
        }
        .invoice-footer-modern .card-header-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 4px;
            margin-bottom: 4px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 11.5px;
            font-weight: 700;
            color: #334155;
        }
        .invoice-footer-modern .item-metric-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 4px;
        }
        .invoice-footer-modern .metric-box {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 3px 6px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .invoice-footer-modern .metric-label {
            font-size: 10.5px;
            color: #64748b;
            margin-bottom: 1px;
            white-space: nowrap;
        }
        .invoice-footer-modern .metric-val {
            font-size: 13px;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .invoice-footer-modern .form-row-compact {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 3px;
        }
        .invoice-footer-modern .form-row-compact:last-child {
            margin-bottom: 0;
        }
        .invoice-footer-modern .form-lbl {
            font-size: 11.5px;
            font-weight: 600;
            color: #475569;
            margin-bottom: 0;
            white-space: nowrap;
        }
        .invoice-footer-modern .hero-net-container {
            background: #0f172a;
            color: #f8fafc;
            border-radius: 6px;
            padding: 2px 8px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 32px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.12);
        }
        .invoice-footer-modern .hero-net-label {
            font-size: 12px;
            font-weight: 700;
            color: #94a3b8;
            margin: 0;
            white-space: nowrap;
        }
        .invoice-footer-modern .hero-net-input {
            background: transparent !important;
            border: none !important;
            color: #38bdf8 !important;
            font-size: 20px !important;
            font-weight: 800 !important;
            text-align: left;
            padding: 0 !important;
            height: auto !important;
            box-shadow: none !important;
        }
        .invoice-footer-modern .hero-paid-input {
            font-size: 17px !important;
            font-weight: 700 !important;
            color: #047857 !important;
            background: #ecfdf5 !important;
            border: 1px solid #10b981 !important;
            height: 29px !important;
            text-align: center;
        }
        .invoice-footer-modern .bottom-action-bar {
            display: flex;
            align-items: center;
            gap: 6px;
            padding-top: 4px;
            border-top: 1px solid #e2e8f0;
        }
        .invoice-footer-modern .notes-input-wrapper {
            flex: 1;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .invoice-footer-modern .notes-input-wrapper .notes-lbl {
            font-size: 11.5px;
            font-weight: 600;
            color: #64748b;
            white-space: nowrap;
        }
        </style>

        <!-- حقل نوع الفاتورة المخفي -->
        <input type="text" name="pro_tybe" hidden value="<?php echo $this->invoiceType; ?>">

        <!-- الشبكة الرئيسية: 3 بطاقات متوازية -->
        <div class="footer-grid">
            <!-- البطاقة 1: بيانات وتاريخ الصنف -->
            <div class="footer-card" id="itemDataCols">
                <div class="card-header-bar">
                    <label for="showItemDataCheck" class="mb-0 d-inline-flex align-items-center" style="gap:4px; cursor:pointer;">
                        <input type="checkbox" id="showItemDataCheck">
                        <span>بيانات الصنف</span>
                    </label>
                    <div class="d-flex align-items-center text-truncate" style="gap:5px; max-width: 70%;">
                        <h6 id="selectedLineName" class="mb-0 text-truncate" style="font-weight:700; color:#0f172a; font-size:11.5px;"></h6>
                        <span id="selectedItemLinks" class="d-none" style="display:inline-flex; gap:2px;">
                            <a id="selectedItemMovement" href="#" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary py-0 px-1" title="حركة الصنف" style="font-size:10px;"><i class="fa fa-exchange-alt"></i></a>
                            <a id="selectedItemEdit" href="#" target="_blank" rel="noopener" class="btn btn-sm btn-outline-warning py-0 px-1" title="تعديل الصنف" style="font-size:10px;"><i class="fa fa-pen"></i></a>
                        </span>
                    </div>
                </div>
                
                <div id="itemDataPanel">
                    <div class="row no-gutters">
                        <div class="col-12">
                            <?php $this->renderItemInfo(); ?>
                            <?php $this->renderCostInfo(); ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- البطاقة 2: ملخص الحساب والإجماليات -->
            <div class="footer-card footer-card-totals">
                <?php $this->renderTotals(); ?>
            </div>

            <!-- البطاقة 3: السداد والخزينة -->
            <div class="footer-card footer-card-payment">
                <?php $this->renderPayment(); ?>
            </div>
        </div>

        <!-- شريط الإجراءات والملاحظات السفلي المدمج -->
        <div class="bottom-action-bar">
            <!-- الملاحظات -->
            <div class="notes-input-wrapper">
                <span class="notes-lbl"><i class="far fa-comment-alt"></i> ملاحظات:</span>
                <input type="text" class="form-control form-control-sm bg-light" name="info" id="info" 
                       placeholder="ملاحظات على الفاتورة..." value="<?php echo $this->getInfo(); ?>" style="height:30px; font-size:12px;">
            </div>

            <!-- زر إظهار الفواتير السابقة -->
            <div id="showOps" style="cursor:pointer;">
                <button type="button" class="btn btn-outline-secondary btn-sm d-flex align-items-center" style="gap:4px; height:30px; font-size:11.5px; white-space:nowrap;">
                    <i class="fas fa-history"></i> فواتير سابقة
                </button>
            </div>

            <!-- أزرار الحفظ والطباعة -->
            <?php $this->renderActionButtons(); ?>
        </div>
        </div>

        <script>
        (function() {
            var COOKIE = 'invoice_show_item_data';
            function readCookie() {
                var m = document.cookie.match(/(?:^|; )invoice_show_item_data=([^;]*)/);
                return m ? decodeURIComponent(m[1]) : '1';
            }
            function writeCookie(value) {
                var exp = new Date();
                exp.setFullYear(exp.getFullYear() + 1);
                document.cookie = COOKIE + '=' + encodeURIComponent(value) + '; expires=' + exp.toUTCString() + '; path=/; SameSite=Lax';
            }
            function applyItemData(on) {
                var panel = document.getElementById('itemDataPanel');
                if (panel) panel.style.display = on ? '' : 'none';
            }
            var cb = document.getElementById('showItemDataCheck');
            if (!cb) return;
            var on = readCookie() !== '0';
            cb.checked = on;
            applyItemData(on);
            cb.addEventListener('change', function() {
                writeCookie(cb.checked ? '1' : '0');
                applyItemData(cb.checked);
            });
        })();
        </script>
        <?php
        return ob_get_clean();
    }

    /**
     * عرض معلومات الصنف
     */
    private function renderItemInfo()
    {
        $isPurchase = in_array($this->invoiceType, [4, 10, 12]);
        if ($isPurchase) {
            ?>
            <div class="item-metric-grid">
                <div class="metric-box">
                    <span class="metric-label">رصيد المخزن</span>
                    <h6 id="storeqty" class="metric-val"></h6>
                </div>
                <div class="metric-box">
                    <span class="metric-label">آخر سعر شراء</span>
                    <h6 id="last_price" class="metric-val text-primary"></h6>
                </div>
                <div class="metric-box">
                    <span class="metric-label">متوسط التكلفة</span>
                    <h6 id="cost_price" class="metric-val"></h6>
                </div>
                <div class="metric-box">
                    <span class="metric-label">سعر البيع</span>
                    <h6 id="price1" class="metric-val text-success"></h6>
                </div>
            </div>
            <!-- عناصر مخفية لضمان توافق سكربتات JS تماماً دون أي خطأ -->
            <div style="display:none;" id="cost_price_div">
                <h6 id="market_price"></h6>
                <h6 id="storemdtime"></h6>
            </div>
            <?php
        } else {
            ?>
            <div class="item-metric-grid">
                <div class="metric-box">
                    <span class="metric-label">الكمية بالمخزن</span>
                    <h6 id="storeqty" class="metric-val"></h6>
                </div>
                <div class="metric-box">
                    <span class="metric-label">سعر البيع</span>
                    <h6 id="price1" class="metric-val text-primary"></h6>
                </div>
            </div>
            <div style="display:none;" id="cost_price_div">
                <h6 id="market_price"></h6>
                <h6 id="storemdtime"></h6>
            </div>
            <?php
        }
    }

    /**
     * عرض معلومات التكلفة
     */
    private function renderCostInfo()
    {
        $isPurchase = in_array($this->invoiceType, [4, 10, 12]);
        if ($isPurchase) {
            // مؤشرات الربح مخفية في فواتير المشتريات لكن موجودة في الـ DOM لتوافق الـ JS
            ?>
            <div style="display:none;">
                <h6 id="unit_profit">-</h6>
                <h6 id="item_total_profit">-</h6>
                <h6 id="invoice_total_profit">-</h6>
            </div>
            <?php
        } else {
            ?>
            <div class="item-metric-grid mt-1">
                <div class="metric-box">
                    <span class="metric-label">سعر التكلفة</span>
                    <h6 id="cost_price" class="metric-val"></h6>
                </div>
                <div class="metric-box">
                    <span class="metric-label">ربح الصنف</span>
                    <h6 id="item_total_profit" class="metric-val text-success font-weight-bold">-</h6>
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-1 px-1">
                <span class="metric-label mb-0">إجمالي ربح الفاتورة:</span>
                <h6 id="invoice_total_profit" class="metric-val text-success mb-0 font-weight-bold">-</h6>
            </div>
            <div style="display:none;">
                <h6 id="last_price"></h6>
                <h6 id="unit_profit">-</h6>
            </div>
            <?php
        }
    }

    /**
     * عرض الإجماليات
     */
    private function renderTotals()
    {
        ?>
        <div class="card-header-bar">
            <span>ملخص الحساب والإجماليات</span>
            <span class="badge badge-light border text-muted" style="font-size:10px;">حسابات الفاتورة</span>
        </div>

        <!-- سطر 1: الكميات والإجمالي الأولي -->
        <div class="row no-gutters mb-1" style="gap:4px;">
            <div class="col">
                <div class="d-flex align-items-center" style="gap:3px;">
                    <label class="form-lbl" style="font-size:11px;">الكمية:</label>
                    <input id="headqty" name="headqty" type="text"
                           class="form-control form-control-sm bg-light text-center font-weight-bold p-0"
                           value="<?php echo $this->getTotalQty(); ?>" readonly style="height:26px; font-size:12px;">
                </div>
            </div>
            <div class="col">
                <div class="d-flex align-items-center" style="gap:3px;">
                    <label class="form-lbl" style="font-size:11px;">الإجمالي:</label>
                    <input id="headtotal" name="headtotal" type="text" 
                           class="form-control form-control-sm bg-light text-center font-weight-bold p-0" 
                           value="<?php echo $this->getFatTotal(); ?>" readonly style="height:26px; font-size:12px;">
                </div>
            </div>
        </div>

        <!-- سطر 2: الخصم والإضافي -->
        <div class="row no-gutters mb-1" style="gap:4px;">
            <div class="col">
                <div class="d-flex align-items-center" style="gap:2px;">
                    <label class="form-lbl" style="font-size:11px;">الخصم <span class="text-orange-600">(F6)</span>:</label>
                    <div class="d-flex flex-grow-1" style="gap:2px;">
                        <input id="headdisc_pct" name="headdisc_pct" type="number" 
                               class="form-control form-control-sm mid select-all hover:select-all nozero text-center px-1" 
                               placeholder="%" step="any"
                               value="<?php echo $this->getFatDiscPct(); ?>" style="width: 42%; height:26px; font-size:11px;">
                        <input id="headdisc" name="headdisc" type="number" 
                               class="form-control form-control-sm mid select-all hover:select-all nozero text-center px-1" 
                               placeholder="قيمة" step="any"
                               value="<?php echo $this->getFatDisc(); ?>" style="width: 58%; height:26px; font-size:11px;">
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="d-flex align-items-center" style="gap:2px;">
                    <label class="form-lbl" style="font-size:11px;">إضافي:</label>
                    <input id="headplus" name="headplus" type="text" 
                           class="form-control form-control-sm text-center p-0" 
                           value="<?php echo $this->getFatPlus(); ?>" style="height:26px; font-size:12px;">
                </div>
            </div>
        </div>

        <!-- سطر 3: الصافي النهائي -->
        <div class="hero-net-container">
            <span class="hero-net-label">الصافي النهائي:</span>
            <input id="headnet" name="headnet" type="text" 
                   class="form-control hero-net-input" readonly 
                   value="<?php echo $this->getProValue(); ?>">
        </div>
        <?php
    }

    /**
     * عرض معلومات الدفع
     */
    private function renderPayment()
    {
        // إخفاء جزء الدفع لأوامر الشراء (12) وأوامر البيع (13) وعروض الأسعار (14)
        $hidePayment = in_array($this->invoiceType, [12, 13, 14]);
        
        ?>
        <div class="card-header-bar">
            <span>السداد والخزينة</span>
            <span class="badge badge-light border text-muted" style="font-size:10px;">حركة النقدية</span>
        </div>

        <?php if ($hidePayment): ?>
            <div class="alert alert-info text-center py-2 mb-0" style="font-size:11.5px;">
                <i class="fas fa-info-circle"></i> لا يتطلب هذا المستند سداداً
            </div>
        <?php else: ?>
            <!-- المدفوع -->
            <div class="form-row-compact">
                <label class="form-lbl" style="width:72px;">المدفوع <span class="text-orange-600">(F7)</span></label>
                <input id="paid" name="paid" type="number" step="any"
                       class="form-control form-control-sm hero-paid-input last" 
                       value="<?php echo $this->getPaidAmount(); ?>">
            </div>
            
            <!-- الباقي -->
            <div class="form-row-compact">
                <label class="form-lbl" style="width:72px;">الباقي (الآجل)</label>
                <input id="change" type="text" class="form-control form-control-sm bg-light font-weight-bold text-danger text-center p-0" 
                       readonly value="0" style="height:26px; font-size:12px;">
            </div>
            
            <!-- الصندوق -->
            <div class="form-row-compact">
                <label class="form-lbl" style="width:72px;">الخزينة</label>
                <select name="fund_id" id="fund_id" class="form-control form-control-sm" style="height:26px; font-size:11.5px; padding: 2px 4px;">
                    <?php $this->renderFundOptions(); ?>
                </select>
            </div>
        <?php endif; ?>

        <!-- hidden inputs مطلوبة بـ doadd_invoice.php -->
        <input type="hidden" name="paid_cash"       id="paid_cash"       value="0">
        <input type="hidden" name="paid_bank"        id="paid_bank"        value="0">
        <input type="hidden" name="payment_fund_id"  id="payment_fund_id"  value="">
        <input type="hidden" name="payment_bank_id"  id="payment_bank_id"  value="0">

        <script>
        (function() {
            function syncPaymentFields(form) {
                var paid          = form.querySelector('#paid');
                var fundSelect    = form.querySelector('#fund_id');
                var paidCash      = form.querySelector('#paid_cash');
                var paymentFundId = form.querySelector('#payment_fund_id');
                if (paid && paidCash)            paidCash.value      = parseFloat(paid.value) || 0;
                if (fundSelect && paymentFundId) paymentFundId.value = fundSelect.value || 0;
            }
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('#submit, #submit2').forEach(function(btn) {
                    btn.addEventListener('click', function() {
                        var form = btn.closest('form');
                        if (form) syncPaymentFields(form);
                    });
                });
            });
        })();
        </script>
        <?php
    }

    /**
     * عرض أزرار التحكم
     */
    private function renderActionButtons()
    {
        $buttonClass = $this->getBackgroundClass();
        $saveText = $this->isEditMode ? 'تحديث' : 'حفظ';
        $isPurchase = in_array($this->invoiceType, [4, 10, 12]);
        ?>
        <div class="d-flex align-items-center" style="gap:6px;">
            <button id="submit2" class="btn <?php echo $buttonClass; ?> btn-sm text-white dis font-weight-bold d-flex align-items-center" 
                    type="submit" formtarget="_blank" name="submit" value="print" style="gap:4px; height:30px; font-size:12px; white-space:nowrap; padding: 4px 12px;">
                <i class="fas fa-print"></i> <?php echo $saveText; ?> وطباعة (F11)
            </button>
            
            <label for="printCashierCheck" class="mb-0 <?php echo $isPurchase ? 'd-none' : 'd-flex'; ?> align-items-center px-2 border rounded bg-white" style="gap:4px; white-space:nowrap; cursor:pointer; height:30px; font-size:11.5px;" title="طباعة إيصال كاشير بدل ورقة A4">
                <input type="checkbox" id="printCashierCheck">
                <span>كاشير</span>
            </label>
            <input type="hidden" name="print_cashier" id="printCashierField" value="0">
            
            <button id="submit" class="btn <?php echo $buttonClass; ?> btn-sm text-white dis font-weight-bold d-flex align-items-center" 
                    type="submit" name="submit" value="save" style="gap:4px; height:30px; font-size:12px; white-space:nowrap; padding: 4px 16px;">
                <i class="fas fa-save"></i> <?php echo $saveText; ?> (F12)
            </button>
        </div>
        <script>
        (function() {
            var COOKIE = 'invoice_print_cashier';
            function readCookie() {
                var m = document.cookie.match(/(?:^|; )invoice_print_cashier=([^;]*)/);
                return m ? decodeURIComponent(m[1]) : '0';
            }
            function writeCookie(value) {
                var exp = new Date();
                exp.setFullYear(exp.getFullYear() + 1);
                document.cookie = COOKIE + '=' + encodeURIComponent(value) + '; expires=' + exp.toUTCString() + '; path=/; SameSite=Lax';
            }
            function applyLinks(on) {
                document.querySelectorAll('.invoice-print-link').forEach(function(a) {
                    var href = on ? a.getAttribute('data-cashier') : a.getAttribute('data-a4');
                    if (href) a.setAttribute('href', href);
                });
            }
            document.addEventListener('DOMContentLoaded', function() {
                var cb = document.getElementById('printCashierCheck');
                var field = document.getElementById('printCashierField');
                if (!cb) return;
                var on = readCookie() === '1';
                cb.checked = on;
                if (field) field.value = on ? '1' : '0';
                applyLinks(on);
                cb.addEventListener('change', function() {
                    var v = cb.checked ? '1' : '0';
                    writeCookie(v);
                    if (field) field.value = v;
                    applyLinks(cb.checked);
                });
            });
        })();
        </script>
        <?php
    }

    /**
     * عرض خيارات الصناديق
     */
    private function renderFundOptions()
    {
        foreach ($this->funds as $fund) {
            $selected = $this->getDefaultSelected('def_fund', $fund['id']);
            if ($this->isEditMode && $this->data && $this->data['fund_id'] == $fund['id']) {
                $selected = 'selected';
            }
            echo "<option value='{$fund['id']}' {$selected}>{$this->sanitizeInput($fund['aname'])}</option>";
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
     * التحقق من صحة البيانات
     */
    public function validate()
    {
        $errors = [];

        // التحقق من الصندوق
        if (empty($_POST['fund_id'])) {
            $errors[] = 'يجب اختيار الصندوق';
        }

        // التحقق من المبلغ المدفوع
        $paid = floatval($_POST['paid'] ?? 0);
        if ($paid < 0) {
            $errors[] = 'المبلغ المدفوع لا يمكن أن يكون سالباً';
        }

        // التحقق من الخصم
        $discount = floatval($_POST['headdisc'] ?? 0);
        if ($discount < 0) {
            $errors[] = 'الخصم لا يمكن أن يكون سالباً';
        }

        return $errors;
    }

    // دوال مساعدة للحصول على القيم
    private function getFatTotal()
    {
        return $this->isEditMode && $this->data ? $this->data['fat_total'] : '0';
    }

    private function getTotalQty()
    {
        if ($this->isEditMode && $this->data && $this->conn) {
            $id = intval($this->data['id']);
            $res = $this->conn->query("SELECT COALESCE(SUM(ABS(qty_in - qty_out) / u_val), 0) as total_qty FROM fat_details WHERE fatid = $id AND isdeleted = 0");
            if ($res && $row = $res->fetch_assoc()) {
                return round($row['total_qty'], 2);
            }
        }
        return '0';
    }

    private function getFatDisc()
    {
        return $this->isEditMode && $this->data ? $this->data['fat_disc'] : '0';
    }

    private function getFatDiscPct()
    {
        return $this->isEditMode && $this->data && isset($this->data['fat_disc_per']) ? $this->data['fat_disc_per'] : '0';
    }

    private function getFatPlus()
    {
        return $this->isEditMode && $this->data ? $this->data['fat_plus'] : '0';
    }

    private function getProValue()
    {
        return $this->isEditMode && $this->data ? $this->data['pro_value'] : '0';
    }

    private function getPaidAmount()
    {
        if ($this->isEditMode && $this->data) {
            try {
                $invoiceId = intval($this->data['id']);
                $query = "SELECT COALESCE(SUM(pro_value), 0) as paid FROM ot_head WHERE op2 = ? AND (pro_tybe = 1 OR pro_tybe = 2) AND isdeleted = 0";
                $result = $this->executeSecureQuery($query, [$invoiceId], 'i');
                $row = $result->fetch_assoc();
                return $row ? floatval($row['paid']) : 0;
            } catch (Exception $e) {
                return 0;
            }
        }
        return 0;
    }

    private function getInfo()
    {
        return $this->isEditMode && $this->data ? $this->sanitizeInput($this->data['info'] ?? '') : '';
    }
}