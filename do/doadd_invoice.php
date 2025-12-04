<?php
session_start();
include('../includes/connect.php');

// Debug: Log all POST data
error_log('POST data received: ' . print_r($_POST, true));

// Debug: Log individual key values
error_log('pro_tybe: ' . (isset($_POST['pro_tybe']) ? $_POST['pro_tybe'] : 'NOT SET'));
error_log('store_id: ' . (isset($_POST['store_id']) ? $_POST['store_id'] : 'NOT SET'));
error_log('acc2_id: ' . (isset($_POST['acc2_id']) ? $_POST['acc2_id'] : 'NOT SET'));
error_log('emp_id: ' . (isset($_POST['emp_id']) ? $_POST['emp_id'] : 'NOT SET'));
error_log('itmname: ' . (isset($_POST['itmname']) ? 'SET' : 'NOT SET'));

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

// إزالة عرض البيانات الحساسة في الإنتاج
// echo "<pre>";
// print_r($_POST);
// echo "</pre>";

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
$pro_tybe = isset($_POST['pro_tybe']) ? intval($_POST['pro_tybe']) : 0;
$store_id = isset($_POST['store_id']) ? intval($_POST['store_id']) : 0;
$pro_serial = isset($_POST['pro_serial']) ? htmlspecialchars(trim($_POST['pro_serial']), ENT_QUOTES, 'UTF-8') : '';
$pro_date = isset($_POST['pro_date']) ? htmlspecialchars($_POST['pro_date'], ENT_QUOTES, 'UTF-8') : date('Y-m-d');
$accural_date = isset($_POST['accural_date']) ? htmlspecialchars($_POST['accural_date'], ENT_QUOTES, 'UTF-8') : '';
$acc2_id = isset($_POST['acc2_id']) ? intval($_POST['acc2_id']) : 0;
$emp_id = isset($_POST['emp_id']) ? intval($_POST['emp_id']) : 0;
$headtotal = isset($_POST['headtotal']) ? floatval($_POST['headtotal']) : 0;
$headdisc = isset($_POST['headdisc']) ? floatval($_POST['headdisc']) : 0;
$headplus = isset($_POST['headplus']) ? floatval($_POST['headplus']) : 0;
$headnet = isset($_POST['headnet']) ? floatval($_POST['headnet']) : 0;
$fund_id = isset($_POST['fund_id']) ? intval($_POST['fund_id']) : 0;
$info = isset($_POST['info']) ? htmlspecialchars(trim($_POST['info']), ENT_QUOTES, 'UTF-8') : '';
$submit = isset($_POST['submit']) ? htmlspecialchars($_POST['submit'], ENT_QUOTES, 'UTF-8') : 'save';

// Get order type from age parameter
$order_type = isset($_POST['age']) ? intval($_POST['age']) : 1; // Default to takeaway (1)

// إضافة نوع الطلب إلى حقل info
$order_type_text = '';
switch($order_type) {
    case 1:
        $order_type_text = 'تيك أواي';
        break;
    case 2:
        $order_type_text = 'طاولة';
        break;
    case 3:
        $order_type_text = 'دليفري';
        break;
    default:
        $order_type_text = 'تيك أواي';
}

// إضافة نوع الطلب إلى حقل info
if (!empty($order_type_text)) {
    $info = empty($info) ? "نوع الطلب: $order_type_text" : "$info - نوع الطلب: $order_type_text";
}

// إضافة بيانات العميل للدليفري
if ($order_type == 3) { // دليفري
    $delivery_name = isset($_POST['delivery_customer_name']) ? htmlspecialchars(trim($_POST['delivery_customer_name']), ENT_QUOTES, 'UTF-8') : '';
    $delivery_phone = isset($_POST['delivery_customer_phone']) ? htmlspecialchars(trim($_POST['delivery_customer_phone']), ENT_QUOTES, 'UTF-8') : '';
    $delivery_address = isset($_POST['delivery_customer_address']) ? htmlspecialchars(trim($_POST['delivery_customer_address']), ENT_QUOTES, 'UTF-8') : '';
    
    if (!empty($delivery_name) && !empty($delivery_phone) && !empty($delivery_address)) {
        $info .= " - العميل: $delivery_name - الهاتف: $delivery_phone - العنوان: $delivery_address";
    }
}

// إضافة اسم الطاولة إلى حقل info إذا كانت موجودة
$table_name = isset($_POST['table_name']) ? htmlspecialchars(trim($_POST['table_name']), ENT_QUOTES, 'UTF-8') : '';
if (!empty($table_name)) {
    $info = empty($info) ? "طاولة: $table_name" : "$info - طاولة: $table_name";
}

// تحديد المبلغ المدفوع حسب نوع الفاتورة
if($pro_tybe == INVOICE_TYPES['POS']){
    $paid = $headnet;
} else {
    $paid = isset($_POST['paid']) ? floatval($_POST['paid']) : 0;
}

// التحقق من صحة البيانات الأساسية
error_log('Validation check - pro_tybe: ' . $pro_tybe . ', store_id: ' . $store_id . ', acc2_id: ' . $acc2_id . ', emp_id: ' . $emp_id);
if ($pro_tybe == 0 || $store_id == 0 || $acc2_id == 0 || $emp_id == 0) {
    error_log('VALIDATION FAILED: Required data missing');
    $missing = [];
    if ($pro_tybe == 0) $missing[] = 'نوع الفاتورة';
    if ($store_id == 0) $missing[] = 'المخزن';
    if ($acc2_id == 0) $missing[] = 'العميل';
    if ($emp_id == 0) $missing[] = 'الموظف';
    die('خطأ: بيانات مطلوبة مفقودة - ' . implode(', ', $missing));
}

// التحقق من وجود أصناف
error_log('Item validation check - itmname set: ' . (isset($_POST['itmname']) ? 'YES' : 'NO'));
if (isset($_POST['itmname'])) {
    error_log('itmname is array: ' . (is_array($_POST['itmname']) ? 'YES' : 'NO'));
    if (is_array($_POST['itmname'])) {
        error_log('itmname array filter count: ' . count(array_filter($_POST['itmname'])));
    }
}
if (!isset($_POST['itmname']) || !is_array($_POST['itmname']) || empty(array_filter($_POST['itmname']))) {
    error_log('VALIDATION FAILED: No items in order');
    die('خطأ: يجب إضافة صنف واحد على الأقل');
}

/**
 * دالة الحصول على إعدادات نوع الفاتورة
 * Get invoice type configuration
 */
function getInvoiceConfig($pro_tybe) {
    $configs = [
        INVOICE_TYPES['PURCHASE'] => [
            'note' => 'فاتورة مشتريات',
            'paid_note' => 'سند دفع',
            'disc_type' => ACCOUNTING_TYPES['PURCHASE_DISC'],
            'paid_type' => ACCOUNTING_TYPES['PAYMENT'],
            'cost_account' => 97
        ],
        INVOICE_TYPES['SALES'] => [
            'note' => 'فاتورة مبيعات',
            'paid_note' => 'سند قبض',
            'disc_type' => ACCOUNTING_TYPES['SALES_DISC'],
            'paid_type' => ACCOUNTING_TYPES['RECEIPT'],
            'cost_account' => 91
        ],
        INVOICE_TYPES['POS'] => [
            'note' => 'فاتورة ريسيت',
            'paid_note' => 'سند قبض',
            'disc_type' => ACCOUNTING_TYPES['SALES_DISC'],
            'paid_type' => ACCOUNTING_TYPES['RECEIPT'],
            'cost_account' => 91
        ]
    ];
    
    return isset($configs[$pro_tybe]) ? $configs[$pro_tybe] : null;
}

/**
 * دالة تحديد الحسابات المحاسبية
 * Get accounting accounts based on invoice type
 */
function getAccountingAccounts($pro_tybe, $store_id, $acc2_id, $fund_id) {
    switch($pro_tybe) {
        case INVOICE_TYPES['PURCHASE']:
            return [
                'acc1' => $store_id,
                'acc2' => $acc2_id,
                'acc3' => $acc2_id,
                'acc4' => 97,
                'acc5' => $acc2_id,
                'acc6' => $fund_id
            ];
            
        case INVOICE_TYPES['SALES']:
        case INVOICE_TYPES['POS']:
            return [
                'acc1' => $acc2_id,
                'acc2' => $store_id,
                'acc3' => 91,
                'acc4' => $acc2_id,
                'acc5' => $fund_id,
                'acc6' => $acc2_id
            ];
            
        default:
            throw new InvalidArgumentException('نوع فاتورة غير مدعوم');
    }
}

// الحصول على إعدادات الفاتورة
$config = getInvoiceConfig($pro_tybe);
error_log('Invoice config for pro_tybe ' . $pro_tybe . ': ' . print_r($config, true));
if (!$config) {
    error_log('VALIDATION FAILED: Invalid invoice type');
    die('خطأ: نوع فاتورة غير صحيح');
}

// تحديد الحسابات المحاسبية
$accounts = getAccountingAccounts($pro_tybe, $store_id, $acc2_id, $fund_id);
error_log('Accounting accounts: ' . print_r($accounts, true));
/**
 * دالة الحصول على رقم الفاتورة التالي باستخدام Prepared Statement
 * Get next invoice number using prepared statement
 */
function getNextInvoiceNumber($conn, $invoice_type) {
    $stmt = $conn->prepare("SELECT MAX(CAST(pro_id AS UNSIGNED)) as max_id FROM ot_head WHERE pro_tybe = ?");
    if (!$stmt) {
        throw new Exception('فشل في تحضير الاستعلام: ' . $conn->error);
    }
    
    $stmt->bind_param("i", $invoice_type);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    
    return $row && $row['max_id'] ? ($row['max_id'] + 1) : 1;
}

/**
 * دالة الحصول على رقم العملية التالي
 * Get next operation number for accounting operations
 */
function getNextOperationNumber($conn, $operation_type) {
    return getNextInvoiceNumber($conn, $operation_type);
}

// الحصول على أرقام العمليات
try {
    $pro_id = getNextInvoiceNumber($conn, $pro_tybe);
    $disc_op_id = getNextOperationNumber($conn, $config['disc_type']);
    $paid_op_id = getNextOperationNumber($conn, $config['paid_type']);
} catch (Exception $e) {
    die('خطأ في الحصول على أرقام العمليات: ' . $e->getMessage());
}
// حساب النسب المئوية للخصم والإضافي
$fat_disc_per = ($headtotal > 0 && $headdisc > 0) ? number_format($headdisc/$headtotal*100, 2) : 0;
$fat_plus_per = ($headtotal > 0 && $headplus > 0) ? number_format($headplus/$headtotal*100, 2) : 0;

// التحقق من وجود متغير الطاولة (اختياري)
$table = isset($_POST['table']) ? intval($_POST['table']) : 0;

// بدء المعاملة لضمان تماسك البيانات
error_log('Starting database transaction');
try {
    error_log('Starting database transaction');
    $conn->begin_transaction();
    error_log('Database transaction started successfully');
    
    // إدخال رأس الفاتورة باستخدام Prepared Statement
    $stmt = $conn->prepare(
        "INSERT INTO ot_head (
            pro_id, pro_tybe, is_stock, is_journal, journal_tybe, info, pro_date, 
            accural_date, pro_pattren, pro_serial, price_list, store_id, emp_id, 
            emp2_id, acc1, acc2, pro_value, fat_cost, cost_center, profit, 
            fat_total, fat_disc, fat_disc_per, fat_plus, fat_plus_per, 
            fat_tax, fat_tax_per, fat_net, user
        ) VALUES (
            ?, ?, 1, 1, ?, ?, ?, ?, 1, ?, 1, ?, ?, ?, ?, ?, ?, 0, 1, 0, 
            ?, ?, ?, ?, ?, 0, 0, ?, ?
        )"
    );
    
    if (!$stmt) {
        throw new Exception('فشل في تحضير استعلام إدخال الفاتورة: ' . $conn->error);
    }
    
    $stmt->bind_param(
        "ssssssssssssssssssss",
        $pro_id, $pro_tybe, $pro_tybe, $info, $pro_date, $accural_date, 
        $pro_serial, $store_id, $emp_id, $emp_id, $accounts['acc1'], 
        $accounts['acc2'], $headtotal, $headtotal, $headdisc, 
        $fat_disc_per, $headplus, $fat_plus_per, $headnet, $usid
    );
    
    error_log('Executing order header insert');
    if (!$stmt->execute()) {
        error_log('FAILED to insert order header: ' . $stmt->error);
        throw new Exception('فشل في إدخال الفاتورة: ' . $stmt->error);
    }
    
    $last_op = $conn->insert_id;
    error_log('Order header inserted successfully with ID: ' . $last_op);
    $stmt->close();
    // إدخال قيود اليومية للفواتير المدعومة
    if(in_array($pro_tybe, [INVOICE_TYPES['PURCHASE'], INVOICE_TYPES['SALES'], INVOICE_TYPES['POS']])) {
        // الحصول على رقم القيد التالي
        $stmt = $conn->prepare("SELECT MAX(journal_id) as max_id FROM journal_heads");
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $journal_id = $row && $row['max_id'] ? ($row['max_id'] + 1) : 1;
        $stmt->close();
        
        // إدخال رأس القيد
        $stmt = $conn->prepare(
            "INSERT INTO journal_heads (journal_id, total, jdate, details, user, op_id) 
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        
        $details = $config['note'] . " _ " . $last_op;
        $stmt->bind_param("ssssss", $journal_id, $headnet, $pro_date, $details, $usid, $last_op);
        
        if (!$stmt->execute()) {
            throw new Exception('فشل في إدخال رأس القيد: ' . $stmt->error);
        }
        
        $journal_lastid = $conn->insert_id;
        $stmt->close();
        
        // إدخال تفاصيل القيد (المدين)
        $stmt = $conn->prepare(
            "INSERT INTO journal_entries (journal_id, account_id, debit, credit, tybe, op_id) 
             VALUES (?, ?, ?, 0, 0, ?)"
        );
        $stmt->bind_param("ssss", $journal_lastid, $accounts['acc1'], $headnet, $last_op);
        
        if (!$stmt->execute()) {
            throw new Exception('فشل في إدخال القيد المدين: ' . $stmt->error);
        }
        $stmt->close();
        
        // إدخال تفاصيل القيد (الدائن)
        $stmt = $conn->prepare(
            "INSERT INTO journal_entries (journal_id, account_id, debit, credit, tybe, op_id) 
             VALUES (?, ?, 0, ?, 1, ?)"
        );
        $stmt->bind_param("ssss", $journal_lastid, $accounts['acc2'], $headnet, $last_op);
        
        if (!$stmt->execute()) {
            throw new Exception('فشل في إدخال القيد الدائن: ' . $stmt->error);
        }
        $stmt->close();
    }
    // معالجة المدفوعات إذا وجدت
    if ($paid > 0) {
        // إدخال عملية الدفع/القبض
        $stmt = $conn->prepare(
            "INSERT INTO ot_head (
                pro_id, pro_tybe, is_journal, journal_tybe, info, pro_date, 
                emp_id, acc1, acc2, pro_value, cost_center, profit, user, op2
            ) VALUES (?, ?, 1, ?, ?, ?, ?, ?, ?, ?, 1, 0, ?, ?)"
        );
        
        $stmt->bind_param(
            "sssssssssss",
            $paid_op_id, $config['paid_type'], $config['paid_type'], $info, $pro_date,
            $emp_id, $accounts['acc5'], $accounts['acc6'], $paid, $usid, $last_op
        );
        
        if (!$stmt->execute()) {
            throw new Exception('فشل في إدخال عملية الدفع: ' . $stmt->error);
        }
        
        $last_paid = $conn->insert_id;
        $stmt->close();
        
        // إدخال قيد الدفع/القبض
        $stmt = $conn->prepare("SELECT MAX(journal_id) as max_id FROM journal_heads");
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $journal_id = $row && $row['max_id'] ? ($row['max_id'] + 1) : 1;
        $stmt->close();
        
        // رأس قيد الدفع
        $stmt = $conn->prepare(
            "INSERT INTO journal_heads (journal_id, op_id, total, jdate, details, user, op2) 
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        
        $paid_details = $config['paid_note'] . " _ " . $pro_id;
        $stmt->bind_param("sssssss", $journal_id, $last_paid, $paid, $pro_date, $paid_details, $usid, $last_op);
        $stmt->execute();
        $journal_lastid = $conn->insert_id;
        $stmt->close();
        
        // تفاصيل قيد الدفع (مدين)
        $stmt = $conn->prepare(
            "INSERT INTO journal_entries (journal_id, account_id, debit, credit, tybe, op2) 
             VALUES (?, ?, ?, 0, 0, ?)"
        );
        $stmt->bind_param("ssss", $journal_lastid, $accounts['acc5'], $paid, $last_op);
        $stmt->execute();
        $stmt->close();
        
        // تفاصيل قيد الدفع (دائن)
        $stmt = $conn->prepare(
            "INSERT INTO journal_entries (journal_id, account_id, debit, credit, tybe, op2) 
             VALUES (?, ?, 0, ?, 1, ?)"
        );
        $stmt->bind_param("ssss", $journal_lastid, $accounts['acc6'], $paid, $last_op);
        $stmt->execute();
        $stmt->close();
    }




    



    // معالجة تفاصيل الفواتير باستخدام Prepared Statements
    error_log('Processing order items');
    if (isset($_POST['itmname'], $_POST['itmqty'], $_POST['itmprice'], $_POST['itmdisc'])) {
        error_log('All item arrays are set');
        // تحضير استعلام إدخال تفاصيل الفاتورة
        $stmt_details = $conn->prepare(
            "INSERT INTO fat_details (
                pro_tybe, pro_id, item_id, u_val, qty_in, qty_out, price, 
                discount, det_value, fatid, fat_tybe, det_store, cost_price, profit
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        
        if (!$stmt_details) {
            throw new Exception('فشل في تحضير استعلام تفاصيل الفاتورة: ' . $conn->error);
        }
        
        // تحضير استعلام الحصول على بيانات الصنف
        $stmt_item = $conn->prepare("SELECT cost_price, itmqty FROM myitems WHERE id = ?");
        if (!$stmt_item) {
            throw new Exception('فشل في تحضير استعلام بيانات الصنف: ' . $conn->error);
        }
        
        // تحضير استعلام تحديث بيانات الصنف
        $stmt_update = $conn->prepare("UPDATE myitems SET last_price = ?, cost_price = ? WHERE id = ?");
        if (!$stmt_update) {
            throw new Exception('فشل في تحضير استعلام تحديث الصنف: ' . $conn->error);
        }
        
        // معالجة كل صنف
        foreach ($_POST['itmname'] as $index => $itmname) {
            if (empty($itmname)) continue;
            
            $itmname = intval($itmname);
            $itmqty = floatval($_POST['itmqty'][$index]);
            $itmprice = floatval($_POST['itmprice'][$index]);
            $itmdisc = floatval($_POST['itmdisc'][$index]);
            $u_val = floatval($_POST['u_val'][$index]);
            
            // تحديد الكميات حسب نوع الفاتورة
            if($pro_tybe == INVOICE_TYPES['PURCHASE']) {
                $qty_in = $itmqty * $u_val;
                $qty_out = 0;
            } elseif(in_array($pro_tybe, [INVOICE_TYPES['SALES'], INVOICE_TYPES['POS']])) {
                $qty_in = 0;
                $qty_out = $itmqty * $u_val;
            }
            
            $det_value = $itmqty * ($itmprice - $itmdisc);
            
            // الحصول على بيانات الصنف الحالية
            $stmt_item->bind_param("i", $itmname);
            $stmt_item->execute();
            $result = $stmt_item->get_result();
            $rowbl = $result->fetch_assoc();
            
            if (!$rowbl) {
                throw new Exception('صنف غير موجود: ' . $itmname);
            }
            
            $oldprice = floatval($rowbl['cost_price']);
            $oldqty = intval($rowbl['itmqty']);
            $cost_price = $oldprice;
            $itmprofit = 0;
            
            // حساب التكلفة والربح
            if($pro_tybe == INVOICE_TYPES['PURCHASE']) {
                // حساب سعر التكلفة المتوسط
                $unit_price = $itmprice / $u_val;
                $oldbalance = $oldprice * $oldqty;
                $newbalance = $qty_in * $unit_price;
                $total_balance = $oldbalance + $newbalance;
                $total_qty = $oldqty + $qty_in;
                
                if($total_qty > 0) {
                    $cost_price = $total_balance / $total_qty;
                }
                
                // تحديث بيانات الصنف
                $stmt_update->bind_param("sss", $unit_price, $cost_price, $itmname);
                if (!$stmt_update->execute()) {
                    throw new Exception('فشل في تحديث بيانات الصنف ' . $itmname);
                }
                
                $itmprice = $unit_price;
                
            } elseif (in_array($pro_tybe, [INVOICE_TYPES['SALES'], INVOICE_TYPES['POS']])) {
                // حساب الربح للمبيعات
                $unit_price = $itmprice / $u_val;
                $itmprofit = $itmqty * $u_val * ($unit_price - $oldprice);
                $itmprice = $unit_price;
            }
            
            // إدخال تفاصيل الفاتورة
            $stmt_details->bind_param(
                "ssssssssssssss",
                $pro_tybe, $last_op, $itmname, $u_val, $qty_in, $qty_out,
                $itmprice, $itmdisc, $det_value, $last_op, $pro_tybe,
                $store_id, $cost_price, $itmprofit
            );
            
            if (!$stmt_details->execute()) {
                throw new Exception('فشل في إدخال تفاصيل الصنف ' . $itmname);
            }
        }
        
        // إغلاق الاستعلامات
        $stmt_details->close();
        $stmt_item->close();
        $stmt_update->close();
    }
    // تحديث إجمالي الأرباح للمبيعات
    if(in_array($pro_tybe, [INVOICE_TYPES['SALES'], INVOICE_TYPES['POS']])) {
        $stmt = $conn->prepare("SELECT SUM(profit) AS tprofit FROM fat_details WHERE fatid = ?");
        $stmt->bind_param("i", $last_op);
        $stmt->execute();
        $result = $stmt->get_result();
        $rowprofit = $result->fetch_assoc();
        $ot_profit = $rowprofit['tprofit'] ?? 0;
        $stmt->close();
        
        // تحديث رقم الربح في رأس الفاتورة
        $stmt = $conn->prepare("UPDATE ot_head SET profit = ? WHERE id = ?");
        $stmt->bind_param("ss", $ot_profit, $last_op);
        $stmt->execute();
        $stmt->close();
    }
    
    // إتمام المعاملة
    error_log('Committing transaction');
    $conn->commit();
    error_log('Transaction committed successfully');
    
    // تسجيل العملية
    $process_types = [
        INVOICE_TYPES['PURCHASE'] => 'add buy',
        INVOICE_TYPES['SALES'] => 'add sales',
        INVOICE_TYPES['POS'] => 'add cash'
    ];
    
    $process_type = $process_types[$pro_tybe] ?? 'add invoice';
    $stmt = $conn->prepare("INSERT INTO process (type) VALUES (?)");
    $stmt->bind_param("s", $process_type);
    $stmt->execute();
    $stmt->close();
    
    // تعيين رسالة نجاح
    $_SESSION['success_message'] = 'تم حفظ الطلب بنجاح - رقم الفاتورة: ' . $pro_id;
    
} catch (Exception $e) {
    // إلغاء المعاملة في حالة الخطأ
    error_log('ERROR in transaction: ' . $e->getMessage());
    error_log('ERROR trace: ' . $e->getTraceAsString());
    $conn->rollback();
    error_log('خطأ في معالجة الفاتورة: ' . $e->getMessage());
    die('حدث خطأ أثناء معالجة الفاتورة: ' . $e->getMessage());
}

// إعادة التوجيه حسب نوع العملية
error_log('Redirecting with submit value: ' . $submit . ' and invoice type: ' . $pro_tybe);
error_log('Last operation ID: ' . $last_op);
if ($submit == 'print') {
    error_log('Redirecting to print sales page');
    error_log('Header: Location: ../print/print_sales.php?id=$last_op');
    header("Location: ../print/print_sales.php?id=$last_op");
} elseif ($submit == 'cash') {
    error_log('Redirecting to receipt page');
    error_log('Header: Location: ../print/receipt.php?id=$last_op');
    header("Location: ../print/receipt.php?id=$last_op");
} elseif ($submit == 'save') {
    error_log('Redirecting with save action');
    // For save action, redirect back to POS for POS invoices, or to sales page for others
    if ($pro_tybe == INVOICE_TYPES['POS']) {
        error_log('Redirecting to POS barcode page');
        error_log('Header: Location: ../pos_barcode.php');
        header("Location: ../pos_barcode.php");
    } else {
        $redirects = [
            INVOICE_TYPES['PURCHASE'] => '../sales.php?q=sale',
            INVOICE_TYPES['SALES'] => '../sales.php?q=buy'
        ];
        $redirect = $redirects[$pro_tybe] ?? '../sales.php';
        error_log('Redirecting to: ' . $redirect);
        error_log('Header: Location: ' . $redirect);
        header("Location: $redirect");
    }
} else {
    error_log('Redirecting with default action');
    // إعادة توجيه افتراضية حسب نوع الفاتورة
    $redirects = [
        INVOICE_TYPES['PURCHASE'] => '../sales.php?q=sale',
        INVOICE_TYPES['SALES'] => '../sales.php?q=buy',
        INVOICE_TYPES['POS'] => '../pos_barcode.php'
    ];
    
    $redirect = $redirects[$pro_tybe] ?? '../sales.php';
    error_log('Redirecting to default: ' . $redirect);
    error_log('Header: Location: ' . $redirect);
    header("Location: $redirect");
}
error_log('Exiting script');
exit;
?>
