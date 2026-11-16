<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once __DIR__ . '/../includes/load_env.php';
require_once __DIR__ . '/../includes/db_name.php';
require_once __DIR__ . '/../classes/PrintAgentToken.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$table_id = isset($_GET['table_id']) ? (int) $_GET['table_id'] : 0;
$agentPrint = $id > 0 && PrintAgentToken::verify($id);
if ($agentPrint) {
    define('KODY_SKIP_GUARD', true);
    PrintAgentToken::applyForcedDb();
} elseif (!isset($_SESSION['login'])) {
    header('Location: ../index.php');
    exit;
}

require_once __DIR__ . '/includes/connect.php';
require_once __DIR__ . '/../includes/receipt_design.php';


if ($id <= 0 && $table_id > 0) {
    $tStmt = $conn->prepare('SELECT tname FROM tables WHERE id = ? LIMIT 1');
    if ($tStmt) {
        $tStmt->bind_param('i', $table_id);
        $tStmt->execute();
        $tRes = $tStmt->get_result();
        $tRow = $tRes ? $tRes->fetch_assoc() : null;
        $tStmt->close();
        $tname = trim((string) ($tRow['tname'] ?? ''));
        if ($tname !== '') {
            $like = '%' . $tname . '%';
            $oStmt = $conn->prepare(
                "SELECT id FROM ot_head
                 WHERE isdeleted = 0 AND pro_tybe = 9 AND info LIKE ?
                 ORDER BY id DESC LIMIT 1"
            );
            if ($oStmt) {
                $oStmt->bind_param('s', $like);
                $oStmt->execute();
                $oRes = $oStmt->get_result();
                $oRow = $oRes ? $oRes->fetch_assoc() : null;
                $oStmt->close();
                $id = (int) ($oRow['id'] ?? 0);
            }
        }
    }
}

if ($id <= 0) {
    echo 'لا يوجد فاتورة بهذا الرقم';
    exit;
}

$rowfat = null;
$stmt = $conn->prepare(
    "SELECT ot.*,
            emp.aname AS employee_name,
            drv.aname AS driver_name
     FROM ot_head ot
     LEFT JOIN acc_head emp ON emp.id = ot.emp_id
     LEFT JOIN acc_head drv ON drv.id = COALESCE(
         NULLIF(ot.delivery_person_id, 0),
         IF(ot.emp2_id IS NOT NULL AND ot.emp2_id <> 0 AND ot.emp2_id <> ot.emp_id, ot.emp2_id, NULL)
     )
     WHERE ot.id = ? AND ot.isdeleted = 0
     LIMIT 1"
);
if ($stmt) {
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $rowfat = $res ? $res->fetch_assoc() : null;
    $stmt->close();
}
if (!$rowfat) {
    echo 'لا يوجد فاتورة بهذا الرقم';
    exit;
}

$tybe = (int) ($rowfat['pro_tybe'] ?? 0);
$design = kody_receipt_design_load($rowstg ?? []);
$receipt_paper_width = trim((string) ($design['paper_width'] ?? '78mm'));
if ($receipt_paper_width === '58mm') {
    $receipt_page_size = '58mm auto';
} elseif ($receipt_paper_width === '100%') {
    $receipt_page_size = 'A4';
} elseif (preg_match('/^\d+(\.\d+)?mm$/i', $receipt_paper_width)) {
    $receipt_page_size = $receipt_paper_width . ' auto';
} else {
    $receipt_page_size = '80mm auto';
    $receipt_paper_width = '78mm';
}

$font_family = $design['font_family'] ?? 'Cairo';
$font_size = max(14, (int) ($design['font_size'] ?? 13) + 2);
$auto_print = !empty($design['auto_print']) || !empty($_GET['agent_print']) || !empty($agentPrint);
if (!empty($_GET['no_auto'])) {
    $auto_print = false;
}
$auto_close = !empty($design['auto_close']) || !empty($_GET['agent_print']) || !empty($agentPrint);
$has_group_filter = array_key_exists('group', $_GET);
$filter_group = $has_group_filter ? (int) $_GET['group'] : -1;

$pos_type = $rowstg['pos_type'] ?? 'barcode';
$pos_back = ($pos_type === 'clothes') ? '../pos_clothes.php' : '../pos_barcode.php';
if (!empty($_SESSION['pos_back_page'])) {
    $back_page = $_SESSION['pos_back_page'];
} else {
    $back_page = in_array($tybe, [6, 9], true) ? $pos_back : '../sales.php';
}

$info_text = trim((string) ($rowfat['info'] ?? ''));
$order_label = 'بون تحضير';
if ($info_text !== '') {
    if (strpos($info_text, 'دليفري') !== false || strpos($info_text, 'delivery') !== false) {
        $order_label = 'تحضير — دليفري';
    } elseif (strpos($info_text, 'صالة') !== false) {
        $order_label = 'تحضير — صالة';
    } elseif (strpos($info_text, 'طاولة') !== false || strpos($info_text, 'Table') !== false) {
        $order_label = 'تحضير — ' . $info_text;
    }
}

$invoice_num = (string) ($rowfat['pro_id'] ?? $id);
$emp_name = trim((string) ($rowfat['employee_name'] ?? ''));
$driver_name = trim((string) ($rowfat['driver_name'] ?? ''));
$pro_date = (string) ($rowfat['pro_date'] ?? date('Y-m-d H:i'));

$items = [];
$group_title = '';
$dStmt = $conn->prepare(
    "SELECT fd.*, i.iname, i.group1
     FROM fat_details fd
     LEFT JOIN myitems i ON i.id = fd.item_id
     WHERE fd.isdeleted = 0 AND (fd.fatid = ? OR fd.pro_id = ?)
     ORDER BY fd.id"
);
if ($dStmt) {
    $dStmt->bind_param('ii', $id, $id);
    $dStmt->execute();
    $dRes = $dStmt->get_result();
    while ($dRes && ($rowdet = $dRes->fetch_assoc())) {
        $item_group = (int) ($rowdet['group1'] ?? 0);
        if ($has_group_filter) {
            if ($filter_group > 0 && $item_group !== $filter_group) {
                continue;
            }
            if ($filter_group === 0 && $item_group > 0) {
                continue; // أصناف بدون مجموعة فقط
            }
        }
        $u = (float) ($rowdet['u_val'] ?? 1);
        if ($u <= 0) {
            $u = 1;
        }
        $useIn = in_array($tybe, [4, 11], true);
        $stock = $useIn ? (float) $rowdet['qty_in'] : (float) $rowdet['qty_out'];
        if ($stock <= 0) {
            $stock = $useIn ? (float) $rowdet['qty_out'] : (float) $rowdet['qty_in'];
        }
        if ($stock <= 0) {
            continue;
        }
        $qty = $stock / $u;
        $unit_name = '';
        $itmid = (int) $rowdet['item_id'];
        $uStmt = $conn->prepare(
            'SELECT mu.uname
             FROM item_units iu
             INNER JOIN myunits mu ON mu.id = iu.unit_id
             WHERE iu.item_id = ? AND ABS(iu.u_val - ?) < 0.0001
             LIMIT 1'
        );
        if ($uStmt) {
            $uStmt->bind_param('id', $itmid, $u);
            $uStmt->execute();
            $uRes = $uStmt->get_result();
            $uRow = $uRes ? $uRes->fetch_assoc() : null;
            $uStmt->close();
            $unit_name = trim((string) ($uRow['uname'] ?? ''));
        }
        $items[] = [
            'name' => (string) ($rowdet['iname'] ?? ('صنف #' . $itmid)),
            'qty' => $qty,
            'unit' => $unit_name,
        ];
    }
    $dStmt->close();
}

if ($filter_group > 0) {
    $gStmt = $conn->prepare('SELECT gname FROM item_group WHERE id = ? LIMIT 1');
    if ($gStmt) {
        $gStmt->bind_param('i', $filter_group);
        $gStmt->execute();
        $gRes = $gStmt->get_result();
        $gRow = $gRes ? $gRes->fetch_assoc() : null;
        $gStmt->close();
        $group_title = trim((string) ($gRow['gname'] ?? ''));
        if ($group_title !== '') {
            $order_label .= ' — ' . $group_title;
        }
    }
}

$paper_css = ($receipt_paper_width === '100%')
    ? '100%; max-width: 420px;'
    : htmlspecialchars($receipt_paper_width, ENT_QUOTES, 'UTF-8');
$embed = !empty($_GET['embed']) || !empty($kody_print_embed);

ob_start();
?>
<div id="<?= $embed ? 'prepPrinted' : 'printed' ?>" class="prep-sheet">
    <div class="prep-title"><?= htmlspecialchars($order_label, ENT_QUOTES, 'UTF-8') ?></div>
    <div class="prep-meta">
        <div>رقم الطلب: <?= htmlspecialchars($invoice_num, ENT_QUOTES, 'UTF-8') ?></div>
        <div>الوقت: <?= htmlspecialchars($pro_date, ENT_QUOTES, 'UTF-8') ?></div>
        <?php if ($emp_name !== ''): ?>
        <div>الكاشير: <?= htmlspecialchars($emp_name, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
        <?php if ($driver_name !== ''): ?>
        <div>المندوب: <?= htmlspecialchars($driver_name, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
        <?php if ($info_text !== '' && strpos($order_label, $info_text) === false): ?>
        <div><?= htmlspecialchars($info_text, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
    </div>

    <?php if (empty($items)): ?>
    <div class="prep-empty">لا توجد أصناف للتحضير</div>
    <?php else: ?>
    <table class="prep-table">
        <thead>
            <tr>
                <th class="prep-qty">الكمية</th>
                <th>الصنف</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
            <tr>
                <td class="prep-qty"><?= (int) round((float) $item['qty']) ?></td>
                <td class="prep-name">
                    <?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') ?>
                    <?php if ($item['unit'] !== ''): ?>
                    <div class="prep-unit"><?= htmlspecialchars($item['unit'], ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>
<?php
$prep_body_html = ob_get_clean();

if ($embed) {
    echo $prep_body_html;
    return;
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>بون تحضير <?= (int) $id ?></title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap">
<style>
* { box-sizing: border-box; }
html, body {
    margin: 0;
    padding: 0;
    background: #f0f0f0;
    font-family: <?= htmlspecialchars($font_family, ENT_QUOTES, 'UTF-8') ?>, Cairo, Arial, Tahoma, sans-serif;
    font-weight: bold;
}
@page {
    size: <?= htmlspecialchars($receipt_page_size, ENT_QUOTES, 'UTF-8') ?>;
    margin: 0;
}
@media print {
    html, body {
        background: #fff !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    body * { visibility: hidden; }
    #printed, #printed * { visibility: visible; }
    #printed {
        position: absolute !important;
        left: 0 !important;
        top: 0 !important;
        width: 100% !important;
        margin: 0 !important;
        padding: 3mm 2mm 4mm !important;
        border: none !important;
        box-shadow: none !important;
    }
    .no-print { display: none !important; }
}
.actions {
    width: <?= $paper_css ?>;
    margin: 12px auto;
    text-align: center;
}
.actions button, .actions a {
    display: inline-block;
    padding: 8px 16px;
    margin: 4px;
    border: 1px solid #000;
    background: #fff;
    color: #000;
    text-decoration: none;
    cursor: pointer;
    font-family: inherit;
    font-weight: bold;
}
#printed {
    width: <?= $paper_css ?>;
    margin: 0 auto 20px;
    background: #fff;
    padding: 10px 8px;
    border: 1px solid #000;
    direction: rtl;
    color: #000;
    font-size: <?= (int) $font_size ?>px;
    line-height: 1.35;
}
.prep-title {
    text-align: center;
    font-size: <?= (int) $font_size + 4 ?>px;
    border: 2px solid #000;
    padding: 6px 4px;
    margin-bottom: 8px;
}
.prep-meta {
    margin-bottom: 8px;
    border-bottom: 1px dashed #000;
    padding-bottom: 6px;
}
.prep-meta div { margin: 2px 0; }
.prep-table {
    width: 100%;
    border-collapse: collapse;
}
.prep-table th, .prep-table td {
    border-bottom: 1px solid #000;
    padding: 8px 4px;
    vertical-align: top;
}
.prep-table th {
    font-size: <?= max(12, (int) $font_size - 1) ?>px;
    text-align: center;
}
.prep-qty {
    width: 22%;
    text-align: center;
    font-size: <?= (int) $font_size + 6 ?>px;
}
.prep-name {
    text-align: right;
    font-size: <?= (int) $font_size + 2 ?>px;
}
.prep-unit {
    font-size: <?= max(11, (int) $font_size - 2) ?>px;
    font-weight: normal;
    color: #333;
}
.prep-empty {
    text-align: center;
    padding: 20px 8px;
}
</style>
</head>
<body>

<div class="actions no-print">
    <button type="button" id="printButton">طباعة بون التحضير</button>
    <a href="receipt.php?id=<?= (int) $id ?>">الفاتورة</a>
    <a href="<?= htmlspecialchars($back_page, ENT_QUOTES, 'UTF-8') ?>" id="back">عودة</a>
</div>

<div id="printed">
<?= $prep_body_html ?>
</div>

<script>
(function () {
    var autoPrint = <?= $auto_print ? 'true' : 'false' ?>;
    var autoClose = <?= $auto_close ? 'true' : 'false' ?>;
    var backPage = <?= json_encode($back_page, JSON_UNESCAPED_UNICODE) ?>;

    function doPrint() {
        window.print();
    }

    var btn = document.getElementById('printButton');
    if (btn) {
        btn.addEventListener('click', doPrint);
    }

    if (autoPrint) {
        setTimeout(doPrint, 250);
    }

    window.addEventListener('afterprint', function () {
        if (!autoClose) return;
        if (window.opener) {
            window.close();
            return;
        }
        if (backPage) {
            window.location.href = backPage;
        }
    });
})();
</script>
</body>
</html>
