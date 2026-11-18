<?php
/**
 * طباعة غير مباشرة: بون التحضير ثم الفاتورة في أمر طباعة واحد (ورقتين).
 */
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (!isset($_SESSION['login'])) {
    header('Location: ../index.php');
    exit;
}
require_once __DIR__ . '/includes/connect.php';
require_once __DIR__ . '/../includes/receipt_design.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    echo 'لا يوجد فاتورة بهذا الرقم';
    exit;
}

$stmt = $conn->prepare('SELECT id, pro_id, pro_tybe FROM ot_head WHERE id = ? AND isdeleted = 0 LIMIT 1');
$rowfat = null;
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

$design = kody_receipt_design_load($rowstg ?? []);
$auto_print = !empty($design['auto_print']);
$auto_close = !empty($design['auto_close']);
$print_jobs = kody_receipt_print_jobs($design);

if ($print_jobs === 'receipt') {
    header('Location: receipt.php?id=' . $id);
    exit;
}
if ($print_jobs === 'prep') {
    header('Location: preparation.php?id=' . $id);
    exit;
}

$tybe = (int) ($rowfat['pro_tybe'] ?? 0);
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
if (!empty($_SESSION['pos_back_page'])) {
    $back_page = $_SESSION['pos_back_page'];
} else {
    $back_page = $type_map[$tybe] ?? '../sales.php';
}

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
$paper_css = ($receipt_paper_width === '100%')
    ? '100%; max-width: 420px;'
    : htmlspecialchars($receipt_paper_width, ENT_QUOTES, 'UTF-8');

$kody_print_embed = true;
ob_start();
include __DIR__ . '/preparation.php';
$prep_html = ob_get_clean();
unset($kody_print_embed);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>طباعة تحضير + فاتورة <?= (int) $id ?></title>
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
    html, body { background: #fff !important; margin: 0 !important; padding: 0 !important; }
    .no-print { display: none !important; }
    .sheet {
        page-break-after: always;
        break-after: page;
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 !important;
        border: none !important;
        box-shadow: none !important;
        padding: 3mm 2mm 4mm !important;
    }
    .sheet:last-child {
        page-break-after: auto;
        break-after: auto;
    }
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
.sheet {
    width: <?= $paper_css ?>;
    margin: 0 auto 16px;
    background: #fff;
    padding: 10px 8px;
    border: 1px solid #000;
    direction: rtl;
    color: #000;
}
.prep-sheet { font-size: <?= (int) $font_size ?>px; line-height: 1.35; }
.prep-title {
    text-align: center;
    font-size: <?= (int) $font_size + 4 ?>px;
    border: 2px solid #000;
    padding: 6px 4px;
    margin-bottom: 8px;
}
.prep-meta { margin-bottom: 8px; border-bottom: 1px dashed #000; padding-bottom: 6px; }
.prep-meta div { margin: 2px 0; }
.prep-table { width: 100%; border-collapse: collapse; }
.prep-table th, .prep-table td { border-bottom: 1px solid #000; padding: 8px 4px; vertical-align: top; }
.prep-table th { font-size: <?= max(12, (int) $font_size - 1) ?>px; text-align: center; }
.prep-qty { width: 22%; text-align: center; font-size: <?= (int) $font_size + 6 ?>px; }
.prep-name { text-align: right; font-size: <?= (int) $font_size + 2 ?>px; }
.prep-unit { font-size: <?= max(11, (int) $font_size - 2) ?>px; font-weight: normal; color: #333; }
.prep-empty { text-align: center; padding: 20px 8px; }
#receiptHost #printed {
    width: 100% !important;
    max-width: 100% !important;
    margin: 0 !important;
    border: none !important;
    box-shadow: none !important;
}
.status {
    text-align: center;
    color: #555;
    font-size: 13px;
    margin: 8px;
}
</style>
</head>
<body>
<div class="actions no-print">
    <button type="button" id="printButton">طباعة التحضير + الفاتورة</button>
    <a href="<?= htmlspecialchars($back_page, ENT_QUOTES, 'UTF-8') ?>" id="back">عودة</a>
</div>
<div id="loadStatus" class="status no-print">جاري تجهيز الفاتورة للطباعة...</div>

<div class="sheet">
    <?= $prep_html ?>
</div>
<div class="sheet" id="receiptHost"></div>

<iframe id="receiptFrame" src="receipt.php?id=<?= (int) $id ?>&no_auto=1" style="position:absolute;width:0;height:0;border:0;visibility:hidden;" title="receipt"></iframe>

<script>
(function () {
    var autoPrint = <?= $auto_print ? 'true' : 'false' ?>;
    var autoClose = <?= $auto_close ? 'true' : 'false' ?>;
    var backPage = <?= json_encode($back_page, JSON_UNESCAPED_UNICODE) ?>;
    var ready = false;
    var statusEl = document.getElementById('loadStatus');
    var host = document.getElementById('receiptHost');
    var frame = document.getElementById('receiptFrame');
    var printBtn = document.getElementById('printButton');

    function doGoBack() {
        if (window.opener) {
            window.close();
            return;
        }
        if (backPage) window.location.href = backPage;
    }

    function doPrint() {
        if (!ready) return;
        window.print();
    }

    function markReady() {
        ready = true;
        if (statusEl) statusEl.textContent = 'جاهز — بون التحضير ثم الفاتورة في أمر طباعة واحد';
        if (autoPrint) setTimeout(doPrint, 250);
    }

    function absorbReceipt() {
        try {
            var doc = frame.contentDocument || frame.contentWindow.document;
            var printed = doc && doc.getElementById('printed');
            if (!printed) throw new Error('no printed');

            Array.prototype.forEach.call(doc.querySelectorAll('style'), function (st) {
                var copy = document.createElement('style');
                copy.textContent = st.textContent;
                document.head.appendChild(copy);
            });

            host.innerHTML = '';
            host.appendChild(printed.cloneNode(true));
            markReady();
        } catch (err) {
            console.warn(err);
            if (statusEl) statusEl.textContent = 'تعذر دمج الفاتورة';
            host.innerHTML = '<div style="padding:12px;text-align:center"><a href="receipt.php?id=<?= (int) $id ?>">فتح الفاتورة</a></div>';
            ready = true;
        }
    }

    if (printBtn) printBtn.addEventListener('click', doPrint);
    var backBtn = document.getElementById('back');
    if (backBtn) {
        backBtn.addEventListener('click', function (e) {
            e.preventDefault();
            doGoBack();
        });
    }
    window.addEventListener('afterprint', function () {
        if (autoClose) doGoBack();
    });

    if (frame) {
        frame.addEventListener('load', function () {
            // بعد ما الباركود/QR يترسموا جوه الـ iframe
            setTimeout(absorbReceipt, 500);
        });
    } else {
        markReady();
    }
})();
</script>
</body>
</html>
