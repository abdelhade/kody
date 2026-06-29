<?php
// إيقاف الأخطاء لضمان استجابة JSON سليمة
error_reporting(0);
ini_set('display_errors', 0);

include('../includes/connect.php');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['valid' => false, 'message' => 'Invalid request']);
    exit;
}

$field = isset($_POST['field']) ? $_POST['field'] : '';
$value = isset($_POST['value']) ? trim($_POST['value']) : '';
$exclude_id = isset($_POST['exclude_id']) ? intval($_POST['exclude_id']) : 0;

if (empty($value)) {
    echo json_encode(['valid' => false, 'message' => 'القيمة مطلوبة']);
    exit;
}

if ($field === 'barcode') {
    // تحقق من جدول myitems
    $sql1 = "SELECT id FROM myitems WHERE barcode = ? AND id != ? AND isdeleted = 0 LIMIT 1";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->bind_param("si", $value, $exclude_id);
    $stmt1->execute();
    $result1 = $stmt1->get_result();
    $existsInItems = $result1->num_rows > 0;
    
    // تحقق من جدول item_units
    $sql2 = "SELECT id FROM item_units WHERE unit_barcode = ? AND item_id != ? LIMIT 1";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("si", $value, $exclude_id);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    $existsInUnits = $result2->num_rows > 0;
    
    if ($existsInItems || $existsInUnits) {
        echo json_encode(['valid' => false, 'message' => 'الباركود مستخدم بالفعل لصنف آخر']);
    } else {
        echo json_encode(['valid' => true]);
    }
} elseif ($field === 'iname') {
    $sql = "SELECT id FROM myitems WHERE iname = ? AND id != ? AND isdeleted = 0 LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $value, $exclude_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode(['valid' => false, 'message' => 'اسم الصنف مسجل بالفعل']);
    } else {
        echo json_encode(['valid' => true]);
    }
} else {
    echo json_encode(['valid' => false, 'message' => 'حقل غير مدعوم']);
}
?>
