<?php
session_start();
include('../includes/connect.php');
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['userid'])) {
    echo json_encode(['success' => false, 'error' => 'غير مصرح']);
    exit;
}

$fat_id = isset($_POST['fat_id']) ? intval($_POST['fat_id']) : 0;
if ($fat_id <= 0) {
    echo json_encode(['success' => false, 'error' => 'معرف الصنف غير صحيح']);
    exit;
}

// جلب بيانات الصنف
$stmt = $conn->prepare("SELECT pro_id, qty_out, qty_in, price, discount FROM fat_details WHERE id = ? AND isdeleted = 0");
$stmt->bind_param("i", $fat_id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$row) {
    echo json_encode(['success' => false, 'error' => 'الصنف غير موجود']);
    exit;
}

$pro_id  = intval($row['pro_id']);
$qty     = floatval($row['qty_out']) - floatval($row['qty_in']); // الكمية الفعلية
$price   = floatval($row['price']);
$disc    = floatval($row['discount']);
$removed = false; // هل اتحذف كلياً

if ($qty > 1) {
    // ينقص 1 من الكمية ويحدث القيمة
    $new_qty      = $qty - 1;
    $new_det_value = ($new_qty * $price) - $disc;
    if ($new_det_value < 0) $new_det_value = 0;

    $stmt = $conn->prepare("UPDATE fat_details SET qty_out = qty_in + ?, det_value = ? WHERE id = ?");
    $stmt->bind_param("ddi", $new_det_value, $new_det_value, $fat_id);
    // نستخدم bind_param بشكل صحيح
    $stmt->close();

    $stmt = $conn->prepare("UPDATE fat_details SET qty_out = (qty_in + ?), det_value = ? WHERE id = ?");
    $stmt->bind_param("ddi", $new_qty, $new_det_value, $fat_id);
    $ok = $stmt->execute();
    $stmt->close();

    $new_qty_display = $new_qty;
} else {
    // الكمية = 1 → حذف كلي (soft delete)
    $stmt = $conn->prepare("UPDATE fat_details SET isdeleted = 1 WHERE id = ?");
    $stmt->bind_param("i", $fat_id);
    $ok = $stmt->execute();
    $stmt->close();
    $removed = true;
}

if (!$ok) {
    echo json_encode(['success' => false, 'error' => 'فشل التحديث: ' . $conn->error]);
    exit;
}

// إعادة حساب إجمالي الأوردر
$stmt = $conn->prepare(
    "UPDATE ot_head
     SET fat_total = (SELECT COALESCE(SUM(det_value),0) FROM fat_details WHERE pro_id = ? AND isdeleted = 0),
         fat_net   = (SELECT COALESCE(SUM(det_value),0) FROM fat_details WHERE pro_id = ? AND isdeleted = 0)
     WHERE id = ?"
);
$stmt->bind_param("iii", $pro_id, $pro_id, $pro_id);
$stmt->execute();
$stmt->close();

// جلب الإجمالي الجديد
$stmt = $conn->prepare("SELECT fat_net FROM ot_head WHERE id = ?");
$stmt->bind_param("i", $pro_id);
$stmt->execute();
$new_total = floatval($stmt->get_result()->fetch_assoc()['fat_net'] ?? 0);
$stmt->close();

echo json_encode([
    'success'   => true,
    'removed'   => $removed,                                    // true = اتحذف كلياً
    'new_qty'   => $removed ? 0 : $new_qty_display,            // الكمية الجديدة
    'new_total' => $new_total,
], JSON_UNESCAPED_UNICODE);
