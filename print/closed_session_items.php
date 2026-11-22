<?php
session_start();
include('../includes/connect.php');
require_once __DIR__ . '/../includes/receipt_design.php';

if (!isset($_GET['id'])) {
    die("رقم الشيفت غير محدد.");
}

$id = intval($_GET['id']);
$query = "SELECT * FROM closed_orders WHERE id = $id";
$res = $conn->query($query);
if ($res->num_rows == 0) {
    die("الشيفت غير موجود.");
}
$shift = $res->fetch_assoc();

$user_name = $shift['user'];
$shift_date = $shift['date'];
$end_time = $shift['crtime'];

// جلب ID المستخدم
$user_id = 0;
$user_stmt = $conn->prepare("SELECT id FROM users WHERE uname = ? LIMIT 1");
if ($user_stmt) {
    $user_stmt->bind_param("s", $user_name);
    $user_stmt->execute();
    $u_res = $user_stmt->get_result();
    if ($u_row = $u_res->fetch_assoc()) {
        $user_id = $u_row['id'];
    }
}

// جلب وقت بداية الشيفت
$start_time = "$shift_date 00:00:00"; // افتراضي بداية اليوم
$prev_stmt = $conn->prepare("SELECT crtime FROM closed_orders WHERE user = ? AND date = ? AND id < ? ORDER BY id DESC LIMIT 1");
if ($prev_stmt) {
    $prev_stmt->bind_param("ssi", $user_name, $shift_date, $id);
    $prev_stmt->execute();
    $p_res = $prev_stmt->get_result();
    if ($p_row = $p_res->fetch_assoc()) {
        $start_time = $p_row['crtime'];
    }
}

// جلب الأصناف المباعة مقسمة بالمجموعات
$items_query = "
    SELECT 
        COALESCE(c.gname, 'بدون مجموعة') as category_name,
        i.iname as item_name,
        SUM(d.qty_out - d.qty_in) as total_qty,
        SUM(CASE
            WHEN h.pro_tybe IN (3, 9) THEN d.det_value
            WHEN h.pro_tybe = 11 OR (h.pro_tybe = 10 AND h.info LIKE '%مردود مبيعات%') THEN -d.det_value
            ELSE 0 END) as total_value
    FROM fat_details d
    JOIN ot_head h ON d.fatid = h.id
    JOIN myitems i ON d.item_id = i.id
    LEFT JOIN item_group c ON i.group1 = c.id
    WHERE h.pro_date = ?
      AND h.crtime > ?
      AND h.crtime <= ?
      AND h.user = ?
      AND d.isdeleted = 0
      AND h.isdeleted = 0
      AND (h.pro_tybe IN (3, 9, 11) OR (h.pro_tybe = 10 AND h.info LIKE '%مردود مبيعات%'))
    GROUP BY c.gname, i.iname
    HAVING ABS(total_qty) > 0.0001
    ORDER BY c.gname ASC, total_qty DESC
";

$items_data = [];
$total_qty_all = 0;
$total_value_all = 0;

if ((int) ($shift['user_id'] ?? 0) > 0) {
    // صافي الكمية/القيمة: مبيعات ناقص مردود
    $items_query = "
        SELECT 
            COALESCE(c.gname, 'بدون مجموعة') as category_name,
            i.iname as item_name,
            SUM(d.qty_out - d.qty_in) as total_qty,
            SUM(CASE
                WHEN h.pro_tybe IN (3, 9) THEN d.det_value
                WHEN h.pro_tybe = 11 OR (h.pro_tybe = 10 AND h.info LIKE '%مردود مبيعات%') THEN -d.det_value
                ELSE 0 END) as total_value
        FROM fat_details d
        JOIN ot_head h ON d.fatid = h.id
        JOIN myitems i ON d.item_id = i.id
        LEFT JOIN item_group c ON i.group1 = c.id
        WHERE h.shift_id = ?
          AND d.isdeleted = 0
          AND h.isdeleted = 0
          AND (h.pro_tybe IN (3, 9, 11) OR (h.pro_tybe = 10 AND h.info LIKE '%مردود مبيعات%'))
        GROUP BY c.gname, i.iname
        HAVING ABS(total_qty) > 0.0001
        ORDER BY c.gname ASC, total_qty DESC
    ";
}

$stmt = $conn->prepare($items_query);
if ($stmt) {
    if ((int) ($shift['user_id'] ?? 0) > 0) {
        $stmt->bind_param("i", $id);
    } else {
        $stmt->bind_param("sssi", $shift_date, $start_time, $end_time, $user_id);
    }
    $stmt->execute();
    $res_items = $stmt->get_result();
    while ($row = $res_items->fetch_assoc()) {
        $items_data[$row['category_name']][] = $row;
        $total_qty_all += $row['total_qty'];
        $total_value_all += $row['total_value'];
    }
}

// جلب إعدادات النظام للترويسة وتصميم الإيصال (الهوامش)
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
    <title>مبيعات الأصناف للشيفت</title>
    <link href="../assets/libs/bootstrap5/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/libs/fontawesome.min.css" rel="stylesheet">
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
        .category-title {
            font-weight: bold;
            font-size: 13px;
            background: #f0f0f0;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 2px 5px;
            margin-top: 10px;
            text-align: right;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .item-table {
            width: 100%;
            font-size: 11px;
            margin-bottom: 5px;
        }
        .item-table th {
            border-bottom: 1px dashed #000;
            text-align: center;
            padding: 2px;
        }
        .item-table td {
            text-align: center;
            padding: 2px;
        }
        .item-table td.text-start {
            text-align: right !important;
        }
        .summary-box {
            border-top: 2px dashed #000;
            padding-top: 5px;
            margin-top: 10px;
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
            <h4 class="mb-1" style="font-size: 16px; font-weight: bold;"><?= $settings['site_name'] ?? 'النظام' ?></h4>
            <p class="mb-0" style="font-size: 11px;">مبيعات الأصناف بالشيفت (Z-Report Items)</p>
        </div>

        <div class="header-title">تفاصيل الوردية</div>
        
        <div class="info-row">
            <span>الكاشير:</span>
            <span><?= $user_name ?></span>
        </div>
        <div class="info-row">
            <span>التاريخ:</span>
            <span><?= $shift_date ?></span>
        </div>
        <div class="info-row" style="font-size: 10px; font-weight: normal;">
            <span>من:</span>
            <span><?= substr($start_time, 11, 8) ?></span>
        </div>
        <div class="info-row" style="font-size: 10px; font-weight: normal;">
            <span>إلى:</span>
            <span><?= substr($end_time, 11, 8) ?></span>
        </div>
        <div class="info-row">
            <span>رقم الشيفت:</span>
            <span>#<?= $shift['id'] ?></span>
        </div>

        <?php if (empty($items_data)): ?>
            <div class="text-center mt-4 mb-4" style="font-size: 12px; font-weight: bold;">
                لا توجد مبيعات أصناف في هذه الوردية
            </div>
        <?php else: ?>
            
            <?php foreach ($items_data as $category => $items): ?>
                <div class="category-title"><?= htmlspecialchars($category) ?></div>
                <table class="item-table">
                    <thead>
                        <tr>
                            <th class="text-start" style="width: 50%;">الصنف</th>
                            <th style="width: 20%;">كمية</th>
                            <th style="width: 30%;">القيمة</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $cat_qty = 0;
                        $cat_val = 0;
                        foreach ($items as $item): 
                            $cat_qty += $item['total_qty'];
                            $cat_val += $item['total_value'];
                        ?>
                        <tr>
                            <td class="text-start"><?= htmlspecialchars($item['item_name']) ?></td>
                            <td><?= number_format($item['total_qty'], 2) ?></td>
                            <td><?= number_format($item['total_value'], 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <tr style="border-top: 1px dotted #ccc; font-weight: bold;">
                            <td class="text-start">الإجمالي</td>
                            <td><?= number_format($cat_qty, 2) ?></td>
                            <td><?= number_format($cat_val, 2) ?></td>
                        </tr>
                    </tbody>
                </table>
            <?php endforeach; ?>

            <div class="summary-box">
                <div class="info-row" style="font-size: 14px;">
                    <span>إجمالي الكميات:</span>
                    <span><?= number_format($total_qty_all, 2) ?></span>
                </div>
                <div class="info-row" style="font-size: 14px;">
                    <span>إجمالي المبيعات:</span>
                    <span><?= number_format($total_value_all, 2) ?></span>
                </div>
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
