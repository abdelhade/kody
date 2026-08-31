<?php
error_reporting(0);
ini_set('display_errors', 0);
ob_start();
session_start();

include('../includes/connect.php');
include('../includes/tables_panel_lib.php');

ob_clean();
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    tpanel_json_response(['success' => false, 'message' => 'طريقة الطلب غير صحيحة'], 405);
}

$action = $_POST['action'] ?? 'state';
$selected_table_id = intval($_POST['selected_table_id'] ?? 0);

try {
    tpanel_ensure_schema($conn);

    switch ($action) {

        case 'state':
            $state = tpanel_build_state($conn, $selected_table_id);
            tpanel_json_response(['success' => true] + $state);
            break;

        /**
         * فصل أصناف محددة في طلب منفصل (بلا أي قيود محاسبية).
         * الدفع يتم بعدها من مودال الـ POS وحده، فلا تتكرر القيود.
         */
        case 'split_items':
            $table_id = intval($_POST['table_id'] ?? 0);
            $order_id = intval($_POST['order_id'] ?? 0);
            $item_ids = $_POST['item_ids'] ?? [];

            if (!is_array($item_ids)) {
                $item_ids = array_filter(array_map('intval', explode(',', (string)$item_ids)));
            } else {
                $item_ids = array_map('intval', array_filter($item_ids));
            }

            if ($table_id <= 0 || $order_id <= 0 || empty($item_ids)) {
                throw new Exception('بيانات غير مكتملة');
            }

            $conn->begin_transaction();

            $stmt = $conn->prepare("SELECT * FROM ot_head WHERE id = ? AND pro_tybe = 9 AND isdeleted = 0");
            $stmt->bind_param('i', $order_id);
            $stmt->execute();
            $orig = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            if (!$orig) throw new Exception('الطلب غير موجود');

            $placeholders = implode(',', array_fill(0, count($item_ids), '?'));
            $types = str_repeat('i', count($item_ids));

            $stmt = $conn->prepare(
                "SELECT SUM((qty_out - qty_in) * price) AS sel_total
                 FROM fat_details
                 WHERE id IN ($placeholders) AND (fatid = ? OR pro_id = ?) AND isdeleted = 0"
            );
            $stmt->bind_param($types . 'ii', ...array_merge($item_ids, [$order_id, $order_id]));
            $stmt->execute();
            $sel_total = floatval($stmt->get_result()->fetch_assoc()['sel_total'] ?? 0);
            $stmt->close();

            if ($sel_total <= 0) throw new Exception('الأصناف المحددة غير صالحة');

            $table = tpanel_get_table($conn, $table_id);
            $table_name = $table['tname'] ?? "طاولة $table_id";

            // طلب منفصل بلا table_id حتى تبقى الطاولة مرتبطة بطلبها الأصلي
            $new_info = "نوع الطلب: طاولة - طاولة: $table_name - سداد أصناف";
            $user_id = $_SESSION['userid'] ?? 1;

            $ins = $conn->prepare(
                "INSERT INTO ot_head
                 (pro_id, pro_tybe, is_stock, is_journal, journal_tybe, pro_date, accural_date,
                  store_id, emp_id, emp2_id, acc1, acc2, pro_value, fat_total, fat_net, info, user)
                 VALUES (?, 9, 1, 1, 9, NOW(), CURDATE(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );
            $num_res = $conn->query("SELECT MAX(CAST(pro_id AS UNSIGNED)) AS m FROM ot_head WHERE pro_tybe = 9");
            $new_pro_id = intval($num_res ? ($num_res->fetch_assoc()['m'] ?? 0) : 0) + 1;
            $ins->bind_param(
                'iiiiiidddsi',
                $new_pro_id, $orig['store_id'], $orig['emp_id'], $orig['emp_id'],
                $orig['acc1'], $orig['acc2'], $sel_total, $sel_total, $sel_total,
                $new_info, $user_id
            );
            $ins->execute();
            $new_head_id = $conn->insert_id;
            $ins->close();

            $move = $conn->prepare(
                "UPDATE fat_details SET fatid = ?, pro_id = ?
                 WHERE id IN ($placeholders) AND (fatid = ? OR pro_id = ?)"
            );
            $move->bind_param(
                'ii' . $types . 'ii',
                ...array_merge([$new_head_id, $new_head_id], $item_ids, [$order_id, $order_id])
            );
            $move->execute();
            $move->close();

            // إعادة حساب الطلب الأصلي
            $calc = $conn->prepare(
                "SELECT SUM((qty_out - qty_in) * price) AS rem
                 FROM fat_details WHERE (fatid = ? OR pro_id = ?) AND isdeleted = 0"
            );
            $calc->bind_param('ii', $order_id, $order_id);
            $calc->execute();
            $remaining = floatval($calc->get_result()->fetch_assoc()['rem'] ?? 0);
            $calc->close();

            if ($remaining <= 0) {
                $conn->query("UPDATE ot_head SET isdeleted = 1, fat_total = 0, fat_net = 0 WHERE id = $order_id");
            } else {
                $upd = $conn->prepare(
                    "UPDATE ot_head SET fat_total = ?, pro_value = ?, fat_net = (? - fat_disc) WHERE id = ?"
                );
                $upd->bind_param('dddi', $remaining, $remaining, $remaining, $order_id);
                $upd->execute();
                $upd->close();
            }

            $conn->commit();

            $state = tpanel_build_state($conn, $table_id);
            tpanel_json_response([
                'success' => true,
                'message' => 'تم فصل الأصناف — أكمل الدفع',
                'split_order_id' => $new_head_id,
                'split_total' => round($sel_total, 2),
                'table_name' => $table_name,
            ] + $state);
            break;

        case 'transfer':
            $old_table_id = intval($_POST['old_table_id'] ?? 0);
            $new_table_id = intval($_POST['new_table_id'] ?? 0);

            if ($old_table_id <= 0 || $new_table_id <= 0 || $old_table_id === $new_table_id) {
                throw new Exception('بيانات النقل غير صحيحة');
            }

            $old_table = tpanel_get_table($conn, $old_table_id);
            $new_table = tpanel_get_table($conn, $new_table_id);
            if (!$old_table || !$new_table) throw new Exception('الطاولة غير موجودة');

            $order = tpanel_get_active_order($conn, tpanel_resolve_primary_id($conn, $old_table_id));
            if (!$order) throw new Exception('لا يوجد طلب للنقل');

            $conn->begin_transaction();

            $old_name = $old_table['tname'];
            $new_name = $new_table['tname'];
            $order_id = intval($order['id']);

            $stmt = $conn->prepare(
                "UPDATE ot_head SET info = REPLACE(info, ?, ?), table_id = ? WHERE id = ?"
            );
            $stmt->bind_param('ssii', $old_name, $new_name, $new_table_id, $order_id);
            $stmt->execute();
            $stmt->close();

            tpanel_sync_table_case($conn, tpanel_resolve_primary_id($conn, $old_table_id), 0);
            tpanel_sync_table_case($conn, $new_table_id, 1);

            $conn->commit();

            $state = tpanel_build_state($conn, $new_table_id);
            tpanel_json_response(['success' => true, 'message' => 'تم نقل الطلب بنجاح'] + $state);
            break;

        case 'merge':
            $table_ids = $_POST['table_ids'] ?? [];
            if (!is_array($table_ids)) {
                $table_ids = array_filter(array_map('intval', explode(',', (string)$table_ids)));
            } else {
                $table_ids = array_map('intval', array_filter($table_ids));
            }

            if (count($table_ids) < 2) {
                throw new Exception('يرجى اختيار طاولتين على الأقل');
            }

            $conn->begin_transaction();

            $primary_id = $table_ids[0];
            foreach ($table_ids as $tid) {
                $ord = tpanel_get_active_order($conn, $tid);
                if ($ord) {
                    $primary_id = tpanel_resolve_primary_id($conn, $tid);
                    break;
                }
            }

            foreach ($table_ids as $tid) {
                if ($tid === $primary_id) {
                    $conn->query("UPDATE tables SET is_merged = 1, parent_table_id = NULL WHERE id = $tid");
                } else {
                    $conn->query("UPDATE tables SET is_merged = 1, parent_table_id = $primary_id WHERE id = $tid");
                }
            }

            // الدمج = فاتورة واحدة: تُنقل أصناف طلبات الطاولات التابعة إلى فاتورة المجموعة
            $consolidated = tpanel_consolidate_group_orders($conn, $primary_id);
            tpanel_sync_group_case($conn, $primary_id);

            $conn->commit();

            $message = $consolidated > 0
                ? "تم دمج الطاولات وتوحيد الفاتورة ($consolidated طلب مدموج)"
                : 'تم دمج الطاولات';

            $state = tpanel_build_state($conn, $primary_id);
            tpanel_json_response(['success' => true, 'message' => $message, 'primary_id' => $primary_id] + $state);
            break;

        case 'unmerge':
            $table_id = intval($_POST['table_id'] ?? 0);
            if ($table_id <= 0) throw new Exception('رقم الطاولة غير صحيح');

            $primary_id = tpanel_resolve_primary_id($conn, $table_id);
            $conn->begin_transaction();

            $res = $conn->query(
                "SELECT id, tname FROM tables WHERE id = $primary_id OR parent_table_id = $primary_id"
            );
            while ($t = $res->fetch_assoc()) {
                $tid = intval($t['id']);
                $has = tpanel_get_active_order($conn, $tid);
                $case = $has ? 1 : 0;
                $conn->query(
                    "UPDATE tables SET is_merged = 0, parent_table_id = NULL, table_case = $case WHERE id = $tid"
                );
            }

            $conn->commit();

            $state = tpanel_build_state($conn, $primary_id);
            tpanel_json_response(['success' => true, 'message' => 'تم فك الدمج'] + $state);
            break;

        default:
            throw new Exception('إجراء غير معروف');
    }

} catch (Exception $e) {
    if ($conn->errno || $conn->connect_errno) { /* noop */ }
    if ($conn && method_exists($conn, 'rollback')) {
        @$conn->rollback();
    }
    tpanel_json_response(['success' => false, 'message' => $e->getMessage()], 400);
}
