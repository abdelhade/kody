<?php
include 'includes/header.php';
include 'includes/navbar.php';
include 'includes/sidebar.php';

function settle_money($n): string
{
    return number_format((float) $n, 2);
}

$col = $conn->query("SHOW COLUMNS FROM item_group LIKE 'supplier_id'");
if ($col && $col->num_rows === 0) {
    $conn->query('ALTER TABLE item_group ADD COLUMN supplier_id INT(11) DEFAULT NULL');
}

$suppliers = [];
$supRes = $conn->query(
    "SELECT s.id, s.aname, s.code,
            COALESCE((
                SELECT SUM(je.debit) - SUM(je.credit)
                FROM journal_entries je
                WHERE je.account_id = s.id AND je.isdeleted = 0
            ), 0) AS balance
     FROM acc_head s
     WHERE s.isdeleted = 0 AND s.code LIKE '211%' AND s.code <> '211'
     ORDER BY s.aname"
);
if ($supRes) {
    while ($row = $supRes->fetch_assoc()) {
        $suppliers[(int) $row['id']] = $row;
    }
}

$itemQty = [];
$needPrice = [];
$itemRes = $conn->query(
    "SELECT i.id, i.cost_price, g.supplier_id,
            COALESCE(b.qty, i.itmqty) AS qty
     FROM myitems i
     INNER JOIN item_group g ON g.id = i.group1 AND g.isdeleted = 0 AND g.supplier_id > 0
     LEFT JOIN (
         SELECT item_id, SUM(qty_in) - SUM(qty_out) AS qty
         FROM fat_details
         WHERE isdeleted = 0
         GROUP BY item_id
     ) b ON b.item_id = i.id
     WHERE i.isdeleted = 0"
);
if ($itemRes) {
    while ($item = $itemRes->fetch_assoc()) {
        $sid = (int) $item['supplier_id'];
        $qty = (float) $item['qty'];
        if ($qty <= 0 || !isset($suppliers[$sid])) {
            continue;
        }
        $cost = (float) $item['cost_price'];
        $itemId = (int) $item['id'];
        $itemQty[] = ['supplier' => $sid, 'qty' => $qty, 'cost' => $cost, 'id' => $itemId];
        if ($cost <= 0) {
            $needPrice[] = $itemId;
        }
    }
}

$lastPrices = [];
if ($needPrice !== []) {
    $in = implode(',', array_map('intval', $needPrice));
    $priceRes = $conn->query(
        "SELECT d.item_id, d.price, d.u_val
         FROM fat_details d
         INNER JOIN (
             SELECT item_id, MAX(id) AS max_id
             FROM fat_details
             WHERE isdeleted = 0 AND pro_tybe = 4 AND qty_in > 0 AND item_id IN ($in)
             GROUP BY item_id
         ) x ON x.max_id = d.id"
    );
    if ($priceRes) {
        while ($pr = $priceRes->fetch_assoc()) {
            $uVal = (float) $pr['u_val'];
            $lastPrices[(int) $pr['item_id']] = $uVal > 0 ? ((float) $pr['price'] / $uVal) : (float) $pr['price'];
        }
    }
}

$costs = [];
foreach ($itemQty as $item) {
    $unitCost = $item['cost'] > 0 ? $item['cost'] : ($lastPrices[$item['id']] ?? 0.0);
    $costs[$item['supplier']] = ($costs[$item['supplier']] ?? 0.0) + ($item['qty'] * $unitCost);
}

$payments = [];
if ($suppliers !== []) {
    $payRes = $conn->query(
        "SELECT h.acc1, h.pro_value, h.pro_date, c.aname AS counter_name
         FROM ot_head h
         LEFT JOIN acc_head c ON c.id = h.acc2
         WHERE h.pro_tybe = 2 AND h.isdeleted = 0 AND h.acc1 > 0
         ORDER BY h.pro_date DESC, h.id DESC"
    );
    if ($payRes) {
        while ($pay = $payRes->fetch_assoc()) {
            $sid = (int) $pay['acc1'];
            if (!isset($payments[$sid]) && isset($suppliers[$sid])) {
                $payments[$sid] = $pay;
            }
        }
    }
}

$rows = [];
foreach ($suppliers as $sid => $sup) {
    $itemsCost = $costs[$sid] ?? 0.0;
    $balance = (float) $sup['balance'];
    // الرصيد = مدين − دائن. السالب يعني مستحق للمورد.
    $diff = $itemsCost + $balance;
    if (abs($diff) < 0.01) {
        $status = 'مطابق';
        $statusClass = 'badge-success';
    } elseif ($diff > 0) {
        $status = 'أصناف أعلى';
        $statusClass = 'badge-warning';
    } else {
        $status = 'مستحق أعلى';
        $statusClass = 'badge-danger';
    }
    $pay = $payments[$sid] ?? null;
    $rows[] = [
        'name' => (string) $sup['aname'],
        'code' => (string) $sup['code'],
        'items' => $itemsCost,
        'balance' => $balance,
        'diff' => $diff,
        'status' => $status,
        'status_class' => $statusClass,
        'paid' => $pay ? (float) $pay['pro_value'] : null,
        'date' => $pay ? (string) $pay['pro_date'] : '',
        'counter' => $pay ? (string) ($pay['counter_name'] ?? '') : '',
    ];
}

$sumItems = 0.0;
$sumBalance = 0.0;
$sumDiff = 0.0;
foreach ($rows as $row) {
    $sumItems += $row['items'];
    $sumBalance += $row['balance'];
    $sumDiff += $row['diff'];
}
?>

<style>
@media print {
    .no-print, .main-sidebar, .main-header, .main-footer { display: none !important; }
    .content-wrapper { margin: 0 !important; padding: 0 !important; }
}
</style>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">تقرير تسوية الموردين</h3>
                    <button type="button" onclick="window.print()" class="btn btn-light btn-sm no-print">
                        <i class="fas fa-print"></i> طباعة
                    </button>
                </div>
                <div class="card-body">
                    <p class="text-muted">قيمة الأصناف هي رصيد البضاعة المتبقي للمجموعات التابعة للمورد مضروباً في التكلفة. الفرق = هذه القيمة + رصيد المورد، والرصيد السالب يعني أن المبلغ مستحق له.</p>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="bg-dark text-white">
                                <tr>
                                    <th>#</th>
                                    <th>المورد</th>
                                    <th>ق. أصناف المورد (تكلفة)</th>
                                    <th>رصيد المورد</th>
                                    <th>الفرق</th>
                                    <th>حالة</th>
                                    <th>آخر دفعة</th>
                                    <th>تاريخ</th>
                                    <th>الحساب المقابل</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($rows === []): ?>
                                    <tr>
                                        <td colspan="9" class="text-center text-muted">لا يوجد موردون</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($rows as $i => $row): ?>
                                        <tr>
                                            <td><?= $i + 1 ?></td>
                                            <td><?= htmlspecialchars($row['code'] . ' - ' . $row['name']) ?></td>
                                            <td class="text-center"><?= settle_money($row['items']) ?></td>
                                            <td class="text-center"><?= settle_money($row['balance']) ?></td>
                                            <td class="text-center font-weight-bold"><?= settle_money($row['diff']) ?></td>
                                            <td class="text-center"><span class="badge <?= $row['status_class'] ?>"><?= $row['status'] ?></span></td>
                                            <td class="text-center"><?= $row['paid'] === null ? '—' : settle_money($row['paid']) ?></td>
                                            <td class="text-center"><?= $row['date'] !== '' ? htmlspecialchars($row['date']) : '—' ?></td>
                                            <td><?= $row['counter'] !== '' ? htmlspecialchars($row['counter']) : '—' ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                            <?php if ($rows !== []): ?>
                            <tfoot class="bg-light">
                                <tr>
                                    <td colspan="2" class="text-right font-weight-bold">الإجمالي</td>
                                    <td class="text-center font-weight-bold"><?= settle_money($sumItems) ?></td>
                                    <td class="text-center font-weight-bold"><?= settle_money($sumBalance) ?></td>
                                    <td class="text-center font-weight-bold"><?= settle_money($sumDiff) ?></td>
                                    <td colspan="4"></td>
                                </tr>
                            </tfoot>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include 'includes/footer.php'; ?>
