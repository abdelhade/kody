<?php
/**
 * تجربة التصنيع على قاعدة التشغيل ثم مسح بيانات التجربة.
 *   php tests/manufacturing_smoke.php
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../includes/load_env.php';
require_once __DIR__ . '/../includes/db_name.php';
require_once __DIR__ . '/../classes/Manufacturing.php';
require_once __DIR__ . '/../classes/InvoiceProcessor.php';

mysqli_report(MYSQLI_REPORT_OFF);
$conn = new mysqli(env('DB_HOST', 'localhost'), env('DB_USER', 'root'), env('DB_PASS', ''));
if ($conn->connect_error) {
    fwrite(STDERR, "db connect failed\n");
    exit(1);
}
$db = kody_select_existing_db($conn);
if ($db === null) {
    fwrite(STDERR, "no database\n");
    exit(1);
}
$conn->set_charset('utf8mb4');
echo "db=$db\n";
$col = $conn->query("SHOW COLUMNS FROM myitems LIKE 'is_assembly'");
if (!$col || $col->num_rows < 1) {
    fwrite(STDERR, "شغّل تحديث قاعدة البيانات الأول (update/044)\n");
    exit(1);
}

$store = $conn->query('SELECT id FROM acc_head WHERE is_stock = 1 AND isdeleted = 0 ORDER BY id LIMIT 1')->fetch_assoc();
if (!$store) {
    fwrite(STDERR, "no store\n");
    exit(1);
}
$storeId = (int) $store['id'];
$user = (int) ($conn->query('SELECT id FROM users ORDER BY id LIMIT 1')->fetch_assoc()['id'] ?? 1);

$stamp = 'mfg' . date('His');
$ids = [];
$make = $conn->prepare('INSERT INTO myitems (iname, barcode, cost_price, price1, is_assembly, user) VALUES (?, ?, ?, 1, ?, ?)');
$specs = [
    [$stamp . ' خام أ', $stamp . 'a', 10, 0],
    [$stamp . ' خام ب', $stamp . 'b', 2, 0],
    [$stamp . ' منتج', $stamp . 'p', 5, 1],
];
foreach ($specs as $i => $spec) {
    $make->bind_param('ssdii', $spec[0], $spec[1], $spec[2], $spec[3], $user);
    if (!$make->execute()) {
        fwrite(STDERR, "item insert: {$conn->error}\n");
        exit(1);
    }
    $ids[$i] = (int) $conn->insert_id;
}
$make->close();
[$compA, $compB, $product] = $ids;

$fail = null;
$orderId = 0;
$purchaseId = 0;
try {
    $type = InvoiceProcessor::INVOICE_TYPES['PURCHASE'];
    $proId = 990000 + (int) date('His');
    $today = date('Y-m-d');
    $info = 'افتتاح تجربة تصنيع';
    $stmt = $conn->prepare(
        'INSERT INTO ot_head (pro_id, pro_tybe, is_stock, is_journal, info, pro_date, store_id, pro_value, fat_net, user)
         VALUES (?, ?, 1, 0, ?, ?, ?, 50, 50, ?)'
    );
    $stmt->bind_param('iissii', $proId, $type, $info, $today, $storeId, $user);
    if (!$stmt->execute()) {
        throw new RuntimeException('purchase head: ' . $stmt->error);
    }
    $purchaseId = (int) $conn->insert_id;
    $stmt->close();

    $stmt = $conn->prepare(
        'INSERT INTO fat_details (pro_tybe, pro_id, item_id, u_val, qty_in, qty_out, doc_qty, price, det_value, fatid, fat_tybe, det_store, cost_price)
         VALUES (?, ?, ?, 1, ?, 0, ?, ?, ?, ?, ?, ?, ?)'
    );
    $qty = 10.0;
    $price = 5.0;
    $value = 50.0;
    $stmt->bind_param('iiiddddiidd', $type, $purchaseId, $product, $qty, $qty, $price, $value, $purchaseId, $type, $storeId, $price);
    if (!$stmt->execute()) {
        throw new RuntimeException('purchase line: ' . $stmt->error);
    }
    $stmt->close();

    $stockIn = $conn->prepare(
        'INSERT INTO fat_details (pro_tybe, pro_id, item_id, u_val, qty_in, qty_out, price, det_value, fatid, fat_tybe, det_store, cost_price)
         VALUES (4, 0, ?, 1, ?, 0, ?, ?, 0, 4, ?, ?)'
    );
    $qa = 100.0;
    $ca = 10.0;
    $va = 1000.0;
    $stockIn->bind_param('idddid', $compA, $qa, $ca, $va, $storeId, $ca);
    $stockIn->execute();
    $qb = 50.0;
    $cb = 2.0;
    $vb = 100.0;
    $stockIn->bind_param('idddid', $compB, $qb, $cb, $vb, $storeId, $cb);
    $stockIn->execute();
    $stockIn->close();

    Manufacturing::saveRecipe($conn, $product, [$compA, $compB], [2, 0.5]);
    $orderId = Manufacturing::save($conn, 0, $storeId, $product, 4, $today, 'تجربة', [$compA, $compB], [8, 2], $user);

    $qtyOf = static function (mysqli $conn, int $item, int $store): float {
        return InvoiceProcessor::getRealStockQuantity($conn, $item, $store);
    };
    $costOf = static function (mysqli $conn, int $item): float {
        $s = $conn->prepare('SELECT cost_price, itmqty, is_assembly FROM myitems WHERE id = ?');
        $s->bind_param('i', $item);
        $s->execute();
        $row = $s->get_result()->fetch_assoc();
        $s->close();
        return (float) $row['cost_price'];
    };

    $a = $qtyOf($conn, $compA, $storeId);
    $b = $qtyOf($conn, $compB, $storeId);
    $p = $qtyOf($conn, $product, $storeId);
    $pc = $costOf($conn, $product);
    echo "after produce A=$a B=$b P=$p cost=$pc order=$orderId\n";
    if (abs($a - 92) > 0.001 || abs($b - 48) > 0.001 || abs($p - 14) > 0.001) {
        throw new RuntimeException('stock mismatch');
    }
    $expect = (5 * 10 + 21 * 4) / 14;
    if (abs($pc - $expect) > 0.02) {
        throw new RuntimeException('cost mismatch ' . $pc . ' expected ' . $expect);
    }

    Manufacturing::save($conn, $orderId, $storeId, $product, 2, $today, 'تعديل', [$compA, $compB], [4, 1], $user);
    $a = $qtyOf($conn, $compA, $storeId);
    $b = $qtyOf($conn, $compB, $storeId);
    $p = $qtyOf($conn, $product, $storeId);
    $pc = $costOf($conn, $product);
    echo "after edit A=$a B=$b P=$p cost=$pc\n";
    if (abs($a - 96) > 0.001 || abs($b - 49) > 0.001 || abs($p - 12) > 0.001) {
        throw new RuntimeException('edit stock mismatch');
    }
    $expect = (5 * 10 + 21 * 2) / 12;
    if (abs($pc - $expect) > 0.02) {
        throw new RuntimeException('edit cost mismatch ' . $pc . ' expected ' . $expect);
    }

    try {
        Manufacturing::save($conn, 0, $storeId, $product, 1000, $today, '', [$compA, $compB], [2000, 500], $user);
        throw new RuntimeException('negative stock was allowed');
    } catch (RuntimeException $e) {
        if ($e->getMessage() === 'negative stock was allowed') {
            throw $e;
        }
        echo "blocked: {$e->getMessage()}\n";
    }

    InvoiceProcessor::hardDelete($conn, $orderId);
    $orderId = 0;
    $a = $qtyOf($conn, $compA, $storeId);
    $p = $qtyOf($conn, $product, $storeId);
    $pc = $costOf($conn, $product);
    echo "after delete A=$a P=$p cost=$pc\n";
    if (abs($a - 100) > 0.001 || abs($p - 10) > 0.001 || abs($pc - 5) > 0.02) {
        throw new RuntimeException('delete did not reverse');
    }
    echo "OK\n";
} catch (Throwable $e) {
    $fail = $e->getMessage();
    echo "FAIL $fail\n";
}

if ($orderId > 0) {
    $conn->query('DELETE FROM fat_details WHERE pro_id = ' . $orderId . ' OR fatid = ' . $orderId);
    $conn->query('DELETE FROM ot_head WHERE id = ' . $orderId);
}
if ($purchaseId > 0) {
    $conn->query('DELETE FROM fat_details WHERE pro_id = ' . $purchaseId . ' OR fatid = ' . $purchaseId);
    $conn->query('DELETE FROM ot_head WHERE id = ' . $purchaseId);
}
$in = implode(',', array_map('intval', $ids));
$conn->query("DELETE FROM fat_details WHERE item_id IN ($in)");
$conn->query("DELETE FROM item_bom WHERE parent_id IN ($in) OR component_id IN ($in)");
$conn->query("DELETE FROM item_units WHERE item_id IN ($in)");
$conn->query("DELETE FROM myitems WHERE id IN ($in)");
foreach ($ids as $id) {
    InvoiceProcessor::syncItemQuantities($conn, 0);
}
exit($fail === null ? 0 : 1);
