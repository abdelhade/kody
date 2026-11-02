<?php
/**
 * إنشاء صنف سريع من بحث الفاتورة (باركود + أول وحدة).
 */
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate');

require_once __DIR__ . '/../includes/connect.php';
require_once __DIR__ . '/../includes/item_unit_sync.php';
require_once __DIR__ . '/../classes/Can.php';

if (!isset($_SESSION['userid'])) {
    echo json_encode(['success' => false, 'error' => 'غير مصرح'], JSON_UNESCAPED_UNICODE);
    exit;
}

Can::authorize('items.auto_invoice');

$barcode = trim((string) ($_POST['barcode'] ?? ''));
if ($barcode === '') {
    echo json_encode(['success' => false, 'error' => 'الباركود مطلوب'], JSON_UNESCAPED_UNICODE);
    exit;
}

if (kody_barcode_taken($conn, $barcode)) {
    echo json_encode(['success' => false, 'error' => 'الباركود مستخدم مسبقاً'], JSON_UNESCAPED_UNICODE);
    exit;
}

$unitRow = $conn->query('SELECT id FROM myunits ORDER BY id ASC LIMIT 1');
$unit = $unitRow ? $unitRow->fetch_assoc() : null;
if (!$unit) {
    echo json_encode(['success' => false, 'error' => 'لا توجد وحدات معرفة في النظام'], JSON_UNESCAPED_UNICODE);
    exit;
}
$unitId = (int) $unit['id'];

$iname = $barcode;
$nameStmt = $conn->prepare('SELECT id FROM myitems WHERE iname = ? AND isdeleted = 0 LIMIT 1');
$nameStmt->bind_param('s', $iname);
$nameStmt->execute();
$nameStmt->store_result();
if ($nameStmt->num_rows > 0) {
    $nameStmt->close();
    $iname = $barcode . ' - ' . date('His');
} else {
    $nameStmt->close();
}

$codeRow = $conn->query('SELECT MAX(code) AS max_code FROM myitems');
$codeAssoc = $codeRow ? $codeRow->fetch_assoc() : null;
$code = ($codeAssoc && $codeAssoc['max_code'] !== null)
    ? (string) ((int) $codeAssoc['max_code'] + 1)
    : '1';

$usid = (string) $_SESSION['userid'];
$name2 = '';
$info = '';
$group1 = 0;
$group2 = 0;
$costPrice = 0.0;
$price1 = 0.0;
$price2 = 0.0;
$marketPrice = 0.0;

$unitRows = [[
    'iu_id' => 0,
    'unit_id' => $unitId,
    'u_val' => 1,
    'barcode' => $barcode,
    'cost' => 0,
    'price1' => 0,
    'price2' => 0,
    'price3' => 0,
]];

try {
    $unitRows = kody_prepare_unit_barcodes($conn, $barcode, $unitRows, 0);
} catch (RuntimeException $e) {
    $msg = $e->getMessage();
    $map = [
        'duplicate_barcode' => 'الباركود مستخدم مسبقاً',
        'no_base_unit' => 'يجب وجود وحدة أساسية بمعامل 1',
        'barcode_length' => 'طول الباركود غير صالح',
    ];
    echo json_encode(['success' => false, 'error' => $map[$msg] ?? 'تعذّر تجهيز الوحدة'], JSON_UNESCAPED_UNICODE);
    exit;
}

$conn->begin_transaction();
try {
    $stmt = $conn->prepare(
        'INSERT INTO myitems (iname, name2, code, barcode, info, market_price, cost_price, price1, price2, group1, group2, user)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->bind_param(
        'sssssddddiis',
        $iname,
        $name2,
        $code,
        $barcode,
        $info,
        $marketPrice,
        $costPrice,
        $price1,
        $price2,
        $group1,
        $group2,
        $usid
    );
    if (!$stmt->execute()) {
        throw new RuntimeException('save_failed');
    }
    $itemId = (int) $conn->insert_id;
    $stmt->close();

    kody_sync_item_units($conn, $itemId, $unitRows);
    $conn->query("INSERT INTO process (type) VALUES ('auto add item')");
    $conn->commit();
} catch (Throwable $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'error' => 'تعذّر حفظ الصنف'], JSON_UNESCAPED_UNICODE);
    exit;
}

echo json_encode([
    'success' => true,
    'id' => $itemId,
    'iname' => $iname,
    'barcode' => $barcode,
    'price' => $price1,
], JSON_UNESCAPED_UNICODE);
