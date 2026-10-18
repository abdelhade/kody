<?php
$id_early = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id_early > 0 && isset($_COOKIE['invoice_print_cashier']) && $_COOKIE['invoice_print_cashier'] === '1') {
    header('Location: receipt.php?id=' . $id_early . '&src=invoice');
    exit;
}
include('includes/header.php');
require_once __DIR__ . '/../includes/receipt_design.php';
$design = kody_receipt_design_load($rowstg ?? []);

if (!isset($_GET['id'])) {
    echo 'لا يوجد فاتورة بهذا الرقم';
    include('includes/footer.php');
    exit;
}
$id = intval($_GET['id']);
$rowfat = $conn->query("SELECT * FROM `ot_head` WHERE id = $id")->fetch_assoc();
if ($rowfat == null) {
    echo 'لا يوجد فاتورة بهذا الرقم';
    include('includes/footer.php');
    exit;
}

$tybe = (int)($rowfat['pro_tybe'] ?? 0);
$pos_type = $rowstg['pos_type'] ?? 'barcode';
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

$font_family = $design['font_family'] ?? 'Cairo';
$font_size = (int)($design['font_size'] ?? 14);
$font_size_sm = max(9, $font_size - 2);
$items_font_size = (int)($design['items']['font_size'] ?? $font_size);
$line_spacing = (float)($design['line_spacing'] ?? 1.25);
$margin_top = (float)($design['margin_top'] ?? 8);
$margin_bottom = (float)($design['margin_bottom'] ?? 8);
$margin_right = (float)($design['margin_right'] ?? 8);
$margin_left = (float)($design['margin_left'] ?? 8);
$show_borders = !empty($design['show_borders']);
$company_name_size = $font_size + 4;
if (!empty($design['header']['company_name_size']) && (int)$design['header']['company_name_size'] !== 16) {
    $company_name_size = (int)$design['header']['company_name_size'];
}

$tblBorder = '1px solid #000';
$table_border_style = $design['items']['border_style'] ?? 'solid';
if ($table_border_style === 'dashed') {
    $tblBorder = '1px dashed #000';
} elseif ($table_border_style === 'minimal') {
    $tblBorder = '1px solid #cbd5e1';
} elseif ($table_border_style === 'none') {
    $tblBorder = 'none';
}
$sheetBorder = $show_borders ? '1px solid #000' : 'none';
$footerAlign = $design['footer']['align'] ?? 'center';
if (!in_array($footerAlign, ['center', 'right', 'left'], true)) {
    $footerAlign = 'center';
}
$headerAlign = $design['header']['align'] ?? 'center';
if (!in_array($headerAlign, ['center', 'right', 'left'], true)) {
    $headerAlign = 'center';
}

if ($tybe == 4) {
    $doc_title = 'فاتورة مشتريات';
} elseif ($tybe == 3 || $tybe == 9) {
    $doc_title = 'فاتورة مبيعات';
} elseif ($tybe == 10) {
    $doc_title = 'مردود مشتريات';
} elseif ($tybe == 11) {
    $doc_title = 'مردود مبيعات';
} elseif ($tybe == 12) {
    $doc_title = 'أمر شراء';
} elseif ($tybe == 13) {
    $doc_title = 'أمر بيع';
} elseif ($tybe == 14) {
    $doc_title = 'عرض سعر';
} else {
    $doc_title = 'فاتورة';
}

if ($tybe == 4 || $tybe == 10) {
    $accid = (int)$rowfat['acc1'];
    if ($tybe == 4) {
        $accid = (int)$rowfat['acc2'];
    }
    $partyLabel = 'المورد';
} else {
    $accid = (int)$rowfat['acc2'];
    $partyLabel = 'العميل';
}
$rowacc1 = null;
if ($accid > 0) {
    $accStmt = $conn->prepare('SELECT aname, phone, address, code FROM acc_head WHERE id = ? LIMIT 1');
    if ($accStmt) {
        $accStmt->bind_param('i', $accid);
        $accStmt->execute();
        $rowacc1 = $accStmt->get_result()->fetch_assoc() ?: null;
        $accStmt->close();
    }
}

$customer_name = $rowacc1['aname'] ?? '';
$customer_phone = $rowacc1['phone'] ?? '';
$customer_address = $rowacc1['address'] ?? '';

// رصيد الطرف قبل وبعد الفاتورة (من قيود اليومية)
$party_bal_before = null;
$party_bal_after = null;
$show_party_balance = !empty($design['customer']['show_balance'])
    && $accid > 0
    && in_array($tybe, [3, 4, 9, 10, 11], true)
    && empty($rowfat['isdeleted']);
if ($show_party_balance) {
    $isSupplierAcc = (strpos((string)($rowacc1['code'] ?? ''), '211') === 0);
    $balSign = $isSupplierAcc ? -1 : 1;

    $balAfter = 0.0;
    $stmtAfter = $conn->prepare(
        'SELECT COALESCE(SUM(debit), 0) - COALESCE(SUM(credit), 0) AS bal
         FROM journal_entries
         WHERE account_id = ? AND isdeleted = 0'
    );
    if ($stmtAfter) {
        $stmtAfter->bind_param('i', $accid);
        $stmtAfter->execute();
        $balAfter = (float)(($stmtAfter->get_result()->fetch_assoc()['bal'] ?? 0));
        $stmtAfter->close();
    }

    $balBefore = $balAfter;
    $stmtBefore = $conn->prepare(
        'SELECT COALESCE(SUM(debit), 0) - COALESCE(SUM(credit), 0) AS bal
         FROM journal_entries
         WHERE account_id = ? AND isdeleted = 0
           AND NOT (COALESCE(op2, 0) = ?
                OR journal_id IN (SELECT id FROM journal_heads WHERE op_id = ? OR op2 = ?))'
    );
    if ($stmtBefore) {
        $stmtBefore->bind_param('iiii', $accid, $id, $id, $id);
        $stmtBefore->execute();
        $balBefore = (float)(($stmtBefore->get_result()->fetch_assoc()['bal'] ?? 0));
        $stmtBefore->close();
    }

    $party_bal_before = round($balBefore * $balSign, 2);
    $party_bal_after = round($balAfter * $balSign, 2);
}

$employee_name = '';
$empid = (int)($rowfat['emp_id'] ?? 0);
if ($empid > 0) {
    $rowemp = $conn->query("SELECT aname FROM acc_head WHERE id = $empid")->fetch_assoc();
    $employee_name = trim((string)($rowemp['aname'] ?? ''));
}

$driver_name = '';
$dpid = (int)($rowfat['delivery_person_id'] ?? 0);
if ($dpid <= 0) {
    $emp2id = (int)($rowfat['emp2_id'] ?? 0);
    if ($emp2id > 0 && $emp2id !== $empid) {
        $dpid = $emp2id;
    }
}
if ($dpid > 0) {
    $rowdrv = $conn->query("SELECT aname FROM acc_head WHERE id = $dpid")->fetch_assoc();
    if ($rowdrv) {
        $driver_name = trim((string)$rowdrv['aname']);
    }
}

$info_text = (string)($rowfat['info'] ?? '');
$is_delivery = (strpos($info_text, 'دليفري') !== false) || (($rowfat['order_type'] ?? '') === 'delivery');
$is_table = (strpos($info_text, 'طاولة') !== false || strpos($info_text, 'Table') !== false);
if ($is_delivery) {
    if (preg_match('/العميل:\s*(.+?)(?:\s+-\s+الهاتف:|$)/u', $info_text, $nm) && isset($nm[1])) {
        $customer_name = trim($nm[1]);
    }
    if (preg_match('/الهاتف:\s*(.+?)(?:\s+-\s+العنوان:|$)/u', $info_text, $ph) && isset($ph[1])) {
        $customer_phone = trim($ph[1]);
    }
    if (preg_match('/العنوان:\s*(.+?)(?:\s+-\s+مندوب التوصيل:|\s+-\s+دفع|$)/u', $info_text, $ad) && isset($ad[1])) {
        $customer_address = trim($ad[1]);
    }
    if ($driver_name === '' && preg_match('/مندوب التوصيل:\s*(.+?)(?:\s+-\s+|$)/u', $info_text, $dr) && isset($dr[1])) {
        $driver_name = trim($dr[1]);
    }
} elseif ($driver_name === '' && preg_match('/مندوب التوصيل:\s*(.+?)(?:\s+-\s+|$)/u', $info_text, $dr) && isset($dr[1])) {
    $driver_name = trim($dr[1]);
}

$_logo_file = !empty($rowstg['company_logo']) ? $rowstg['company_logo'] : 'logo.jpg';
$logo_path = '../assets/logo/' . $_logo_file;
if (!file_exists($logo_path)) {
    $logo_path = '../assets/logo/logo.jpg';
}
$show_logo = !empty($design['header']['show_logo']) && file_exists($logo_path);
$logo_width = (int)($design['header']['logo_width'] ?? 120);
$logo_align = $design['header']['logo_align'] ?? 'right';
if (!in_array($logo_align, ['center', 'right', 'left'], true)) {
    $logo_align = 'right';
}

$col_idx  = !empty($design['items']['show_index']);
$col_code = !empty($design['items']['show_code']);
$col_name = !empty($design['items']['show_name']);
$col_qty  = !empty($design['items']['show_qty']);
$col_unit = !empty($design['items']['show_unit']);
$col_price = !empty($design['items']['show_price']);
$col_disc = !empty($design['items']['show_disc']);
$col_tot  = !empty($design['items']['show_total']);
$show_disc_subline = !empty($design['items']['show_item_disc_subline']);
$has_item_cols = $col_idx || $col_code || $col_name || $col_qty || $col_unit || $col_price || $col_disc || $col_tot;
$col_weights = [];
if ($col_idx) { $col_weights['idx'] = 6; }
if ($col_code) { $col_weights['code'] = 16; }
if ($col_name) { $col_weights['name'] = 28; }
if ($col_qty) { $col_weights['qty'] = 10; }
if ($col_unit) { $col_weights['unit'] = 10; }
if ($col_price) { $col_weights['price'] = 11; }
if ($col_disc) { $col_weights['disc'] = 10; }
if ($col_tot) { $col_weights['tot'] = 13; }
$col_weight_sum = array_sum($col_weights) ?: 1;
$col_w = [];
foreach ($col_weights as $ck => $cw) {
    $col_w[$ck] = round($cw / $col_weight_sum * 100, 2);
}
$discSum = $conn->query("SELECT COALESCE(SUM(discount), 0) AS d FROM fat_details WHERE (fatid = $id OR pro_id = $id) AND isdeleted = 0")->fetch_assoc();
$total_item_disc = (float)($discSum['d'] ?? 0);

$numStyle = $design['meta']['invoice_num_style'] ?? 'badge';
$is_doc_order = in_array($tybe, [12, 13, 14], true);
?>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap">
<style>
body {
    font-family: <?= htmlspecialchars($font_family, ENT_QUOTES, 'UTF-8') ?>, Cairo, Arial, Tahoma, sans-serif;
    background: #f5f5f5;
    padding: 20px;
    direction: rtl;
    color: #0f172a;
    font-size: <?= $font_size ?>px;
    line-height: <?= $line_spacing ?>;
}
.sheet {
    max-width: 900px;
    margin: 0 auto;
    background: #fff;
    color: #0f172a;
    padding: <?= $margin_top ?>mm <?= $margin_left ?>mm <?= $margin_bottom ?>mm <?= $margin_right ?>mm;
    border: <?= $sheetBorder ?>;
    box-shadow: 0 0 20px rgba(0,0,0,0.08);
}
.top-head {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 16px;
    border-bottom: 2px solid #0f172a;
    padding-bottom: 14px;
    margin-bottom: 14px;
}
.brand { flex: 1; text-align: right; }
.brand .logo { text-align: <?= htmlspecialchars($logo_align, ENT_QUOTES, 'UTF-8') ?>; margin-bottom: 6px; }
.brand .logo img { width: <?= $logo_width ?>px; height: auto; display: inline-block; }
.company-name { font-weight: 700; font-size: <?= $company_name_size ?>px; margin: 2px 0; }
.company-sub { font-size: <?= $font_size_sm ?>px; color: #334155; }
.header-text { font-size: <?= $font_size_sm ?>px; margin-top: 6px; white-space: pre-wrap; font-weight: 400; text-align: <?= htmlspecialchars($headerAlign, ENT_QUOTES, 'UTF-8') ?>; }
.meta-box {
    flex: 0 0 270px;
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    padding: 10px 14px;
    text-align: right;
}
.doc-title {
    font-weight: 700;
    font-size: <?= $font_size ?>px;
    color: #1e293b;
    border-bottom: 1px solid #cbd5e1;
    padding-bottom: 4px;
    margin-bottom: 8px;
}
.inv-num { display: inline-block; font-weight: 700; margin: 4px 0; }
.inv-num.badge { border: 1px solid #000; padding: 2px 10px; font-size: <?= $font_size ?>px; }
.inv-num.large { font-size: <?= $font_size + 6 ?>px; }
.inv-num.plain { font-size: <?= $font_size ?>px; }
.meta-line { font-size: <?= $font_size_sm ?>px; color: #333; margin: 2px 0; }
.order-banner { border: 1px dashed #000; padding: 4px 6px; font-weight: 700; font-size: <?= $font_size_sm ?>px; margin: 6px 0; text-align: center; }
.cust {
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    padding: 10px 16px;
    margin: 12px 0 16px;
}
.cust-title { font-weight: 700; margin-bottom: 4px; color: #1e293b; }
.cust-fields { display: flex; flex-wrap: wrap; gap: 8px 18px; }
.items {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
    font-size: <?= $items_font_size ?>px;
    margin: 6px 0 14px;
}
.items th, .items td {
    padding: 8px 6px;
    border: <?= $tblBorder ?>;
    text-align: center;
    vertical-align: middle;
    overflow-wrap: anywhere;
    word-break: break-word;
}
.items th { background: #f1f5f9; color: #0f172a; font-weight: 700; }
.items td.name { text-align: right; }
.disc-sub { font-size: 10px; color: #555; font-weight: 400; }
.totals {
    width: 320px;
    margin-right: auto;
    margin-left: 0;
    border: 1px solid #cbd5e1;
    background: #f8fafc;
    border-radius: 8px;
    border-collapse: separate;
    border-spacing: 0;
    overflow: hidden;
}
.totals td { padding: 6px 12px; }
.totals td.val { text-align: left; }
.totals tr.net td {
    background: #e2e8f0;
    border-top: 2px solid #334155;
    font-weight: 700;
    font-size: <?= !empty($design['totals']['highlight_net']) ? ($font_size + 4) : $font_size ?>px;
}
.foot-block {
    text-align: <?= htmlspecialchars($footerAlign, ENT_QUOTES, 'UTF-8') ?>;
    margin-top: 16px;
    border-top: 1px dashed #000;
    padding-top: 8px;
}
.foot-text { font-weight: 700; font-size: <?= $font_size_sm ?>px; margin-bottom: 4px; }
.foot-notes { font-size: <?= max(8, $font_size_sm - 2) ?>px; color: #444; white-space: pre-wrap; font-weight: 400; }
.actions { max-width: 900px; margin: 16px auto 0; display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
.actions button, .actions a.back {
    background: #1e293b;
    color: #fff;
    border: none;
    padding: 8px 20px;
    font-size: 14px;
    font-weight: 700;
    border-radius: 5px;
    cursor: pointer;
    text-decoration: none;
}
.actions a.back { background: #6c757d; }
.actions label { display: inline-flex; align-items: center; gap: 6px; margin: 0; cursor: pointer; font-weight: 700; }
@page { size: A4; margin: 0; }
@media print {
    body { background: #fff !important; padding: 0 !important; margin: 0 !important; }
    .sheet { max-width: none; box-shadow: none; margin: 0; }
    .no-print { display: none !important; }
}
</style>

<div class="sheet" id="printed">
    <div class="top-head">
        <div class="brand">
            <?php if ($show_logo): ?>
            <div class="logo"><img src="<?= htmlspecialchars($logo_path, ENT_QUOTES, 'UTF-8') ?>" alt=""></div>
            <?php endif; ?>
            <?php if (!empty($design['header']['show_company_name']) && !empty($rowstg['company_name'])): ?>
            <div class="company-name"><?= htmlspecialchars($rowstg['company_name'], ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
            <?php if (!empty($design['header']['show_company_tel']) && !empty($rowstg['company_tel'])): ?>
            <div class="company-sub"><?= htmlspecialchars($rowstg['company_tel'], ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
            <?php if (!empty($design['header']['show_company_add']) && !empty($rowstg['company_add'])): ?>
            <div class="company-sub"><?= htmlspecialchars($rowstg['company_add'], ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
            <?php if (!empty($design['header']['show_tax_no']) && !empty($design['header']['tax_no'])): ?>
            <div class="company-sub">الرقم الضريبي: <?= htmlspecialchars($design['header']['tax_no'], ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
            <?php if (!empty($design['header']['show_cr_no']) && !empty($design['header']['cr_no'])): ?>
            <div class="company-sub">السجل التجاري: <?= htmlspecialchars($design['header']['cr_no'], ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
            <?php if (!empty($design['header']['header_text'])): ?>
            <div class="header-text"><?= nl2br(htmlspecialchars($design['header']['header_text'], ENT_QUOTES, 'UTF-8')) ?></div>
            <?php endif; ?>
        </div>
        <div class="meta-box">
            <div class="doc-title"><?= htmlspecialchars($doc_title, ENT_QUOTES, 'UTF-8') ?></div>
            <?php if (!empty($design['meta']['show_invoice_num'])): ?>
            <div class="inv-num <?= htmlspecialchars($numStyle, ENT_QUOTES, 'UTF-8') ?>">#<?= htmlspecialchars((string)$rowfat['pro_id'], ENT_QUOTES, 'UTF-8') ?></div>
            <?php
            $serial = trim((string)($rowfat['pro_serial'] ?? ''));
            if ($serial !== '' && $serial !== (string)$rowfat['pro_id']):
            ?>
            <div class="meta-line">SN: <?= htmlspecialchars($serial, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
            <?php endif; ?>
            <?php if (!empty($design['meta']['show_datetime'])): ?>
            <div class="meta-line">التاريخ: <?= htmlspecialchars((string)$rowfat['pro_date'], ENT_QUOTES, 'UTF-8') ?></div>
            <?php if (!empty($rowfat['crtime'])): ?>
            <div class="meta-line"><?= htmlspecialchars((string)$rowfat['crtime'], ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
            <?php
            $due = trim((string)($rowfat['accural_date'] ?? ''));
            if ($due !== '' && $due !== '0000-00-00'):
            ?>
            <div class="meta-line">الاستحقاق: <?= htmlspecialchars($due, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
            <?php endif; ?>
            <?php if (!empty($design['meta']['show_cashier']) && $employee_name !== ''): ?>
            <div class="meta-line">الكاشير: <?= htmlspecialchars($employee_name, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
            <?php
            $showOrderMeta = !empty($design['meta']['show_order_type']);
            $showDriverMeta = !empty($design['meta']['show_driver']);
            if ($is_delivery && ($showOrderMeta || $showDriverMeta)):
                $banner = $showOrderMeta ? 'دليفري' : '';
                if ($driver_name !== '' && $showDriverMeta) {
                    $banner = trim($banner . ($banner !== '' ? ' — ' : '') . $driver_name);
                }
                if ($banner !== ''):
            ?>
            <div class="order-banner"><?= htmlspecialchars($banner, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
            <?php elseif ($showOrderMeta && $is_table): ?>
            <div class="order-banner"><?= htmlspecialchars($info_text, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
            <?php if (!empty($design['meta']['show_invoice_barcode'])): ?>
            <div style="margin-top:6px;"><svg id="invoiceBarcodeSvg" style="display:block; max-width:100%;"></svg></div>
            <?php endif; ?>
        </div>
    </div>

    <?php
    $show_client_block = !empty($design['customer']['show']);
    $show_cust_name = !empty($design['customer']['show_name']);
    $show_cust_phone = !empty($design['customer']['show_phone']);
    $show_cust_add = !empty($design['customer']['show_address']);
    $show_driver_line = $showDriverMeta && $driver_name !== '' && !($is_delivery && $showDriverMeta);
    $show_bal_lines = $show_party_balance && $party_bal_before !== null && $party_bal_after !== null;
    $cust_has_any = ($show_client_block && (
        ($show_cust_name && $customer_name !== '')
        || ($show_cust_phone && $customer_phone !== '')
        || ($show_cust_add && $customer_address !== '')
    )) || $show_driver_line || $show_bal_lines;
    if ($cust_has_any):
        $fmtPartyBal = static function ($v) {
            $v = (float)$v;
            $abs = number_format(abs($v), 2);
            if ($v > 0.00001) {
                return $abs . ' مدين';
            }
            if ($v < -0.00001) {
                return $abs . ' دائن';
            }
            return '0.00';
        };
    ?>
    <div class="cust">
        <div class="cust-title">بيانات <?= htmlspecialchars($partyLabel, ENT_QUOTES, 'UTF-8') ?></div>
        <div class="cust-fields">
            <?php if ($show_client_block && $show_cust_name && $customer_name !== ''): ?>
            <div><strong><?= htmlspecialchars($partyLabel, ENT_QUOTES, 'UTF-8') ?>:</strong> <?= htmlspecialchars($customer_name, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
            <?php if ($show_client_block && $show_cust_phone && $customer_phone !== ''): ?>
            <div><strong>الهاتف:</strong> <?= htmlspecialchars($customer_phone, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
            <?php if ($show_client_block && $show_cust_add && $customer_address !== ''): ?>
            <div><strong>العنوان:</strong> <?= htmlspecialchars($customer_address, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
            <?php if ($show_driver_line): ?>
            <div><strong>الدليفري:</strong> <?= htmlspecialchars($driver_name, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
            <?php if ($show_bal_lines): ?>
            <div><strong>الحساب السابق:</strong> <?= htmlspecialchars($fmtPartyBal($party_bal_before), ENT_QUOTES, 'UTF-8') ?></div>
            <div><strong>الحساب بعد الفاتورة:</strong> <?= htmlspecialchars($fmtPartyBal($party_bal_after), ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($has_item_cols): ?>
    <table class="items">
        <thead>
        <tr>
            <?php if ($col_idx): ?><th style="width:<?= $col_w['idx'] ?>%;">#</th><?php endif; ?>
            <?php if ($col_code): ?><th style="width:<?= $col_w['code'] ?>%;">كود</th><?php endif; ?>
            <?php if ($col_name): ?><th class="name" style="width:<?= $col_w['name'] ?>%;">الصنف</th><?php endif; ?>
            <?php if ($col_qty): ?><th style="width:<?= $col_w['qty'] ?>%;">كمية</th><?php endif; ?>
            <?php if ($col_unit): ?><th style="width:<?= $col_w['unit'] ?>%;">وحدة</th><?php endif; ?>
            <?php if ($col_price): ?><th style="width:<?= $col_w['price'] ?>%;">سعر</th><?php endif; ?>
            <?php if ($col_disc): ?><th style="width:<?= $col_w['disc'] ?>%;">خصم</th><?php endif; ?>
            <?php if ($col_tot): ?><th style="width:<?= $col_w['tot'] ?>%;">إجمالي</th><?php endif; ?>
        </tr>
        </thead>
        <tbody>
        <?php
        $x = 0;
        $resdet = $conn->query("SELECT * FROM fat_details WHERE (fatid = $id OR pro_id = $id) AND isdeleted = 0 ORDER BY id");
        while ($rowdet = $resdet->fetch_assoc()) {
            $x++;
            $itmid = (int)$rowdet['item_id'];
            $rowitm = $conn->query("SELECT iname, barcode FROM myitems WHERE id = $itmid")->fetch_assoc();
            $u = (float)$rowdet['u_val'];
            if ($u <= 0) {
                $u = 1;
            }
            if ($tybe == 4 || $tybe == 10) {
                $qty = (float)$rowdet['qty_in'] / $u;
            } elseif ($tybe == 3 || $tybe == 9 || $tybe == 11) {
                $qty = (float)$rowdet['qty_out'] / $u;
            } elseif (in_array($tybe, [12, 13, 14], true)) {
                $qty = (float)($rowdet['doc_qty'] ?? 0) / $u;
            } else {
                $qty = 0;
            }
            $item_disc = (float)($rowdet['discount'] ?? 0);
            $unit_name = '';
            if ($col_unit) {
                $u_sql = $u;
                $itmunt = $conn->query("SELECT unit_id FROM item_units WHERE item_id = $itmid AND u_val = $u_sql LIMIT 1")->fetch_assoc();
                if ($itmunt && !empty($itmunt['unit_id'])) {
                    $unitid = (int)$itmunt['unit_id'];
                    $unitRow = $conn->query("SELECT uname FROM myunits WHERE id = $unitid")->fetch_assoc();
                    $unit_name = $unitRow['uname'] ?? '';
                }
            }
            $iname = $rowitm['iname'] ?? '';
            $barcode_item = $rowitm['barcode'] ?? $itmid;
        ?>
        <tr>
            <?php if ($col_idx): ?><td><?= $x ?></td><?php endif; ?>
            <?php if ($col_code): ?><td><?= htmlspecialchars((string)$barcode_item, ENT_QUOTES, 'UTF-8') ?></td><?php endif; ?>
            <?php if ($col_name): ?>
            <td class="name">
                <?= htmlspecialchars($iname, ENT_QUOTES, 'UTF-8') ?>
                <?php if ($show_disc_subline && $item_disc > 0 && !$col_disc): ?>
                <div class="disc-sub">(خصم: <?= number_format($item_disc, 2) ?>)</div>
                <?php endif; ?>
            </td>
            <?php endif; ?>
            <?php if ($col_qty): ?><td><?= number_format($qty, 2) ?></td><?php endif; ?>
            <?php if ($col_unit): ?><td><?= htmlspecialchars($unit_name, ENT_QUOTES, 'UTF-8') ?></td><?php endif; ?>
            <?php if ($col_price): ?><td><?= number_format((float)$rowdet['price'] * $u, 2) ?></td><?php endif; ?>
            <?php if ($col_disc): ?><td><?= $item_disc > 0 ? number_format($item_disc, 2) : '-' ?></td><?php endif; ?>
            <?php if ($col_tot): ?><td><strong><?= number_format((float)$rowdet['det_value'], 2) ?></strong></td><?php endif; ?>
        </tr>
        <?php } ?>
        </tbody>
    </table>
    <?php endif; ?>

    <?php
    $fat_total = (float)($rowfat['fat_total'] ?? 0);
    $fat_net = (float)($rowfat['fat_net'] ?? 0);
    $disc_val = (float)($rowfat['fat_disc'] ?? 0);
    $disc_pct_head = !empty($rowfat['fat_disc_per']) ? (float)$rowfat['fat_disc_per'] : (($fat_total > 0 && $disc_val > 0) ? round(($disc_val / $fat_total) * 100, 2) : 0);
    $plus_val = (float)($rowfat['fat_plus'] ?? 0);
    $plus_pct_head = !empty($rowfat['fat_plus_per']) ? (float)$rowfat['fat_plus_per'] : (($fat_total > 0 && $plus_val > 0) ? round(($plus_val / $fat_total) * 100, 2) : 0);

    $paid_res = $conn->query("SELECT SUM(pro_value) AS paid_amount FROM ot_head WHERE (pro_tybe = 1 OR pro_tybe = 2) AND op2 = $id AND isdeleted = 0");
    $paid_row = $paid_res ? $paid_res->fetch_assoc() : null;
    if ($paid_row && $paid_row['paid_amount'] !== null) {
        $paidval = (float)$paid_row['paid_amount'];
    } else {
        $paidval = $fat_net;
    }
    $remaining = $fat_net - $paidval;
    $totCfg = $design['totals'] ?? [];
    ?>
    <table class="totals">
        <?php if (!empty($totCfg['show_items_total']) && $total_item_disc > 0): ?>
        <tr><td>إجمالي الأصناف</td><td class="val"><?= number_format($fat_total + $total_item_disc, 2) ?></td></tr>
        <?php endif; ?>
        <?php if (!empty($totCfg['show_items_disc']) && $total_item_disc > 0): ?>
        <tr><td>خصم الأصناف</td><td class="val" style="color:#c00;">- <?= number_format($total_item_disc, 2) ?></td></tr>
        <?php endif; ?>
        <?php if (!empty($totCfg['show_subtotal'])): ?>
        <tr><td>الإجمالي</td><td class="val"><?= number_format($fat_total, 2) ?></td></tr>
        <?php endif; ?>
        <?php if (!empty($totCfg['show_inv_disc']) && $disc_val > 0): ?>
        <tr><td>الخصم <?= $disc_pct_head > 0 ? '(' . $disc_pct_head . '%)' : '' ?></td><td class="val" style="color:#c00;">- <?= number_format($disc_val, 2) ?></td></tr>
        <?php endif; ?>
        <?php if (!empty($totCfg['show_extra']) && $plus_val > 0): ?>
        <tr><td>الإضافي <?= $plus_pct_head > 0 ? '(' . $plus_pct_head . '%)' : '' ?></td><td class="val" style="color:#15803d;">+ <?= number_format($plus_val, 2) ?></td></tr>
        <?php endif; ?>
        <?php if (!empty($totCfg['show_net'])): ?>
        <tr class="net"><td>الصافي</td><td class="val"><?= number_format($fat_net, 2) ?></td></tr>
        <?php endif; ?>
        <?php if (!$is_doc_order && !empty($totCfg['show_paid'])): ?>
        <tr><td>المدفوع</td><td class="val" style="color:#15803d;"><?= number_format($paidval, 2) ?></td></tr>
        <?php endif; ?>
        <?php if (!$is_doc_order && !empty($totCfg['show_change'])): ?>
        <tr><td><?= $remaining < 0 ? 'الباقي' : 'المتبقي' ?></td><td class="val" style="color:<?= $remaining > 0 ? '#c00' : '#15803d' ?>; font-weight:700;"><?= number_format(abs($remaining), 2) ?></td></tr>
        <?php endif; ?>
    </table>

    <?php if (!empty($design['footer']['show_footer_text']) && !empty($design['footer']['footer_text'])): ?>
    <div class="foot-block">
        <div class="foot-text"><?= nl2br(htmlspecialchars($design['footer']['footer_text'], ENT_QUOTES, 'UTF-8')) ?></div>
    </div>
    <?php endif; ?>
    <?php if (!empty($design['footer']['show_notes']) && !empty($design['footer']['notes_text'])): ?>
    <div class="foot-block">
        <div class="foot-notes"><?= nl2br(htmlspecialchars($design['footer']['notes_text'], ENT_QUOTES, 'UTF-8')) ?></div>
    </div>
    <?php endif; ?>
</div>

<div class="actions no-print">
    <button type="button" id="printButton">طباعة الفاتورة</button>
    <a href="<?= htmlspecialchars($back_page, ENT_QUOTES, 'UTF-8') ?>" id="back" class="back">عودة</a>
    <label for="printCashierCheck">
        <input type="checkbox" id="printCashierCheck">
        كاشير
    </label>
</div>

<script src="code.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if (!empty($design['meta']['show_invoice_barcode'])): ?>
    if (window.JsBarcode) {
        try {
            window.JsBarcode('#invoiceBarcodeSvg', <?= json_encode((string)$rowfat['pro_id']) ?>, {
                format: 'CODE128',
                height: <?= (int)($design['meta']['barcode_height'] ?? 32) ?>,
                width: 1.6,
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
            if (!cashierCheck.checked) return;
            var exp = new Date();
            exp.setFullYear(exp.getFullYear() + 1);
            document.cookie = 'invoice_print_cashier=1; expires=' + exp.toUTCString() + '; path=/; SameSite=Lax';
            var params = new URLSearchParams(window.location.search);
            var id = params.get('id');
            if (id) {
                window.location.href = 'receipt.php?id=' + encodeURIComponent(id) + '&src=invoice';
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
        if (event.key === 'Escape') {
            doGoBack();
        }
    });

    <?php if (!empty($design['auto_close'])): ?>
    window.addEventListener('afterprint', function() {
        doGoBack();
    });
    <?php endif; ?>

    <?php if (!empty($design['auto_print'])): ?>
    setTimeout(function() {
        window.print();
    }, 300);
    <?php endif; ?>
});
</script>
<?php include('includes/footer.php'); ?>
