<?php
include('../includes/connect.php');

header('Content-Type: application/json; charset=utf-8');

// التحقق من المصادقة
if (!isset($_SESSION['userid'])) {
    echo json_encode(['success' => false, 'error' => 'غير مصرح - يرجى تسجيل الدخول']);
    exit;
}

$userid      = $_SESSION['userid'];
$parent_code = isset($_POST['parent_code']) ? trim($_POST['parent_code']) : '';
$aname       = isset($_POST['aname'])       ? trim($_POST['aname'])       : '';
$phone       = isset($_POST['phone'])       ? trim($_POST['phone'])       : '';
$address     = isset($_POST['address'])     ? trim($_POST['address'])     : '';
$info        = isset($_POST['info'])        ? trim($_POST['info'])        : '';

if (empty($parent_code) || empty($aname)) {
    echo json_encode(['success' => false, 'error' => 'الاسم مطلوب']);
    exit;
}

// تحديد parent_id
if ($parent_code === '211') {
    $parent_id = 211;
} elseif ($parent_code === '122') {
    $parent_id = 122;
} else {
    echo json_encode(['success' => false, 'error' => 'نوع حساب غير صحيح']);
    exit;
}

try {
    $conn->begin_transaction();

    // الكود التالي
    $esc_code  = $conn->real_escape_string($parent_code);
    $res_code  = $conn->query("SELECT MAX(CAST(code AS UNSIGNED)) AS max_code FROM acc_head WHERE code LIKE '{$esc_code}%' AND is_basic = 0");
    $row_code  = $res_code->fetch_assoc();
    $max_num   = $row_code['max_code'] ? intval($row_code['max_code']) : intval($parent_code . '0000');
    $new_code  = (string)($max_num + 1);

    // تحقق من تكرار الاسم
    $stmt_chk = $conn->prepare("SELECT id FROM acc_head WHERE aname = ? AND parent_id = ? AND isdeleted = 0 LIMIT 1");
    $stmt_chk->bind_param('si', $aname, $parent_id);
    $stmt_chk->execute();
    if ($stmt_chk->get_result()->num_rows > 0) {
        throw new Exception('اسم الحساب مستخدم مسبقاً');
    }
    $stmt_chk->close();

    // إدخال الحساب
    $stmt = $conn->prepare(
        "INSERT INTO acc_head (code, aname, parent_id, is_basic, info, phone, address)
         VALUES (?, ?, ?, 0, ?, ?, ?)"
    );
    if (!$stmt) {
        throw new Exception('prepare failed: ' . $conn->error);
    }
    $stmt->bind_param('ssisss', $new_code, $aname, $parent_id, $info, $phone, $address);
    if (!$stmt->execute()) {
        throw new Exception('فشل حفظ الحساب: ' . $stmt->error);
    }
    $account_id = $conn->insert_id;
    $stmt->close();

    $conn->commit();

    echo json_encode([
        'success' => true,
        'id'      => $account_id,
        'aname'   => $aname,
        'code'    => $new_code
    ]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
