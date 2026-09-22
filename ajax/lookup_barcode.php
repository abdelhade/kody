<?php
/**
 * البحث عن صنف بالباركود
 * يغطي: باركود الصنف الرئيسي، باركود الوحدات، وكود الصنف
 */

header('Content-Type: application/json; charset=utf-8');
require_once '../includes/connect.php';

$barcode = isset($_REQUEST['barcode']) ? trim($_REQUEST['barcode']) : '';

if ($barcode === '') {
    echo json_encode(['success' => false, 'error' => 'لم يتم إرسال باركود']);
    exit;
}

/**
 * تنفيذ استعلام مُعد وإرجاع أول صف
 */
function barcodeFetchOne($conn, $query, $value, $type = 's')
{
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        return null;
    }
    $stmt->bind_param($type, $value);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $row ?: null;
}

try {
    // 1) باركود الصنف الرئيسي
    $row = barcodeFetchOne(
        $conn,
        "SELECT id, iname, name2, price1, cost_price, barcode, NULL AS u_val
         FROM myitems
         WHERE barcode = ? AND isdeleted = 0
         LIMIT 1",
        $barcode
    );

    // 2) باركود وحدة فرعية
    if (!$row) {
        $row = barcodeFetchOne(
            $conn,
            "SELECT mi.id, mi.iname, mi.name2, mi.price1, mi.cost_price, iu.unit_barcode AS barcode, iu.u_val
             FROM item_units iu
             JOIN myitems mi ON mi.id = iu.item_id
             WHERE iu.unit_barcode = ? AND iu.isdeleted = 0 AND mi.isdeleted = 0
             LIMIT 1",
            $barcode
        );
    }

    // 3) كود الصنف (رقمي فقط)
    if (!$row && ctype_digit($barcode)) {
        $row = barcodeFetchOne(
            $conn,
            "SELECT id, iname, name2, price1, cost_price, barcode, NULL AS u_val
             FROM myitems
             WHERE code = ? AND isdeleted = 0
             LIMIT 1",
            (int) $barcode,
            'i'
        );
    }

    if (!$row) {
        echo json_encode(['success' => false, 'error' => 'الصنف غير موجود']);
        exit;
    }

    echo json_encode([
        'success' => true,
        'item' => [
            'id'         => (int) $row['id'],
            'iname'      => $row['iname'],
            'name2'      => $row['name2'] ?? '',
            'price1'     => (float) ($row['price1'] ?? 0),
            'cost_price' => (float) ($row['cost_price'] ?? 0),
            'barcode'    => $row['barcode'] ?? '',
            'u_val'      => isset($row['u_val']) ? (float) $row['u_val'] : null,
        ],
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'خطأ في البحث']);
}
