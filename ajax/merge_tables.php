<?php
error_reporting(0);
ini_set('display_errors', 0);
ob_start();

$root_path = dirname(__DIR__);
include($root_path . '/includes/connect.php');

ob_clean();
header('Content-Type: application/json; charset=utf-8');

try {
    // Ensure database columns exist
    $chk_col = $conn->query("SHOW COLUMNS FROM tables LIKE 'parent_table_id'");
    if ($chk_col && $chk_col->num_rows == 0) {
        $conn->query("ALTER TABLE tables ADD COLUMN parent_table_id INT DEFAULT NULL");
    }
    $chk_col2 = $conn->query("SHOW COLUMNS FROM tables LIKE 'is_merged'");
    if ($chk_col2 && $chk_col2->num_rows == 0) {
        $conn->query("ALTER TABLE tables ADD COLUMN is_merged TINYINT(1) DEFAULT 0");
    }

    $action = $_POST['action'] ?? '';

    if ($action === 'merge') {
        $table_ids = $_POST['table_ids'] ?? [];
        if (!is_array($table_ids) || count($table_ids) < 2) {
            throw new Exception('يرجى اختيار طاولتين على الأقل للدمج');
        }

        $clean_ids = array_map('intval', $table_ids);
        $clean_ids = array_values(array_filter($clean_ids, function($id) { return $id > 0; }));
        
        if (count($clean_ids) < 2) {
            throw new Exception('الطاولات المختارة غير صالحة');
        }

        $id_list = implode(',', $clean_ids);

        // Find primary table (prefer the one with active order if any, else the first one)
        $primary_id = $clean_ids[0];
        
        $order_chk = $conn->query("SELECT t.id FROM tables t
            JOIN ot_head o ON o.info LIKE CONCAT('%', t.tname, '%')
            WHERE t.id IN ($id_list) AND o.pro_tybe = 9 AND o.isdeleted = 0 AND o.fat_net > 0
            ORDER BY o.id DESC LIMIT 1");
        
        if ($order_chk && $order_chk->num_rows > 0) {
            $primary_id = intval($order_chk->fetch_assoc()['id']);
        }

        // Get primary table name
        $primary_res = $conn->query("SELECT tname FROM tables WHERE id = $primary_id");
        $primary_name = ($primary_res && $primary_res->num_rows > 0) ? $primary_res->fetch_assoc()['tname'] : '';

        foreach ($clean_ids as $tid) {
            if ($tid == $primary_id) {
                $conn->query("UPDATE tables SET is_merged = 1, parent_table_id = NULL, table_case = 1 WHERE id = $tid");
            } else {
                $conn->query("UPDATE tables SET is_merged = 1, parent_table_id = $primary_id, table_case = 1 WHERE id = $tid");
                
                if (!empty($primary_name)) {
                    $sec_res = $conn->query("SELECT tname FROM tables WHERE id = $tid");
                    if ($sec_res && $sec_res->num_rows > 0) {
                        $sec_name = $sec_res->fetch_assoc()['tname'];
                        $conn->query("UPDATE ot_head SET info = CONCAT(info, ' (دمج مع ', '$sec_name', ')') 
                            WHERE info LIKE '%$primary_name%' AND pro_tybe = 9 AND isdeleted = 0");
                    }
                }
            }
        }

        echo json_encode([
            'success' => true,
            'message' => 'تم دمج الطاولات بنجاح',
            'primary_id' => $primary_id
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if ($action === 'unmerge') {
        $table_ids = $_POST['table_ids'] ?? [];
        
        if (is_array($table_ids) && count($table_ids) > 0) {
            $clean_ids = array_map('intval', $table_ids);
            $clean_ids = array_values(array_filter($clean_ids, function($id) { return $id > 0; }));
            $id_list = implode(',', $clean_ids);
            
            // Find all tables that belong to these merge groups
            $parents = $conn->query("SELECT DISTINCT IFNULL(parent_table_id, id) as p_id FROM tables WHERE id IN ($id_list) OR parent_table_id IN ($id_list)");
            $parent_ids = [];
            if ($parents) {
                while ($r = $parents->fetch_assoc()) {
                    if ($r['p_id']) $parent_ids[] = intval($r['p_id']);
                }
            }
            
            if (!empty($parent_ids)) {
                $p_list = implode(',', $parent_ids);
                $all_affected = $conn->query("SELECT id, tname FROM tables WHERE id IN ($p_list) OR parent_table_id IN ($p_list)");
                if ($all_affected) {
                    while ($t = $all_affected->fetch_assoc()) {
                        $tid = $t['id'];
                        $tname = $t['tname'];
                        
                        $order_chk = $conn->query("SELECT COUNT(*) as c FROM ot_head WHERE info LIKE '%$tname%' AND pro_tybe = 9 AND isdeleted = 0 AND fat_net > 0");
                        $has_order = ($order_chk && $order_chk->fetch_assoc()['c'] > 0);
                        
                        $new_case = $has_order ? 1 : 0;
                        $conn->query("UPDATE tables SET is_merged = 0, parent_table_id = NULL, table_case = $new_case WHERE id = $tid");
                    }
                }
            } else {
                $conn->query("UPDATE tables SET is_merged = 0, parent_table_id = NULL, table_case = 0 WHERE id IN ($id_list)");
            }
        } else {
            // Unmerge all tables
            $all_merged = $conn->query("SELECT id, tname FROM tables WHERE is_merged = 1");
            if ($all_merged) {
                while ($t = $all_merged->fetch_assoc()) {
                    $tid = $t['id'];
                    $tname = $t['tname'];
                    $order_chk = $conn->query("SELECT COUNT(*) as c FROM ot_head WHERE info LIKE '%$tname%' AND pro_tybe = 9 AND isdeleted = 0 AND fat_net > 0");
                    $has_order = ($order_chk && $order_chk->fetch_assoc()['c'] > 0);
                    $new_case = $has_order ? 1 : 0;
                    $conn->query("UPDATE tables SET is_merged = 0, parent_table_id = NULL, table_case = $new_case WHERE id = $tid");
                }
            }
        }

        echo json_encode([
            'success' => true,
            'message' => 'تم فك دمج الطاولات وتحويلها لمتاحة بنجاح'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if ($action === 'get_grid_html') {
        include($root_path . '/includes/tables_grid_render.php');
        $html = ob_get_clean();
        echo json_encode([
            'success' => true,
            'html' => $html
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    throw new Exception('إجراء غير معروف');

} catch (Exception $e) {
    if (ob_get_length()) ob_clean();
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>
