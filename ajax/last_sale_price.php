<?php
/**
 * آخر سعر بيع لصنف (أو عدة أصناف) لدى عميل محدد.
 * السعر المُرجَع هو سعر الوحدة الأساسية.
 */
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../includes/connect.php';

$clientId = isset($_REQUEST['client_id']) ? (int) $_REQUEST['client_id'] : 0;
$raw = '';
if (isset($_REQUEST['item_ids'])) {
    $raw = (string) $_REQUEST['item_ids'];
} elseif (isset($_REQUEST['item_id'])) {
    $raw = (string) $_REQUEST['item_id'];
}

$ids = [];
foreach (explode(',', $raw) as $part) {
    $id = (int) trim($part);
    if ($id > 0) {
        $ids[$id] = $id;
    }
}
$ids = array_values($ids);
if (count($ids) > 200) {
    $ids = array_slice($ids, 0, 200);
}

if ($clientId <= 0 || !$ids) {
    echo json_encode(['success' => false, 'prices' => new stdClass()], JSON_UNESCAPED_UNICODE);
    exit;
}

$placeholders = implode(',', array_fill(0, count($ids), '?'));
$sql = "
    SELECT fd.item_id, fd.price, fd.u_val, h.pro_tybe, h.acc1, h.acc2
    FROM fat_details fd
    INNER JOIN ot_head h ON h.id = fd.pro_id
    WHERE fd.item_id IN ($placeholders)
      AND COALESCE(fd.isdeleted, 0) = 0
      AND COALESCE(h.isdeleted, 0) = 0
      AND h.pro_tybe IN (3, 9)
      AND (h.acc2 = ? OR (h.pro_tybe = 9 AND h.acc1 = ?))
    ORDER BY h.id DESC, fd.id DESC
";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['success' => false, 'prices' => new stdClass(), 'error' => 'query'], JSON_UNESCAPED_UNICODE);
    exit;
}

$types = str_repeat('i', count($ids)) . 'ii';
$params = array_merge($ids, [$clientId, $clientId]);
$bind = [$types];
foreach ($params as $i => $value) {
    $params[$i] = (int) $value;
    $bind[] = &$params[$i];
}
call_user_func_array([$stmt, 'bind_param'], $bind);
$stmt->execute();
$result = $stmt->get_result();

$prices = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $itemId = (int) $row['item_id'];
        if (isset($prices[$itemId])) {
            continue;
        }
        $clothesSale = ((int) $row['pro_tybe'] === 9)
            && ((int) $row['acc1'] === $clientId)
            && ((int) $row['acc2'] !== $clientId);
        if ($clothesSale) {
            $unit = (float) $row['u_val'];
            if ($unit <= 0) {
                $unit = 1;
            }
            $base = (float) $row['price'] / $unit;
        } else {
            $base = (float) $row['price'];
        }
        if ($base > 0) {
            $prices[(string) $itemId] = round($base, 4);
        }
    }
}
$stmt->close();

echo json_encode(['success' => true, 'prices' => $prices ?: new stdClass()], JSON_UNESCAPED_UNICODE);
