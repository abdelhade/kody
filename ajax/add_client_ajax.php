<?php
header('Content-Type: application/json');
include('../includes/connect.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'طريقة الطلب غير صالحة']);
    exit;
}

$aname = trim($_POST['name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$address = trim($_POST['address'] ?? '');

if (empty($aname)) {
    echo json_encode(['success' => false, 'message' => 'اسم العميل مطلوب']);
    exit;
}

// التحقق من تكرار الاسم في الحسابات
$check_stmt = $conn->prepare("SELECT id FROM acc_head WHERE aname = ? AND isdeleted = 0");
$check_stmt->bind_param("s", $aname);
$check_stmt->execute();
$result = $check_stmt->get_result();
if ($result->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'هذا الاسم موجود بالفعل كحساب/عميل']);
    exit;
}

// حساب كود العميل الجديد
$parent = '122';
$sqllst = "SELECT code FROM acc_head WHERE code LIKE '122%' AND is_basic = 0 AND isdeleted = 0 ORDER BY code DESC LIMIT 1";
$reslast = $conn->query($sqllst);
if ($reslast && $reslast->num_rows > 0) {
    $rowlast = $reslast->fetch_assoc();
    // استخراج الجزء الأخير بعد 122
    $lstacc = substr($rowlast['code'], 3);
    $lstacc_int = (int)$lstacc;
    $lstacc_int++;
    $lstacc_new = sprintf("%03d", $lstacc_int);
    $last_id = $parent . $lstacc_new;
} else {
    $last_id = $parent . "001";
}

// جلب تفاصيل الحساب الأب
$parent_id = 0;
$kind = '';
$parent_stmt = $conn->prepare("SELECT id, kind FROM acc_head WHERE code = '122' LIMIT 1");
$parent_stmt->execute();
$parent_res = $parent_stmt->get_result();
if ($parent_row = $parent_res->fetch_assoc()) {
    $parent_id = intval($parent_row['id']);
    $kind = $parent_row['kind'];
}

// بدء عملية الإدراج
$conn->begin_transaction();
try {
    // 1. الإدراج في جدول الحسابات
    $insert_stmt = $conn->prepare("INSERT INTO acc_head (code, aname, is_basic, parent_id, kind, phone, address, is_stock, secret, rentable, is_fund) VALUES (?, ?, 0, ?, ?, ?, ?, 0, 0, 0, 0)");
    $insert_stmt->bind_param("ssisss", $last_id, $aname, $parent_id, $kind, $phone, $address);
    if (!$insert_stmt->execute()) {
        throw new Exception("خطأ أثناء إضافة الحساب: " . $conn->error);
    }
    
    // الحصول على معرف الحساب المدرج حديثاً
    $new_acc_id = $conn->insert_id;

    // 2. الإدراج في جدول العملاء
    $insert_client = $conn->prepare("INSERT INTO clients (name, phone, address) VALUES (?, ?, ?)");
    $insert_client->bind_param("sss", $aname, $phone, $address);
    if (!$insert_client->execute()) {
        throw new Exception("خطأ أثناء إضافة العميل: " . $conn->error);
    }

    // 3. إضافة العملية لسجل العمليات
    $process_stmt = $conn->prepare("INSERT INTO `process`(`type`) VALUES (?)");
    $process_type = "add account >> " . $aname;
    $process_stmt->bind_param("s", $process_type);
    $process_stmt->execute();

    $conn->commit();
    echo json_encode([
        'success' => true,
        'id' => $new_acc_id,
        'name' => $aname,
        'code' => $last_id,
        'message' => 'تم إضافة العميل "' . $aname . '" بنجاح بكود: ' . $last_id
    ]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
