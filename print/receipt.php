<?php
session_start();
if (!isset($_SESSION['login'])) {
    header('Location: ../index.php');
    exit;
}
require_once __DIR__ . '/includes/connect.php';

if (!isset($_GET['id'])) {
    echo "لا يوجد فاتورة بهذا الرقم";
    exit;
}

$id = intval($_GET['id']);

// تأكد من عمود الطيار على ot_head
$col_dp = @$conn->query("SHOW COLUMNS FROM ot_head LIKE 'delivery_person_id'");
if ($col_dp && $col_dp->num_rows == 0) {
    @$conn->query("ALTER TABLE ot_head ADD COLUMN delivery_person_id INT(11) DEFAULT NULL AFTER emp2_id");
}

$rowfat = null;
try {
    $rowfat = $conn->query(
        "SELECT ot.*,
                drv.aname AS driver_name,
                emp.aname AS employee_name
         FROM ot_head ot
         LEFT JOIN acc_head emp ON emp.id = ot.emp_id
         LEFT JOIN acc_head drv ON drv.id = COALESCE(
             NULLIF(ot.delivery_person_id, 0),
             IF(ot.emp2_id IS NOT NULL AND ot.emp2_id <> 0 AND ot.emp2_id <> ot.emp_id, ot.emp2_id, NULL)
         )
         WHERE ot.id = $id"
    )->fetch_assoc();
} catch (Throwable $e) {
    $rowfat = null;
}
if ($rowfat == null) {
    $rowfat = $conn->query("SELECT * FROM `ot_head` where id = $id")->fetch_assoc();
}
if ($rowfat == null) {
    echo "لا يوجد فاتورة بهذا الرقم";
    exit;
}

$tybe = $rowfat['pro_tybe'];
$pos_type = $rowstg['pos_type'] ?? 'barcode';
if (isset($_SESSION['lock_after_print']) && $_SESSION['lock_after_print'] === true) {
    $back_page = '../pos_barcode.php?logout=1';
    unset($_SESSION['lock_after_print']);
} else {
    if (!empty($_SESSION['pos_back_page'])) {
        $back_page = $_SESSION['pos_back_page'];
    } else {
        $back_page = ($pos_type === 'clothes') ? '../pos_clothes.php' : '../pos_barcode.php';
    }
}

$is_return = (in_array($rowfat['pro_tybe'], [3, 10, 11]) || strpos($rowfat['info'], 'مردود') !== false);

$receipt_paper_width = trim((string)($rowstg['receipt_paper_width'] ?? '78mm'));
if (!in_array($receipt_paper_width, ['78mm', '58mm', '100%'], true)) {
    $receipt_paper_width = '78mm';
}
if ($receipt_paper_width === '58mm') {
    $receipt_page_size = '58mm auto';
} elseif ($receipt_paper_width === '100%') {
    $receipt_page_size = 'A4';
} else {
    $receipt_page_size = '80mm auto';
}

$font_size = (int)($rowstg['receipt_font_size'] ?? 14);
$font_size_sm = max(10, $font_size - 2);
?>
<!DOCTYPE html>
<html lang="ar">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>فاتورة <?= (int)$id ?></title>
<style>
* { box-sizing: border-box; }
html, body {
    margin: 0;
    padding: 0;
    background: #f0f0f0;
    font-family: Arial, Tahoma, sans-serif;
    font-weight: bold;
    direction: ltr; /* اتجاه الصفحة يسار عشان الإيصال ما يتزحلقش للنص/اليمين */
}
@page {
    size: <?= htmlspecialchars($receipt_page_size, ENT_QUOTES, 'UTF-8') ?>;
    margin: 0;
}
@media print {
    html, body {
        width: 100% !important;
        min-width: 0 !important;
        max-width: none !important;
        margin: 0 !important;
        padding: 0 !important;
        background: #fff !important;
        direction: ltr !important;
    }
    body * {
        visibility: hidden;
    }
    #printed, #printed * {
        visibility: visible;
    }
    #printed {
        position: absolute !important;
        left: 0 !important;
        top: 0 !important;
        right: auto !important;
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 !important;
        padding: 2mm !important;
        border: none !important;
        box-shadow: none !important;
        direction: rtl !important;
    }
    .no-print { display: none !important; }
}
:root {
    --text: #000;
}
.actions {
    width: <?= htmlspecialchars($receipt_paper_width, ENT_QUOTES, 'UTF-8') ?>;
    margin: 12px 0;
    text-align: left;
    padding-left: 8px;
}
.actions button, .actions a {
    display: inline-block;
    padding: 8px 16px;
    margin: 4px;
    border: 1px solid #000;
    border-radius: 4px;
    text-decoration: none;
    cursor: pointer;
    font-family: inherit;
    font-size: 14px;
    background: #fff;
    color: #000;
}
#printed {
    width: <?= htmlspecialchars($receipt_paper_width, ENT_QUOTES, 'UTF-8') ?>;
    margin: 0;
    border: 1px solid #000;
    background: #fff;
    color: #000;
    direction: rtl;
    padding: 8px;
    font-family: Arial, Tahoma, sans-serif;
    font-weight: bold;
}
#printed, #printed * {
    font-family: Arial, Tahoma, sans-serif !important;
    font-weight: bold !important;
    color: #000 !important;
    background: #fff !important;
}
.company-name {
    font-size: 15px;
    letter-spacing: 1px;
    text-align: center;
    margin: 2px 0;
}
.invoice-num {
    display: block;
    text-align: center;
    background: #fff !important;
    color: #000 !important;
    border: 1px solid #000;
    border-radius: 0;
    padding: 2px 10px;
    font-size: 12px;
    margin: 6px auto 8px;
    width: fit-content;
}
#printed p, #printed address {
    margin-bottom: 2px !important;
    font-size: <?= $font_size_sm ?>px !important;
}
.rcpt-customer .info-row {
    display: flex;
    align-items: flex-start;
    gap: 5px;
    margin-bottom: 3px;
    text-align: right;
}
.rcpt-customer .info-label {
    white-space: nowrap;
}
.rcpt-customer .info-val {
    word-break: break-word;
}
.rcpt-items {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 8px;
    font-size: <?= $font_size ?>px;
}
.rcpt-items thead tr {
    background: #fff !important;
    color: #000 !important;
}
.rcpt-items thead th {
    padding: 4px 3px;
    text-align: center;
    border: 1px solid #000;
}
.rcpt-items tbody tr:nth-child(even) {
    background: #fff !important;
}
.rcpt-items tbody td {
    padding: 4px 3px;
    text-align: center;
    border: 1px solid #000;
    word-break: break-word;
    font-size: <?= $font_size ?>px;
}
.rcpt-totals {
    width: 100%;
    border-collapse: collapse;
    font-size: <?= $font_size ?>px;
    margin-bottom: 8px;
}
.rcpt-totals td {
    padding: 5px 8px;
    border-bottom: 1px dashed #000;
}
.rcpt-totals .tot-label {
    text-align: right;
}
.rcpt-totals .tot-val {
    text-align: left;
    font-size: 13px;
}
.rcpt-totals .tot-val.net { font-size: 14px; }
.return-badge {
    background: #fff !important;
    color: #000 !important;
    text-align: center;
    padding: 4px;
    border: 1px solid #000;
    border-radius: 0;
    font-size: 13px;
    margin-bottom: 8px;
}
</style>
</head>
<body>

<div class="actions no-print">
    <button type="button" id="printButton">طباعة</button>
    <a href="<?= htmlspecialchars($back_page, ENT_QUOTES, 'UTF-8') ?>" id="back">عودة</a>
</div>

<div id="printed">
<?php if ($is_return): ?>
<div class="return-badge">مردود</div>
<?php endif; ?>

<?php
if (!isset($rowstg['receipt_show_logo']) || !empty($rowstg['receipt_show_logo'])) {
    $_logo_file = !empty($rowstg['company_logo']) ? $rowstg['company_logo'] : 'logo.jpg';
    $logo_path = '../assets/logo/' . $_logo_file;
    if (!file_exists($logo_path)) {
        $logo_path = '../assets/logo/logo.jpg';
    }
    if (file_exists($logo_path)) {
        echo '<img src="' . htmlspecialchars($logo_path, ENT_QUOTES, 'UTF-8') . '" alt="" style="width: 90px; height: auto; display: block; margin: 0 auto;">';
    }
}
?>
<div class="company-name"><?= htmlspecialchars($rowstg['company_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
<?php if (!empty($rowstg['receipt_header_text'])): ?>
<div style="text-align:center; font-size:11px; margin: 4px 0 6px; white-space: pre-wrap;"><?= nl2br(htmlspecialchars($rowstg['receipt_header_text'], ENT_QUOTES, 'UTF-8')) ?></div>
<?php endif; ?>
<div class="invoice-num"><?= date('md', strtotime($rowfat['pro_date'])) . $rowfat['pro_id'] ?></div>

<?php
$accid = (int)$rowfat['acc1'];
$rowacc1 = $conn->query("SELECT aname, phone, address, info from acc_head where id = $accid")->fetch_assoc();
$employee_name = trim((string)($rowfat['employee_name'] ?? ''));
if ($employee_name === '') {
    $empid = (int)$rowfat['emp_id'];
    $rowemp = $conn->query("SELECT aname from acc_head where id = $empid")->fetch_assoc();
    $employee_name = $rowemp ? $rowemp['aname'] : '';
}
$is_delivery = (strpos((string)$rowfat['info'], 'دليفري') !== false)
    || (($rowfat['order_type'] ?? '') === 'delivery');
$customer_name = $rowacc1 ? $rowacc1['aname'] : '';
$customer_phone = $rowacc1 ? $rowacc1['phone'] : '';
$customer_address = $rowacc1 ? $rowacc1['address'] : '';

// اسم الدليفري من ot_head (delivery_person_id / emp2_id عبر JOIN)
$driver_name = trim((string)($rowfat['driver_name'] ?? ''));
if ($driver_name === '' && !empty($rowfat['delivery_person_id'])) {
    $dpid = (int)$rowfat['delivery_person_id'];
    $rowdrv = $conn->query("SELECT aname FROM acc_head WHERE id = $dpid")->fetch_assoc();
    if ($rowdrv) $driver_name = $rowdrv['aname'];
}
if ($driver_name === '') {
    $emp2id = (int)($rowfat['emp2_id'] ?? 0);
    $empid = (int)$rowfat['emp_id'];
    if ($emp2id > 0 && $emp2id !== $empid) {
        $rowdrv = $conn->query("SELECT aname FROM acc_head WHERE id = $emp2id")->fetch_assoc();
        if ($rowdrv) $driver_name = $rowdrv['aname'];
    }
}
if ($is_delivery) {
    $info_d = (string)$rowfat['info'];
    preg_match('/العميل:\s*(.+?)(?:\s+-\s+الهاتف:|$)/u', $info_d, $nm);
    preg_match('/الهاتف:\s*(.+?)(?:\s+-\s+العنوان:|$)/u', $info_d, $ph);
    preg_match('/العنوان:\s*(.+?)(?:\s+-\s+مندوب التوصيل:|\s+-\s+دفع|$)/u', $info_d, $ad);
    if ($driver_name === '') {
        preg_match('/مندوب التوصيل:\s*(.+?)(?:\s+-\s+|$)/u', $info_d, $dr);
        if (isset($dr[1])) $driver_name = trim($dr[1]);
    }
    if (isset($nm[1])) $customer_name = trim($nm[1]);
    if (isset($ph[1])) $customer_phone = trim($ph[1]);
    if (isset($ad[1])) $customer_address = trim($ad[1]);
}

$info_text = (string)$rowfat['info'];
if ($is_delivery) {
    $delivery_banner = 'دليفري';
    if ($driver_name !== '') {
        $delivery_banner .= ' — ' . $driver_name;
    }
    echo '<div style="text-align:center;font-weight:bold;font-size:16px;margin-bottom:6px;border:1px dashed #000;padding:4px;">'
        . htmlspecialchars($delivery_banner, ENT_QUOTES, 'UTF-8')
        . '</div>';
} elseif (strpos($info_text, 'طاولة') !== false || strpos($info_text, 'Table') !== false) {
    echo '<div style="text-align:center;font-weight:bold;font-size:13px;margin-bottom:6px;border:1px dashed #000;padding:2px;">' . htmlspecialchars($info_text) . '</div>';
}

$show_client = !isset($rowstg['receipt_show_client']) || !empty($rowstg['receipt_show_client']);
if (($show_client && ($customer_name || $customer_phone || $customer_address || $employee_name)) || $driver_name):
?>
<div class="rcpt-customer">
<?php if ($show_client && $customer_name): ?>
<div class="info-row"><span class="info-label">العميل:</span><span class="info-val"><?= htmlspecialchars($customer_name) ?></span></div>
<?php endif; ?>
<?php if ($show_client && $customer_phone): ?>
<div class="info-row"><span class="info-label">التليفون:</span><span class="info-val"><?= htmlspecialchars($customer_phone) ?></span></div>
<?php endif; ?>
<?php if ($show_client && $customer_address): ?>
<div class="info-row"><span class="info-label">العنوان:</span><span class="info-val"><?= htmlspecialchars($customer_address) ?></span></div>
<?php endif; ?>
<?php if ($driver_name): ?>
<div class="info-row"><span class="info-label">الدليفري:</span><span class="info-val"><?= htmlspecialchars($driver_name) ?></span></div>
<?php endif; ?>
<?php if ($show_client && $employee_name): ?>
<div class="info-row"><span class="info-label">الموظف:</span><span class="info-val"><?= htmlspecialchars($employee_name) ?></span></div>
<?php endif; ?>
</div>
<?php endif; ?>

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
    $itmid = (int)$rowdet['item_id'];
    $rowitm = $conn->query("SELECT * FROM myitems where id = $itmid")->fetch_assoc();
    $qty = $is_return ? $rowdet['qty_in'] : $rowdet['qty_out'];
    $iname = $rowitm['iname'] ?? '';
?>
<tr>
    <td style="text-align:right; padding-right:4px;"><?= htmlspecialchars($iname) ?></td>
    <td><?= htmlspecialchars((string)$qty) ?></td>
    <td><?= htmlspecialchars((string)$rowdet['price']) ?></td>
    <td><?= htmlspecialchars((string)$rowdet['det_value']) ?></td>
</tr>
<?php } ?>
</tbody>
</table>

<?php
$paid_res = $conn->query("SELECT SUM(debit) as paid_amount FROM journal_entries WHERE op2 = $id AND tybe = 0 AND debit > 0");
$paid_row = $paid_res ? $paid_res->fetch_assoc() : null;
$paid_amount = $paid_row && $paid_row['paid_amount'] ? floatval($paid_row['paid_amount']) : 0;
$change_amount = $paid_amount - floatval($rowfat['fat_net']);
?>
<table class="rcpt-totals">
<tr>
    <td class="tot-label">إجمالي</td>
    <td class="tot-val"><?= number_format($rowfat['fat_total'], 2) ?></td>
</tr>
<tr>
    <td class="tot-label">الصافي</td>
    <td class="tot-val net"><?= number_format($rowfat['fat_net'], 2) ?></td>
</tr>
<?php if ($rowfat['fat_disc'] > 0): ?>
<tr>
    <td class="tot-label">الخصم</td>
    <td class="tot-val disc"><?= number_format($rowfat['fat_disc'], 2) ?></td>
</tr>
<?php endif; ?>
<?php if ($paid_amount > 0): ?>
<tr>
    <td class="tot-label">المدفوع</td>
    <td class="tot-val paid"><?= number_format($paid_amount, 2) ?></td>
</tr>
<tr>
    <td class="tot-label">الباقي</td>
    <td class="tot-val <?= $change_amount >= 0 ? 'change-pos' : 'change-neg' ?>"><?= number_format(abs($change_amount), 2) ?></td>
</tr>
<?php endif; ?>
</table>

<p style="font-size:12px;text-align:center"><?= htmlspecialchars($rowfat['crtime']) ?></p>
<div style="text-align: center; font-size: 12px; font-weight: bold; margin-top: 10px;">
    <p><?= nl2br(htmlspecialchars($rowstg['receipt_footer_text'] ?? '❤ perfect place to grow', ENT_QUOTES, 'UTF-8')) ?></p>
</div>
<?php if (!empty($rowstg['receipt_notes_text'])): ?>
<div style="text-align:center; font-size:10px; margin-top:6px; border-top:1px dashed #000; padding-top:4px; white-space:pre-wrap; font-weight:normal;">
    <?= nl2br(htmlspecialchars($rowstg['receipt_notes_text'], ENT_QUOTES, 'UTF-8')) ?>
</div>
<?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var printButton = document.getElementById('printButton');
    if (printButton) {
        printButton.addEventListener('click', function() {
            window.print();
        });
    }
    document.addEventListener('keydown', function(event) {
        if (event.key === "Escape") {
            var backButton = document.getElementById('back');
            if (backButton) backButton.click();
        }
    });
    // طباعة تلقائية بعد فتح الصفحة (بعد الحفظ)
    setTimeout(function() {
        window.print();
    }, 300);
});
</script>
</body>
</html>
