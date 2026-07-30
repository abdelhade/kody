<?php
include('../includes/connect.php');

header('Content-Type: application/json');

$order_id       = $_POST['order_id'] ?? '';
$old_table_id   = $_POST['old_table_id'] ?? '';
$new_table_id   = $_POST['new_table_id'] ?? '';
$new_table_name = $_POST['new_table_name'] ?? '';
$transfer_type  = $_POST['transfer_type'] ?? 'all';
$item_ids_raw   = $_POST['item_ids'] ?? [];

if (empty($old_table_id) || empty($new_table_id) || empty($new_table_name)) {
    echo json_encode(['success' => false, 'message' => 'بيانات ناقصة']);
    exit;
}

if ($transfer_type === 'single' && empty($order_id)) {
    echo json_encode(['success' => false, 'message' => 'رقم الطلب غير محدد']);
    exit;
}

// معالجة قائمة IDs الأصناف
$item_ids = [];
if (is_array($item_ids_raw)) {
    $item_ids = array_map('intval', array_filter($item_ids_raw));
} elseif (is_string($item_ids_raw) && !empty($item_ids_raw)) {
    $item_ids = array_map('intval', array_filter(explode(',', $item_ids_raw)));
}

if ($transfer_type === 'items' && empty($item_ids)) {
    echo json_encode(['success' => false, 'message' => 'لم يتم تحديد أي أصناف لنقلها']);
    exit;
}

try {
    // بدء المعاملة
    $conn->begin_transaction();
    
    // جلب اسم الطاولة القديمة
    $old_table_query = "SELECT tname FROM tables WHERE id = ?";
    $stmt = $conn->prepare($old_table_query);
    $stmt->bind_param('i', $old_table_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $old_table_data = $result->fetch_assoc();
    $old_table_name = $old_table_data['tname'] ?? '';

    // التحقق من وجود عمود table_id في ot_head
    $has_table_id = false;
    $col_check = $conn->query("SHOW COLUMNS FROM ot_head LIKE 'table_id'");
    if ($col_check && $col_check->num_rows > 0) {
        $has_table_id = true;
    }

    if ($transfer_type === 'all') {
        // 1. نقل جميع الطلبات النشطة للطاولة القديمة
        // أنماط دقيقة: اسم الطاولة في نهاية النص أو يليه مسافة (حتى لا تتطابق "طاولة 1" مع "طاولة 12" أو أرقام عشوائية)
        $pat1 = "%$old_table_name";
        $pat2 = "%$old_table_name %";
        
        if ($has_table_id) {
            $info_update_query = "UPDATE ot_head 
                                  SET info = REPLACE(info, ?, ?),
                                      table_id = ?
                                  WHERE (table_id = ? OR info LIKE ? OR info LIKE ?)
                                    AND pro_tybe = 9 AND isdeleted = 0";
            $stmt = $conn->prepare($info_update_query);
            $stmt->bind_param('ssiiss', $old_table_name, $new_table_name, $new_table_id, $old_table_id, $pat1, $pat2);
        } else {
            $info_update_query = "UPDATE ot_head 
                                  SET info = REPLACE(info, ?, ?)
                                  WHERE (info LIKE ? OR info LIKE ?)
                                    AND pro_tybe = 9 AND isdeleted = 0";
            $stmt = $conn->prepare($info_update_query);
            $stmt->bind_param('ssss', $old_table_name, $new_table_name, $pat1, $pat2);
        }
        $stmt->execute();

        // الطاولة القديمة تصبح فارغة (0)
        $update_old_table = "UPDATE tables SET table_case = 0 WHERE id = ?";
        $stmt = $conn->prepare($update_old_table);
        $stmt->bind_param('i', $old_table_id);
        $stmt->execute();

    } elseif ($transfer_type === 'single') {
        // 2. نقل طلب واحد كامل
        if ($has_table_id) {
            $info_update_query = "UPDATE ot_head SET info = REPLACE(info, ?, ?), table_id = ? WHERE id = ?";
            $stmt = $conn->prepare($info_update_query);
            $stmt->bind_param('ssii', $old_table_name, $new_table_name, $new_table_id, $order_id);
        } else {
            $info_update_query = "UPDATE ot_head SET info = REPLACE(info, ?, ?) WHERE id = ?";
            $stmt = $conn->prepare($info_update_query);
            $stmt->bind_param('ssi', $old_table_name, $new_table_name, $order_id);
        }
        $stmt->execute();

        // التأكد مما إذا كان هناك طلبات نشطة أخرى على الطاولة القديمة
        $pat1 = "%$old_table_name";
        $pat2 = "%$old_table_name %";
        
        if ($has_table_id) {
            $check_query = "SELECT COUNT(*) as active_cnt FROM ot_head 
                            WHERE (table_id = ? OR info LIKE ? OR info LIKE ?)
                            AND pro_tybe = 9 AND isdeleted = 0";
            $check_stmt = $conn->prepare($check_query);
            $check_stmt->bind_param('iss', $old_table_id, $pat1, $pat2);
        } else {
            $check_query = "SELECT COUNT(*) as active_cnt FROM ot_head 
                            WHERE (info LIKE ? OR info LIKE ?)
                            AND pro_tybe = 9 AND isdeleted = 0";
            $check_stmt = $conn->prepare($check_query);
            $check_stmt->bind_param('ss', $pat1, $pat2);
        }
        $check_stmt->execute();
        $check_res = $check_stmt->get_result()->fetch_assoc();
        $remaining = intval($check_res['active_cnt'] ?? 0);

        if ($remaining === 0) {
            $update_old_table = "UPDATE tables SET table_case = 0 WHERE id = ?";
            $stmt = $conn->prepare($update_old_table);
            $stmt->bind_param('i', $old_table_id);
            $stmt->execute();
        }

    } elseif ($transfer_type === 'items') {
        // 3. نقل أصناف محددة فقط
        // البحث عن طلب نشط على الطاولة الجديدة أو إنشاء طلب جديد
        // أنماط دقيقة على اسم الطاولة الكامل (نهاية النص أو يليه مسافة) حتى لا يتطابق رقم الطاولة مع أرقام هواتف/عناوين أو طاولات أخرى
        $pat_n1 = "%$new_table_name";
        $pat_n2 = "%$new_table_name %";
        
        if ($has_table_id) {
            $new_ord_stmt = $conn->prepare("SELECT id FROM ot_head WHERE (table_id = ? OR info LIKE ? OR info LIKE ?) AND pro_tybe = 9 AND isdeleted = 0 ORDER BY id DESC LIMIT 1");
            $new_ord_stmt->bind_param('iss', $new_table_id, $pat_n1, $pat_n2);
        } else {
            $new_ord_stmt = $conn->prepare("SELECT id FROM ot_head WHERE (info LIKE ? OR info LIKE ?) AND pro_tybe = 9 AND isdeleted = 0 ORDER BY id DESC LIMIT 1");
            $new_ord_stmt->bind_param('ss', $pat_n1, $pat_n2);
        }
        $new_ord_stmt->execute();
        $new_ord_res = $new_ord_stmt->get_result()->fetch_assoc();

        if ($new_ord_res) {
            $target_order_id = intval($new_ord_res['id']);
        } else {
            // نفس صيغة info المستخدمة عند إنشاء طلب طاولة عادي حتى تتعرف عليه صفحة الطاولات
            $info_str = "نوع الطلب: طاولة - طاولة: $new_table_name";
            if ($has_table_id) {
                $ins = $conn->prepare("INSERT INTO ot_head (info, pro_tybe, table_id, fat_total, fat_disc, fat_net, crtime, isdeleted) VALUES (?, 9, ?, 0, 0, 0, NOW(), 0)");
                $ins->bind_param('si', $info_str, $new_table_id);
            } else {
                $ins = $conn->prepare("INSERT INTO ot_head (info, pro_tybe, fat_total, fat_disc, fat_net, crtime, isdeleted) VALUES (?, 9, 0, 0, 0, NOW(), 0)");
                $ins->bind_param('s', $info_str);
            }
            $ins->execute();
            $target_order_id = $conn->insert_id;
        }

        // جلب الـ pro_id القديم للأصناف المنقولة
        $placeholders = implode(',', array_fill(0, count($item_ids), '?'));
        $types_str    = str_repeat('i', count($item_ids));

        $stmt_old_pids = $conn->prepare("SELECT DISTINCT pro_id FROM fat_details WHERE id IN ($placeholders)");
        $stmt_old_pids->bind_param($types_str, ...$item_ids);
        $stmt_old_pids->execute();
        $r_pids = $stmt_old_pids->get_result();
        $old_pids = [];
        while ($prow = $r_pids->fetch_assoc()) {
            $old_pids[] = intval($prow['pro_id']);
        }

        // نقل الأصناف إلى target_order_id
        $types_move = 'i' . $types_str;
        $move_params = array_merge([$target_order_id], $item_ids);
        $stmt_move = $conn->prepare("UPDATE fat_details SET pro_id = ? WHERE id IN ($placeholders)");
        $stmt_move->bind_param($types_move, ...$move_params);
        $stmt_move->execute();

        // إعادة حساب الإجماليات لكل الطلبات المباشرة
        $affected_pids = array_unique(array_merge($old_pids, [$target_order_id]));
        foreach ($affected_pids as $pid) {
            $calc_stmt = $conn->prepare("SELECT SUM(price * (qty_out - qty_in)) as calc_total FROM fat_details WHERE pro_id = ? AND isdeleted = 0");
            $calc_stmt->bind_param('i', $pid);
            $calc_stmt->execute();
            $calc_res = $calc_stmt->get_result()->fetch_assoc();
            $new_tot = floatval($calc_res['calc_total'] ?? 0);

            if ($new_tot <= 0 && $pid != $target_order_id) {
                // إذا خلى الطلب القديم تماماً -> يلغى
                $conn->query("UPDATE ot_head SET isdeleted = 1, fat_total = 0, fat_net = 0 WHERE id = $pid");
            } else {
                $conn->query("UPDATE ot_head SET fat_total = $new_tot, fat_net = ($new_tot - fat_disc) WHERE id = $pid");
            }
        }

        // فحص الطاولة القديمة إذا كان بها أي أصناف متبقية
        $pat1 = "%$old_table_name";
        $pat2 = "%$old_table_name %";
        if ($has_table_id) {
            $check_query = "SELECT COUNT(fd.id) as active_items 
                            FROM ot_head h
                            JOIN fat_details fd ON fd.pro_id = h.id AND fd.isdeleted = 0
                            WHERE (h.table_id = ? OR h.info LIKE ? OR h.info LIKE ?)
                              AND h.pro_tybe = 9 AND h.isdeleted = 0";
            $check_stmt = $conn->prepare($check_query);
            $check_stmt->bind_param('iss', $old_table_id, $pat1, $pat2);
        } else {
            $check_query = "SELECT COUNT(fd.id) as active_items 
                            FROM ot_head h
                            JOIN fat_details fd ON fd.pro_id = h.id AND fd.isdeleted = 0
                            WHERE (h.info LIKE ? OR h.info LIKE ?)
                              AND h.pro_tybe = 9 AND h.isdeleted = 0";
            $check_stmt = $conn->prepare($check_query);
            $check_stmt->bind_param('ss', $pat1, $pat2);
        }
        $check_stmt->execute();
        $check_res = $check_stmt->get_result()->fetch_assoc();
        $rem_items = intval($check_res['active_items'] ?? 0);

        if ($rem_items === 0) {
            $update_old_table = "UPDATE tables SET table_case = 0 WHERE id = ?";
            $stmt = $conn->prepare($update_old_table);
            $stmt->bind_param('i', $old_table_id);
            $stmt->execute();
        }
    }

    // الطاولة الجديدة تصبح محجوزة/مشغولة (1)
    $update_new_table = "UPDATE tables SET table_case = 1 WHERE id = ?";
    $stmt = $conn->prepare($update_new_table);
    $stmt->bind_param('i', $new_table_id);
    $stmt->execute();

    // تأكيد المعاملة
    $conn->commit();

    $msg = 'تم النقل بنجاح';
    if ($transfer_type === 'all')    $msg = 'تم نقل جميع الطلبات للطاولة الجديدة بنجاح';
    if ($transfer_type === 'single') $msg = 'تم نقل الطلب الكامل للطاولة الجديدة بنجاح';
    if ($transfer_type === 'items')  $msg = 'تم نقل الأصناف المحددة للطاولة الجديدة بنجاح';

    echo json_encode(['success' => true, 'message' => $msg]);

} catch (Exception $e) {
    // تراجع عن المعاملة في حالة الخطأ
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'خطأ: ' . $e->getMessage()]);
}
