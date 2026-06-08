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

    <?php
    $id = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
    $sql = "SELECT * FROM `financial_transactions` WHERE snd_id = '$id'";
    $result = $conn->query($sql);
    if ($result->num_rows === 0) {
        echo "<div class='alert alert-danger'>الحركة غير موجودة أو تم حذفها.</div>";
        include('includes/footer.php');
        exit;
    }
    // جلب أول سطر لمعرفة التاريخ العام للمجموعة
    $first_row = $conn->query("SELECT * FROM `financial_transactions` WHERE snd_id = '$id' LIMIT 1")->fetch_assoc();
    ?>

    <div class="card card-warning shadow">
        <div class="card-header bg-gradient-warning text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="card-title font-weight-bold m-0">
                    <i class="fas fa-edit me-2"></i> تعديل حركة الخصومات / الإضافيات
                </h2>
                <div>
                    <a href="financial_transactions.php" class="btn btn-light btn-sm shadow-sm text-warning font-weight-bold">
                        <i class="fas fa-list"></i> قائمة الحركات
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <span class="badge badge-secondary" style="font-size: 1.1rem; padding: 8px 12px;">رقم الحركة: #<?= $id ?></span>
                </div>
                <button type="button" class="btn btn-danger shadow-sm" data-toggle="modal" data-target="#deleteTransactionModal">
                    <i class="fa fa-trash"></i> حذف هذه الحركة بالكامل
                </button>
            </div>

            <!-- مودال الحذف -->
            <div class="modal fade" id="deleteTransactionModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title"><i class="fas fa-exclamation-triangle"></i> تأكيد الحذف</h5>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p class="font-weight-bold">هل أنت متأكد من حذف هذه الحركة (بجميع أسطر الموظفين المرفقة بها)؟</p>
                            <p class="text-muted small">هذا الإجراء لا يمكن التراجع عنه وسيلغي أثرها من الحسابات.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                            <a href="do/dodel_financial_transaction.php?id=<?= $id ?>" class="btn btn-danger">تأكيد الحذف</a>
                        </div>
                    </div>
                </div>
            </div>

            <form action="do/doedit_financial_transaction.php?edit=<?= $id ?>" method="post">
            
            <div class="table">
                <div class="row mb-4">
                    <div class="form-group col-md-3">
                        <label for="date" class="font-weight-bold">التاريخ</label>
                        <input type="date" name="date" id="date" class="form-control" required value="<?= $first_row['date'] ?>">
                    </div>
                    
                    <input type="hidden" name="snd_id" value="<?= $id ?>">
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
                        <?php
                        $x = 0;
                        $respro = $conn->query("SELECT * FROM `financial_transactions` WHERE snd_id = '$id' ORDER BY id ASC");
                        while($rowpro = $respro->fetch_assoc()){
                            $x++;
                        ?>
                        <tr>
                            <td class="mslsl font-weight-bold"><?= $x ?></td>
                            <td>
                                <select autofocus name="emp_name[]" class="form-hors" required>
                                    <option value="">— اختر الموظف —</option>
                                    <?php
                                    $resemp = $conn->query("SELECT * FROM `employees` where isdeleted = 0 order by name");
                                    while($rowemp = $resemp->fetch_assoc()){
                                        $selected = ($rowemp['name'] == $rowpro['emp_name']) ? 'selected' : '';
                                    ?>
                                    <option <?= $selected ?> value="<?= htmlspecialchars($rowemp['name'])?>"><?= htmlspecialchars($rowemp['name'])?></option>
                                    <?php }?>
                                </select>
                            </td>
                            <td>
                                <select name="type[]" class="form-hors" required>
                                    <option value="1" <?= $rowpro['type'] == 1 ? 'selected' : '' ?>>إضافي / مكافأة</option>
                                    <option value="0" <?= $rowpro['type'] == 0 ? 'selected' : '' ?>>خصم فلوس</option>
                                </select>
                            </td>
                            <td>
                                <input type="number" class="form-hors amount" step="0.01" min="0.01" value="<?= $rowpro['amount'] ?>" name="amount[]" required placeholder="0.00">
                            </td>
                            <td>
                                <input type="text" class="form-hors reason" value="<?= htmlspecialchars($rowpro['reason']) ?>" name="reason[]" required placeholder="السبب التفصيلي">
                            </td>
                            <td>
                                <input type="text" class="form-hors notes" value="<?= htmlspecialchars($rowpro['notes'] ?? '') ?>" name="notes[]" placeholder="ملاحظات اختيارية">
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm delete-row shadow-sm">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </td>
                        </tr>
                        <?php } ?>
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
                            <i class="fas fa-check"></i> حفظ التعديلات
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
