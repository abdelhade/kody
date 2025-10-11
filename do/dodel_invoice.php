<?php
session_start();
include('../includes/connect.php');

// التحقق من المصادقة والصلاحيات
if (!isset($_SESSION['userid'])) {
    header('Location: ../login.php');
    exit;
}

// التحقق من صحة الطلب
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../sales.php');
    exit;
}

$usid = $_SESSION['userid'];

// تضمين فئات النظام الجديد
require_once('../classes/InvoiceElementFactory.php');

// تعريف ثوابت أنواع الفواتير
define('INVOICE_TYPES', [
    'PURCHASE' => 4,    // مشتريات
    'SALES' => 3,       // مبيعات  
    'POS' => 9,         // كاشير
    'PURCHASE_RETURN' => 10,  // مردود مشتريات
    'SALES_RETURN' => 11      // مردود مبيعات
]);

// تعريف أنواع العمليات المحاسبية
define('ACCOUNTING_TYPES', [
    'RECEIPT' => 1,     // سند قبض
    'PAYMENT' => 2,     // سند دفع
    'SALES_DISC' => 7,  // خصم مبيعات
    'PURCHASE_DISC' => 6 // خصم مشتريات
]);

// استخراج وتنظيف البيانات المدخلة
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$pass = isset($_POST['pass']) ? htmlspecialchars($_POST['pass'], ENT_QUOTES, 'UTF-8') : '';
$q = isset($_POST['q']) ? htmlspecialchars($_POST['q'], ENT_QUOTES, 'UTF-8') : '';

// التحقق من صحة البيانات الأساسية
if ($id == 0) {
    header('Location: ../warning.php?error=invalid_id');
    exit;
}

if (empty($pass)) {
    header('Location: ../warning.php?error=missing_password');
    exit;
}

// الحصول على إعدادات النظام باستخدام Prepared Statement
$stmt = $conn->prepare("SELECT edit_pass FROM settings LIMIT 1");
if (!$stmt) {
    die('خطأ في تحضير الاستعلام: ' . $conn->error);
}

$stmt->execute();
$result = $stmt->get_result();
$rowstg = $result->fetch_assoc();
$stmt->close();

if (!$rowstg) {
    header('Location: ../warning.php?error=settings_not_found');
    exit;
}

// التحقق من كلمة المرور
if ($pass !== $rowstg['edit_pass']) {
    header('Location: ../warning.php?q=' . urlencode($q) . '&error=invalid_password');
    exit;
}

// الحصول على بيانات الفاتورة للتحقق من وجودها ونوعها
$stmt = $conn->prepare("SELECT * FROM ot_head WHERE id = ? AND isdeleted = 0");
if (!$stmt) {
    die('خطأ في تحضير الاستعلام: ' . $conn->error);
}

$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$invoice = $result->fetch_assoc();
$stmt->close();

if (!$invoice) {
    header('Location: ../warning.php?q=' . urlencode($q) . '&error=invoice_not_found');
    exit;
}

$pro_tybe = intval($invoice['pro_tybe']);

/**
 * دالة الحصول على إعدادات نوع الفاتورة
 * Get invoice type configuration
 */
function getInvoiceConfig($pro_tybe) {
    $configs = [
        INVOICE_TYPES['PURCHASE'] => [
            'note' => 'حذف فاتورة مشتريات',
            'process_type' => 'delete buy'
        ],
        INVOICE_TYPES['SALES'] => [
            'note' => 'حذف فاتورة مبيعات',
            'process_type' => 'delete sales'
        ],
        INVOICE_TYPES['POS'] => [
            'note' => 'حذف فاتورة ريسيت',
            'process_type' => 'delete cash'
        ]
    ];
    
    return isset($configs[$pro_tybe]) ? $configs[$pro_tybe] : [
        'note' => 'حذف فاتورة',
        'process_type' => 'delete invoice'
    ];
}

// الحصول على إعدادات الفاتورة
$config = getInvoiceConfig($pro_tybe);

// بدء المعاملة لضمان تماسك البيانات
try {
    $conn->begin_transaction();
    
    // حذف تفاصيل الفاتورة باستخدام Prepared Statement
    $stmt = $conn->prepare("UPDATE fat_details SET isdeleted = 1 WHERE pro_id = ?");
    if (!$stmt) {
        throw new Exception('فشل في تحضير استعلام حذف تفاصيل الفاتورة: ' . $conn->error);
    }
    
    $stmt->bind_param("i", $id);
    if (!$stmt->execute()) {
        throw new Exception('فشل في حذف تفاصيل الفاتورة: ' . $stmt->error);
    }
    $stmt->close();
    
    // حذف رأس الفاتورة
    $stmt = $conn->prepare("UPDATE ot_head SET isdeleted = 1 WHERE id = ?");
    if (!$stmt) {
        throw new Exception('فشل في تحضير استعلام حذف رأس الفاتورة: ' . $conn->error);
    }
    
    $stmt->bind_param("i", $id);
    if (!$stmt->execute()) {
        throw new Exception('فشل في حذف رأس الفاتورة: ' . $stmt->error);
    }
    $stmt->close();
    
    // حذف القيود المحاسبية المرتبطة بالفاتورة الأساسية
    $stmt = $conn->prepare("UPDATE journal_entries SET isdeleted = 1 WHERE op_id = ?");
    if (!$stmt) {
        throw new Exception('فشل في تحضير استعلام حذف القيود المحاسبية: ' . $conn->error);
    }
    
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    
    $stmt = $conn->prepare("UPDATE journal_heads SET isdeleted = 1 WHERE op_id = ?");
    if (!$stmt) {
        throw new Exception('فشل في تحضير استعلام حذف رؤوس القيود: ' . $conn->error);
    }
    
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    
    // حذف العمليات المرتبطة (مدفوعات/مقبوضات)
    $stmt = $conn->prepare("UPDATE ot_head SET isdeleted = 1 WHERE op2 = ?");
    if (!$stmt) {
        throw new Exception('فشل في تحضير استعلام حذف العمليات المرتبطة: ' . $conn->error);
    }
    
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    
    // حذف القيود المحاسبية للعمليات المرتبطة
    $stmt = $conn->prepare("UPDATE journal_entries SET isdeleted = 1 WHERE op2 = ?");
    if (!$stmt) {
        throw new Exception('فشل في تحضير استعلام حذف قيود العمليات المرتبطة: ' . $conn->error);
    }
    
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    
    $stmt = $conn->prepare("UPDATE journal_heads SET isdeleted = 1 WHERE op2 = ?");
    if (!$stmt) {
        throw new Exception('فشل في تحضير استعلام حذف رؤوس قيود العمليات المرتبطة: ' . $conn->error);
    }
    
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    
    // إتمام المعاملة
    $conn->commit();
    
    // تسجيل العملية
    $stmt = $conn->prepare("INSERT INTO process (type, details, user, date) VALUES (?, ?, ?, NOW())");
    if ($stmt) {
        $details = $config['note'] . ' رقم: ' . $id;
        $stmt->bind_param("ssi", $config['process_type'], $details, $usid);
        $stmt->execute();
        $stmt->close();
    }
    
} catch (Exception $e) {
    // إلغاء المعاملة في حالة الخطأ
    $conn->rollback();
    error_log('خطأ في حذف الفاتورة: ' . $e->getMessage());
    header('Location: ../warning.php?q=' . urlencode($q) . '&error=delete_failed');
    exit;
}

// إعادة التوجيه حسب نوع العملية
$redirects = [
    INVOICE_TYPES['PURCHASE'] => '../operations_summary.php?q=sale',
    INVOICE_TYPES['SALES'] => '../operations_summary.php?q=buy',
    INVOICE_TYPES['POS'] => '../pos_barcode.php'
];

$redirect = $redirects[$pro_tybe] ?? '../operations_summary.php?q=' . urlencode($q);
header("Location: $redirect&success=deleted");
exit;
?>