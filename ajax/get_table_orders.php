<?php
error_reporting(0);
ini_set('display_errors', 0);
ob_start();

include('../includes/connect.php');
ob_clean();

header('Content-Type: application/json');

$table_id = isset($_GET['table_id']) ? intval($_GET['table_id']) : 0;

if (!$table_id) {
    echo json_encode(['success' => false, 'message' => 'رقم الطاولة غير محدد']);
    exit;
}

try {
    // جلب اسم الطاولة
    $tname_stmt = $conn->prepare("SELECT tname FROM tables WHERE id = ?");
    $tname_stmt->bind_param('i', $table_id);
    $tname_stmt->execute();
    $tname_res = $tname_stmt->get_result()->fetch_assoc();
    $table_name = $tname_res['tname'] ?? '';

    // التحقق من وجود عمود table_id في ot_head
    $has_table_id = false;
    $col_check = $conn->query("SHOW COLUMNS FROM ot_head LIKE 'table_id'");
    if ($col_check && $col_check->num_rows > 0) {
        $has_table_id = true;
    }

    // تجهيز أنماط البحث المرنة (طاولة 6 / طاولة رقم 6 / table 6 / اسم الطاولة)
    $patterns = [
        "%$table_id%",
        "%طاولة $table_id%",
        "%طاولة رقم $table_id%",
        "%table $table_id%"
    ];
    if ($table_name) {
        $patterns[] = "%$table_name%";
    }

    // بناء شروط الاستعلام
    $where_conditions = [];
    $params = [];
    $types = "";

    if ($has_table_id) {
        $where_conditions[] = "table_id = ?";
        $params[] = $table_id;
        $types .= "i";
    }

    foreach ($patterns as $pat) {
        $where_conditions[] = "info LIKE ?";
        $params[] = $pat;
        $types .= "s";
    }

    $where_sql = "(" . implode(" OR ", $where_conditions) . ")";

    $query = "SELECT id, info, fat_total, fat_disc, fat_net, crtime
              FROM ot_head
              WHERE $where_sql
                AND pro_tybe = 9
                AND isdeleted = 0
              ORDER BY id ASC";

    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    $orders = [];
    while ($row = $result->fetch_assoc()) {
        $order_id = intval($row['id']);

        // جلب تفاصيل الأصناف لهذا الطلب
        $items_stmt = $conn->prepare("SELECT fd.id as detail_id, fd.qty_out, fd.price, i.iname 
                                      FROM fat_details fd 
                                      LEFT JOIN myitems i ON fd.item_id = i.id 
                                      WHERE fd.pro_id = ? AND fd.isdeleted = 0");
        $items_stmt->bind_param('i', $order_id);
        $items_stmt->execute();
        $items_result = $items_stmt->get_result();

        $items_list = [];
        $items_count = 0;
        while ($item = $items_result->fetch_assoc()) {
            $qty = floatval($item['qty_out']);
            $items_list[] = [
                'detail_id' => intval($item['detail_id']),
                'name'      => $item['iname'] ?? 'صنف غير معروف',
                'qty'       => $qty,
                'price'     => floatval($item['price'])
            ];
            $items_count++;
        }

        $net = floatval($row['fat_net'] ?? 0);
        if ($net == 0) {
            $net = floatval($row['fat_total'] ?? 0) - floatval($row['fat_disc'] ?? 0);
        }

        $orders[] = [
            'id'          => $order_id,
            'info'        => $row['info'],
            'total'       => floatval($row['fat_total'] ?? 0),
            'discount'    => floatval($row['fat_disc'] ?? 0),
            'net'         => $net,
            'crtime'      => $row['crtime'] ?? null,
            'items_count' => $items_count,
            'items'       => $items_list
        ];
    }

    echo json_encode(['success' => true, 'orders' => $orders, 'table_name' => $table_name]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
