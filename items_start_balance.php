<?php include('includes/header.php') ?>
<?php include('includes/navbar.php') ?>
<?php include('includes/sidebar.php') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                <div class="row">
                    <div class="col">
                <h2 class="hadi-fade-in2 bg-zinc-200">ضبط الارصدة الافتتاحية للمحازن</h2>
                    </div>
                    <div class="col text-left" >
                    <ul style="float: left;">
                    <li>
                                <button class="btn bg-red-400 ">تصفير الرصيد الافتتاحي</button>
                                <button class="btn bg-yellow-400">تعديل بضاعه اول المدة</button>
                                <button class="btn bg-green-400">حفظ</button>
                        </li>
                    </ul>    
                    </div>
                </div>    
            </div>






            <div class="card-body">
                <div class="table table-responsive table-stripped" id="horsTable">
                    <table class="table" id="myTable" data-page-length="50">
                        <thead>
                            <tr class="bg-gray-300">
                                <th>#</th>
                                <th>كود الصنف</th>
                                <th>اسم الصنف</th>
                                <th>الوحده</th>
                                <th>رصيد اول المدة الجديد</th>
                                <th>رصيد اول المدة الحالي</th>
                                <th>سعر اول المدة الجديد</th>
                                <th>سعر اول المدة الحالي</th>
                                <th>التسوية</th>   
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT * FROM myitems WHERE isdeleted = 0 ORDER BY iname";
                            $result = $conn->query($sql);
                            $count = 0;
                            while ($row = $result->fetch_assoc()) {
                                $itmid = $row['id'];
                                
                                // جلب اسم الوحدة
                                $unit_result = $conn->query("SELECT uname FROM myunits WHERE id = (SELECT unit_id FROM item_units WHERE item_id = $itmid)");
                                $unit_name = '';
                                if ($unit_result && $unit_result->num_rows > 0) {
                                    $unit_row = $unit_result->fetch_assoc();
                                    $unit_name = $unit_row['uname'];
                                }
                                
                                // الرصيد الحالي من جدول myitems
                                $current_qty = isset($row['itmqty']) ? floatval($row['itmqty']) : 0;
                                $current_price = isset($row['cost_price']) ? floatval($row['cost_price']) : 0;
                                
                                $count++;
                            ?>
                            <tr>
                                <td><?= $count ?></td>
                                <td><?= htmlspecialchars($row['code']) ?></td>
                                <td><?= htmlspecialchars($row['iname']) ?></td>
                                <td><?= htmlspecialchars($unit_name) ?></td>
                                <td>
                                    <input type="number" 
                                           class="form-control form-control-sm new-qty" 
                                           name="new_qty[<?= $itmid ?>]" 
                                           step="0.01" 
                                           value="<?= $current_qty ?>"
                                           data-item-id="<?= $itmid ?>"
                                           style="width: 120px;">
                                </td>
                                <td class="current-qty"><?= number_format($current_qty, 2) ?></td>
                                <td>
                                    <input type="number" 
                                           class="form-control form-control-sm new-price" 
                                           name="new_price[<?= $itmid ?>]" 
                                           step="0.01" 
                                           value="<?= $current_price ?>"
                                           data-item-id="<?= $itmid ?>"
                                           style="width: 120px;">
                                </td>
                                <td class="current-price"><?= number_format($current_price, 2) ?></td>
                                <td class="settlement-<?= $itmid ?>"><?= number_format($current_qty * $current_price, 2) ?></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
            
        </div>
    </section>
</div>

<?php include('includes/footer.php') ?>

<script>
// حساب التسوية تلقائياً عند تغيير الكمية أو السعر
document.addEventListener('DOMContentLoaded', function() {
    const qtyInputs = document.querySelectorAll('.new-qty');
    const priceInputs = document.querySelectorAll('.new-price');
    
    function calculateSettlement(itemId) {
        const qtyInput = document.querySelector(`.new-qty[data-item-id="${itemId}"]`);
        const priceInput = document.querySelector(`.new-price[data-item-id="${itemId}"]`);
        const settlementCell = document.querySelector(`.settlement-${itemId}`);
        
        const qty = parseFloat(qtyInput.value) || 0;
        const price = parseFloat(priceInput.value) || 0;
        const settlement = qty * price;
        
        settlementCell.textContent = settlement.toFixed(2);
    }
    
    qtyInputs.forEach(input => {
        input.addEventListener('input', function() {
            const itemId = this.getAttribute('data-item-id');
            calculateSettlement(itemId);
        });
    });
    
    priceInputs.forEach(input => {
        input.addEventListener('input', function() {
            const itemId = this.getAttribute('data-item-id');
            calculateSettlement(itemId);
        });
    });
});
</script>

