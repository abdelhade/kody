<?php include('includes/header.php') ?>
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



<div class="card" id="printed">
<div class="card-body">

<h1 class="text-center p-3 border-4 border-indigo-900 bg-orange-500">
<?= $rowstg['company_name'] ?></h1>

<div class="row" >
<div class="col-12">
<h4>
<i class="fas fa-globe"></i> <strong>
<?php if($tybe == 4){echo "فاتورة مشتريات" ;}elseif($tybe == 4){echo "فاتورة مبيعات";};?></strong>
<small class="float-right">Date: <?= $rowfat['pro_date'] ?></small>
</h4>
</div>

</div>

<div class="row invoice-info">
<div class="col-sm-4 invoice-col">
<address>
<?= $rowstg['company_add'] ?><br>
<?= $rowstg['company_tel'] ?><br>
<?= $rowstg['company_email'] ?>
</address>
</div>

<div class="col-sm-4 invoice-col">
<address>
<strong>
<?php
if($tybe == 4){$accid = $rowfat['acc2'];}elseif($tybe == 3){
    $accid = $rowfat['acc1'];}
$rowacc1= $conn->query("SELECT aname,address,phone,e_mail from acc_head where id = $accid")->fetch_assoc();
 echo  $rowacc1['aname'];?>
 </strong>
 <?= $rowacc1['address']?><br>
Phone: <?= $rowacc1['phone']?><br>
Email: <?= $rowacc1['e_mail']?>
</address>
</div>

<div class="col-sm-4 invoice-col">
<b>رقم : #<?=$rowfat['pro_id']?></b><br>
<b>SN:</b> <?= $rowfat['pro_serial']?>
</div>

</div>


<div class="row">
<div class="col-12 table-responsive">
<table class="table table-striped table-bordered table-group-divider">
<thead>
<tr class="bg-sky-300">
<th>#</th>
<th>كود</th>
<th>الصنف</th>
<th>الكميه</th>
<th>الوحدة</th>
<th>السعر</th>
<th>القيمة</th>
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
        if ($tybe == 4){$qty = $rowdet['qty_in'] /$rowdet['u_val'];}elseif($tybe == 3){$qty = $rowdet['qty_out']/$rowdet['u_val'];} 
              
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
</div>

</div>

<div class="row">

<div class="col-6">
<b class="lead">سياسة المدفوعات:</b>
<p>الرصيد في الفاتورة يعتبر صحيح ما لم يتم المراجعة قبل 15 يوم  </p>
</div>

<div class="col-6">
<p class="lead">تاريخ الاستحقاق :<?= $rowfat['accural_date']?></p>
<div class="table-responsive">
<table class="table">
<tbody><tr>
<th style="width:50%">اجمالي:</th>
<td><?= $rowfat['fat_total'] ?></td>
</tr>

<?php if ($rowfat['fat_disc'] > 0 ){?>
<tr>
<th>خصم</th>
<td><?= $rowfat['fat_disc'] ?></td>
</tr>
<?php }?>

<?php if ($rowfat['fat_plus'] > 0 ){?>
<tr>
<th>اضافي:</th>
<td><?= $rowfat['fat_plus'] ?></td>
</tr>
<?php }?>

<tr>
<th>صافي الفاتورة:</th>
<td><?= $rowfat['pro_value'] ?></td>
</tr>
<tr>
<th>المدفوع:</th>
<td><?php
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
    ?></td>
</tr>
<tr>
<th>المتبقي من الفاتورة:</th>
<td><?= $change ?></td>
</tr>
</tbody></table>
</div>
</div>

</div>







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

<?php include('includes/footer.php') ?>