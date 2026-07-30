<?php
include('../includes/connect.php');

header('Content-Type: application/json');

$order_id = intval($_POST['order_id'] ?? 0);
$item_id  = intval($_POST['item_id'] ?? 0);
$qty      = floatval($_POST['qty'] ?? 1);

if ($order_id <= 0 || $item_id <= 0 || $qty <= 0) {
    echo json_encode(['success' => false, 'message' => 'بيانات ناقصة أو غير صحيحة']);
    exit;
}

try {
    // جلب الطلب
    $stmt = $conn->prepare("SELECT id, store_id, fat_disc, fat_plus FROM ot_head WHERE id = ? AND pro_tybe = 9 AND isdeleted = 0");
    $stmt->bind_param('i', $order_id);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();
    if (!$order) {
        echo json_encode(['success' => false, 'message' => 'الطلب غير موجود أو تم إغلاقه']);
        exit;
    }

    // جلب الصنف
    $stmt = $conn->prepare("SELECT id, iname, price1, cost_price FROM myitems WHERE id = ? AND isdeleted = 0");
    $stmt->bind_param('i', $item_id);
    $stmt->execute();
    $item = $stmt->get_result()->fetch_assoc();
    if (!$item) {
        echo json_encode(['success' => false, 'message' => 'الصنف غير موجود']);
        exit;
    }

    // المخزن: من الطلب أو الإعدادات أو أول مخزن
    $store_id = intval($order['store_id'] ?? 0);
    if ($store_id == 0) {
        $store_id = intval($rowstg['def_pos_store'] ?? 0);
    }
    if ($store_id == 0) {
        $r = $conn->query("SELECT id FROM stores WHERE isdeleted = 0 ORDER BY id LIMIT 1");
        if ($r && $r->num_rows > 0) $store_id = intval($r->fetch_assoc()['id']);
    }

    $price      = floatval($item['price1']);
    $cost_price = floatval($item['cost_price']);
    $det_value  = $qty * $price;
    $itmprofit  = $qty * ($price - $cost_price);
    $pro_tybe   = 9;

    $conn->begin_transaction();

    // إدراج الصنف بنفس أعمدة إنشاء الطلب العادي
    $stmt = $conn->prepare(
        "INSERT INTO fat_details (
            pro_tybe, pro_id, item_id, u_val, qty_in, qty_out, price,
            discount, det_value, fatid, fat_tybe, det_store, cost_price, profit
        ) VALUES (?, ?, ?, 1, 0, ?, ?, 0, ?, ?, ?, ?, ?, ?)"
    );
    $stmt->bind_param('iiidddiiidd',
        $pro_tybe, $order_id, $item_id, $qty, $price,
        $det_value, $order_id, $pro_tybe, $store_id, $cost_price, $itmprofit
    );
    $stmt->execute();

    // إعادة حساب إجماليات الطلب
    $stmt = $conn->prepare("SELECT SUM(price * (qty_out - qty_in)) as total, SUM(profit) as profit FROM fat_details WHERE pro_id = ? AND isdeleted = 0");
    $stmt->bind_param('i', $order_id);
    $stmt->execute();
    $sums = $stmt->get_result()->fetch_assoc();
    $new_total  = floatval($sums['total'] ?? 0);
    $new_profit = floatval($sums['profit'] ?? 0);
    $new_net    = $new_total - floatval($order['fat_disc'] ?? 0) + floatval($order['fat_plus'] ?? 0);

    $stmt = $conn->prepare("UPDATE ot_head SET fat_total = ?, fat_net = ?, pro_value = ?, profit = ? WHERE id = ?");
    $stmt->bind_param('ddddi', $new_total, $new_net, $new_total, $new_profit, $order_id);
    $stmt->execute();

    // مزامنة القيود المحاسبية المرتبطة بالفاتورة (إن وجدت)
    $stmt = $conn->prepare("UPDATE journal_heads SET total = ? WHERE op_id = ?");
    $stmt->bind_param('di', $new_net, $order_id);
    $stmt->execute();

    $stmt = $conn->prepare("UPDATE journal_entries SET debit = ? WHERE op_id = ? AND tybe = 0");
    $stmt->bind_param('di', $new_net, $order_id);
    $stmt->execute();

    $stmt = $conn->prepare("UPDATE journal_entries SET credit = ? WHERE op_id = ? AND tybe = 1");
    $stmt->bind_param('di', $new_net, $order_id);
    $stmt->execute();

    $conn->commit();

    echo json_encode([
        'success'   => true,
        'message'   => 'تم إضافة ' . $item['iname'] . ' بنجاح',
        'new_total' => $new_total,
        'new_net'   => $new_net
    ]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'خطأ: ' . $e->getMessage()]);
}
