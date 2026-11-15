<?php
session_start();
if (!isset($_SESSION['login'])) {
    header('Location: ../index.php');
    exit;
}
require_once __DIR__ . '/includes/connect.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    echo 'لا يوجد فاتورة بهذا الرقم';
    exit;
}

$stmt = $conn->prepare('SELECT id, pro_id, pro_tybe, info, pro_date FROM ot_head WHERE id = ? AND isdeleted = 0 LIMIT 1');
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

$tybe = (int) ($rowfat['pro_tybe'] ?? 0);
$pos_type = $rowstg['pos_type'] ?? 'barcode';
$pos_back = ($pos_type === 'clothes') ? '../pos_barcode.php' : '../pos_barcode.php';
if ($pos_type === 'clothes') {
    $pos_back = '../pos_clothes.php';
}
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

$from_invoice = isset($_GET['src']) && $_GET['src'] === 'invoice';
$invoice_url = $from_invoice
    ? ('receipt.php?id=' . $id . '&src=invoice')
    : ('receipt.php?id=' . $id);
$prep_url = 'preparation.php?id=' . $id;
$invoice_num = htmlspecialchars((string) ($rowfat['pro_id'] ?? $id), ENT_QUOTES, 'UTF-8');
$info_text = trim((string) ($rowfat['info'] ?? ''));
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>اختيار الطباعة — <?= $invoice_num ?></title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap">
<script src="../js/print_agent_client.js"></script>
<style>
* { box-sizing: border-box; }
body {
    margin: 0;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(160deg, #f4f6f8 0%, #e8eef5 100%);
    font-family: Cairo, Tahoma, Arial, sans-serif;
    color: #1a1a1a;
}
.box {
    width: min(420px, 94vw);
    background: #fff;
    border: 1px solid #d0d7de;
    border-radius: 12px;
    padding: 22px 18px 16px;
    box-shadow: 0 10px 28px rgba(0,0,0,.08);
}
h1 {
    margin: 0 0 6px;
    font-size: 1.35rem;
    text-align: center;
}
.meta {
    text-align: center;
    color: #555;
    font-size: .95rem;
    margin-bottom: 18px;
}
.btn {
    display: block;
    width: 100%;
    border: 1px solid #222;
    background: #fff;
    color: #111;
    font-family: inherit;
    font-weight: 700;
    font-size: 1.05rem;
    padding: 14px 12px;
    margin: 0 0 10px;
    border-radius: 8px;
    cursor: pointer;
    text-decoration: none;
    text-align: center;
}
.btn-primary { background: #1f6feb; border-color: #1f6feb; color: #fff; }
.btn-warn { background: #f0ad00; border-color: #d99800; color: #111; }
.btn-both { background: #111; border-color: #111; color: #fff; }
.btn-back { background: #f5f5f5; border-color: #bbb; color: #333; margin-top: 6px; }
.hint {
    text-align: center;
    font-size: .85rem;
    color: #777;
    margin-top: 4px;
}
.agent {
    text-align: center;
    font-size: .82rem;
    margin-bottom: 12px;
    color: #666;
}
.agent.on { color: #166534; }
.agent.off { color: #9a3412; }
</style>
</head>
<body>
<div class="box">
    <h1>اختر نوع الطباعة</h1>
    <div class="meta">
        فاتورة رقم <?= $invoice_num ?>
        <?php if ($info_text !== ''): ?>
        <div><?= htmlspecialchars($info_text, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
    </div>
    <div id="agentStatus" class="agent">جاري فحص وكيل الطباعة...</div>

    <button type="button" class="btn btn-primary" data-mode="receipt">طباعة الفاتورة</button>
    <button type="button" class="btn btn-warn" data-mode="prep">طباعة بون التحضير</button>
    <button type="button" class="btn btn-both" data-mode="both">طباعة الاتنين</button>
    <a class="btn btn-back" href="<?= htmlspecialchars($back_page, ENT_QUOTES, 'UTF-8') ?>">عودة بدون طباعة</a>
    <div class="hint">لو الوكيل شغال: الطباعة تروح للطابعات المحلية حسب إعداداته</div>
</div>

<script>
(function () {
    var invoiceId = <?= (int) $id ?>;
    var invoiceUrl = <?= json_encode($invoice_url, JSON_UNESCAPED_UNICODE) ?>;
    var prepUrl = <?= json_encode($prep_url, JSON_UNESCAPED_UNICODE) ?>;
    var statusEl = document.getElementById('agentStatus');
    var busy = false;

    function setStatus(on) {
        if (!statusEl) return;
        statusEl.className = 'agent ' + (on ? 'on' : 'off');
        statusEl.textContent = on
            ? 'وكيل الطباعة متصل — هتتطبع على طابعات الجهاز'
            : 'وكيل الطباعة غير متصل — هتتفتح صفحة المتصفح';
    }

    if (window.KodyPrintAgent) {
        window.KodyPrintAgent.health().then(setStatus);
    } else {
        setStatus(false);
    }

    function fallbackFor(mode) {
        if (mode === 'receipt') return [invoiceUrl];
        if (mode === 'prep') return [prepUrl];
        return [prepUrl, invoiceUrl];
    }

    document.querySelectorAll('button[data-mode]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (busy) return;
            busy = true;
            btn.disabled = true;
            var mode = btn.getAttribute('data-mode');
            var label = btn.textContent;
            btn.textContent = 'جاري الطباعة...';

            var done = function () {
                busy = false;
                btn.disabled = false;
                btn.textContent = label;
            };

            if (!window.KodyPrintAgent) {
                fallbackFor(mode).forEach(function (u, i) {
                    setTimeout(function () { window.open(u, '_blank'); }, i * 250);
                });
                done();
                return;
            }

            window.KodyPrintAgent.printOrFallback(invoiceId, mode, fallbackFor(mode))
                .then(function (usedAgent) {
                    if (usedAgent && statusEl) {
                        statusEl.textContent = 'تم الإرسال لوكيل الطباعة';
                        statusEl.className = 'agent on';
                    }
                })
                .finally(done);
        });
    });
})();
</script>
</body>
</html>
