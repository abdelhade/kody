<?php
include('../includes/connect.php');

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['userid'])) {
    echo json_encode(['success' => false, 'error' => 'غير مصرح - يرجى تسجيل الدخول']);
    exit;
}

// استقبال البيانات (بدون [] في الأسماء)
$barcode      = trim($_POST['barcode']      ?? '');
$iname        = trim($_POST['iname']        ?? '');
$name2        = trim($_POST['name2']        ?? '');
$group1       = intval($_POST['group1']     ?? 0);
$group2       = intval($_POST['group2']     ?? 0);
$info         = trim($_POST['info']         ?? '');
$unit_id      = intval($_POST['unit_id']    ?? 0);
$cost_price   = floatval($_POST['cost_price']   ?? 0);
$price1       = floatval($_POST['price1']       ?? 0);
$price2       = floatval($_POST['price2']       ?? 0);
$market_price = floatval($_POST['market_price'] ?? 0);
$unit_barcode = trim($_POST['unit_barcode'] ?? '') ?: $barcode;

if (empty($barcode) || empty($iname) || $unit_id == 0) {
    echo json_encode(['success' => false, 'error' => 'الباركود واسم الصنف والوحدة مطلوبة']);
    exit;
}

try {
    $conn->begin_transaction();

    // كود الصنف التالي
    $row_code = $conn->query("SELECT MAX(code) as max_code FROM myitems")->fetch_assoc();
    $new_code = $row_code['max_code'] ? intval($row_code['max_code']) + 1 : 1;

    // تحقق من تكرار الباركود
    $s = $conn->prepare("SELECT id FROM myitems WHERE barcode = ? LIMIT 1");
    $s->bind_param('s', $barcode);
    $s->execute();
    if ($s->get_result()->num_rows > 0) throw new Exception('الباركود مستخدم مسبقاً');
    $s->close();

    $s2 = $conn->prepare("SELECT item_id FROM item_units WHERE unit_barcode = ? LIMIT 1");
    $s2->bind_param('s', $unit_barcode);
    $s2->execute();
    if ($s2->get_result()->num_rows > 0) throw new Exception('باركود الوحدة مستخدم مسبقاً');
    $s2->close();

    // تحقق من أعمدة myitems الموجودة
    $cols = [];
    $r = $conn->query("DESCRIBE myitems");
    while ($row = $r->fetch_assoc()) $cols[] = $row['Field'];

    // بناء INSERT ديناميكي حسب الأعمدة الموجودة
    $fields = ['code', 'barcode', 'iname', 'name2', 'group1', 'group2', 'info',
               'cost_price', 'price1', 'last_price', 'market_price'];
    $values = [$new_code, $barcode, $iname, $name2, $group1, $group2, $info,
               $cost_price, $price1, $price1, $market_price];
    $types  = 'isssiisd ddd';

    // أضف user لو الـ column موجود
    if (in_array('user', $cols)) {
        $fields[] = 'user';
        $values[] = $_SESSION['userid'];
        $types   .= 's';
    }

    $types = str_replace(' ', '', $types); // remove spaces

    $placeholders = implode(', ', array_fill(0, count($fields), '?'));
    $fieldList    = implode(', ', $fields);

    $stmt = $conn->prepare("INSERT INTO myitems ($fieldList) VALUES ($placeholders)");
    if (!$stmt) throw new Exception('prepare error: ' . $conn->error);
    $stmt->bind_param($types, ...$values);
    if (!$stmt->execute()) throw new Exception('فشل حفظ الصنف: ' . $stmt->error);
    $item_id = $conn->insert_id;
    $stmt->close();

    // إدخال الوحدة
    $u_val = 1;
    $su = $conn->prepare(
        "INSERT INTO item_units (item_id, unit_id, u_val, unit_barcode, cost_price, price1, price2, price3)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
    );
    $su->bind_param('iiisiddd', $item_id, $unit_id, $u_val, $unit_barcode,
                    $cost_price, $price1, $price2, $market_price);
    if (!$su->execute()) throw new Exception('فشل حفظ الوحدة: ' . $su->error);
    $su->close();

    $conn->commit();
    echo json_encode(['success' => true, 'id' => $item_id, 'iname' => $iname,
                      'barcode' => $barcode, 'price' => $price1]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
