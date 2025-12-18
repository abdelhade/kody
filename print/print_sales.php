<?php include('../includes/header.php') ?>
<?php
if (!isset($_GET['id'])) {
    echo "لا يوجد فاتورة بهذا الرقم";die;
}else
$id=$_GET['id'];
$rowfat = $conn->query("SELECT * FROM `ot_head` where id = $id")->fetch_assoc();
if ($rowfat == null) {
    echo "لا يوجد فاتورة بهذا الرقم";die;
}else{
    $tybe = $rowfat['pro_tybe'];
?>



<style>
@media print {
  body { font-size: 10px !important; }
  .invoice-header { font-size: 14px !important; }
  .invoice-title { font-size: 12px !important; }
  table { font-size: 9px !important; }
  th, td { padding: 2px !important; }
}
body { font-size: 11px; }
.invoice-header { font-size: 16px; }
.invoice-title { font-size: 14px; }
table { font-size: 10px; }
th, td { padding: 3px; }
</style>

<div class="card" id="printed">
<div class="card-body">

<table class="table table-bordered" style="margin-bottom: 10px;">
<tr>
<td class="text-center invoice-header" style="background-color: #f8f9fa; font-weight: bold;">
<?= $rowstg['company_name'] ?>
</td>
</tr>
<tr>
<td class="text-center">
<?= $rowstg['company_add'] ?> | <?= $rowstg['company_tel'] ?> | <?= $rowstg['company_email'] ?>
</td>
</tr>
</table>

<table class="table table-bordered" style="margin-bottom: 10px;">
<tr>
<td class="invoice-title" style="font-weight: bold;">
<?php 
if($tybe == 4){
    echo "فاتورة مشتريات";
} elseif($tybe == 3 || $tybe == 9){
    echo "فاتورة مبيعات";
} else {
    echo "فاتورة";
}
?>
</td>
<td class="text-right">
التاريخ: <?= $rowfat['pro_date'] ?>
</td>
</tr>
<tr>
<td>
<?php
if($tybe == 4){
    $accid = $rowfat['acc2'];
    $label = "المورد";
} elseif($tybe == 3 || $tybe == 9){
    $accid = $rowfat['acc1'];
    $label = "العميل";
} else {
    $accid = $rowfat['acc1'];
    $label = "العميل";
}
$rowacc1= $conn->query("SELECT aname from acc_head where id = $accid")->fetch_assoc();
echo $label . ": " . $rowacc1['aname'];?>
</td>
<td class="text-right">
رقم: #<?=$rowfat['pro_id']?> | SN: <?= $rowfat['pro_serial']?>
</td>
</tr>
</table>


<table class="table table-bordered table-striped">
<thead style="background-color: #e9ecef;">
<tr>
<th style="width: 5%;">#</th>
<th style="width: 10%;">كود</th>
<th style="width: 35%;">الصنف</th>
<th style="width: 10%;">الكمية</th>
<th style="width: 10%;">الوحدة</th>
<th style="width: 15%;">السعر</th>
<th style="width: 15%;">القيمة</th>
</tr>
</thead>
<tbody>
    <?php 
    $x =0;
    $resdet = $conn->query("SELECT * FROM fat_details where pro_id = $id AND isdeleted = 0 order by id desc");
    while ($rowdet =$resdet->fetch_assoc()) {
        $x++;
        $itmid= $rowdet['item_id']; 
        $rowitm = $conn->query("SELECT * FROM myitems where id = $itmid ")->fetch_assoc();
        if ($tybe == 4 || $tybe == 10){  // مشتريات أو مردود مشتريات
            $qty = $rowdet['qty_in'] / $rowdet['u_val'];
        } elseif($tybe == 3 || $tybe == 9 || $tybe == 11){  // مبيعات أو كاشير أو مردود مبيعات
            $qty = $rowdet['qty_out'] / $rowdet['u_val'];
        } else {
            $qty = 0;  // قيمة افتراضية
        } 
              
    ?>
<tr>
<td><?= $x ?></td>
<td><?= $rowitm['barcode']  ?></td>
<td><?= $rowitm['iname']  ?></td>
<td><?= $qty  ?></td>
<td title="<?= $rowdet['u_val'] ?>"><?php 
$uval = $rowdet['u_val'];
$itmuntsql = "SELECT unit_id from item_units where item_id = $itmid AND u_val = $uval";
$itmunt=$conn->query($itmuntsql)->fetch_assoc();
$unitid =$conn->query("SELECT uname from myunits where id = $itmunt[unit_id]")->fetch_assoc();
echo $unitid['uname'];
?></td>
<td><?= $rowdet['price'] *$rowdet['u_val'] ?></td>
<td><?= $rowdet['det_value']?></td>
</tr>
<?php }?>
</tbody>
</table>

<table class="table table-bordered" style="margin-top: 10px;">
<tr>
<td style="width: 50%; vertical-align: top;">
<strong>سياسة المدفوعات:</strong><br>
الرصيد في الفاتورة يعتبر صحيح ما لم يتم المراجعة قبل 15 يوم<br>
<strong>تاريخ الاستحقاق:</strong> <?= $rowfat['accural_date']?>
</td>
<td style="width: 50%;">
<table class="table table-sm table-bordered">
<tr><th>الإجمالي:</th><td><?= $rowfat['fat_total'] ?></td></tr>
<?php if ($rowfat['fat_disc'] > 0 ){?>
<tr><th>خصم:</th><td><?= $rowfat['fat_disc'] ?></td></tr>
<?php }?>
<?php if ($rowfat['fat_plus'] > 0 ){?>
<tr><th>إضافي:</th><td><?= $rowfat['fat_plus'] ?></td></tr>
<?php }?>
<tr style="background-color: #f8f9fa;"><th>صافي الفاتورة:</th><td><strong><?= $rowfat['pro_value'] ?></strong></td></tr>
<tr><th>المدفوع:</th><td><?php
           $rowpaid = $conn->query("SELECT * FROM ot_head WHERE (pro_tybe = '2' OR pro_tybe = '1') AND op2 = $id AND isdeleted = 0")->fetch_assoc();
           if ($rowpaid) {
               if ($rowpaid['pro_value'] !== null) {
                   $paidval = $rowpaid['pro_value'];
                   $change = $rowfat['pro_value'] - $paidval;
               }}else {
                $paidval = 0;
                   $change = $rowfat['pro_value'];
            }
            echo $paidval;
    ?></td></tr>
<tr><th>المتبقي:</th><td><?= $change ?></td></tr>
</table>
</td>
</tr>
</table>







<div class="row">
    <div class="col"></div>
    <div class="col">
        <div class="row">
            <div class="col">
       
             </div>
            <div class="col">
                
            </div>
            <div class="col"></div>
        </div>
    </div>
</div>




</div>
</div>

<div class="row no-print">
<div class="col-12">
    <button id="printButton">
<i class="fas fa-print" ></i> طباعه
</button>


</div>
</div>

<?php }?>
<script>
$(function() {
  $('#printButton').click(function() {
    window.print();
  });
});
</script>

<?php include('../includes/footer.php') ?>