<?php
include('includes/header.php');
include('includes/navbar.php');
include('includes/sidebar.php');
require_once __DIR__ . '/classes/Manufacturing.php';
require_once __DIR__ . '/classes/UserDefaults.php';
require_once __DIR__ . '/classes/InvoiceProcessor.php';

$orderId = (int) ($_GET['e'] ?? 0);
Can::authorize($orderId > 0 ? 'manufacturing.edit' : 'manufacturing.add');

$order = $orderId > 0 ? Manufacturing::loadOrder($conn, $orderId) : null;
if ($orderId > 0 && $order === null) {
    echo '<div class="content-wrapper"><section class="content"><div class="alert alert-danger m-3">أمر التصنيع غير موجود.</div></section></div>';
    include('includes/footer.php');
    exit;
}

$error = $_SESSION['mfg_error'] ?? '';
$old = $_SESSION['mfg_old'] ?? null;
unset($_SESSION['mfg_error'], $_SESSION['mfg_old']);

$isNew = $order === null;
$storeId = (int) ($old['store_id'] ?? ($order['store_id'] ?? 0));
$itemId = (int) ($old['item_id'] ?? ($order['item_id'] ?? 0));
$produceQty = (string) ($old['produce_qty'] ?? ($order['qty'] ?? ''));
$proDate = (string) ($old['pro_date'] ?? ($order['pro_date'] ?? date('Y-m-d')));
$note = (string) ($old['note'] ?? '');
if ($note === '' && $order && str_contains((string) $order['info'], ' — ')) {
    $note = explode(' — ', (string) $order['info'], 2)[1] ?? '';
}

$lines = [];
if (is_array($old['comp_id'] ?? null)) {
    foreach ($old['comp_id'] as $i => $cid) {
        $cid = (int) $cid;
        if ($cid < 1) {
            continue;
        }
        $st = $conn->prepare('SELECT iname, cost_price FROM myitems WHERE id = ? LIMIT 1');
        $st->bind_param('i', $cid);
        $st->execute();
        $row = $st->get_result()->fetch_assoc();
        $st->close();
        $q = (float) ($old['comp_qty'][$i] ?? 0);
        $cost = (float) ($row['cost_price'] ?? 0);
        $lines[] = [
            'item_id' => $cid,
            'iname' => (string) ($row['iname'] ?? ''),
            'qty' => $q,
            'cost_price' => $cost,
            'stock' => InvoiceProcessor::getRealStockQuantity($conn, $cid, $storeId),
        ];
    }
} elseif ($order) {
    foreach ($order['lines'] as $line) {
        $stock = InvoiceProcessor::getRealStockQuantity($conn, $line['item_id'], (int) $order['store_id']);
        $line['stock'] = $stock + $line['qty'];
        $lines[] = $line;
    }
}

$productRows = [];
$products = $conn->query(
    'SELECT i.id, i.iname FROM myitems i
     WHERE i.isdeleted = 0 AND i.is_assembly = 1
       AND EXISTS (SELECT 1 FROM item_bom b WHERE b.parent_id = i.id AND b.isdeleted = 0 AND b.qty > 0)
     ORDER BY i.iname'
);
if ($products) {
    while ($p = $products->fetch_assoc()) {
        $productRows[(int) $p['id']] = (string) $p['iname'];
    }
}
if ($itemId > 0 && !isset($productRows[$itemId])) {
    $pst = $conn->prepare('SELECT iname FROM myitems WHERE id = ? LIMIT 1');
    $pst->bind_param('i', $itemId);
    $pst->execute();
    $prow = $pst->get_result()->fetch_assoc();
    $pst->close();
    if ($prow) {
        $productRows[$itemId] = (string) $prow['iname'];
    }
}
$stores = $conn->query('SELECT id, aname FROM acc_head WHERE is_stock = 1 AND isdeleted = 0 ORDER BY aname');
?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1 class="m-0"><?= $isNew ? 'أمر تصنيع' : 'تعديل أمر تصنيع' ?></h1>
            <a class="btn btn-sm btn-outline-secondary" href="operations_summary.php?q=manufacturing">سجل التصنيع</a>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <?php if (isset($_GET['ok'])) { ?>
                <div class="alert alert-success">تم حفظ أمر التصنيع. الخامات اتخصمت والمنتج اتضاف، والتكلفة اتحدثت.</div>
            <?php } ?>
            <?php if ($error !== '') { ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
            <?php } ?>
            <form method="post" action="do/dosave_manufacturing.php" id="mfg-form">
                <input type="hidden" name="order_id" value="<?= $order ? (int) $order['id'] : 0 ?>">
                <div class="card">
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label>التاريخ</label>
                                <input type="date" name="pro_date" class="form-control" required value="<?= htmlspecialchars($proDate, ENT_QUOTES, 'UTF-8') ?>">
                            </div>
                            <div class="form-group col-md-3">
                                <label>المخزن</label>
                                <select name="store_id" id="mfg-store" class="form-control" required>
                                    <option value="">—</option>
                                    <?php while ($st = $stores->fetch_assoc()) {
                                        $sid = (int) $st['id'];
                                        $picked = UserDefaults::pick($conn, 'store', $sid, $sid === $storeId ? 'selected' : '', $isNew);
                                        if ($picked === null) {
                                            continue;
                                        }
                                        ?>
                                        <option value="<?= $sid ?>" <?= $picked ?>><?= htmlspecialchars($st['aname'], ENT_QUOTES, 'UTF-8') ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label>الصنف المجمع</label>
                                <select name="item_id" id="mfg-item" class="form-control" required>
                                    <option value="">—</option>
                                    <?php foreach ($productRows as $pid => $pname) { ?>
                                        <option value="<?= (int) $pid ?>" <?= (int) $pid === $itemId ? 'selected' : '' ?>><?= htmlspecialchars($pname, ENT_QUOTES, 'UTF-8') ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group col-md-2">
                                <label>كمية الإنتاج</label>
                                <input type="number" name="produce_qty" id="mfg-qty" class="form-control" step="any" min="0.0001" required value="<?= htmlspecialchars($produceQty, ENT_QUOTES, 'UTF-8') ?>">
                            </div>
                        </div>
                        <div class="form-group mb-0">
                            <label>ملاحظة</label>
                            <input type="text" name="note" class="form-control" maxlength="120" value="<?= htmlspecialchars($note, ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>الخامات</span>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="mfg-fill">احسب من الوصفة</button>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>الخام</th>
                                    <th style="width:140px">الكمية</th>
                                    <th style="width:120px">التكلفة</th>
                                    <th style="width:140px">المتاح</th>
                                    <th style="width:140px">قيمة</th>
                                </tr>
                            </thead>
                            <tbody id="mfg-lines">
                            <?php foreach ($lines as $line) {
                                $value = $line['qty'] * $line['cost_price'];
                                $short = $line['stock'] + 0.0000001 < $line['qty'];
                                ?>
                                <tr class="<?= $short ? 'table-danger' : '' ?>">
                                    <td>
                                        <?= htmlspecialchars($line['iname'], ENT_QUOTES, 'UTF-8') ?>
                                        <input type="hidden" name="comp_id[]" value="<?= (int) $line['item_id'] ?>">
                                    </td>
                                    <td><input type="number" name="comp_qty[]" class="form-control form-control-sm mfg-cqty" step="any" min="0" value="<?= htmlspecialchars((string) $line['qty'], ENT_QUOTES, 'UTF-8') ?>" data-cost="<?= htmlspecialchars((string) $line['cost_price'], ENT_QUOTES, 'UTF-8') ?>" data-stock="<?= htmlspecialchars((string) $line['stock'], ENT_QUOTES, 'UTF-8') ?>"></td>
                                    <td class="mfg-cost"><?= number_format($line['cost_price'], 2) ?></td>
                                    <td class="mfg-stock"><?= rtrim(rtrim(number_format($line['stock'], 4, '.', ''), '0'), '.') ?></td>
                                    <td class="mfg-val"><?= number_format($value, 2) ?></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer d-flex justify-content-between">
                        <strong>إجمالي التكلفة: <span id="mfg-total">0.00</span></strong>
                        <button type="submit" class="btn btn-primary">حفظ أمر التصنيع</button>
                    </div>
                </div>
            </form>
            <?php if ($productRows === [] && $isNew) { ?>
                <p class="text-muted">مفيش صنف مجمع بوصفة. افتح الصنف واحفظ وصفته الأول.</p>
            <?php } ?>
        </div>
    </section>
</div>
<?php
$extra_footer_scripts = <<<'HTML'
<script>
(function () {
  var body = document.getElementById('mfg-lines');
  function money(n) { return (Math.round(n * 100) / 100).toFixed(2); }
  function qtyText(n) {
    var s = (Math.round(n * 10000) / 10000).toString();
    return s;
  }
  function recount() {
    var total = 0;
    body.querySelectorAll('tr').forEach(function (tr) {
      var input = tr.querySelector('.mfg-cqty');
      if (!input) return;
      var q = parseFloat(input.value) || 0;
      var cost = parseFloat(input.getAttribute('data-cost')) || 0;
      var stock = parseFloat(input.getAttribute('data-stock')) || 0;
      var val = q * cost;
      total += val;
      tr.querySelector('.mfg-val').textContent = money(val);
      tr.classList.toggle('table-danger', q > stock + 0.0000001);
    });
    document.getElementById('mfg-total').textContent = money(total);
  }
  function fill() {
    var item = document.getElementById('mfg-item').value;
    var store = document.getElementById('mfg-store').value;
    var qty = document.getElementById('mfg-qty').value || '0';
    var order = document.querySelector('input[name="order_id"]').value || '0';
    if (!item) { body.innerHTML = ''; recount(); return; }
    var url = 'ajax/manufacturing_preview.php?item_id=' + encodeURIComponent(item)
      + '&store_id=' + encodeURIComponent(store)
      + '&qty=' + encodeURIComponent(qty)
      + '&order_id=' + encodeURIComponent(order);
    fetch(url).then(function (r) { return r.json(); }).then(function (data) {
      body.innerHTML = '';
      (data.lines || []).forEach(function (line) {
        var tr = document.createElement('tr');
        var short = line.qty > line.stock + 0.0000001;
        if (short) tr.className = 'table-danger';
        tr.innerHTML = '<td></td><td><input type="number" name="comp_qty[]" class="form-control form-control-sm mfg-cqty" step="any" min="0"></td><td class="mfg-cost"></td><td class="mfg-stock"></td><td class="mfg-val"></td>';
        tr.cells[0].textContent = line.name;
        var hid = document.createElement('input');
        hid.type = 'hidden';
        hid.name = 'comp_id[]';
        hid.value = line.id;
        tr.cells[0].appendChild(hid);
        var input = tr.querySelector('.mfg-cqty');
        input.value = line.qty;
        input.setAttribute('data-cost', line.cost);
        input.setAttribute('data-stock', line.stock);
        tr.querySelector('.mfg-cost').textContent = money(line.cost);
        tr.querySelector('.mfg-stock').textContent = qtyText(line.stock);
        body.appendChild(tr);
      });
      recount();
    });
  }
  document.getElementById('mfg-fill').addEventListener('click', fill);
  document.getElementById('mfg-item').addEventListener('change', fill);
  document.getElementById('mfg-qty').addEventListener('change', fill);
  document.getElementById('mfg-store').addEventListener('change', fill);
  body.addEventListener('input', recount);
  recount();
})();
</script>
HTML;
include('includes/footer.php');
