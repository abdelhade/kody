<?php 
include('includes/header.php');
include('includes/navbar.php');
include('includes/sidebar.php');
?>

<style>
    .form-hors {
        width: 100%;
        padding: 6px 12px;
        margin: 0px;
        border: 1px solid #ced4da;
        border-radius: 4px;
        background-color: #fff;
    }
    .form-hors:focus {
        border-color: #80bdff;
        outline: 0;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
</style>

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">

    <div class="card card-primary shadow">
        <div class="card-header bg-gradient-primary">
            <h2 class="card-title font-weight-bold m-0">
                <i class="fas fa-plus-circle me-2"></i> إضافة حركة خصومات / إضافيات جديدة
            </h2>
        </div>
        <div class="card-body">
            <form action="do/doadd_financial_transaction.php" method="post">
            
            <div class="table">
                <div class="row mb-4">
                    <div class="form-group col-md-3">
                        <label for="date" class="font-weight-bold">التاريخ</label>
                        <input type="date" name="date" id="date" class="form-control" required value="<?= date('Y-m-d');?>">
                    </div>
                    
                    <input type="hidden" value="<?php                          
                        $rowprod = $conn->query("SELECT MAX(snd_id) as max_id FROM financial_transactions")->fetch_assoc();
                        $next_id = ($rowprod['max_id'] == null) ? 1 : $rowprod['max_id'] + 1;
                        echo $next_id;
                        ?>" name="snd_id">
                </div>
                
                <table class="table table-bordered table-striped text-center align-middle" id="transactionsTable">
                    <thead class="bg-light text-secondary">
                        <tr>
                            <th style="width: 5%;">م</th>
                            <th style="width: 25%;">اسم الموظف</th>
                            <th style="width: 15%;">النوع</th>
                            <th style="width: 15%;">المبلغ</th>
                            <th style="width: 20%;">السبب (مطلوب)</th>
                            <th style="width: 15%;">ملاحظات</th>
                            <th style="width: 5%;">حذف</th>
                        </tr>
                    </thead>
                    <tbody id="empRow">
                        <tr>
                            <td class="mslsl font-weight-bold">1</td>
                            <td>
                                <select autofocus name="emp_name[]" class="form-hors select2-emp" required>
                                    <option value="">— اختر الموظف —</option>
                                    <?php
                                    $resemp = $conn->query("SELECT * FROM `employees` where isdeleted = 0 order by name");
                                    while($rowemp = $resemp->fetch_assoc()){
                                    ?>
                                    <option value="<?= htmlspecialchars($rowemp['name'])?>"><?= htmlspecialchars($rowemp['name'])?></option>
                                    <?php }?>
                                </select>
                            </td>
                            <td>
                                <select name="type[]" class="form-hors" required>
                                    <option value="1">إضافي / مكافأة</option>
                                    <option value="0">خصم فلوس</option>
                                </select>
                            </td>
                            <td>
                                <input type="number" class="form-hors amount" step="0.01" min="0.01" value="0.00" name="amount[]" required placeholder="0.00">
                            </td>
                            <td>
                                <input type="text" class="form-hors reason" name="reason[]" required placeholder="السبب التفصيلي">
                            </td>
                            <td>
                                <input type="text" class="form-hors notes" name="notes[]" placeholder="ملاحظات اختيارية">
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm delete-row shadow-sm">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                
                <div class="row mt-4">
                    <div class="col-md-2">
                        <button id="addRow" class="btn btn-outline-primary shadow-sm btn-block" type="button">
                            <i class="fas fa-plus"></i> إضافة سطر
                        </button>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-success shadow-sm btn-block">
                            <i class="fas fa-check"></i> تأكيد وحفظ الحركة
                        </button>
                    </div>
                    <div class="col-md-6"></div>
                </div>
            </div>    
            
            </form>
        </div>
    </div>

    </div>
  </section>
</div>

<script>
$(document).ready(function() {
    // إضافة سطر جديد
    $('#addRow').click(function() {
        var $table = $('#empRow');
        var $firstRow = $table.find('tr:first');
        var $mslsl = $table.find('.mslsl:last');
        var $newRow = $firstRow.clone();
        
        // إعادة تعيين الحقول
        $newRow.find('select').val('');
        $newRow.find('input').val('');
        $newRow.find('.amount').val('0.00');
        $newRow.find('.mslsl').html(Number($mslsl.html()) + 1);
        
        $table.append($newRow);
    });

    // حذف السطر
    $('#empRow').on('click', '.delete-row', function() {
        var $row = $(this).closest('tr');
        if ($('#empRow tr').length > 1) {
            $row.remove();
            // إعادة ترتيب المسلسل
            $('#empRow tr').each(function(index) {
                $(this).find('.mslsl').html(index + 1);
            });
        } else {
            alert('يجب الإبقاء على سطر واحد على الأقل.');
        }
    });
});
</script>

<?php include('includes/footer.php');?>
