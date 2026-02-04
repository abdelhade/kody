<?php
session_start();
include('../includes/connect.php');

// التحقق من المصادقة
if (!isset($_SESSION['userid'])) {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pos_clothes.php');
    exit;
}

$usid = $_SESSION['userid'];

// استخراج البيانات
$pro_tybe = 9; // نوع POS
$store_id = intval($_POST['store_id']);
$pro_date = $_POST['pro_date'] ?: date('Y-m-d');
$accural_date = $_POST['accural_date'] ?: date('Y-m-d');
$acc2_id = intval($_POST['acc2_id']);
$emp_id = intval($_POST['emp_id']);
$headtotal = floatval($_POST['headtotal']);
$headdisc = floatval($_POST['headdisc']);
$headnet = floatval($_POST['headnet']);
$fund_id = intval($_POST['fund_id']);
$info = trim($_POST['info']);
$submit = $_POST['submit'] ?: 'save';
$paid = floatval($_POST['paid']);

// إضافة نوع الطلب
$order_type = intval($_POST['age']);
$order_types = [1 => 'بيع مباشر', 2 => 'حجز', 3 => 'توصيل'];
$order_type_text = $order_types[$order_type] ?? 'بيع مباشر';
$info = empty($info) ? "نوع الطلب: $order_type_text" : "$info - نوع الطلب: $order_type_text";

// التحقق من البيانات
if (!$store_id || !$acc2_id || !$emp_id) {
    die('خطأ: بيانات مطلوبة مفقودة');
}

if (!isset($_POST['itmname']) || !is_array($_POST['itmname']) || empty(array_filter($_POST['itmname']))) {
    die('خطأ: يجب إضافة صنف واحد على الأقل');
}

try {
    $conn->begin_transaction();
    
    // الحصول على رقم الفاتورة التالي
    $stmt = $conn->prepare("SELECT MAX(CAST(pro_id AS UNSIGNED)) as max_id FROM ot_head WHERE pro_tybe = ?");
    $stmt->bind_param("i", $pro_tybe);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $pro_id = $row && $row['max_id'] ? ($row['max_id'] + 1) : 1;
    $stmt->close();
    
    // إدخال رأس الفاتورة كحجز (is_journal = 0)
    $stmt = $conn->prepare("
        INSERT INTO ot_head (
            pro_id, pro_tybe, is_stock, is_journal, journal_tybe, info, pro_date, 
            accural_date, pro_serial, store_id, emp_id, emp2_id, acc1, acc2, 
            pro_value, fat_total, fat_disc, fat_plus, fat_net, user
        ) VALUES (
            ?, ?, 1, 0, ?, ?, ?, ?, '0', ?, ?, ?, ?, ?, ?, ?, ?, 0, ?, ?
        )
    ");
    
    $stmt->bind_param(
        "iiissssiiiiidddi",
        $pro_id, $pro_tybe, $pro_tybe, $info, $pro_date, $accural_date,
        $store_id, $emp_id, $emp_id, $acc2_id, $acc2_id, $headtotal,
        $headtotal, $headdisc, $headnet, $usid
    );
    
    if (!$stmt->execute()) {
        throw new Exception('فشل في إدخال الفاتورة: ' . $stmt->error);
    }
    
    $last_op = $conn->insert_id;
    $stmt->close();
    
    // إدخال تفاصيل الفاتورة كحجز
    $stmt_details = $conn->prepare("
        INSERT INTO fat_details (
            pro_tybe, pro_id, item_id, u_val, qty_in, qty_out, price, 
            discount, det_value, fatid, fat_tybe, det_store
        ) VALUES (?, ?, ?, ?, 0, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    foreach ($_POST['itmname'] as $index => $itmname) {
        if (empty($itmname)) continue;
        
        $itmname = intval($itmname);
        $itmqty = floatval($_POST['itmqty'][$index]);
        $itmprice = floatval($_POST['itmprice'][$index]);
        $itmdisc = floatval($_POST['itmdisc'][$index]);
        $u_val = floatval($_POST['u_val'][$index]);
        $det_value = $itmqty * ($itmprice - $itmdisc);
        $qty_out = $itmqty * $u_val;
        
        $stmt_details->bind_param(
            "iiididdiiii",
            $pro_tybe, $last_op, $itmname, $u_val, $qty_out,
            $itmprice, $itmdisc, $det_value, $last_op, $pro_tybe, $store_id
        );
        
        if (!$stmt_details->execute()) {
            throw new Exception('فشل في إدخال تفاصيل الصنف ' . $itmname);
        }
    }
    
    $stmt_details->close();
    
    $conn->commit();
    
    $_SESSION['success_message'] = 'تم حفظ الطلب كحجز بنجاح - رقم الفاتورة: ' . $pro_id;
    
} catch (Exception $e) {
    $conn->rollback();
    die('حدث خطأ أثناء معالجة الفاتورة: ' . $e->getMessage());
}

// إعادة التوجيه
if ($submit == 'cash') {
    echo "<script>window.open('../print/receipt.php?id=$last_op', '_blank'); window.location.href='../pos_clothes.php';</script>";
} else {
    header("Location: ../pos_clothes.php?r=" . time());
}
exit;
?>