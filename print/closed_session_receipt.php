<?php
session_start();
include('../includes/connect.php');
require_once __DIR__ . '/../includes/receipt_design.php';
require_once __DIR__ . '/../classes/ShiftCloser.php';

if (!isset($_GET['id'])) {
    die("رقم الشيفت غير محدد.");
}

$id = intval($_GET['id']);
$detail = (new ShiftCloser($conn))->getShift($id);
if (!$detail) {
    die("الشيفت غير موجود.");
}
$shift = $detail['shift'];
$payments = $detail['payments'];
$changes = $detail['changes'];
$isNewShift = (int) ($shift['user_id'] ?? 0) > 0;

// جلب إعدادات النظام للترويسة وتصميم الإيصال
$settings_query = $conn->query("SELECT * FROM settings WHERE id = 1");
$settings = $settings_query->fetch_assoc();
$design = kody_receipt_design_load($settings ?: []);

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
$line_spacing = (float)($design['line_spacing'] ?? 1.25);
$margin_top = (float)($design['margin_top'] ?? 2);
$margin_bottom = (float)($design['margin_bottom'] ?? 4);
$margin_right = (float)($design['margin_right'] ?? 2);
$margin_left = (float)($design['margin_left'] ?? 2);
$show_borders = !empty($design['show_borders']);
$paper_css = ($receipt_paper_width === '100%')
    ? '100%; max-width: 850px;'
    : htmlspecialchars($receipt_paper_width, ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>طباعة تقرير شيفت مغلق</title>
    <link href="../assets/libs/bootstrap5/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap">
    <style>
        * { box-sizing: border-box; }
        html, body {
            margin: 0;
            padding: 0;
            background: #f0f0f0;
            font-family: <?= htmlspecialchars($font_family, ENT_QUOTES, 'UTF-8') ?>, Cairo, Arial, Tahoma, sans-serif;
            direction: rtl;
        }
        @page {
            size: <?= htmlspecialchars($receipt_page_size, ENT_QUOTES, 'UTF-8') ?>;
            margin: 0;
        }
        @media print {
            html, body {
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                background: #fff !important;
            }
            body * { visibility: hidden; }
            #printed, #printed * { visibility: visible; }
            #printed {
                position: absolute !important;
                left: 0 !important;
                top: 0 !important;
                right: auto !important;
                float: none !important;
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: <?= $margin_top ?>mm <?= $margin_left ?>mm <?= $margin_bottom ?>mm <?= $margin_right ?>mm !important;
                border: <?= $show_borders ? '1px solid #000' : 'none' ?> !important;
                box-shadow: none !important;
            }
            .no-print { display: none !important; }
        }
        .actions {
            width: <?= $paper_css ?>;
            margin: 12px auto;
            text-align: center;
        }
        #printed {
            width: <?= $paper_css ?>;
            margin: 0 auto;
            float: none;
            border: <?= $show_borders ? '1px solid #000' : '1px solid #eee' ?>;
            background: #fff;
            color: #000;
            direction: rtl;
            padding: <?= $margin_top ?>mm <?= $margin_left ?>mm <?= $margin_bottom ?>mm <?= $margin_right ?>mm;
            font-family: <?= htmlspecialchars($font_family, ENT_QUOTES, 'UTF-8') ?>, Cairo, Arial, Tahoma, sans-serif;
            font-weight: bold;
            font-size: <?= $font_size ?>px;
            line-height: <?= $line_spacing ?>;
        }
        .header-title {
            font-weight: bold;
            font-size: 14px;
            text-align: center;
            border-bottom: 2px dashed #000;
            padding-bottom: 5px;
            margin-bottom: 5px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            margin-bottom: 3px;
            font-weight: bold;
        }
        .summary-box {
            border: 1px dashed #000;
            padding: 5px;
            margin-top: 10px;
            margin-bottom: 10px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            font-weight: bold;
            font-size: 14px;
            border-top: 1px dashed #000;
            padding-top: 5px;
            margin-top: 5px;
        }
    </style>
</head>
<body>

<div class="actions no-print">
    <button onclick="window.print()" class="btn btn-primary w-100"><i class="fas fa-print"></i> طباعة الان</button>
    <button onclick="window.close()" class="btn btn-secondary w-100 mt-2">إغلاق</button>
</div>

<div id="printed">
        
        <div class="text-center mb-2">
            <h4 class="mb-1" style="font-size: 16px; font-weight: bold;"><?= htmlspecialchars($settings['site_name'] ?? 'النظام') ?></h4>
            <p class="mb-0" style="font-size: 11px;">نسخة مطبوعة من شيفت مغلق</p>
        </div>

        <div class="header-title">تقرير إغلاق وردية (Z-Report)</div>
        
        <div class="info-row">
            <span>التاريخ:</span>
            <span><?= htmlspecialchars((string) $shift['date']) ?></span>
        </div>
        <div class="info-row">
            <span>وقت الإغلاق:</span>
            <span><?= htmlspecialchars((string) $shift['endtime']) ?></span>
        </div>
        <div class="info-row">
            <span>الكاشير:</span>
            <span><?= htmlspecialchars((string) $shift['user']) ?></span>
        </div>
        <div class="info-row">
            <span>رقم الشيفت:</span>
            <span>#<?= (int) $shift['id'] ?></span>
        </div>

        <?php if ($isNewShift): ?>
        <div class="info-row">
            <span>من:</span>
            <span><?= htmlspecialchars($shift['opened_at'] ?? '') ?></span>
        </div>

        <?php if (!empty($changes)): ?>
        <div style="border: 2px solid #000; padding: 4px; margin-top: 6px; font-size: 11px;">
            <b>تنبيه: فواتير الشيفت اتعدلت بعد التقفيل</b>
            <?php foreach ($changes as $c): ?>
            <div>- <?= htmlspecialchars($c) ?></div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <div class="summary-box">
            <div class="info-row"><span>عدد فواتير البيع:</span><span><?= (int) $shift['orders_count'] ?></span></div>
            <div class="info-row"><span>صافي فواتير البيع:</span><span><?= number_format($shift['total_sales'], 2) ?></span></div>
            <?php if (!empty($detail['summary'])): ?>
            <div class="info-row"><span>مبيعات نقدية:</span><span><?= number_format($detail['summary']['cash_sales'], 2) ?></span></div>
            <div class="info-row"><span>مبيعات آجلة:</span><span><?= number_format($detail['summary']['credit_sales'], 2) ?></span></div>
            <?php endif; ?>
            <?php if ((float) $shift['total_returns'] > 0): ?>
            <div class="info-row"><span>المرتجعات:</span><span><?= number_format($shift['total_returns'], 2) ?></span></div>
            <?php endif; ?>
            <?php if ((float) $shift['expenses'] > 0): ?>
            <div class="info-row"><span>المصروفات:</span><span><?= number_format($shift['expenses'], 2) ?></span></div>
            <?php endif; ?>
            <?php if ((float) $shift['total_receipts'] > 0): ?>
            <div class="info-row"><span>سندات قبض:</span><span><?= number_format($shift['total_receipts'], 2) ?></span></div>
            <?php endif; ?>
            <div class="total-row">
                <span>صافي المبيعات:</span>
                <span><?= number_format($shift['total_sales'] - $shift['total_returns'], 2) ?></span>
            </div>
        </div>

        <div class="summary-box">
            <?php foreach ($payments as $p): ?>
            <div style="font-size: 12px; font-weight: bold; border-bottom: 1px dotted #000; margin-bottom: 3px;">
                <?= htmlspecialchars($p['aname'] ?? ('#' . $p['account_id'])) ?>
            </div>
            <?php if ((float) $p['start_amount'] != 0): ?>
            <div class="info-row"><span>بداية:</span><span><?= number_format($p['start_amount'], 2) ?></span></div>
            <?php endif; ?>
            <div class="info-row"><span>المتوقع:</span><span><?= number_format($p['expected'], 2) ?></span></div>
            <div class="info-row"><span>المعدود:</span><span><?= number_format($p['counted'], 2) ?></span></div>
            <div class="info-row"><span><?= $p['diff'] < 0 ? 'عجز' : 'زيادة' ?>:</span><span><?= number_format(abs($p['diff']), 2) ?></span></div>
            <?php endforeach; ?>

            <div class="total-row">
                <span><?= $shift['deficit'] < 0 ? 'إجمالي العجز:' : 'إجمالي الزيادة:' ?></span>
                <span><?= number_format(abs($shift['deficit']), 2) ?></span>
            </div>
            <div class="info-row mt-1"><span>تسليم نقدية:</span><span><?= number_format($shift['cash'], 2) ?></span></div>
            <div class="info-row"><span>متبقي في الدرج:</span><span><?= number_format($shift['fund_after'], 2) ?></span></div>
        </div>
        <?php else: ?>
        <div class="summary-box">
            <div class="info-row">
                <span>مبلغ بداية الشيفت:</span>
                <span><?= number_format($shift['fund_before'], 2) ?></span>
            </div>
            <div class="info-row">
                <span>صافي النقدية الواردة:</span>
                <span><?= number_format($shift['total_sales'], 2) ?></span>
            </div>
            <div class="info-row">
                <span>المصروفات:</span>
                <span><?= number_format($shift['expenses'], 2) ?></span>
            </div>
            <?php if (!empty($shift['exp_notes'])): ?>
            <div style="font-size: 10px; color: #555; text-align: right; margin-bottom: 5px;">
                (<?= htmlspecialchars($shift['exp_notes']) ?>)
            </div>
            <?php endif; ?>
            
            <div class="total-row">
                <span>الباقي (مستنتج):</span>
                <span><?= number_format($shift['fund_after'], 2) ?></span>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($shift['info'])): ?>
        <div class="info-row" style="flex-direction: column; text-align: center;">
            <span style="border-bottom: 1px solid #000; margin-bottom: 3px;">ملاحظات</span>
            <span style="font-weight: normal;"><?= nl2br(htmlspecialchars($shift['info'])) ?></span>
        </div>
        <?php endif; ?>

        <div class="text-center mt-3" style="font-size: 10px; border-top: 1px dashed #000; padding-top: 5px;">
            تمت الطباعة في: <?= date('Y-m-d H:i:s') ?>
        </div>

</div>

<script>
    window.onload = function() {
        window.print();
    }
</script>
</body>
</html>
