<?php include('includes/header.php');?>
<?php include('includes/navbar.php');?>
<?php include('includes/sidebar.php');

$copyRows = [];
$copyDate = date('Y-m-d');
$copyInfo = '';
if (!empty($_GET['copy'])) {
    $copyId = (int) $_GET['copy'];
    $resCopy = $conn->query("SELECT * FROM productions WHERE snd_id = $copyId ORDER BY id ASC");
    while ($resCopy && ($r = $resCopy->fetch_assoc())) {
        $copyRows[] = $r;
        $copyDate = $r['date'];
        $copyInfo = $r['info'] ?? '';
    }
}
if (empty($copyRows)) {
    $copyRows[] = ['emp_name' => '', 'qty' => 1, 'price' => 1, 'value' => 1, 'info2' => ''];
}
?>

<style>
    .form-hors{
        width: 100%;
        display: flex;
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
        padding:5px;
        margin: 0px;
    }
</style>
<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">

    <div class="card card-primary">
        <div class="card-header">
            <h2>
                <?= !empty($_GET['copy']) ? 'نسخ انتاجية' : 'اضافة انتاجية' ?>
            </h2>
        </div>
        <div class="card-body">
            <form action="do/doadd_production.php" method="post" enctype="multipart/form-data">
            
            <div class="table">
                <div class="row">
                    <div class="form-group">
                        <label for="">التاريخ</label>
                        <input type="date" name="date" class="form-control" required value="<?= htmlspecialchars($copyDate); ?>">
                    </div>
                    <div class="form-group">
                        <label for="">بيان</label>
                        <input type="text" name="info" class="form-control bg-orange-200" style="width: 300px;" value="<?= htmlspecialchars($copyInfo) ?>">
                    </div>
                    <div class="form-group">
                    <input type="text" value="<?php                          
                        $rowprod = $conn->query("SELECT MAX(snd_id) as max_id FROM productions")->fetch_assoc();
                        $next_id = ($rowprod['max_id'] == null) ? 1 : $rowprod['max_id'] + 1;
                        echo $next_id;
                        ?>"  name="snd_id" hidden class="form-control">
                    </div>
                </div>
                <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>م</th>
                    <th>اسم الموظف</th>
                    <th>الوحدات المنتجه</th>
                    <th>السعر</th>
                    <th>القيمة</th>
                    <th>ملاحظات</th>
                </tr>
                </thead>
                <tbody id="empRow">
                    <?php foreach ($copyRows as $idx => $cr) { ?>
                    <tr>
                        <td CLASS="mslsl"><?= $idx + 1 ?></td>
                        <td>
                            <select autofocus name="emp_name[]" class="form-hors">
                                <?php
                                $resemp = $conn->query("SELECT * FROM `employees` where isdeleted = 0 order by name");
                                while ($rowemp = $resemp->fetch_assoc()) {
                                    $sel = (($cr['emp_name'] ?? '') === $rowemp['name']) ? 'selected' : '';
                                ?>
                            <option value="<?= $rowemp['name'] ?>" <?= $sel ?>><?= $rowemp['name'] ?></option>
                            <?php } ?>
                            </select>
                        </td>
                        <td><input type="text" class="form-hors qty" pattern="[0-9]*\.?[0-9]+" value="<?= htmlspecialchars((string) ($cr['qty'] ?? 1)) ?>" name="qty[]"></td>
                        <td><input type="text" class="form-hors price" pattern="[0-9]*\.?[0-9]+" value="<?= htmlspecialchars((string) ($cr['price'] ?? 1)) ?>" name="price[]"></td>
                        <td><input type="text" class="form-hors value" pattern="[0-9]*\.?[0-9]+" value="<?= htmlspecialchars((string) ($cr['value'] ?? 1)) ?>" name="val[]"></td>
                        <td><input type="text" class="form-hors info2" value="<?= htmlspecialchars($cr['info2'] ?? '') ?>" name="info2[]"></td>
                        <td><button type="button" class="btn btn-danger delete-row">-</button></td>
                    </tr>
                    <?php } ?>
                </tbody>
                
                </table>
                <div class="row">
                <div class="col"><button id="addRow" class="btn btn-primary" type="button">+</button></div>
                <div class="col"><button tybe="submit" class="btn bg-sky-200 btn-block ">تأكيد</button></div>
                    <div class="col"></div>
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
    $('#addRow').click(function() {
        var $table = $('#empRow');
        var $firstRow = $table.find('tr:first');
        var $mslsl = $table.find('.mslsl:last');
        var $newRow = $firstRow.clone();
        $newRow.find('input').val('1');
        $newRow.find('.mslsl').html(Number($mslsl.html()) + 1);
        $table.append($newRow);
    });
});
</script>
<script>
    $(document).ready(function() {
    $('#empRow').on('input', '.qty, .price', function() {
        var $row = $(this).closest('tr');
        var qty = parseFloat($row.find('.qty').val()) || 0;
        var price = parseFloat($row.find('.price').val()) || 0;
        var value = qty * price;
        $row.find('.value').val(value.toFixed(2));
    });
});

</script>
<script>
$(document).ready(function() {
    $('#empRow').on('click', '.delete-row', function() {
        var $row = $(this).closest('tr');
        if ($row.index() !== 0) {
            $row.remove();
        }
    });
});
</script>




<?php include('includes/footer.php');?>
