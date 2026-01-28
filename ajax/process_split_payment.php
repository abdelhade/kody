<?php
// Prevent PHP notices/warnings from corrupting JSON
error_reporting(0);
ini_set('display_errors', 0);

// Start output buffering to catch any stray whitespace or includes output
ob_start();

session_start();
include('../includes/connect.php');

// Clear any output generated so far (like whitespace from includes)
ob_clean();

header('Content-Type: application/json');

// استلام البيانات
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'No data received']);
    exit;
}

$original_order_id = intval($data['order_id']);
$table_id = intval($data['table_id']);
$selected_items = $data['items']; // Array of detail IDs to split
$paid_amount = floatval($data['paid_amount']);
$payment_method = $data['payment_method'] ?? 'cash'; // 'cash' or 'visa'

if (empty($selected_items)) {
    echo json_encode(['success' => false, 'message' => 'No items selected']);
    exit;
}

try {
    $conn->begin_transaction();

    // 1. إنشاء فاتورة جديدة للأصناف المدفوعة
    
    // جلب بيانات الفاتورة الأصلية لنسخ البيانات (العميل، الموظف، الخ)
    $stmt = $conn->prepare("SELECT * FROM ot_head WHERE id = ?");
    $stmt->bind_param("i", $original_order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $orig_order = $result->fetch_assoc();
    $stmt->close();
    
    if (!$orig_order) throw new Exception("Original order not found");
    
    // إنشاء رأس فاتورة جديدة (مدفوعة)
    $type_sales = 3; // Sales Invoce
    
    // الحصول على رقم فاتورة جديد
    $next_id_stmt = $conn->prepare("SELECT MAX(CAST(pro_id AS UNSIGNED)) + 1 FROM ot_head WHERE pro_tybe = ?");
    $next_id_stmt->bind_param("i", $type_sales);
    $next_id_stmt->execute();
    $inv_num_res = $next_id_stmt->get_result()->fetch_row();
    $new_invoice_num = $inv_num_res[0] ?? 1;
    $next_id_stmt->close();

    $new_info = "سداد جزئي من طاولة " . $table_id;

    $insert_head = $conn->prepare("INSERT INTO ot_head 
        (pro_id, pro_tybe, pro_date, store_id, emp_id, acc1, acc2, pro_value, fat_net, info, user)
        VALUES (?, ?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?)");
        
    $pro_value = $paid_amount;
    $fat_net = $paid_amount;
    
    // Note: acc2 here uses store_id, which matches original logic. Ensure store_id is int.
    $acc2 = $orig_order['store_id']; 
    $user_id = isset($_SESSION['userid']) ? $_SESSION['userid'] : 1;

    $insert_head->bind_param("siiiidddsi", 
        $new_invoice_num, 
        $type_sales, 
        $orig_order['store_id'], 
        $orig_order['emp_id'],
        $orig_order['acc1'], 
        $acc2,
        $pro_value,
        $fat_net,
        $new_info,
        $user_id
    );
    
    $insert_head->execute();
    $new_head_id = $conn->insert_id;
    $insert_head->close();

    // 2. نقل الأصناف المختارة من الفاتورة القديمة للجديدة
    foreach ($selected_items as $detail_id) {
        $update_detail = $conn->prepare("UPDATE fat_details SET fatid = ?, pro_id = ?, pro_tybe = ? WHERE id = ?");
        $update_detail->bind_param("isii", $new_head_id, $new_invoice_num, $type_sales, $detail_id);
        $update_detail->execute();
        $update_detail->close();
    }
    
    // 3. إعادة حساب إجمالي الفاتورة القديمة
    $calc_old = $conn->prepare("SELECT SUM(det_value) FROM fat_details WHERE fatid = ? AND isdeleted = 0");
    $calc_old->bind_param("i", $original_order_id);
    $calc_old->execute();
    $res_old = $calc_old->get_result()->fetch_row();
    $remaining_total = floatval($res_old[0] ?? 0);
    $calc_old->close();
    
    // تحديث الفاتورة القديمة
    $update_old = $conn->prepare("UPDATE ot_head SET pro_value = ?, fat_total = ?, fat_net = ? WHERE id = ?");
    $update_old->bind_param("dddi", $remaining_total, $remaining_total, $remaining_total, $original_order_id);
    $update_old->execute();
    $update_old->close();
    
    // 5. التحقق مما إذا كانت الفاتورة القديمة فرغت تماماً
    if ($remaining_total <= 0) {
        // إغلاق الطاولة
        $close_tbl = $conn->prepare("UPDATE tables SET table_case = 0 WHERE id = ?");
        $close_tbl->bind_param("i", $table_id);
        $close_tbl->execute();
        
        // تعليم الفاتورة القديمة كمحذوفة
        $conn->query("UPDATE ot_head SET isdeleted = 1 WHERE id = $original_order_id");
    }

    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'تم سداد الأصناف المختارة بنجاح', 'new_invoice_id' => $new_head_id]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
