<?php
session_start();
include('../includes/connect.php');
require_once('../includes/item_unit_sync.php');

$item_id = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;
if ($item_id < 1) {
    header('Location: ../add_item.php?error=save_failed');
    exit;
}
$usid = $_SESSION['userid'];

// Ensure user is authenticated
if (!isset($usid)) {
    header('location:login.php');
    exit();
} 

// Barcode Handling
if (isset($_POST['barcode'])) {
    $barcode = trim($_POST['barcode']);
} else {
    // Get the last barcode from the database and generate a new one
    $last_barcode_result = $conn->query('SELECT barcode FROM myitems ORDER BY id DESC LIMIT 1');
    if ($last_barcode_result && $last_barcode_result->num_rows > 0) {
        $last_barcode = $last_barcode_result->fetch_assoc()['barcode'];
        $barcode = $last_barcode + 1;
    } else {
        $barcode = 1000001; // Starting point if no barcodes exist
    }
}
$barcode = (string) $barcode;

if ($barcode !== '' && kody_barcode_taken($conn, $barcode, $item_id)) {
    header('Location: ../add_item.php?edit=' . (int) $item_id . '&error=duplicate_barcode&bc=' . urlencode($barcode));
    exit;
}

// Item Name Validation (Check for duplicate names)
$iname = $_POST['iname'];
$sqlchkname  = "SELECT * FROM myitems WHERE iname = ? AND id != ? AND isdeleted = 0";
$stmt = $conn->prepare($sqlchkname);
$stmt->bind_param('si', $iname, $item_id);
$stmt->execute();
$chkname = $stmt->get_result()->fetch_assoc();

if ($chkname !== null) {
    header('Location: ../add_item.php?edit=' . (int) $item_id . '&error=duplicate_name');
    exit;
}

// Prepare to update the main item
$code = $_POST['code'];
$name2 = $_POST['name2']; 
$group1 = $_POST['group1']; 
$group2 = $_POST['group2']; 
$info = $_POST['info']; 
$group1 = (int) $group1;
$group2 = (int) $group2;

$postedUnits = isset($_POST['unit_id']) && is_array($_POST['unit_id']) ? $_POST['unit_id'] : [];
if ($postedUnits === []) {
    header('Location: ../add_item.php?edit=' . $item_id . '&error=no_units');
    exit;
}

$unitRows = [];
foreach ($postedUnits as $index => $unit_id) {
    $unitRows[] = [
        'iu_id' => (int) ($_POST['iu_id'][$index] ?? 0),
        'unit_id' => (int) $unit_id,
        'u_val' => $_POST['u_val'][$index] ?? 1,
        'barcode' => trim((string) ($_POST['unit_barcode'][$index] ?? '')),
        'cost' => (float) ($_POST['cost_price'][$index] ?? 0),
        'price1' => (float) ($_POST['price1'][$index] ?? 0),
        'price2' => (float) ($_POST['price2'][$index] ?? 0),
        'price3' => (float) ($_POST['market_price'][$index] ?? ($_POST['price3'][$index] ?? 0)),
    ];
}

try {
    $unitRows = kody_prepare_unit_barcodes($conn, (string) $barcode, $unitRows, $item_id);
} catch (RuntimeException $e) {
    $code = $e->getMessage();
    $allowed = ['duplicate_barcode', 'no_base_unit', 'barcode_length'];
    if (!in_array($code, $allowed, true)) {
        $code = 'save_failed';
    }
    header('Location: ../add_item.php?edit=' . $item_id . '&error=' . $code);
    exit;
}

$cost_price = 0.0;
$price1 = 0.0;
$price2 = 0.0;
$market_price = 0.0;
foreach ($unitRows as $row) {
    if (number_format((float) $row['u_val'], 3, '.', '') === '1.000') {
        $cost_price = (float) $row['cost'];
        $price1 = (float) $row['price1'];
        $price2 = (float) $row['price2'];
        $market_price = (float) $row['price3'];
        break;
    }
}




// Handle image upload
if (isset($_FILES['imgs']) && !empty($_FILES['imgs']['name'][0])) {
    $imgs_name = $_FILES['imgs']['name'][0];
    $tmp_name = $_FILES['imgs']['tmp_name'][0];
    
    $arrkvr = explode(".", $imgs_name);
    $kvr_ext = end($arrkvr);
    
    $allow_ext = ["jpg", "png", "gif", "jpeg", "webp"];
    if (in_array($kvr_ext, $allow_ext)) {
        $new_kvr_name = $arrkvr[0] . rand(1, 1000000) . "." . $kvr_ext;
        if (move_uploaded_file($tmp_name, "../uploads/$new_kvr_name")) {
            // حذف الصور القديمة للصنف
            $conn->query("DELETE FROM imgs WHERE itemid = '$item_id'");
            // إضافة الصورة الجديدة
            $conn->query("INSERT INTO imgs (iname, itemid) VALUES ('$new_kvr_name', '$item_id')");
        }
    }
}

// إضافة العمود إذا لم يكن موجوداً (DDL يُنهي أي معاملة مفتوحة)
$checkColumn = $conn->query("SHOW COLUMNS FROM myitems LIKE 'manual_price_edit'");
if ($checkColumn->num_rows == 0) {
    $conn->query("ALTER TABLE myitems ADD COLUMN manual_price_edit TINYINT(1) DEFAULT 0");
}

$conn->begin_transaction();
try {
    $stmtItem = $conn->prepare(
        'UPDATE myitems SET iname = ?, name2 = ?, code = ?, barcode = ?, info = ?, cost_price = ?, price1 = ?, price2 = ?, market_price = ?, group1 = ?, group2 = ?, manual_price_edit = 1 WHERE id = ?'
    );
    $stmtItem->bind_param(
        'sssssddddiii',
        $iname,
        $name2,
        $code,
        $barcode,
        $info,
        $cost_price,
        $price1,
        $price2,
        $market_price,
        $group1,
        $group2,
        $item_id
    );
    $stmtItem->execute();
    $stmtItem->close();

    kody_sync_item_units($conn, $item_id, $unitRows);
    $conn->commit();
} catch (RuntimeException $e) {
    $conn->rollback();
    $code = $e->getMessage();
    $allowed = ['no_units', 'duplicate_unit', 'invalid_unit', 'unit_in_use', 'duplicate_barcode', 'no_base_unit', 'barcode_length'];
    if (!in_array($code, $allowed, true)) {
        $code = 'save_failed';
    }
    header('Location: ../add_item.php?edit=' . $item_id . '&error=' . $code);
    exit;
} catch (Throwable $e) {
    $conn->rollback();
    header('Location: ../add_item.php?edit=' . $item_id . '&error=save_failed');
    exit;
}

header('Location: ../add_item.php?edit=' . $item_id . '&saved=1');
exit;
?>
