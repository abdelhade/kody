<?php 

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
    
    // تحديد صفحة العودة حسب نوع POS
    $pos_type = $rowstg['pos_type'] ?? 'barcode';
    // التحقق من طلب القفل بعد الطباعة
    if (isset($_SESSION['lock_after_print']) && $_SESSION['lock_after_print'] === true) {
        $back_page = '../pos_barcode.php?logout=1';
        unset($_SESSION['lock_after_print']); // مسح المتغير بعد الاستخدام
    } else {
        if (!empty($_SESSION['pos_back_page'])) {
            $back_page = $_SESSION['pos_back_page'];
        } else {
            $back_page = ($pos_type === 'clothes') ? '../pos_clothes.php' : '../pos_barcode.php';
        }
    }

    $is_return = (in_array($rowfat['pro_tybe'], [3, 10, 11]) || strpos($rowfat['info'], 'مردود') !== false);
?>

<style>
@media print {
    body, html { margin: 0; padding: 0; background-color: #fff; }
    #printed { box-shadow: none !important; border: none !important; margin: 0 !important; }
    .no-print { display: none !important; }
}
#printed {
    font-family: 'Tahoma', Arial, sans-serif;
    color: #000;
}
#printed .table {
    border: 2px solid #000 !important;
    width: 100% !important;
    margin: 0 0 5px 0 !important;
}
#printed .table th, #printed .table td {
    padding: 2px !important;
    font-size: 9px !important;
    font-weight: normal !important;
    vertical-align: middle;
    border: 1px solid #000 !important;
}
#printed .table thead th {
    border-bottom: 2px solid #000 !important;
    font-weight: bold !important;
}
#printed h1 {
    font-size: 16px !important;
    margin-bottom: 5px !important;
    padding: 5px 0 !important;
}
#printed p, #printed address {
    margin-bottom: 2px !important;
    font-size: 12px !important;
}
.dashed-separator {
    border-top: 2px dashed #000;
    margin: 10px 0;
}
</style>

<div class="card shadow-sm" id="printed" style="width: 78mm; margin: 0; border: 1px solid #eee;">
<div class="card-body" style="padding: 8px !important;">

<?php 
$logo_path = '../assets/logo/logo.jpg';
if (file_exists($logo_path)) {
    echo '<img src="' . $logo_path . '" alt="" style="width: 90px; height: auto; display: block; margin: 0 auto;">';
} else {
    echo '<div class="text-center p-2">لوجو الشركة</div>';
}
?>
<h1 class="text-center font-bold">
<?= $rowstg['company_name'] ?></h1>

<?php if($is_return): ?>
<div class="text-center mb-2" style="border: 2px solid #000; padding: 5px; font-weight: bold; font-size: 18px; background-color: #f8f9fa;">
    مردود مبيعات
</div>
<?php endif; ?>

<?php
$prodate = date('md', strtotime($rowfat['pro_date']));
?>
<div class="row" >
    <div class="col-12">
<p style="text-align:center; font-size: 10px;">
    <?= $prodate.$rowfat['pro_id'] ?></p>

    <?php
    // Check for Table information
    $info = $rowfat['info'];
    $table_name = '';
    
    if (strpos($info, 'طاولة') !== false || strpos($info, 'Table') !== false) {
        // Try to extract full table name if it's formatted in a specific way, otherwise just use info if it's short
        // Assuming info might contain just "طاولة 1" or mixed text.
        // If it comes from tables.php, info is usually just the table name.
        $table_name = $info;
    }
    
    if (!empty($table_name)) {
        echo '<div style="text-align:center; font-weight:bold; font-size:16px; margin-bottom:5px; border:1px dashed #000; padding:2px;">' . $table_name . '</div>';
    }
    ?>

<?php
$accid = $rowfat['acc1'];
$rowacc1= $conn->query("SELECT aname,info from acc_head where id = $accid")->fetch_assoc();
$is_delivery = strpos($rowfat['info'], 'دليفري') !== false;

if ($is_delivery) {
    $info = $rowfat['info'];
    preg_match('/العميل: ([^-]+)/', $info, $name_match);
    preg_match('/الهاتف: ([^-]+)/', $info, $phone_match);
    preg_match('/العنوان: (.+)$/', $info, $address_match);
    
    $customer_name = isset($name_match[1]) ? trim($name_match[1]) : $rowacc1['aname'];
    $customer_phone = isset($phone_match[1]) ? trim($phone_match[1]) : '';
    $customer_address = isset($address_match[1]) ? trim($address_match[1]) : '';
    
    echo '<div class="row invoice-info font-thin m-0"><div class="col-sm-12 invoice-col"><address>';
    if($customer_name) echo "<b>العميل:</b> " . $customer_name;
    if ($customer_address) echo "<br><b>العنوان:</b> " . $customer_address;
    if ($customer_phone) echo "<br><b>الموبايل:</b> " . $customer_phone;
    echo '</address></div></div>';
}
?>

<div class="row">





<table class="table table-bordered text-center mb-1" style="border: 1px solid #000; width: 100%;">
<thead>
<tr style="background-color: #f0f0f0;">
<th style="border: 1px solid #000; width: 40%;">الصنف</th>
<th style="border: 1px solid #000; width: 20%;">الكمية</th>
<th style="border: 1px solid #000; width: 20%;">السعر</th>
<th style="border: 1px solid #000; width: 20%;">القيمة</th>
</tr>
</thead>
<tbody>
    <?php 
    $x =0;
    $resdet = $conn->query("SELECT * FROM fat_details where fatid = $id");
    while ($rowdet =$resdet->fetch_assoc()) {
        $x++;
        $itmid= $rowdet['item_id']; 
        $rowitm = $conn->query("SELECT * FROM myitems where id = $itmid ")->fetch_assoc();
        $qty = $is_return ? $rowdet['qty_in'] : $rowdet['qty_out'];       
    ?>
<tr>
<td style="border: 1px solid #000; word-break: break-all;"><?= $rowitm['iname']  ?></td>
<td style="border: 1px solid #000;"><?= $qty  ?></td>
<td style="border: 1px solid #000;"><?= $rowdet['price']?></td>
<td style="border: 1px solid #000;"><?= $rowdet['det_value']?></td>
</tr>
<?php }?>
</tbody>
</table>

<table class="table table-bordered text-center" style="border: 1px solid #000; width: 100%; margin-top: 0;">
<tbody>
<tr style="background-color: #f0f0f0;">
<td style="border: 1px solid #000;">اجمالي</td>
<?php if ($rowfat['fat_disc'] > 0 ){?>
<td style="border: 1px solid #000;">خصم</td>
<?php }?>
<?php if ($rowfat['fat_plus'] > 0 ){?>
<td style="border: 1px solid #000;">اضافي</td>
<?php }?>
<td style="border: 1px solid #000; font-weight: bold;">الصافي</td>
</tr>
<tr>
<td style="border: 1px solid #000;"><?= $rowfat['fat_total'] ?></td>
<?php if ($rowfat['fat_disc'] > 0 ){?>
<td style="border: 1px solid #000;"><?= $rowfat['fat_disc'] ?></td>
<?php }?>
<?php if ($rowfat['fat_plus'] > 0 ){?>
<td style="border: 1px solid #000;"><?= $rowfat['fat_plus'] ?></td>
<?php }?>
<td style="border: 1px solid #000; font-weight: bold; font-size: 11px !important;"><?= $rowfat['fat_net'] ?></td>
</tr>
</tbody>
</table>

</div>


<div class="row">
<div class="col">
    <p style="font-size:12px;text-align:center"><?= $rowfat['crtime'] ?></p>
    <div style="text-align: center; direction: ltr; font-size: 12px; font-weight: bold;">
    
        <p>❤ perfect place to grow</p>
    </div>
    
    <div style="text-align: center; margin-top: 15px;">
     
        </div>
    </div>
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
<a href="<?= $back_page ?>" id="back">عودة</a>


</div>
</div>

<?php }?>
<script>
// استخدام JavaScript عادي بدلاً من jQuery
document.addEventListener('DOMContentLoaded', function() {
    var printButton = document.getElementById('printButton');
    
    if (printButton) {
        printButton.addEventListener('click', function() {
            console.log('Print button clicked');
            window.print();
        });
    }
    
    // زر Escape للعودة
    document.addEventListener('keydown', function(event) {
        if (event.key === "Escape") {
            var backButton = document.getElementById('back');
            if (backButton) {
                backButton.click();
            }
        }
    });
});
</script>

<?php include('includes/footer.php') ?>