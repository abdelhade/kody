<?php
// إيقاف عرض الأخطاء لضمان JSON نظيف
error_reporting(0);
ini_set('display_errors', 0);

session_start();

// استخدام dirname للحصول على المسار الصحيح
$root_path = dirname(__DIR__);
include($root_path . '/includes/connect.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['barcode'])) {
    $barcode = trim($_POST['barcode']);
    
    if (empty($barcode)) {
        echo json_encode(['success' => false, 'message' => 'الباركود فارغ']);
        exit;
    }
    
    // البحث بالباركود أو ID أو اسم الصنف
    // محاولة تحويل الباركود لرقم للبحث بالـ ID
    $numericBarcode = is_numeric($barcode) ? intval($barcode) : 0;
    $store_id = isset($_POST['store_id']) ? intval($_POST['store_id']) : 0;
    
    $balance_subquery_items = $store_id > 0 ? "COALESCE((SELECT SUM(qty_in - qty_out) FROM fat_details WHERE item_id = myitems.id AND det_store = $store_id AND isdeleted = 0), 0)" : "0";
    $balance_subquery_units = $store_id > 0 ? "COALESCE((SELECT SUM(qty_in - qty_out) FROM fat_details WHERE item_id = m.id AND det_store = $store_id AND isdeleted = 0), 0)" : "0";

    $sql = "SELECT *, $balance_subquery_items as balance FROM myitems
            WHERE (barcode = ? OR code = ? OR id = ?) AND isdeleted = 0 
            ORDER BY 
                CASE 
                    WHEN barcode = ? THEN 1 
                    WHEN code = ? THEN 2 
                    WHEN id = ? THEN 3 
                    ELSE 4 
                END 
            LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssissi", $barcode, $barcode, $numericBarcode, $barcode, $barcode, $numericBarcode);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $item = $result->fetch_assoc();
        
        // تحديد السعر - جرب price أو price1
        $price = 0;
        if (isset($item['price']) && !empty($item['price'])) {
            $price = floatval($item['price']);
        } elseif (isset($item['price1']) && !empty($item['price1'])) {
            $price = floatval($item['price1']);
        }
        
        echo json_encode([
            'success' => true,
            'item' => [
                'id' => $item['id'],
                'name' => $item['iname'],
                'price' => $price,
                'barcode' => $item['barcode'],
                'u_val' => 1,
                'balance' => floatval($item['balance'] ?? 0)
            ]
        ]);
    } else {
        // البحث في باركود الوحدات (item_units)
        $sql_units = "SELECT iu.item_id, m.iname, iu.unit_barcode, iu.price1, iu.u_val, u.uname as unit_name,
                             $balance_subquery_units as balance
                      FROM item_units iu
                      JOIN myitems m ON m.id = iu.item_id
                      LEFT JOIN myunits u ON u.id = iu.unit_id
                      WHERE iu.unit_barcode = ? AND iu.isdeleted = 0 AND m.isdeleted = 0
                      LIMIT 1";
        $stmt_units = $conn->prepare($sql_units);
        $stmt_units->bind_param("s", $barcode);
        $stmt_units->execute();
        $result_units = $stmt_units->get_result();

        if ($result_units->num_rows > 0) {
            $unit_row = $result_units->fetch_assoc();
            $unit_name = $unit_row['unit_name'] ?? '';
            $item_name = $unit_row['iname'];
            if (!empty($unit_name)) {
                $item_name .= ' (' . $unit_name . ')';
            }

            echo json_encode([
                'success' => true,
                'item' => [
                    'id' => $unit_row['item_id'],
                    'name' => $item_name,
                    'price' => floatval($unit_row['price1']),
                    'barcode' => $unit_row['unit_barcode'],
                    'u_val' => floatval($unit_row['u_val']),
                    'balance' => floatval($unit_row['balance'] ?? 0)
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'الصنف غير موجود']);
        }
        $stmt_units->close();
    }
} else {
    echo json_encode(['success' => false, 'message' => 'طلب غير صحيح']);
}
?>
