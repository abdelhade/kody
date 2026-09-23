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

// إزالة عرض البيानات الحساسة في الإنتاج
// echo "<pre>";
// print_r($_POST);
// echo "</pre>";
// die;

// تضمين فئات النظام الجديد
require_once('../classes/InvoiceElementFactory.php');
require_once('../classes/InvoiceProcessor.php');

// تعريف ثوابت أنواع الفواتير


// تعريف أنواع العمليات المحاسبية


// استخراج وتنظيف البيانات المدخلة
$ot_id = isset($_POST['ot_id']) ? intval($_POST['ot_id']) : 0;
$acc2_id = isset($_POST['acc2_id']) ? intval($_POST['acc2_id']) : 0;
$store_id = isset($_POST['store_id']) ? intval($_POST['store_id']) : 0;
$emp_id = isset($_POST['emp_id']) ? intval($_POST['emp_id']) : 0;
$pro_date = isset($_POST['pro_date']) ? htmlspecialchars($_POST['pro_date'], ENT_QUOTES, 'UTF-8') : date('Y-m-d');
$accural_date = isset($_POST['accural_date']) ? htmlspecialchars($_POST['accural_date'], ENT_QUOTES, 'UTF-8') : '';
$pro_id = isset($_POST['pro_id']) ? htmlspecialchars($_POST['pro_id'], ENT_QUOTES, 'UTF-8') : '';
$pro_serial = isset($_POST['pro_serial']) ? htmlspecialchars(trim($_POST['pro_serial']), ENT_QUOTES, 'UTF-8') : '';
$headtotal = isset($_POST['headtotal']) ? floatval($_POST['headtotal']) : 0;
$headdisc = isset($_POST['headdisc']) ? floatval($_POST['headdisc']) : 0;
$headplus = isset($_POST['headplus']) ? floatval($_POST['headplus']) : 0;
$headnet = isset($_POST['headnet']) ? floatval($_POST['headnet']) : 0;

// ملاحظة: تحديد المبلغ المدفوع يتم بعد جلب $pro_tybe من الفاتورة الأصلية (انظر أسفل)

$fund_id = isset($_POST['fund_id']) ? intval($_POST['fund_id']) : 0;

if ($fund_id == 0) {
    // محاولة استرجاع الصندوق الافتراضي إذا كان مفقوداً
    $stmt_def = $conn->prepare("SELECT cur_value FROM myoptions WHERE oname = 'def_fund'");
    if ($stmt_def) {
        $stmt_def->execute();
        $res_def = $stmt_def->get_result();
        if ($row_def = $res_def->fetch_assoc()) {
            $fund_id = intval($row_def['cur_value']);
        }
        $stmt_def->close();
    }
}

$submit = isset($_POST['submit']) ? htmlspecialchars($_POST['submit'], ENT_QUOTES, 'UTF-8') : 'save';
$info = isset($_POST['info']) ? htmlspecialchars(trim($_POST['info']), ENT_QUOTES, 'UTF-8') : '';

// التحقق من صحة البيانات الأساسية
if ($ot_id == 0) {
    die('خطأ: معرف الفاتورة مطلوب');
}

if ($acc2_id == 0 || $store_id == 0 || $emp_id == 0) {
    die('خطأ: بيانات مطلوبة مفقودة');
}

// التحقق من وجود أصناف
if (!isset($_POST['itmname']) || !is_array($_POST['itmname']) || empty(array_filter($_POST['itmname']))) {
    die('خطأ: يجب إضافة صنف واحد على الأقل');
}

// الحصول على بيانات الفاتورة الأصلية باستخدام Prepared Statement
$stmt = $conn->prepare("SELECT * FROM ot_head WHERE id = ?");
if (!$stmt) {
    die('خطأ في تحضير الاستعلام: ' . $conn->error);
}

$stmt->bind_param("i", $ot_id);
$stmt->execute();
$result = $stmt->get_result();
$rowop = $result->fetch_assoc();
$stmt->close();

if (!$rowop) {
    die('خطأ: الفاتورة غير موجودة');
}

// إزالة عرض البيانات الحساسة
// echo "<pre>";
// print_r($rowop);
// echo "</pre>";

$pro_tybe = intval($rowop['pro_tybe']);

// تحديد المبلغ المدفوع - أوامر الشراء والبيع وعروض الأسعار لا تحتاج مدفوعات
if(in_array($pro_tybe, [InvoiceProcessor::INVOICE_TYPES['PURCHASE_ORDER'], InvoiceProcessor::INVOICE_TYPES['SALES_ORDER'], InvoiceProcessor::INVOICE_TYPES['OFFER']])) {
    $paid = 0;
} else {
    $paid = isset($_POST['paid']) ? floatval($_POST['paid']) : 0;
}

// الحصول على إعدادات الفاتورة
$config = InvoiceProcessor::getInvoiceConfig($pro_tybe);
if (!$config) {
    die('خطأ: نوع فاتورة غير صحيح');
}

// تحديد الحسابات المحاسبية
$accounts = InvoiceProcessor::getAccountingAccounts($pro_tybe, $store_id, $acc2_id, $fund_id);

// تحقق من أن الصندوق محدد إذا كان هناك مبلغ مدفوع
if ($paid > 0 && $fund_id == 0) {
    die('خطأ: الصندوق المطلوب لمعالجة المدفوعات غير محدد. الرجاء التأكد من اختيار الصندوق.');
}

// بدء المعاملة لضمان تماسك البيانات
try {
    $conn->begin_transaction();
    
    // حساب النسب المئوية
    $fat_disc_per = isset($_POST['headdisc_pct']) ? floatval($_POST['headdisc_pct']) : (($headtotal > 0 && $headdisc > 0) ? number_format($headdisc/$headtotal*100, 2) : 0);
    $fat_plus_per = ($headtotal > 0 && $headplus > 0) ? number_format($headplus/$headtotal*100, 2) : 0;
    
    // تحديث رأس الفاتورة
    InvoiceProcessor::updateHeader($conn, (int) $ot_id, [
        'info' => $info,
        'pro_date' => $pro_date,
        'accural_date' => $accural_date,
        'pro_serial' => $pro_serial,
        'store_id' => $store_id,
        'emp_id' => $emp_id,
        'acc1' => $accounts['acc1'],
        'acc2' => $accounts['acc2'],
        'headtotal' => $headtotal,
        'headdisc' => $headdisc,
        'fat_disc_per' => $fat_disc_per,
        'headplus' => $headplus,
        'fat_plus_per' => $fat_plus_per,
        'headnet' => $headnet,
        'fund_id' => $fund_id,
    ]);

    // تحديث القيود المحاسبية لجميع الفواتير الفعلية (ليس الأوامر أو العروض)
    if(!in_array($pro_tybe, [InvoiceProcessor::INVOICE_TYPES['PURCHASE_ORDER'], InvoiceProcessor::INVOICE_TYPES['SALES_ORDER'], InvoiceProcessor::INVOICE_TYPES['OFFER']])) {
        InvoiceProcessor::updateMainJournal(
            $conn,
            (int) $ot_id,
            (int) $pro_tybe,
            $config,
            $accounts,
            (int) $acc2_id,
            (float) $headnet,
            (string) $pro_date
        );
    }

    // معالجة المدفوعات
    $paid = isset($_POST['paid']) ? floatval($_POST['paid']) : 0;
    InvoiceProcessor::syncSimplePayment(
        $conn,
        (int) $ot_id,
        (int) $pro_tybe,
        $config,
        $accounts,
        (string) $info,
        (string) $pro_date,
        (int) $emp_id,
        (int) $usid,
        (float) $paid
    );

    // تحديث تفاصيل الفاتورة — soft delete ثم إعادة الإدراج
    $lines = InvoiceProcessor::linesFromPost($_POST);
    InvoiceProcessor::replaceDetails(
        $conn,
        (int) $ot_id,
        (int) $pro_tybe,
        (int) $store_id,
        $lines,
        true,
        true
    );
    
    // تحديث إجمالي الأرباح للمبيعات
    if(in_array($pro_tybe, [InvoiceProcessor::INVOICE_TYPES['SALES'], InvoiceProcessor::INVOICE_TYPES['POS'], InvoiceProcessor::INVOICE_TYPES['OFFER']])) {
        InvoiceProcessor::recalcProfit($conn, (int) $ot_id);
    }
    
    // إتمام المعاملة
    $conn->commit();
    
    InvoiceProcessor::syncItemQuantities($conn, (int) $ot_id);
    
    // تسجيل العملية
    $process_types = [
        InvoiceProcessor::INVOICE_TYPES['PURCHASE'] => 'edit buy',
        InvoiceProcessor::INVOICE_TYPES['SALES'] => 'edit sales',
        InvoiceProcessor::INVOICE_TYPES['POS'] => 'edit cash'
    ];
    
    $process_type = $process_types[$pro_tybe] ?? 'edit invoice';
    $stmt = $conn->prepare("INSERT INTO process (type) VALUES (?)");
    $stmt->bind_param("s", $process_type);
    $stmt->execute();
    $stmt->close();
    
} catch (Exception $e) {
    // إلغاء المعاملة في حالة الخطأ
    $conn->rollback();
    
    // تسجيل تفاصيل إضافية لتشخيص الخطأ
    $debug_info = [
        'error' => $e->getMessage(),
        'paid' => $paid,
        'fund_id' => $fund_id,
        'store_id' => $store_id,
        'acc2_id' => $acc2_id,
        'accounts' => $accounts ?? [],
        'post_data' => $_POST
    ];
    error_log('=== INVOICE EDIT ERROR ===');
    error_log(print_r($debug_info, true));
    
    die('حدث خطأ أثناء تعديل الفاتورة: ' . $e->getMessage() . ' | ' . json_encode(['fund_id' => $fund_id, 'acc1' => ($accounts['acc1'] ?? 'null'), 'acc2' => ($accounts['acc2'] ?? 'null'), 'acc5' => ($accounts['acc5'] ?? 'null'), 'acc6' => ($accounts['acc6'] ?? 'null')]));
}

// إعادة التوجيه حسب نوع العملية
$redirects = [
    InvoiceProcessor::INVOICE_TYPES['PURCHASE'] => '../operations_summary.php?q=purchase&success=1',
    InvoiceProcessor::INVOICE_TYPES['SALES'] => '../operations_summary.php?q=sale&success=1',
    InvoiceProcessor::INVOICE_TYPES['POS'] => '../pos_barcode.php?success=1'
];

$redirect = $redirects[$pro_tybe] ?? '../operations_summary.php?success=1';
header("Location: $redirect");
exit;
?>