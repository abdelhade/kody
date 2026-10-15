<?php
session_start();
if (!isset($_SESSION['login'])) {
    header('Location: ../index.php');
    exit;
}
require_once __DIR__ . '/includes/connect.php';
require_once __DIR__ . '/../includes/receipt_design.php';

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

$tybe = (int)($rowfat['pro_tybe'] ?? 0);
$pos_type = $rowstg['pos_type'] ?? 'barcode';
if (isset($_SESSION['lock_after_print']) && $_SESSION['lock_after_print'] === true) {
    $back_page = '../pos_barcode.php?logout=1';
    unset($_SESSION['lock_after_print']);
} else {
    $pos_back = ($pos_type === 'clothes') ? '../pos_clothes.php' : '../pos_barcode.php';
    $type_map = [
        3 => '../sales.php?q=sale',
        4 => '../sales.php?q=purchase',
        9 => $pos_back,
        10 => '../sales.php?q=resale',
        11 => '../sales.php?q=rebuy',
        12 => '../sales.php?q=po',
        13 => '../sales.php?q=so',
        14 => '../sales.php?q=offer',
        6 => $pos_back,
    ];
    $default_back = $type_map[$tybe] ?? '../sales.php';

    if (!empty($_SESSION['pos_back_page'])) {
        $back_page = $_SESSION['pos_back_page'];
        unset($_SESSION['pos_back_page']);
        if (!in_array($tybe, [6, 9], true) && strpos($back_page, 'pos_') !== false) {
            $back_page = $default_back;
        }
    } else {
        $back_page = $default_back;
    }
}
$from_invoice = isset($_GET['src']) && $_GET['src'] === 'invoice';
$is_return = in_array((int)$rowfat['pro_tybe'], [10, 11], true);

// تحميل التصميم الديناميكي الكامل
$design = kody_receipt_design_load($rowstg);

// إعدادات الورقة والمظهر
$receipt_paper_width = trim((string)($design['paper_width'] ?? '78mm'));
if ($receipt_paper_width === '58mm') {
    $receipt_page_size = '58mm auto';
} elseif ($receipt_paper_width === '100%') {
    $receipt_page_size = 'A4';
} elseif (preg_match('/^\d+(\.\d+)?mm$/i', $receipt_paper_width)) {
    $receipt_page_size = $receipt_paper_width . ' auto';
} else {
    $receipt_page_size = '80mm auto';
}

$font_family = $design['font_family'] ?? 'Cairo';
$font_size = (int)($design['font_size'] ?? 13);
$font_size_sm = max(9, $font_size - 2);
$items_font_size = (int)($design['items']['font_size'] ?? $font_size);
$line_spacing = (float)($design['line_spacing'] ?? 1.25);
$margin_top = (float)($design['margin_top'] ?? 2);
$margin_bottom = (float)($design['margin_bottom'] ?? 4);
$margin_right = (float)($design['margin_right'] ?? 2);
$margin_left = (float)($design['margin_left'] ?? 2);
$show_borders = !empty($design['show_borders']);

// نمط حدود الجدول
$tblBorder = '1px solid #000';
$table_border_style = $design['items']['border_style'] ?? 'solid';
if ($table_border_style === 'dashed') {
    $tblBorder = '1px dashed #000';
} elseif ($table_border_style === 'minimal') {
    $tblBorder = '1px solid #ddd';
} elseif ($table_border_style === 'none') {
    $tblBorder = 'none';
}
?>
<!DOCTYPE html>
<html lang="ar">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>فاتورة <?= (int)$id ?></title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap">
<style>
* { box-sizing: border-box; }
html, body {
    margin: 0;
    padding: 0;
    background: #f0f0f0;
    font-family: <?= htmlspecialchars($font_family, ENT_QUOTES, 'UTF-8') ?>, Cairo, Arial, Tahoma, sans-serif;
    font-weight: bold;
    direction: ltr;
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
        padding: <?= $margin_top ?>mm <?= $margin_left ?>mm <?= $margin_bottom ?>mm <?= $margin_right ?>mm !important;
        border: <?= $show_borders ? '1px solid #000' : 'none' ?> !important;
        box-shadow: none !important;
        direction: rtl !important;
    }
    .no-print { display: none !important; }
}
:root {
    --text: #000;
}
.actions {
    width: <?= ($receipt_paper_width === '100%') ? '100%; max-width: 850px;' : htmlspecialchars($receipt_paper_width, ENT_QUOTES, 'UTF-8') ?>;
    margin: 12px auto;
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
    width: <?= ($receipt_paper_width === '100%') ? '100%; max-width: 850px;' : htmlspecialchars($receipt_paper_width, ENT_QUOTES, 'UTF-8') ?>;
    margin: 0 auto;
    border: <?= $show_borders ? '1px solid #000' : 'none' ?>;
    background: #fff;
    color: #000;
    direction: rtl;
    padding: <?= $margin_top ?>mm <?= $margin_left ?>mm <?= $margin_bottom ?>mm <?= $margin_right ?>mm;
    font-family: <?= htmlspecialchars($font_family, ENT_QUOTES, 'UTF-8') ?>, Cairo, Arial, Tahoma, sans-serif;
    font-weight: bold;
    font-size: <?= $font_size ?>px;
    line-height: <?= $line_spacing ?>;
}
#printed, #printed * {
    font-family: <?= htmlspecialchars($font_family, ENT_QUOTES, 'UTF-8') ?>, Cairo, Arial, Tahoma, sans-serif !important;
    font-weight: bold !important;
    color: #000 !important;
    background: #fff !important;
}
.company-name {
    font-size: <?= (int)($design['header']['company_name_size'] ?? 16) ?>px;
    letter-spacing: 0.5px;
    text-align: <?= htmlspecialchars($design['header']['align'] ?? 'center', ENT_QUOTES, 'UTF-8') ?>;
    margin: 2px 0;
}
.company-subinfo {
    font-size: <?= $font_size_sm ?>px;
    text-align: <?= htmlspecialchars($design['header']['align'] ?? 'center', ENT_QUOTES, 'UTF-8') ?>;
    margin: 1px 0;
    font-weight: normal !important;
}
.invoice-num-badge {
    display: block;
    text-align: center;
    background: #fff !important;
    color: #000 !important;
    border: 1px solid #000;
    border-radius: 0;
    padding: 2px 10px;
    font-size: <?= $font_size ?>px;
    margin: 6px auto 8px;
    width: fit-content;
}
.invoice-num-large {
    display: block;
    text-align: center;
    font-size: <?= $font_size + 6 ?>px;
    margin: 6px auto 8px;
    width: fit-content;
}
.invoice-num-plain {
    display: block;
    text-align: center;
    font-size: <?= $font_size ?>px;
    margin: 4px auto;
    width: fit-content;
}
#printed p, #printed address {
    margin-bottom: 2px !important;
    font-size: <?= $font_size_sm ?>px !important;
}
.rcpt-customer {
    border-top: 1px dashed #000;
    padding-top: 4px;
    margin-top: 4px;
    margin-bottom: 6px;
    font-size: <?= $font_size_sm ?>px;
}
.rcpt-customer .info-row {
    display: flex;
    align-items: flex-start;
    gap: 5px;
    margin-bottom: 2px;
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
    font-size: <?= $items_font_size ?>px;
}
.rcpt-items thead tr {
    background: #fff !important;
    color: #000 !important;
}
.rcpt-items thead th {
    padding: 4px 3px;
    text-align: center;
    border: <?= $tblBorder ?>;
}
.rcpt-items tbody tr:nth-child(even) {
    background: #fff !important;
}
.rcpt-items tbody td {
    padding: 4px 3px;
    text-align: center;
    border: <?= $tblBorder ?>;
    word-break: break-word;
    font-size: <?= $items_font_size ?>px;
}
.rcpt-totals {
    width: 100%;
    border-collapse: collapse;
    font-size: <?= $font_size ?>px;
    margin-bottom: 8px;
}
.rcpt-totals td {
    padding: 4px 6px;
    border-bottom: 1px dashed #000;
}
.rcpt-totals .tot-label {
    text-align: right;
}
.rcpt-totals .tot-val {
    text-align: left;
    font-size: <?= $font_size ?>px;
}
.rcpt-totals .tot-val.net {
    font-size: <?= !empty($design['totals']['highlight_net']) ? ($font_size + 3) : $font_size ?>px;
}
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
    <?php if ($from_invoice): ?>
    <label for="printCashierCheck" style="display:inline-flex; align-items:center; gap:6px; margin:4px; cursor:pointer; font-weight:bold;">
        <input type="checkbox" id="printCashierCheck" checked>
        كاشير
    </label>
    <?php endif; ?>
    <a href="<?= htmlspecialchars($back_page, ENT_QUOTES, 'UTF-8') ?>" id="back">عودة</a>
</div>

<div id="printed">
<?php if ($is_return): ?>
<div class="return-badge">مردود</div>
<?php endif; ?>

<!-- الترويسة والشعار -->
<?php
if (!empty($design['header']['show_logo'])) {
    $_logo_file = !empty($rowstg['company_logo']) ? $rowstg['company_logo'] : 'logo.jpg';
    $logo_path = '../assets/logo/' . $_logo_file;
    if (!file_exists($logo_path)) {
        $logo_path = '../assets/logo/logo.jpg';
    }
    if (file_exists($logo_path)) {
        $logoWidth = (int)($design['header']['logo_width'] ?? 90);
        $logoAlign = $design['header']['logo_align'] ?? 'center';
        $marginCss = '0 auto';
        if ($logoAlign === 'right') $marginCss = '0 0 0 auto';
        if ($logoAlign === 'left')  $marginCss = '0 auto 0 0';
        echo '<div style="text-align:' . htmlspecialchars($logoAlign, ENT_QUOTES, 'UTF-8') . '; margin-bottom: 4px;">';
        echo '<img src="' . htmlspecialchars($logo_path, ENT_QUOTES, 'UTF-8') . '" alt="" style="width: ' . $logoWidth . 'px; height: auto; display: block; margin: ' . $marginCss . ';">';
        echo '</div>';
    }
}
?>

<?php if (!empty($design['header']['show_company_name']) && !empty($rowstg['company_name'])): ?>
<div class="company-name"><?= htmlspecialchars($rowstg['company_name'], ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>

<?php if (!empty($design['header']['show_company_tel']) && !empty($rowstg['company_tel'])): ?>
<div class="company-subinfo"><?= htmlspecialchars($rowstg['company_tel'], ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>

<?php if (!empty($design['header']['show_company_add']) && !empty($rowstg['company_add'])): ?>
<div class="company-subinfo"><?= htmlspecialchars($rowstg['company_add'], ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>

<?php if (!empty($design['header']['show_tax_no']) && !empty($design['header']['tax_no'])): ?>
<div class="company-subinfo">الرقم الضريبي: <?= htmlspecialchars($design['header']['tax_no'], ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>

<?php if (!empty($design['header']['show_cr_no']) && !empty($design['header']['cr_no'])): ?>
<div class="company-subinfo">السجل التجاري: <?= htmlspecialchars($design['header']['cr_no'], ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>

<?php if (!empty($design['header']['header_text'])): ?>
<div style="text-align:<?= htmlspecialchars($design['header']['align'] ?? 'center', ENT_QUOTES, 'UTF-8') ?>; font-size:<?= $font_size_sm ?>px; margin: 4px 0 6px; white-space: pre-wrap; font-weight:normal !important;">
    <?= nl2br(htmlspecialchars($design['header']['header_text'], ENT_QUOTES, 'UTF-8')) ?>
</div>
<?php endif; ?>

<!-- بيانات الفاتورة ورقمها -->
<?php if (!empty($design['meta']['show_invoice_num'])): 
    $numStyle = $design['meta']['invoice_num_style'] ?? 'badge';
    $numClass = 'invoice-num-badge';
    if ($numStyle === 'large') $numClass = 'invoice-num-large';
    if ($numStyle === 'plain') $numClass = 'invoice-num-plain';
?>
<div class="<?= $numClass ?>">#<?= htmlspecialchars((string)$rowfat['pro_id'], ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>

<?php if (!empty($design['meta']['show_invoice_barcode'])): ?>
<div style="text-align: center; margin: 4px 0;">
    <svg id="invoiceBarcodeSvg" style="display:block; margin: 0 auto; max-width: 100%;"></svg>
</div>
<?php endif; ?>

<?php
// أطراف الفاتورة
$tybe = (int)$rowfat['pro_tybe'];
if ($tybe === 10) {
    $partyLabel = 'المورد';
    $partyId = (int)$rowfat['acc1'];
} elseif ($tybe === 4) {
    $partyLabel = 'المورد';
    $partyId = (int)$rowfat['acc2'];
} else {
    $partyLabel = 'العميل';
    $partyId = (int)$rowfat['acc2'];
}
$rowacc1 = $partyId > 0
    ? $conn->query("SELECT aname, phone, address, info from acc_head where id = $partyId")->fetch_assoc()
    : null;

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

// اسم الدليفري
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
$showOrderMeta = !empty($design['meta']['show_order_type']);
$showDriverMeta = !empty($design['meta']['show_driver']);

if ($is_delivery && ($showOrderMeta || $showDriverMeta)) {
    $delivery_banner = 'دليفري';
    if ($driver_name !== '' && $showDriverMeta) {
        $delivery_banner .= ' — ' . $driver_name;
    }
    echo '<div style="text-align:center;font-weight:bold;font-size:15px;margin-bottom:6px;border:1px dashed #000;padding:4px;">'
        . htmlspecialchars($delivery_banner, ENT_QUOTES, 'UTF-8')
        . '</div>';
} elseif ($showOrderMeta && (strpos($info_text, 'طاولة') !== false || strpos($info_text, 'Table') !== false)) {
    echo '<div style="text-align:center;font-weight:bold;font-size:13px;margin-bottom:6px;border:1px dashed #000;padding:2px;">' . htmlspecialchars($info_text) . '</div>';
}

$show_client_block = !empty($design['customer']['show']);
$show_cust_name    = !empty($design['customer']['show_name']);
$show_cust_phone   = !empty($design['customer']['show_phone']);
$show_cust_add     = !empty($design['customer']['show_address']);
$show_cashier      = !empty($design['meta']['show_cashier']);

if ($show_client_block || $show_cashier):
?>
<div class="rcpt-customer">
<?php if ($show_client_block && $show_cust_name && $customer_name): ?>
<div class="info-row"><span class="info-label"><?= htmlspecialchars($partyLabel) ?>:</span><span class="info-val"><?= htmlspecialchars($customer_name) ?></span></div>
<?php endif; ?>
<?php if ($show_client_block && $show_cust_phone && $customer_phone): ?>
<div class="info-row"><span class="info-label">التليفون:</span><span class="info-val"><?= htmlspecialchars($customer_phone) ?></span></div>
<?php endif; ?>
<?php if ($show_client_block && $show_cust_add && $customer_address): ?>
<div class="info-row"><span class="info-label">العنوان:</span><span class="info-val"><?= htmlspecialchars($customer_address) ?></span></div>
<?php endif; ?>
<?php if ($show_cashier && $employee_name): ?>
<div class="info-row"><span class="info-label">الكاشير:</span><span class="info-val"><?= htmlspecialchars($employee_name) ?></span></div>
<?php endif; ?>
</div>
<?php endif; ?>

<!-- جدول الأصناف الديناميكي -->
<?php
$col_idx  = !empty($design['items']['show_index']);
$col_code = !empty($design['items']['show_code']);
$col_name = !empty($design['items']['show_name']);
$col_qty  = !empty($design['items']['show_qty']);
$col_unit = !empty($design['items']['show_unit']);
$col_price= !empty($design['items']['show_price']);
$col_disc = !empty($design['items']['show_disc']);
$col_tot  = !empty($design['items']['show_total']);
$show_disc_subline = !empty($design['items']['show_item_disc_subline']);
?>
<table class="rcpt-items">
<thead>
<tr>
    <?php if ($col_idx): ?><th style="width:7%;">#</th><?php endif; ?>
    <?php if ($col_code): ?><th style="width:14%;">كود</th><?php endif; ?>
    <?php if ($col_name): ?><th style="text-align:right;">الصنف</th><?php endif; ?>
    <?php if ($col_qty): ?><th style="width:14%;">الكمية</th><?php endif; ?>
    <?php if ($col_unit): ?><th style="width:12%;">الوحدة</th><?php endif; ?>
    <?php if ($col_price): ?><th style="width:16%;">السعر</th><?php endif; ?>
    <?php if ($col_disc): ?><th style="width:12%;">خصم</th><?php endif; ?>
    <?php if ($col_tot): ?><th style="width:18%;">القيمة</th><?php endif; ?>
</tr>
</thead>
<tbody>
<?php
$line_counter = 0;
$total_item_disc = 0;
$resdet = $conn->query("SELECT * FROM fat_details WHERE isdeleted = 0 AND (fatid = $id OR pro_id = $id) ORDER BY id");
while ($rowdet = $resdet->fetch_assoc()) {
    $line_counter++;
    $itmid = (int)$rowdet['item_id'];
    $rowitm = $conn->query("SELECT iname, barcode FROM myitems where id = $itmid")->fetch_assoc();
    $u = (float)$rowdet['u_val'];
    if ($u <= 0) {
        $u = 1;
    }
    $useIn = in_array($tybe, [4, 11], true);
    $stock = $useIn ? (float)$rowdet['qty_in'] : (float)$rowdet['qty_out'];
    if ($stock <= 0) {
        $stock = $useIn ? (float)$rowdet['qty_out'] : (float)$rowdet['qty_in'];
    }
    if ($stock <= 0) {
        $stock = (float)($rowdet['doc_qty'] ?? 0);
    }
    $qty = $stock / $u;
    $linePrice = (float)$rowdet['price'] * $u;
    $iname = $rowitm['iname'] ?? '';
    $barcode_item = $rowitm['barcode'] ?? $itmid;
    $lineDisc = (float)($rowdet['discount'] ?? 0);
    $total_item_disc += $lineDisc;

    // اسم الوحدة
    $unit_name = '';
    if ($col_unit) {
        $itmuntsql = "SELECT unit_id from item_units where item_id = $itmid AND u_val = $u LIMIT 1";
        $itmunt = @$conn->query($itmuntsql)->fetch_assoc();
        if ($itmunt && !empty($itmunt['unit_id'])) {
            $unitRow = @$conn->query("SELECT uname from myunits where id = " . intval($itmunt['unit_id']))->fetch_assoc();
            if ($unitRow) $unit_name = $unitRow['uname'];
        }
    }
?>
<tr>
    <?php if ($col_idx): ?><td><?= $line_counter ?></td><?php endif; ?>
    <?php if ($col_code): ?><td><?= htmlspecialchars((string)$barcode_item) ?></td><?php endif; ?>
    <?php if ($col_name): ?>
    <td style="text-align:right; padding-right:4px;">
        <?= htmlspecialchars($iname) ?>
        <?php if ($show_disc_subline && $lineDisc > 0 && !$col_disc): ?>
            <div style="font-size:10px; font-weight:normal; color:#555;">(خصم: <?= number_format($lineDisc, 2) ?>)</div>
        <?php endif; ?>
    </td>
    <?php endif; ?>
    <?php if ($col_qty): ?><td><?= number_format($qty, 2) ?></td><?php endif; ?>
    <?php if ($col_unit): ?><td><?= htmlspecialchars($unit_name) ?></td><?php endif; ?>
    <?php if ($col_price): ?><td><?= number_format($linePrice, 2) ?></td><?php endif; ?>
    <?php if ($col_disc): ?><td><?= $lineDisc > 0 ? number_format($lineDisc, 2) : '-' ?></td><?php endif; ?>
    <?php if ($col_tot): ?><td><?= number_format((float)$rowdet['det_value'], 2) ?></td><?php endif; ?>
</tr>
<?php } ?>
</tbody>
</table>

<!-- ملخص المبالغ والإجماليات -->
<?php
$paid_res = $conn->query("SELECT SUM(pro_value) as paid_amount FROM ot_head WHERE (pro_tybe = 1 OR pro_tybe = 2) AND op2 = $id AND isdeleted = 0");
$paid_row = $paid_res ? $paid_res->fetch_assoc() : null;
$paid_amount = $paid_row && $paid_row['paid_amount'] ? floatval($paid_row['paid_amount']) : 0;
$change_amount = $paid_amount - floatval($rowfat['fat_net']);

$fat_total = (float)($rowfat['fat_total'] ?? 0);
$fat_net = (float)($rowfat['fat_net'] ?? 0);
$disc_val = (float)($rowfat['fat_disc'] ?? 0);
$disc_pct = !empty($rowfat['fat_disc_per']) ? (float)$rowfat['fat_disc_per'] : (($fat_total > 0 && $disc_val > 0) ? round(($disc_val / $fat_total) * 100, 2) : 0);
$plus_val = (float)($rowfat['fat_plus'] ?? 0);
$plus_pct = !empty($rowfat['fat_plus_per']) ? (float)$rowfat['fat_plus_per'] : (($fat_total > 0 && $plus_val > 0) ? round(($plus_val / $fat_total) * 100, 2) : 0);

$totCfg = $design['totals'] ?? [];
?>
<table class="rcpt-totals">
<?php if (!empty($totCfg['show_items_total']) && $total_item_disc > 0): ?>
<tr>
    <td class="tot-label">إجمالي الأصناف</td>
    <td class="tot-val"><?= number_format($fat_total + $total_item_disc, 2) ?></td>
</tr>
<?php endif; ?>

<?php if (!empty($totCfg['show_items_disc']) && $total_item_disc > 0): ?>
<tr>
    <td class="tot-label">خصم الأصناف</td>
    <td class="tot-val disc">- <?= number_format($total_item_disc, 2) ?></td>
</tr>
<?php endif; ?>

<?php if (!empty($totCfg['show_subtotal'])): ?>
<tr>
    <td class="tot-label">الإجمالي</td>
    <td class="tot-val"><?= number_format($fat_total, 2) ?></td>
</tr>
<?php endif; ?>

<?php if (!empty($totCfg['show_inv_disc']) && $disc_val > 0): ?>
<tr>
    <td class="tot-label">الخصم <?= $disc_pct > 0 ? '('.$disc_pct.'%)' : '' ?></td>
    <td class="tot-val disc">- <?= number_format($disc_val, 2) ?></td>
</tr>
<?php endif; ?>

<?php if (!empty($totCfg['show_extra']) && $plus_val > 0): ?>
<tr>
    <td class="tot-label">الإضافي <?= $plus_pct > 0 ? '('.$plus_pct.'%)' : '' ?></td>
    <td class="tot-val plus">+ <?= number_format($plus_val, 2) ?></td>
</tr>
<?php endif; ?>

<?php if (!empty($totCfg['show_net'])): ?>
<tr>
    <td class="tot-label">الصافي</td>
    <td class="tot-val net"><?= number_format($fat_net, 2) ?></td>
</tr>
<?php endif; ?>

<?php if (!empty($totCfg['show_paid']) && $paid_amount > 0): ?>
<tr>
    <td class="tot-label">المدفوع</td>
    <td class="tot-val paid"><?= number_format($paid_amount, 2) ?></td>
</tr>
<?php endif; ?>

<?php if (!empty($totCfg['show_change']) && $paid_amount > 0): ?>
<tr>
    <td class="tot-label">الباقي</td>
    <td class="tot-val <?= $change_amount >= 0 ? 'change-pos' : 'change-neg' ?>"><?= number_format(abs($change_amount), 2) ?></td>
</tr>
<?php endif; ?>
</table>

<!-- التاريخ والوقت -->
<?php if (!empty($design['meta']['show_datetime']) && !empty($rowfat['crtime'])): ?>
<p style="font-size:<?= $font_size_sm ?>px; text-align:center; margin: 4px 0;"><?= htmlspecialchars($rowfat['crtime']) ?></p>
<?php endif; ?>

<!-- نص التذييل (الفوتر) والملاحظات -->
<?php if (!empty($design['footer']['show_footer_text']) && !empty($design['footer']['footer_text'])): ?>
<div style="text-align: center; font-size: <?= $font_size_sm ?>px; font-weight: bold; margin-top: 8px;">
    <p><?= nl2br(htmlspecialchars($design['footer']['footer_text'], ENT_QUOTES, 'UTF-8')) ?></p>
</div>
<?php endif; ?>

<?php if (!empty($design['footer']['show_notes']) && !empty($design['footer']['notes_text'])): ?>
<div style="text-align:center; font-size:<?= max(8, $font_size_sm - 2) ?>px; margin-top:6px; border-top:1px dashed #000; padding-top:4px; white-space:pre-wrap; font-weight:normal;">
    <?= nl2br(htmlspecialchars($design['footer']['notes_text'], ENT_QUOTES, 'UTF-8')) ?>
</div>
<?php endif; ?>

</div> <!-- /#printed -->

<script src="code.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if (!empty($design['meta']['show_invoice_barcode'])): ?>
    if (window.JsBarcode) {
        try {
            window.JsBarcode("#invoiceBarcodeSvg", <?= json_encode((string)$rowfat['pro_id']) ?>, {
                format: "CODE128",
                height: <?= (int)($design['meta']['barcode_height'] ?? 30) ?>,
                width: 1.5,
                displayValue: false,
                margin: 0
            });
        } catch (e) {}
    }
    <?php endif; ?>

    var printButton = document.getElementById('printButton');
    if (printButton) {
        printButton.addEventListener('click', function() {
            window.print();
        });
    }

    var cashierCheck = document.getElementById('printCashierCheck');
    if (cashierCheck) {
        cashierCheck.addEventListener('change', function() {
            if (cashierCheck.checked) return;
            var exp = new Date();
            exp.setFullYear(exp.getFullYear() + 1);
            document.cookie = 'invoice_print_cashier=0; expires=' + exp.toUTCString() + '; path=/; SameSite=Lax';
            var params = new URLSearchParams(window.location.search);
            var id = params.get('id');
            if (id) {
                window.location.href = 'print_sales.php?id=' + encodeURIComponent(id);
            }
        });
    }

    function doGoBack() {
        if (window.opener) {
            window.close();
            return;
        }
        if (window.history.length > 1) {
            window.history.back();
            return;
        }
        var backBtn = document.getElementById('back');
        if (backBtn && backBtn.getAttribute('href')) {
            window.location.href = backBtn.getAttribute('href');
        }
    }

    var backBtn = document.getElementById('back');
    if (backBtn) {
        backBtn.addEventListener('click', function(e) {
            e.preventDefault();
            doGoBack();
        });
    }

    document.addEventListener('keydown', function(event) {
        if (event.key === "Escape") {
            doGoBack();
        }
    });

    <?php if (!empty($design['auto_close'])): ?>
    // إغلاق أو الرجوع تلقائياً بعد اكتمال أمر الطباعة
    window.addEventListener('afterprint', function() {
        doGoBack();
    });
    <?php endif; ?>

    <?php if (!empty($design['auto_print'])): ?>
    // طباعة تلقائية بعد فتح الصفحة
    setTimeout(function() {
        window.print();
    }, 300);
    <?php endif; ?>
});
</script>
</body>
</html>
