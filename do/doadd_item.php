<?php
session_start();
include('../includes/connect.php');
require_once('../includes/item_unit_sync.php');

$kodyAddItemJson = isset($_POST['return_json']) && (string) $_POST['return_json'] === '1';

if (!isset($_SESSION['userid'])) {
    if ($kodyAddItemJson) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'error' => 'غير مصرح'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    header('Location: ../index.php');
    exit;
}

$usid = (string) $_SESSION['userid'];

function kody_add_item_fail(string $code): void
{
    global $kodyAddItemJson;
    if ($kodyAddItemJson) {
        $messages = [
            'duplicate_barcode' => 'الباركود مستخدم مسبقاً، يرجى إدخال باركود فريد.',
            'duplicate_name' => 'يوجد صنف بنفس الاسم، اختر اسماً مختلفاً.',
            'save_failed' => 'تعذّر حفظ البيانات. حاول مرة أخرى.',
            'invalid_image' => 'صيغة الصورة غير مسموحة. استخدم jpg أو png أو gif أو jpeg أو webp.',
            'no_units' => 'لا يمكن حفظ صنف بدون وحدات.',
            'duplicate_unit' => 'لا يمكن تكرار نفس الوحدة أو نفس المعامل.',
            'invalid_unit' => 'بيانات الوحدة غير صالحة. اختر وحدة ومعاملاً أكبر من صفر.',
            'no_base_unit' => 'يجب وجود وحدة أساسية بمعامل 1.',
        ];
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => false,
            'error' => $messages[$code] ?? 'حدث خطأ أثناء الحفظ.',
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    header('Location: ../add_item.php?error=' . rawurlencode($code));
    exit;
}

$barcode = isset($_POST['barcode']) ? trim((string) $_POST['barcode']) : '';
if ($barcode === '') {
    $barcode = kody_next_barcode($conn);
}
if (kody_barcode_taken($conn, $barcode)) {
    kody_add_item_fail('duplicate_barcode');
}

$postedUnits = isset($_POST['unit_id']) && is_array($_POST['unit_id']) ? $_POST['unit_id'] : [];
if ($postedUnits === []) {
    kody_add_item_fail('no_units');
}

$unitRows = [];
foreach ($postedUnits as $index => $unitId) {
    $unitRows[] = [
        'iu_id' => 0,
        'unit_id' => (int) $unitId,
        'u_val' => $_POST['u_val'][$index] ?? 1,
        'barcode' => trim((string) ($_POST['unit_barcode'][$index] ?? '')),
        'cost' => (float) ($_POST['cost_price'][$index] ?? 0),
        'price1' => (float) ($_POST['price1'][$index] ?? 0),
        'price2' => (float) ($_POST['price2'][$index] ?? 0),
        'price3' => (float) ($_POST['market_price'][$index] ?? 0),
    ];
}

try {
    $unitRows = kody_prepare_unit_barcodes($conn, $barcode, $unitRows, 0);
} catch (RuntimeException $e) {
    $code = $e->getMessage();
    $allowed = ['duplicate_barcode', 'no_base_unit', 'barcode_length'];
    kody_add_item_fail(in_array($code, $allowed, true) ? $code : 'save_failed');
}

$base = null;
foreach ($unitRows as $row) {
    if (number_format((float) $row['u_val'], 3, '.', '') === '1.000') {
        $base = $row;
        break;
    }
}
if ($base === null) {
    kody_add_item_fail('no_base_unit');
}

$iname = trim((string) ($_POST['iname'] ?? ''));
if ($iname === '') {
    kody_add_item_fail('save_failed');
}

$nameStmt = $conn->prepare('SELECT id FROM myitems WHERE iname = ? AND isdeleted = 0 LIMIT 1');
$nameStmt->bind_param('s', $iname);
$nameStmt->execute();
$nameStmt->store_result();
if ($nameStmt->num_rows > 0) {
    $nameStmt->close();
    kody_add_item_fail('duplicate_name');
}
$nameStmt->close();

$allowExt = ['jpg', 'png', 'gif', 'jpeg', 'webp'];
$imageFiles = [];
if (!empty($_FILES['imgs']['name'][0])) {
    $count = count($_FILES['imgs']['name']);
    for ($i = 0; $i < $count; $i++) {
        if (($_FILES['imgs']['error'][$i] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            continue;
        }
        if (($_FILES['imgs']['error'][$i] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            kody_add_item_fail('save_failed');
        }
        $ext = strtolower((string) pathinfo((string) $_FILES['imgs']['name'][$i], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowExt, true)) {
            kody_add_item_fail('invalid_image');
        }
        $imageFiles[] = [
            'tmp' => $_FILES['imgs']['tmp_name'][$i],
            'ext' => $ext,
        ];
    }
}

$name2 = (string) ($_POST['name2'] ?? '');
$code = (string) ($_POST['code'] ?? '');
$info = (string) ($_POST['info'] ?? '');
$group1 = (int) ($_POST['group1'] ?? 0);
$group2 = (int) ($_POST['group2'] ?? 0);
$costPrice = (float) $base['cost'];
$price1 = (float) $base['price1'];
$price2 = (float) $base['price2'];
$marketPrice = (float) $base['price3'];

$moved = [];
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

    $imgStmt = $conn->prepare('INSERT INTO imgs (iname, itemid) VALUES (?, ?)');
    foreach ($imageFiles as $image) {
        $fileName = 'item_' . $itemId . '_' . bin2hex(random_bytes(4)) . '.' . $image['ext'];
        $dest = dirname(__DIR__) . '/uploads/' . $fileName;
        if (!move_uploaded_file($image['tmp'], $dest)) {
            throw new RuntimeException('save_failed');
        }
        $moved[] = $dest;
        $imgStmt->bind_param('si', $fileName, $itemId);
        if (!$imgStmt->execute()) {
            throw new RuntimeException('save_failed');
        }
    }
    $imgStmt->close();

    $conn->query("INSERT INTO process (type) VALUES ('add item')");
    $conn->commit();
} catch (RuntimeException $e) {
    $conn->rollback();
    foreach ($moved as $path) {
        if (is_file($path)) {
            unlink($path);
        }
    }
    $code = $e->getMessage();
    $allowed = ['no_units', 'duplicate_unit', 'invalid_unit', 'duplicate_barcode', 'no_base_unit', 'barcode_length'];
    kody_add_item_fail(in_array($code, $allowed, true) ? $code : 'save_failed');
} catch (Throwable $e) {
    $conn->rollback();
    foreach ($moved as $path) {
        if (is_file($path)) {
            unlink($path);
        }
    }
    kody_add_item_fail('save_failed');
}

if ($kodyAddItemJson) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => true,
        'id' => $itemId,
        'iname' => $iname,
        'barcode' => $barcode,
        'price' => $price1,
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

header('Location: ../add_item.php?saved=1');
exit;
