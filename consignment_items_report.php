<?php
include 'includes/header.php';
include 'includes/navbar.php';
include 'includes/sidebar.php';

function consign_qty($n): string
{
    $s = number_format((float) $n, 3, '.', '');
    $s = rtrim(rtrim($s, '0'), '.');
    return $s === '' || $s === '-' ? '0' : $s;
}

function consign_money($n): string
{
    return number_format((float) $n, 2);
}

$supplierId = isset($_GET['supplier']) ? (int) $_GET['supplier'] : 0;

$suppliers = [];
$supRes = $conn->query("SELECT id, aname FROM acc_head WHERE code LIKE '211%' AND code != '211' AND isdeleted = 0 ORDER BY aname");
if ($supRes) {
    while ($row = $supRes->fetch_assoc()) {
        $suppliers[] = $row;
    }
}

$supplierName = '';
foreach ($suppliers as $sup) {
    if ((int) $sup['id'] === $supplierId) {
        $supplierName = (string) $sup['aname'];
        break;
    }
}

$rows = [];
if ($supplierId > 0 && $supplierName !== '') {
    $stmtItems = $conn->prepare(
        "SELECT i.id, i.code, i.iname, i.itmqty,
                (
                    SELECT u.uname
                    FROM item_units iu
                    INNER JOIN myunits u ON u.id = iu.unit_id
                    WHERE iu.item_id = i.id
                    ORDER BY CASE WHEN iu.u_val = 1 THEN 0 ELSE 1 END, iu.id
                    LIMIT 1
                ) AS unit_name
         FROM myitems i
         INNER JOIN item_group g ON g.id = i.group1 AND g.isdeleted = 0 AND g.supplier_id = ?
         WHERE i.isdeleted = 0
         ORDER BY i.iname"
    );
    $stmtItems->bind_param('i', $supplierId);
    $stmtItems->execute();
    $itemRes = $stmtItems->get_result();
    $items = [];
    $ids = [];
    while ($item = $itemRes->fetch_assoc()) {
        $id = (int) $item['id'];
        $ids[] = $id;
        $items[$id] = $item;
    }
    $stmtItems->close();

    $moves = [];
    $prices = [];
    if ($ids !== []) {
        $in = implode(',', $ids);
        $moveRes = $conn->query(
            "SELECT d.item_id,
                    SUM(CASE WHEN d.pro_tybe = 4 THEN d.qty_in ELSE 0 END) AS purch_in,
                    SUM(CASE WHEN d.pro_tybe = 10 THEN d.qty_out ELSE 0 END) AS purch_ret,
                    SUM(CASE WHEN d.pro_tybe IN (3, 9) THEN d.qty_out ELSE 0 END) AS sales_out,
                    SUM(CASE WHEN d.pro_tybe = 11 THEN d.qty_in ELSE 0 END) AS sales_ret,
                    SUM(CASE WHEN d.pro_tybe = 14 THEN d.qty_in - d.qty_out ELSE 0 END) AS opening_doc,
                    SUM(d.qty_in) - SUM(d.qty_out) AS balance
             FROM fat_details d
             WHERE d.isdeleted = 0 AND d.item_id IN ($in)
             GROUP BY d.item_id"
        );
        if ($moveRes) {
            while ($mv = $moveRes->fetch_assoc()) {
                $moves[(int) $mv['item_id']] = $mv;
            }
        }

        $priceRes = $conn->query(
            "SELECT d.item_id, d.price, d.u_val, h.acc2
             FROM fat_details d
             LEFT JOIN ot_head h ON h.id = d.fatid AND h.isdeleted = 0
             WHERE d.isdeleted = 0 AND d.pro_tybe = 4 AND d.qty_in > 0 AND d.item_id IN ($in)
             ORDER BY d.item_id, COALESCE(h.pro_date, DATE(d.crtime)) DESC, d.id DESC"
        );
        if ($priceRes) {
            while ($pr = $priceRes->fetch_assoc()) {
                $itemId = (int) $pr['item_id'];
                $uVal = (float) $pr['u_val'];
                $unitPrice = $uVal > 0 ? ((float) $pr['price'] / $uVal) : (float) $pr['price'];
                $prices[$itemId][] = [
                    'price' => $unitPrice,
                    'supplier' => (int) $pr['acc2'],
                ];
            }
        }
    }

    foreach ($items as $id => $item) {
        $mv = $moves[$id] ?? null;
        $netPurch = $mv ? ((float) $mv['purch_in'] - (float) $mv['purch_ret']) : 0.0;
        $netSales = $mv ? ((float) $mv['sales_out'] - (float) $mv['sales_ret']) : 0.0;
        $openingDoc = $mv ? (float) $mv['opening_doc'] : 0.0;
        $current = $mv ? (float) $mv['balance'] : (float) $item['itmqty'];
        $purchPlusOpen = $netPurch + $openingDoc + ($current - ($openingDoc + $netPurch - $netSales));

        $itemPrices = $prices[$id] ?? [];
        $fromSupplier = [];
        $any = [];
        foreach ($itemPrices as $pr) {
            $any[] = $pr['price'];
            if ($pr['supplier'] === $supplierId) {
                $fromSupplier[] = $pr['price'];
            }
        }
        $used = $fromSupplier !== [] ? $fromSupplier : $any;
        $lastPrice = $used[0] ?? null;
        $prevPrice = $used[1] ?? null;

        $rows[] = [
            'code' => (string) $item['code'],
            'name' => (string) $item['iname'],
            'unit' => (string) ($item['unit_name'] ?? ''),
            'last' => $lastPrice,
            'prev' => $prevPrice,
            'purch_open' => $purchPlusOpen,
            'sales' => $netSales,
            'balance' => $current,
        ];
    }
}

$sumPurch = 0.0;
$sumSales = 0.0;
$sumBalance = 0.0;
foreach ($rows as $row) {
    $sumPurch += $row['purch_open'];
    $sumSales += $row['sales'];
    $sumBalance += $row['balance'];
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
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">تقرير أصناف الأمانة</h3>
                    <div>بضاعة الأمانة من مورد<?= $supplierName !== '' ? ' — ' . htmlspecialchars($supplierName) : '' ?></div>
                </div>
                <div class="card-body">
                    <form method="get" class="row mb-4 no-print">
                        <div class="col-md-4">
                            <label>المورد</label>
                            <select name="supplier" class="form-control" required>
                                <option value="">اختر المورد</option>
                                <?php foreach ($suppliers as $sup): ?>
                                    <option value="<?= (int) $sup['id'] ?>" <?= $supplierId === (int) $sup['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($sup['aname']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label style="visibility: hidden;">عرض</label>
                            <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-search"></i> عرض</button>
                        </div>
                        <div class="col-md-2">
                            <label style="visibility: hidden;">طباعة</label>
                            <button type="button" onclick="window.print()" class="btn btn-success btn-block"><i class="fas fa-print"></i> طباعة</button>
                        </div>
                    </form>

                    <?php if ($supplierId <= 0): ?>
                        <p class="text-muted mb-0">اختر المورد لعرض أصناف الأمانة التابعة لمجموعاته.</p>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="bg-dark text-white">
                                <tr>
                                    <th>كود الصنف</th>
                                    <th>اسم الصنف</th>
                                    <th>الوحدة</th>
                                    <th>آخر سعر شراء</th>
                                    <th>س. شراء قبل الأخير</th>
                                    <th>ك صافي المشتريات وأول المدة</th>
                                    <th>ك صافي المبيعات</th>
                                    <th>ك الرصيد الحالي</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($rows === []): ?>
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">لا توجد أصناف أمانة لهذا المورد</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($rows as $row): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($row['code']) ?></td>
                                            <td><?= htmlspecialchars($row['name']) ?></td>
                                            <td><?= htmlspecialchars($row['unit']) ?></td>
                                            <td class="text-center"><?= $row['last'] === null ? '—' : consign_money($row['last']) ?></td>
                                            <td class="text-center"><?= $row['prev'] === null ? '—' : consign_money($row['prev']) ?></td>
                                            <td class="text-center"><?= consign_qty($row['purch_open']) ?></td>
                                            <td class="text-center"><?= consign_qty($row['sales']) ?></td>
                                            <td class="text-center font-weight-bold"><?= consign_qty($row['balance']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                            <?php if ($rows !== []): ?>
                            <tfoot class="bg-light">
                                <tr>
                                    <td colspan="5" class="text-right font-weight-bold">الإجمالي</td>
                                    <td class="text-center font-weight-bold"><?= consign_qty($sumPurch) ?></td>
                                    <td class="text-center font-weight-bold"><?= consign_qty($sumSales) ?></td>
                                    <td class="text-center font-weight-bold"><?= consign_qty($sumBalance) ?></td>
                                </tr>
                            </tfoot>
                            <?php endif; ?>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include 'includes/footer.php'; ?>
