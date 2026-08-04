<div class="row full bg--200 border">
  
<input type="text" name="pro_tybe" hidden value="<?php if (isset($_GET)) {
                    if (isset($_GET['q'])) {
                        if ($_GET['q'] == "sale") {$pro_tybe = '4';echo $pro_tybe;
                        }elseif ($_GET['q']== "buy") {$pro_tybe = '3';echo $pro_tybe;
                        }elseif ($_GET['q']== "resale"){$pro_tybe = '10';echo $pro_tybe;
                        }elseif ($_GET['q']== "rebuy"){$pro_tybe = '11';echo $pro_tybe;
                        }elseif ($_GET['q']== "po"){$pro_tybe = '12';echo $pro_tybe;
                        }elseif ($_GET['q']== "so"){$pro_tybe = '13';echo $pro_tybe;
                        }
                      }
                }elseif($_GET['e']){$pro_tybe =$rowop['pro_tybe'];}else{
                    $pro_tybe =0; 
                }  ?>
                ">
                <?php 
                if ($pro_tybe == 0) {
                    echo "<h1> يبدو انك دخلت الفاتورة من مكان غير المخصص</h2>";
                    die;
                }
                ?>
<div class="col-md-3">

<div class="row">
                    <div class="col bg-light"> الكمية </div>
                    <div class="col border border-light">
                    <h6 id="storeqty"></h6> 
                </div>
            </div>

            
            <div class="row">
                    <div class="col bg-light"> سعر البيع</div>


                    <div class="col border border-light" id="cost_price_div">
                    <h6 id="price1" class=""></h6>
                </div>

            </div>
            <div class="row">
                    <div class="col bg-light"> سعر السوق</div>
                    <div class="col border border-light" id="cost_price_div">
                    <h6 id="market_price" class=""></h6>
                </div>
            </div>


                </div>
                <div class="col-md-3">

              <div class="row">
                    <div class="col bg-light"> سعر الشراءالمتوسط</div>
                    <div class="col border border-light" id="cost_price_div">
                    <h6 id="cost_price" class="text-white hover:bg-slate-400"></h6>
                </div>
            </div>


            
            <div class="row">
                    <div class="col bg-light"> سعر الشراءالاخير</div>
                    <div class="col border border-light" id="">
                   <h6 id="last_price" class="text-white hover:bg-slate-400" ></h6>
                </div>
            </div>

</div>
<div class="col-md-3">
<div class="row">
                            <div class="col col-md-4">
                                <label for="">إجمالي الكميات</label>
                            </div>
                                <div class="col-md-8">
                                <input id="headqty" name="headqty" type="text" class="form-control form-control-sm bg-light" readonly value="0">
                            </div>
                            </div>
<div class="row">
                            <div class="col col-md-4">
                                <label for="">الاجمالي</label>
                            </div>
                                <div class="col-md-8 ">
                                    
                                <input
                                id="headtotal" name="headtotal"  type="text" class="form-control form-control-sm" value="<?php if(isset($_GET['e'])){echo $rowedit['fat_total'];}?>" readonly >
                            </div>
                            </div>
                            <div class="row">
                                <div class="col col-md-4">
                            <label for="">الخصم<span class="text-orange-600">(F6)</span></label>
                            </div>
                                <div class="col ">
                                    <div style="display:flex; gap:4px;">
                                        <input id="headdisc_pct" name="headdisc_pct" type="number" 
                                               class="form-control form-control-sm mid select-all hover:select-all nozero" 
                                               placeholder="%" step="0.01" min="0" max="99"
                                               value="<?php echo isset($_GET['e']) && isset($rowedit['fat_disc_per']) ? $rowedit['fat_disc_per'] : 0; ?>" style="width: 40%;">
                                        <input id="headdisc" name="headdisc" type="number" 
                                               class="form-control form-control-sm mid select-all hover:select-all nozero" 
                                               placeholder="القيمة" step="0.01"
                                               value="<?php if(isset($_GET['e'])){echo $rowedit['fat_disc'];}else {echo 0;}?>" style="width: 60%;">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                            <div class="col col-md-4">
                            <label for="">الاضافي</label>
                            </div>
                                <div class="col "><input id="headplus" name="headplus" type="text" class="form-control form-control-sm " value="<?php if(isset($_GET['e'])){echo $rowedit['fat_plus'];}else {echo 0;}?>" ></div>
                            </div>
                            <div class="row">
                            <div class="col col-md-4">
                                
                            <label for=""  >الصافي</label>
                            </div>
                                <div class="col-md-8"><input 
                                id="headnet" name="headnet" type="text" class="form-control"  readonly style="font-size:30px;" value="<?php if(isset($_GET['e'])){echo $rowedit['pro_value'];}else {echo 0;}?>"></div>
                            </div>

</div>
<div class="col-md-3">
<?php 
// إخفاء جزء الدفع لأوامر الشراء والبيع وعروض الأسعار
// Debug: عرض قيمة pro_tybe
echo "<!-- pro_tybe = " . (isset($pro_tybe) ? $pro_tybe : 'NOT SET') . " -->";
$hide_payment = (isset($pro_tybe) && (intval($pro_tybe) == 12 || intval($pro_tybe) == 13 || intval($pro_tybe) == 14));
echo "<!-- hide_payment = " . ($hide_payment ? 'true' : 'false') . " -->";
if (!$hide_payment): 
?>
                            <div class="row">
                              <?php
                              if(isset($_GET['e'])){
                              $rowpaid = $conn->query("SELECT * FROM ot_head where op2 = $opid AND pro_tybe = 1 or pro_tybe = 2 ")->fetch_assoc();
                              if (isset($rowpaid)) {
                                $paid = $rowpaid['pro_value'];
                              }else {
                                $paid = 0.00;
                              }
                              }?>





                              <div class="col-md-4">
                              <label for=""  >المدفوع <span class="text-orange-600">(F7)</span></label>
                              </div>
                                <div class="col-md-8">
                                    <input id="paid" name="paid" type="number" class="form-control form-control-lg bg-light last" style="font-size:30px;" value="<?php if(isset($_GET['e'])){echo $paid ;}else{echo 0;} ?>" >
                                </div>
                                </div>

                                <div class="row">
                              <div class="col-md-4">
                              <label for=""  >الباقي</label>
                              </div>
                                <div class="col-md-8">
                                    <input id="change" type="text" class="form-control form-control-sm" id=""  readonly value="0.00">
                                </div>
                            </div>
                            
                            
                            <div class="row">
                        <div class="col col-4">
                          <label for="">الصندوق</label>
                        </div>    
                         <div class="col col-md-8">
                                <select name="fund_id" id="fund_id" class="form-control form-control-sm">
                                    <?php
                            $resfund = $conn->query("SELECT * FROM `acc_head` WHERE is_fund =1;");
                            while ($rowfund = $resfund->fetch_assoc()) { ?>
                            <option
                            <?php  echo ($conn->query("SELECT cur_value FROM myoptions WHERE oname = 'def_fund'")->fetch_assoc()['cur_value'] == $rowfund['id']) ? " selected " : ""; ?>
                            value="<?= $rowfund['id'] ?>"><?= $rowfund['aname'] ?></option>
                            <?php } ?>
                        
                        
                                </select>
                            </div>
                            </div>

                            <!-- hidden inputs مطلوبة بـ doadd_invoice.php للدفع الكاش -->
                            <input type="hidden" name="paid_cash" id="paid_cash" value="0">
                            <input type="hidden" name="paid_bank" id="paid_bank" value="0">
                            <input type="hidden" name="payment_fund_id" id="payment_fund_id" value="">
                            <input type="hidden" name="payment_bank_id" id="payment_bank_id" value="0">

<?php endif; // نهاية إخفاء جزء الدفع ?>
                            
                          </div>
</div>




<script>
// قبل الـ submit، انقل قيمة paid و fund_id للـ hidden inputs المطلوبة بـ doadd_invoice.php
(function() {
    function syncPaymentFields(form) {
        var paidInput = form.querySelector('#paid');
        var fundSelect = form.querySelector('#fund_id');
        var paidCash = form.querySelector('#paid_cash');
        var paymentFundId = form.querySelector('#payment_fund_id');
        if (paidInput && paidCash) {
            paidCash.value = parseFloat(paidInput.value) || 0;
        }
        if (fundSelect && paymentFundId) {
            paymentFundId.value = fundSelect.value || 0;
        }
    }

    // التحقق من أن المدفوع مكتمل لو المورد افتراضي
    function validatePayment(form) {
        var supplierSelect = document.getElementById('mySelectEmp');
        if (!supplierSelect) return true; // مفيش مورد → اعمل submit عادي

        // الـ option الأول في القائمة هو المورد الافتراضي
        var firstOption = supplierSelect.options[0];
        if (!firstOption) return true;

        var selectedVal = supplierSelect.value;
        var defaultVal  = firstOption.value;

        // لو المختار مش المورد الافتراضي → مفيش قيود
        if (selectedVal !== defaultVal) return true;

        var paid = parseFloat(document.getElementById('paid') ? document.getElementById('paid').value : 0) || 0;
        var net  = parseFloat(document.getElementById('headnet') ? document.getElementById('headnet').value : 0) || 0;

        if (paid < net) {
            Swal.fire({
                icon: 'warning',
                title: 'تنبيه',
                text: 'المورد الافتراضي لا يقبل الآجل، يجب أن يكون المدفوع مساوياً للصافي (' + net + ')',
                confirmButtonText: 'حسناً'
            }).then(function() {
                // انقل التركيز لحقل المدفوع
                var paidField = document.getElementById('paid');
                if (paidField) { paidField.focus(); paidField.select(); }
            });
            return false;
        }

        return true;
    }

    // تحديث المدفوع تلقائياً لو المورد افتراضي
    window.syncPaidIfDefault = function() {
        var supplierSelect = document.getElementById('mySelectEmp');
        if (!supplierSelect) return;
        var firstOption = supplierSelect.options[0];
        if (!firstOption) return;
        if (supplierSelect.value !== firstOption.value) return;

        var net = parseFloat(document.getElementById('headnet') ? document.getElementById('headnet').value : 0) || 0;
        var paidField = document.getElementById('paid');
        var changeField = document.getElementById('change');
        if (paidField) paidField.value = net.toFixed(2);
        if (changeField) changeField.value = '0.00';
    };

    document.addEventListener('DOMContentLoaded', function() {
        // عند تغيير المورد
        var supplierSelect = document.getElementById('mySelectEmp');
        if (supplierSelect) {
            supplierSelect.addEventListener('change', function() {
                syncPaidIfDefault();
            });
        }

        // عند تغيير الصافي (headnet) - لو المورد افتراضي يحدث المدفوع
        var headnetField = document.getElementById('headnet');
        if (headnetField) {
            var observer = new MutationObserver(function() { syncPaidIfDefault(); });
            observer.observe(headnetField, { attributes: true, attributeFilter: ['value'] });
            headnetField.addEventListener('input', syncPaidIfDefault);
            headnetField.addEventListener('change', syncPaidIfDefault);
        }

        // تشغيل عند التحميل
        setTimeout(syncPaidIfDefault, 600);

        var submitBtns = document.querySelectorAll('#submit, #submit2');
        submitBtns.forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                var form = btn.closest('form');
                if (!validatePayment(form)) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    return false;
                }
                if (form) syncPaymentFields(form);
            });
        });

        // تأمين إضافي: منع submit الفورم نفسه لو لم يمر على validation
        var form = document.getElementById('myForm2');
        if (form) {
            form.addEventListener('submit', function(e) {
                if (!validatePayment(form)) {
                    e.preventDefault();
                    return false;
                }
            });
        }
    });
})();
</script>

<div class="row">
                            <div class=" col-md-4">
                              <button id="submit" class="btn <?php if(isset($_GET['q'])){echo  'bg-teal-500';}else{echo 'bg-red-500';}?> btn-block btn-lg dis" type="submit" name="submit" value="save" onclick="this.form.submit_action.value='save';">حفظ (F12) </button>
                              <button id="submit2" class="btn <?php if(isset($_GET['q'])){echo  'bg-teal-500';}else{echo 'bg-red-500';}?> btn-block btn-lg dis" type="submit" formtarget="_blank" name="submit" value="print" onclick="this.form.submit_action.value='print';">حفظ و طباعه (F11) </button>
                              <input type="hidden" name="submit_action" value="save">
                          </div>                            
                        <div class="col-md-6">
                        <input type="text" class="form-control bg-orange-300" name="info" id="info" placeholder="ملاحظات">
                        </div>
                        <div class="col-md-2" id="showOps">
                            <div class="btn" >اظهار الفواتير السابقة</div>
                        </div>
        </div>