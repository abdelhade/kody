
<div class="row frst-row bg--200">
      <div class="col-lg-2">
        <div class="tool">
      <label for=""><?php if ($pro_tybe == '4' OR $pro_tybe == '10' OR $pro_tybe == '12') {
                                echo 'المورد<button type="button" class="btn bg-lime-200 btn-sm" data-toggle="modal" data-target="#addSupplierModal">+</button>';
                            }elseif ($pro_tybe == '3' OR $pro_tybe == '11' OR $pro_tybe == '13') {
                                echo 'العميل<button type="button" class="btn bg-lime-200 btn-sm" data-toggle="modal" data-target="#addClientModal">+</button>';
                           }?>
                            </label>
                            <div class="tooltext">
                              اضافه جديد
                            </div>
                            </div>
                            <select class="select2 form-control form-control-sm" name="acc2_id" id="mySelectEmp">
                            <?php
                            if ($pro_tybe == '4' OR $pro_tybe == '11' OR $pro_tybe == '12') {
                                $resclients = $conn->query("SELECT * FROM `acc_head` WHERE code like '211%'  AND is_basic = 0 AND isdeleted = 0 order by id ;");
                            }elseif ($pro_tybe == '3' OR $pro_tybe == '10' OR $pro_tybe == '13') {
                                $resclients = $conn->query("SELECT * FROM `acc_head` WHERE code like '122%'  AND is_basic = 0 AND isdeleted = 0 order by id ;");
                            }


                            while ($rowclients = $resclients->fetch_assoc()) { ?>
                            <option 
                            
                             <?php  
                            //  echo ($conn->query("SELECT cur_value FROM myoptions WHERE oname = 'def_cl'")->fetch_assoc()['cur_value'] == $rowclients['id']) ? " selected " : ""; 
                             ?>
                  

                             <?php if (isset($_GET['e']) && $rowedit['acc2'] == $rowclients['id'] ){echo "selected";}?>

                            
                             value="<?= $rowclients['id'] ?>"><?= $rowclients['aname'] ?></option>
                            <?php } ?>
                        </select>
      </div>




      <div class="col-md-2">
      <label for="">المخزن</label>
                         
                         <select name="store_id" class="form-control form-control-sm" id="">
                             <?php
                     $resstore = $conn->query("SELECT * FROM `acc_head` WHERE is_stock =1;");
                     while ($rowstore = $resstore->fetch_assoc()) { ?>
                     <option
                     <?php  echo ($conn->query("SELECT cur_value FROM myoptions WHERE oname = 'def_store'")->fetch_assoc()['cur_value'] == $rowstore['id']) ? " selected " : ""; ?>
                     <?php if (isset($_GET['e']) && $rowedit['store_id'] == $rowstore['id'] ){echo " selected ";}?>

                     value="<?= $rowstore['id'] ?>"><?= $rowstore['aname'] ?></option>
                     <?php } ?>
                         </select>
      </div>
      <div class="col-md-2">
        
      <label for="">الموظف</label>
                        <select class="form-control form-control-sm" name="emp_id" id="">
                        <?php
                            $resemp = $conn->query("SELECT * FROM `acc_head` WHERE parent_id = 35 AND is_basic = 0;");
                            while ($rowemp = $resemp->fetch_assoc()) { ?>
                            <option
                            <?php  echo ($conn->query("SELECT cur_value FROM myoptions WHERE oname = 'def_emp'")->fetch_assoc()['cur_value'] == $rowemp['id']) ? " selected " : ""; ?>
                            <?php if (isset($_GET['e']) && $rowedit['emp_id'] == $rowemp['id'] ){echo "selected";}?>

                            value="<?= $rowemp['id'] ?>"><?= $rowemp['aname'] ?></option>
                            <?php } ?>
                        </select>
                            </div>

                              <div class="col-md-2">
                                <label for="">التاريخ</label>
                                <input type="date" class="form-control bg-secondary" name="pro_date" id="pro_date" value="<?php if(isset($_GET['e'])){echo $rowedit['pro_date'];}else {echo date('Y-m-d');}?>">
                              </div>
                              <div class="col-md-2">
                              <label for="">تاريخ الاستحقاق</label><input type="date" class="form-control" name="accural_date" id="" value="<?php if(isset($_GET['e'])){echo $rowedit['accural_date'];}?>">
                              </div>


                              <div class="col-md-1">
                              <label for="">رقم الفاتورة</label>
                          <input name="pro_id"  type="text" class="form-control form-control-sm" 
                          value="<?php if(isset($_GET['e'])){echo $rowedit['pro_id'];}else{
                            $resnum = $conn->query("SELECT * FROM ot_head where pro_tybe = $pro_tybe");
                            $rownum = $resnum->fetch_assoc();

                            
                            }?>"  readonly>
                              </div>
                              <div class="col-md-1">
                              <label for="">S.N</label>
                                <input type="text"  name="pro_serial" class="form-control form-control-sm" placeholder="" value="<?php if(isset($_GET['e'])){echo $rowedit['pro_serial'];}?>">
                              </div>
                            </div>

<!-- Modal إضافة مورد -->
<div class="modal fade" id="addSupplierModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title"><i class="fas fa-user-plus ml-2"></i> إضافة مورد جديد</h5>
        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body">
        <div id="addSupplierMsg"></div>
        <form id="addSupplierForm">
          <input type="hidden" name="parent_code" value="211">
          <div class="form-group">
            <label class="small">اسم المورد <span class="text-danger">*</span></label>
            <input type="text" name="aname" id="supplierName" class="form-control form-control-sm" required placeholder="اسم المورد">
          </div>
          <div class="row">
            <div class="col-md-6 form-group">
              <label class="small">رقم الهاتف</label>
              <input type="text" name="phone" class="form-control form-control-sm" placeholder="اختياري">
            </div>
            <div class="col-md-6 form-group">
              <label class="small">العنوان</label>
              <input type="text" name="address" class="form-control form-control-sm" placeholder="اختياري">
            </div>
          </div>
          <div class="form-group">
            <label class="small">ملاحظات</label>
            <input type="text" name="info" class="form-control form-control-sm" placeholder="اختياري">
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">إلغاء</button>
        <button type="button" class="btn btn-success btn-sm" id="saveSupplierBtn">
          <i class="fas fa-save ml-1"></i> حفظ المورد
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Modal إضافة عميل -->
<div class="modal fade" id="addClientModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title"><i class="fas fa-user-plus ml-2"></i> إضافة عميل جديد</h5>
        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body">
        <div id="addClientMsg"></div>
        <form id="addClientForm">
          <input type="hidden" name="parent_code" value="122">
          <div class="form-group">
            <label class="small">اسم العميل <span class="text-danger">*</span></label>
            <input type="text" name="aname" id="clientName" class="form-control form-control-sm" required placeholder="اسم العميل">
          </div>
          <div class="row">
            <div class="col-md-6 form-group">
              <label class="small">رقم الهاتف</label>
              <input type="text" name="phone" class="form-control form-control-sm" placeholder="اختياري">
            </div>
            <div class="col-md-6 form-group">
              <label class="small">العنوان</label>
              <input type="text" name="address" class="form-control form-control-sm" placeholder="اختياري">
            </div>
          </div>
          <div class="form-group">
            <label class="small">ملاحظات</label>
            <input type="text" name="info" class="form-control form-control-sm" placeholder="اختياري">
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">إلغاء</button>
        <button type="button" class="btn btn-primary btn-sm" id="saveClientBtn">
          <i class="fas fa-save ml-1"></i> حفظ العميل
        </button>
      </div>
    </div>
  </div>
</div>

<script>
(function() {
  // حفظ مورد أو عميل عبر AJAX وإضافته للقائمة
  function saveAccount(formId, btnId, msgId, modalId, selectId) {
    document.getElementById(btnId).addEventListener('click', function() {
      const $btn = $(this);
      const form = document.getElementById(formId);
      const nameInput = form.querySelector('[name="aname"]');
      if (!nameInput.value.trim()) {
        document.getElementById(msgId).innerHTML = '<div class="alert alert-danger py-1 mb-2">الاسم مطلوب</div>';
        return;
      }
      $btn.prop('disabled', true).text('جاري الحفظ...');
      const formData = new FormData(form);
      
      fetch('ajax/modal_add_account.php', {
        method: 'POST',
        body: formData
      })
      .then(r => r.json())
      .then(res => {
        if (res.success) {
          document.getElementById(msgId).innerHTML = '<div class="alert alert-success py-1 mb-2">✓ تم الحفظ: <strong>' + res.aname + '</strong></div>';
          // إضافة الخيار للقائمة واختياره
          const select = document.getElementById(selectId);
          const opt = new Option(res.aname, res.id, true, true);
          select.appendChild(opt);
          $(select).trigger('change');
          form.reset();
          setTimeout(() => $(modalId).modal('hide'), 700);
        } else {
          document.getElementById(msgId).innerHTML = '<div class="alert alert-danger py-1 mb-2">' + (res.error || 'حدث خطأ') + '</div>';
        }
      })
      .catch(() => {
        document.getElementById(msgId).innerHTML = '<div class="alert alert-danger py-1 mb-2">خطأ في الاتصال</div>';
      })
      .finally(() => {
        $btn.prop('disabled', false).html('<i class="fas fa-save ml-1"></i> حفظ');
      });
    });
  }

  saveAccount('addSupplierForm', 'saveSupplierBtn', 'addSupplierMsg', '#addSupplierModal', 'mySelectEmp');
  saveAccount('addClientForm',   'saveClientBtn',   'addClientMsg',   '#addClientModal',   'mySelectEmp');

  // مسح الرسائل عند إغلاق المودال
  $('#addSupplierModal, #addClientModal').on('hidden.bs.modal', function() {
    $(this).find('[id$="Msg"]').html('');
  });
})();
</script>
