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

// التحقق من أن الباركود الرئيسي فريد (مع استثناء الصنف الحالي)
if ($barcode !== '') {
    // تحقق من myitems (باركود صنف آخر)
    $stmtbc = $conn->prepare("SELECT id FROM myitems WHERE barcode = ? AND id != ? LIMIT 1");
    $stmtbc->bind_param('si', $barcode, $item_id);
    $stmtbc->execute();
    $stmtbc->store_result();
    if ($stmtbc->num_rows > 0) {
        $stmtbc->close();
        header('Location: ../add_item.php?edit=' . (int)$item_id . '&error=duplicate_barcode&bc=' . urlencode($barcode));
        exit;
    }
    $stmtbc->close();

    // تحقق من item_units: باركود موجود في وحدة تابعة لصنف آخر
    // نستثني الوحدات ذات u_val=1 التابعة لنفس الصنف لأنها قد تحمل نفس باركود الصنف الرئيسي
    $stmtbc2 = $conn->prepare(
        "SELECT id FROM item_units 
         WHERE unit_barcode = ? 
           AND item_id != ? 
         LIMIT 1"
    );
    $stmtbc2->bind_param('si', $barcode, $item_id);
    $stmtbc2->execute();
    $stmtbc2->store_result();
    if ($stmtbc2->num_rows > 0) {
        $stmtbc2->close();
        header('Location: ../add_item.php?edit=' . (int)$item_id . '&error=duplicate_barcode&bc=' . urlencode($barcode));
        exit;
    }
    $stmtbc2->close();
}

// التحقق من أن باركودات الوحدات فريدة
if (!empty($_POST['unit_barcode'])) {
    $unitBarcodes = array_filter(array_map('trim', $_POST['unit_barcode']));

    // استثناء باركود الوحدة الأولى (u_val=1) لأنه قد يطابق باركود الصنف الرئيسي عمداً
    $unitBarcodesForCheck = $unitBarcodes;
    if (isset($_POST['u_val'][0]) && floatval($_POST['u_val'][0]) == 1) {
        array_shift($unitBarcodesForCheck);
    }

    // تحقق من التكرار داخل النموذج (بين الوحدات نفسها + الباركود الرئيسي مقابل الوحدات غير الأساسية)
    $allBarcodes = array_merge([$barcode], array_values($unitBarcodesForCheck));
    if (count($allBarcodes) !== count(array_unique($allBarcodes))) {
        header('Location: ../add_item.php?edit=' . (int)$item_id . '&error=duplicate_barcode&bc=internal');
        exit;
    }

    // تحقق من عدم الوجود في صنوف أخرى (كل الوحدات بما فيها الأولى)
    foreach ($unitBarcodes as $idx => $ub) {
        // تخطي لو الوحدة الأولى بنفس باركود الصنف الرئيسي (مسموح)
        if ($idx == 0 && $ub === $barcode) {
            continue;
        }
        $stmtub = $conn->prepare("SELECT id FROM myitems WHERE barcode = ? AND id != ? LIMIT 1");
        $stmtub->bind_param('si', $ub, $item_id);
        $stmtub->execute();
        $stmtub->store_result();
        $existsInItems = $stmtub->num_rows > 0;
        $stmtub->close();

        $stmtub2 = $conn->prepare("SELECT id FROM item_units WHERE unit_barcode = ? AND item_id != ? LIMIT 1");
        $stmtub2->bind_param('si', $ub, $item_id);
        $stmtub2->execute();
        $stmtub2->store_result();
        $existsInUnits = $stmtub2->num_rows > 0;
        $stmtub2->close();

        if ($existsInItems || $existsInUnits) {
                header('Location: ../add_item.php?edit=' . (int)$item_id . '&error=duplicate_barcode&bc=' . urlencode($ub));
                exit;
            }
    }
}

// Item Name Validation (Check for duplicate names)
$iname = $_POST['iname'];
$sqlchkname  = "SELECT * FROM myitems WHERE iname = ? AND id != ?";
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
$cost_price = isset($_POST['cost_price'][0]) ? (float) $_POST['cost_price'][0] : 0;
$price1 = isset($_POST['price1'][0]) ? (float) $_POST['price1'][0] : 0;
$price2 = isset($_POST['price2'][0]) ? (float) $_POST['price2'][0] : 0;
$market_price = isset($_POST['market_price'][0]) ? (float) $_POST['market_price'][0] : 0;
$group1 = (int) $group1;
$group2 = (int) $group2;




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

$postedUnits = isset($_POST['unit_id']) && is_array($_POST['unit_id']) ? $_POST['unit_id'] : [];
if ($postedUnits === []) {
    header('Location: ../add_item.php?edit=' . $item_id . '&error=no_units');
    exit;
}

$unitRows = [];
foreach ($postedUnits as $index => $unit_id) {
    $unit_barcode = trim((string) ($_POST['unit_barcode'][$index] ?? ''));
    if ($unit_barcode === '') {
        $baseBarcode = trim((string) ($_POST['unit_barcode'][0] ?? ''));
        $unit_barcode = '99' . $index . $baseBarcode;
    }
    $unitRows[] = [
        'iu_id' => (int) ($_POST['iu_id'][$index] ?? 0),
        'unit_id' => (int) $unit_id,
        'u_val' => $_POST['u_val'][$index] ?? 1,
        'barcode' => $unit_barcode,
        'cost' => (float) ($_POST['cost_price'][$index] ?? 0),
        'price1' => (float) ($_POST['price1'][$index] ?? 0),
        'price2' => (float) ($_POST['price2'][$index] ?? 0),
        'price3' => (float) ($_POST['market_price'][$index] ?? ($_POST['price3'][$index] ?? 0)),
    ];
}

$conn->begin_transaction();
try {
    $stmtItem = $conn->prepare(
        'UPDATE myitems SET iname = ?, name2 = ?, code = ?, info = ?, cost_price = ?, price1 = ?, price2 = ?, market_price = ?, group1 = ?, group2 = ?, manual_price_edit = 1 WHERE id = ?'
    );
    $stmtItem->bind_param(
        'ssssddddiii',
        $iname,
        $name2,
        $code,
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
    $allowed = ['no_units', 'duplicate_unit', 'invalid_unit', 'unit_in_use', 'duplicate_barcode'];
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
