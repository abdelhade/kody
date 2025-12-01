<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('includes/header.php'); 

if (!isset($_GET['id'])) {
    echo "لا يوجد فاتورة بهذا الرقم";
    die;
}

$id = intval($_GET['id']); // حماية من SQL injection
$rowfat = $conn->query("SELECT * FROM `ot_head` where id = $id")->fetch_assoc();
if ($rowfat == null) {
    echo "لا يوجد فاتورة بهذا الرقم";die;
}else{
    $tybe = $rowfat['pro_tybe'];
?>



<div class="card" id="printed" style="width: 72mm;">
<div class="card-body">

<?php 
$logo_path = '../assets/logo/logo.jpg';
if (file_exists($logo_path)) {
    echo '<img src="' . $logo_path . '" alt="" class="img-fluid">';
} else {
    echo '<div class="text-center p-2">لوجو الشركة</div>';
}
?>
<h1 class="text-center p-3 p-0 font-bold text-xl">
<?= $rowstg['company_name'] ?></h1>

<?php
$prodate = date('md', strtotime($rowfat['pro_date']));
?>
<div class="row" >
    <div class="col-12">
<p style="font-size:12px;text-align:center">
    <?= $prodate.$rowfat['pro_id'] ?></p>
<div class="row invoice-info font-thin border-1 border-indigo-300 m-0">

<div class="col-sm-12 invoice-col">
<address><b>العميل :</b><?php
    $accid = $rowfat['acc1'];
$rowacc1= $conn->query("SELECT aname,address,phone,e_mail from acc_head where id = $accid")->fetch_assoc();
 echo  $rowacc1['aname'];?>
 <?= $rowacc1['address']?>
 <br>
<b>البائع    :</b> <?php
$emp = $rowfat['emp_id'];
$rowemp = $conn->query("SELECT * from acc_head where id = '$emp'")->fetch_assoc();
echo $rowemp['aname'];
?>
<br>
</address>
</div>

</div>

<p class="text-center">************</p>
<div class="row">





<table class="table col-md-12 table-bordered table-lg text-center">
<thead>
<tr class="bg-slate-100 border-2 border-slate-900" style="font-size:x-small">
<th class="border-3 border-slate-900">الصــــنـــف</th>
<th class="border-3 border-slate-900">الكمية</th>
<th class="border-3 border-slate-900">السعر</th>
<th class="border-3 border-slate-900">القيمة</th>

</tr>
</thead>
<tbody>
    <?php 
    $x =0;
    $resdet = $conn->query("SELECT * FROM fat_details where pro_id = $id");
    while ($rowdet =$resdet->fetch_assoc()) {
        $x++;
        $itmid= $rowdet['item_id']; 
        $rowitm = $conn->query("SELECT * FROM myitems where id = $itmid ")->fetch_assoc();
        $qty = $rowdet['qty_out'];       
    ?>
<tr class="border-2 border-slate-900">
<td class="p-1" style="font-size:small"><?= $rowitm['iname']  ?></td>
<td><?= $qty  ?></td>

<td><?= $rowdet['price']?></td>
<td><?= $rowdet['det_value']?></td>

</tr>

<?php }?>
</tbody>
</table>

</div>
<p class="text-center">************</p>

<div class="row">

<div class="col-12">
<div class="table-responsive">
<table class="table table-bordered table-sm bg-slate-50">
<tbody>
    <tr class="bg-slate-100 border-b-2 border-l-2 border-slate-900">
<th style="width:35%">اجمالي:</th>
<td class="float-right"><?= $rowfat['fat_total'] ?> LE</td>
</tr>

<?php if ($rowfat['fat_disc'] > 0 ){?>
<tr class="bg-slate-100 border-b-2 border-l-2 border-slate-900">
<th><b>خصم :D</b></th>
<td class="float-right"><?= $rowfat['fat_disc'] ?></td>
</tr>
<?php }?>

<?php if ($rowfat['fat_plus'] > 0 ){?>
<tr class="bg-slate-100 border-b-2 border-l-2 border-slate-900">
<th>اضافي:</th>
<td class="float-right"><?= $rowfat['fat_plus'] ?></td>
</tr>
<?php }?>

<tr class="bg-slate-100 border-b-2 border-l-2 border-slate-900">
<th>الصافي:</th>
<td class="float-right"><?= $rowfat['fat_net'] ?> LE</td>
</tr>
</tbody>
</table>
</div>
</div>


</div>
<p class="text-center">************</p>
<div class="row">
<div class="col">
        <p style="font-size:12px;text-align:center"><?= $rowfat['crtime'] ?></p>
    <p class="text-center">زورونا مره اخري .. تسعدنا خدمتكم</p>
    <p class="text-center">هاوس.com</p>
    
</div>
</div>

</div>
</div>

</div>
</div>

<div class="row no-print">
<div class="col-12">
    <button id="printButton" class="btn btn-secondary frst" >
<i class="fas fa-print" ></i> طباعه
</button>
<a href="../pos_barcode.php" id="back">عودة</a>


</div>
</div>

<?php }?>
<script>
$(function() {
  $('#printButton').click(function() {
    window.print();
  });
});
document.addEventListener('keydown', function(event) {
    if (event.key === "Escape") {
        document.getElementById('back').click();
    }
});
</script>

<?php include('includes/footer.php') ?>