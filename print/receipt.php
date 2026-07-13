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
@import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap');
@media print {
    body, html { margin: 0; padding: 0; background-color: #fff; }
    #printed { box-shadow: none !important; border: none !important; margin: 0 !important; }
    .no-print { display: none !important; }
}
:root {
    --teal: #0d5a6a;
    --teal-light: #e8f4f6;
    --gold: #f0a500;
    --text: #1a1a2e;
}
#printed {
    font-family: 'Cairo', 'Tahoma', Arial, sans-serif;
    color: var(--text);
    direction: rtl;
}
.rcpt-header {
    text-align: center;
    padding-bottom: 8px;
    border-bottom: 2px dashed var(--teal);
    margin-bottom: 8px;
}
.rcpt-header img {
    width: 75px; height: 75px;
    object-fit: contain;
    border-radius: 50%;
    border: 3px solid var(--teal);
    margin-bottom: 4px;
}
.rcpt-header .company-name {
    font-size: 15px;
    font-weight: 700;
    letter-spacing: 2px;
    color: var(--teal);
    text-transform: uppercase;
    margin: 2px 0;
}
.rcpt-header .invoice-num {
    display: inline-block;
    background: var(--teal);
    color: #fff;
    border-radius: 20px;
    padding: 1px 14px;
    font-size: 12px;
    font-weight: 600;
    margin-top: 3px;
}
.rcpt-customer {
    background: var(--teal-light);
    border-radius: 8px;
    padding: 7px 10px;
    margin-bottom: 8px;
    font-size: 12px;
    direction: rtl;
}
.rcpt-customer .info-row {
    display: flex;
    align-items: flex-start;
    gap: 5px;
    margin-bottom: 3px;
    flex-direction: row;
    text-align: right;
    justify-content: flex-start;
}
.rcpt-customer .info-icon {
    background: var(--teal);
    color: #fff;
    border-radius: 4px;
    width: 20px; height: 20px;
    display: flex; align-items: center; justify-content: center;
    font-size: 10px;
    flex-shrink: 0;
}
.rcpt-customer .info-label {
    font-weight: 700;
    color: var(--teal);
    white-space: nowrap;
}
.rcpt-customer .info-val {
    color: var(--text);
    word-break: break-word;
}
.rcpt-items {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 8px;
    font-size: 12px;
}
.rcpt-items thead tr {
    background: var(--teal);
    color: #fff;
}
.rcpt-items thead th {
    padding: 4px 3px;
    font-weight: 600;
    text-align: center;
    border: 1px solid var(--teal);
}
.rcpt-items tbody tr:nth-child(even) {
    background: var(--teal-light);
}
.rcpt-items tbody td {
    padding: 4px 3px;
    text-align: center;
    border: 1px solid #ccc;
    word-break: break-word;
}
.rcpt-totals {
    width: 100%;
    border-collapse: collapse;
    font-size: 12px;
    margin-bottom: 8px;
}
.rcpt-totals td {
    padding: 5px 8px;
    border-bottom: 1px dashed #ccc;
}
.rcpt-totals .tot-label {
    color: var(--teal);
    font-weight: 600;
    text-align: right;
    display: flex;
    align-items: center;
    gap: 5px;
    justify-content: flex-start;
}
.rcpt-totals .tot-icon {
    background: var(--teal);
    color: #fff;
    border-radius: 5px;
    width: 22px; height: 22px;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 10px;
}
.rcpt-totals .tot-val {
    font-weight: 700;
    text-align: left;
    font-size: 13px;
}
.rcpt-totals .tot-val.net {
    color: var(--teal);
    font-size: 14px;
}
.rcpt-totals .tot-val.disc {
    color: #e67e22;
}
.rcpt-totals .tot-val.paid {
    color: #27ae60;
}
.rcpt-totals .tot-val.change-pos {
    color: #27ae60;
}
.rcpt-totals .tot-val.change-neg {
    color: #e74c3c;
}
.rcpt-footer {
    border-top: 2px dashed var(--teal);
    margin-top: 8px;
    padding-top: 6px;
    text-align: center;
}
.rcpt-footer .datetime {
    font-size: 11px;
    color: #555;
    margin-bottom: 4px;
}
.rcpt-footer .tagline {
    font-size: 12px;
    font-weight: 700;
    color: var(--teal);
    direction: ltr;
}
.return-badge {
    background: #e74c3c;
    color: #fff;
    text-align: center;
    padding: 4px;
    border-radius: 6px;
    font-weight: 700;
    font-size: 13px;
    margin-bottom: 8px;
}
</style>

<div class="card shadow" id="printed" style="width: 78mm; margin: 0; border: none; background: #fff;">
<div class="card-body" style="padding: 8px !important;">

<?php if($is_return): ?>
<div class="return-badge">↩ مردود مبيعات</div>
<?php endif; ?>

<!-- HEADER -->
<div class="rcpt-header">
<?php
$logo_path = '../assets/logo/logo.jpg';
if (file_exists($logo_path)) {
    echo '<img src="' . $logo_path . '" alt="logo">';
}
?>
<div class="company-name"><?= $rowstg['company_name'] ?></div>
<div class="invoice-num">
<?php echo date('md', strtotime($rowfat['pro_date'])) . $rowfat['pro_id']; ?>
</div>
</div>

<?php
// Check for table info
$info_text = $rowfat['info'];
if (strpos($info_text, 'طاولة') !== false || strpos($info_text, 'Table') !== false) {
    echo '<div style="text-align:center;font-weight:bold;font-size:13px;margin-bottom:6px;border:1px dashed var(--teal);padding:2px;border-radius:4px;">' . htmlspecialchars($info_text) . '</div>';
}
?>

<!-- CUSTOMER INFO -->
<?php
$accid = $rowfat['acc1'];
$rowacc1 = $conn->query("SELECT aname, phone, address, info from acc_head where id = $accid")->fetch_assoc();
$empid = $rowfat['emp_id'];
$rowemp = $conn->query("SELECT aname from acc_head where id = $empid")->fetch_assoc();
$employee_name = $rowemp ? $rowemp['aname'] : '';
$is_delivery = strpos($rowfat['info'], 'دليفري') !== false;
$customer_name = $rowacc1 ? $rowacc1['aname'] : '';
$customer_phone = $rowacc1 ? $rowacc1['phone'] : '';
$customer_address = $rowacc1 ? $rowacc1['address'] : '';
if ($is_delivery) {
    $info_d = $rowfat['info'];
    preg_match('/العميل: ([^-]+)/', $info_d, $nm); preg_match('/الهاتف: ([^-]+)/', $info_d, $ph); preg_match('/العنوان: (.+)$/', $info_d, $ad);
    if (isset($nm[1])) $customer_name = trim($nm[1]);
    if (isset($ph[1])) $customer_phone = trim($ph[1]);
    if (isset($ad[1])) $customer_address = trim($ad[1]);
}
if ($customer_name || $customer_phone || $customer_address || $employee_name):
?>
<div class="rcpt-customer">
<?php if ($customer_name): ?>
<div class="info-row">
    <span class="info-label">العميل:</span>
    <span class="info-val"><?= htmlspecialchars($customer_name) ?></span>
</div>
<?php endif; ?>
<?php if ($customer_phone): ?>
<div class="info-row">
    <span class="info-label">التليفون:</span>
    <span class="info-val"><?= htmlspecialchars($customer_phone) ?></span>
</div>
<?php endif; ?>
<?php if ($customer_address): ?>
<div class="info-row">
    <span class="info-label">العنوان:</span>
    <span class="info-val"><?= htmlspecialchars($customer_address) ?></span>
</div>
<?php endif; ?>
<?php if ($employee_name): ?>
<div class="info-row">
    <span class="info-label">الموظف:</span>
    <span class="info-val"><?= htmlspecialchars($employee_name) ?></span>
</div>
<?php endif; ?>
</div>
<?php endif; ?>

<!-- ITEMS TABLE -->
<table class="rcpt-items">
<thead>
<tr>
    <th style="width:38%;">الصنف</th>
    <th style="width:18%;">الكمية</th>
    <th style="width:22%;">السعر</th>
    <th style="width:22%;">القيمة</th>
</tr>
</thead>
<tbody>
<?php
$resdet = $conn->query("SELECT * FROM fat_details where fatid = $id");
while ($rowdet = $resdet->fetch_assoc()) {
    $itmid = $rowdet['item_id'];
    $rowitm = $conn->query("SELECT * FROM myitems where id = $itmid")->fetch_assoc();
    $qty = $is_return ? $rowdet['qty_in'] : $rowdet['qty_out'];
?>
<tr>
    <td style="text-align:right; padding-right:4px;"><?= $rowitm['iname'] ?></td>
    <td><?= $qty ?></td>
    <td><?= $rowdet['price'] ?></td>
    <td><?= $rowdet['det_value'] ?></td>
</tr>
<?php } ?>
</tbody>
</table>

<!-- TOTALS -->
<?php
$paid_res = $conn->query("SELECT SUM(debit) as paid_amount FROM journal_entries WHERE op2 = $id AND tybe = 0 AND debit > 0");
$paid_row = $paid_res ? $paid_res->fetch_assoc() : null;
$paid_amount = $paid_row && $paid_row['paid_amount'] ? floatval($paid_row['paid_amount']) : 0;
$change_amount = $paid_amount - floatval($rowfat['fat_net']);
?>
<table class="rcpt-totals">
<tr>
    <td class="tot-label">
        إجمالي 
    </td>
    <td class="tot-val"><?= number_format($rowfat['fat_total'], 2) ?></td>
</tr>
<tr>
    <td class="tot-label">
        الصافي 
    </td>
    <td class="tot-val net"><?= number_format($rowfat['fat_net'], 2) ?></td>
</tr>
<?php if ($rowfat['fat_disc'] > 0): ?>
<tr>
    <td class="tot-label">
        الخصم 
    </td>
    <td class="tot-val disc"><?= number_format($rowfat['fat_disc'], 2) ?></td>
</tr>
<?php endif; ?>
<?php if ($paid_amount > 0): ?>
<tr>
    <td class="tot-label">
        المدفوع 
    </td>
    <td class="tot-val paid"><?= number_format($paid_amount, 2) ?></td>
</tr>
<tr>
    <td class="tot-label">
        الباقي 
    </td>
    <td class="tot-val <?= $change_amount >= 0 ? 'change-pos' : 'change-neg' ?>"><?= number_format(abs($change_amount), 2) ?></td>
</tr>
<?php endif; ?>
</table>

<!-- FOOTER -->
<div class="rcpt-footer">
    <div class="datetime"><i class="fas fa-calendar-alt me-1"></i><?= $rowfat['crtime'] ?></div>
    <div class="tagline">❤ perfect place to grow</div>
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

<?php } // end else ?>
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