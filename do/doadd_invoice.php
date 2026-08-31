<?php
session_start();
include('../includes/connect.php');

// Debug مؤقت - اعرض الـ POST data
if (isset($_GET['debug'])) {
    header('Content-Type: text/plain');
    print_r($_POST);
    exit;
}



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
require_once('../classes/InvoiceProcessor.php');

// تعريف ثوابت أنواع الفواتير


// تعريف أنواع العمليات المحاسبية


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
$submit = isset($_POST['submit_action']) ? htmlspecialchars($_POST['submit_action'], ENT_QUOTES, 'UTF-8') : (isset($_POST['submit']) ? htmlspecialchars($_POST['submit'], ENT_QUOTES, 'UTF-8') : 'save');
$jal_name = isset($_POST['jal_name']) ? htmlspecialchars(trim($_POST['jal_name']), ENT_QUOTES, 'UTF-8') : NULL;
$jal_notes = isset($_POST['jal_notes']) ? htmlspecialchars(trim($_POST['jal_notes']), ENT_QUOTES, 'UTF-8') : NULL;
$jal_amount = isset($_POST['jal_amount']) ? floatval($_POST['jal_amount']) : 0;

// معالجة الدفع المقسم (كاش + صرافة)
$paid_cash = isset($_POST['paid_cash']) ? floatval($_POST['paid_cash']) : 0;
$paid_bank = isset($_POST['paid_bank']) ? floatval($_POST['paid_bank']) : 0;
$payment_fund_id = isset($_POST['payment_fund_id']) && intval($_POST['payment_fund_id']) > 0
    ? intval($_POST['payment_fund_id'])
    : $fund_id;
$payment_bank_id = isset($_POST['payment_bank_id']) ? intval($_POST['payment_bank_id']) : 0;

// fallback: لو paid_cash مش موجود أو صفر، استخدم paid العادي مع fund_id
if ($paid_cash == 0 && $paid_bank == 0) {
    $paid_cash = isset($_POST['paid']) ? floatval($_POST['paid']) : 0;
    $payment_fund_id = $fund_id > 0 ? $fund_id : $payment_fund_id;
}

error_log('=== SPLIT PAYMENT DEBUG ===');
error_log('POST paid_cash: ' . (isset($_POST['paid_cash']) ? $_POST['paid_cash'] : 'NOT SET'));
error_log('POST paid_bank: ' . (isset($_POST['paid_bank']) ? $_POST['paid_bank'] : 'NOT SET'));
error_log('POST payment_fund_id: ' . (isset($_POST['payment_fund_id']) ? $_POST['payment_fund_id'] : 'NOT SET'));
error_log('POST payment_bank_id: ' . (isset($_POST['payment_bank_id']) ? $_POST['payment_bank_id'] : 'NOT SET'));
error_log('Processed: paid_cash=' . $paid_cash . ', paid_bank=' . $paid_bank . ', payment_fund_id=' . $payment_fund_id . ', payment_bank_id=' . $payment_bank_id);
error_log('=========================');

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
    $delivery_person_id = isset($_POST['delivery_person_id']) ? intval($_POST['delivery_person_id']) : 0;
    
    if (!empty($delivery_name) && !empty($delivery_phone) && !empty($delivery_address)) {
        $info .= " - العميل: $delivery_name - الهاتف: $delivery_phone - العنوان: $delivery_address";
        if ($delivery_person_id > 0) {
            // Fetch delivery person name
            $dp_result = $conn->query("SELECT aname FROM acc_head WHERE id = $delivery_person_id");
            if ($dp_result && $dp_row = $dp_result->fetch_assoc()) {
                $info .= " - مندوب التوصيل: " . $dp_row['aname'];
            }
        }

        $delivery_stmt = $conn->prepare(
            "INSERT INTO delivery_clients (client_name, phone, address)
             VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE client_name = VALUES(client_name), address = VALUES(address), isdeleted = 0"
        );
        if ($delivery_stmt) {
            $delivery_stmt->bind_param('sss', $delivery_name, $delivery_phone, $delivery_address);
            $delivery_stmt->execute();
            $delivery_stmt->close();
        }
    }
}

// إضافة اسم الطاولة إلى حقل info إذا كانت موجودة
$table_name = isset($_POST['table_name']) ? htmlspecialchars(trim($_POST['table_name']), ENT_QUOTES, 'UTF-8') : '';
$table_id = isset($_POST['table_id']) ? intval($_POST['table_id']) : 0;
if ($table_id <= 0 && !empty($table_name)) {
    $stmt_tid = $conn->prepare("SELECT id FROM tables WHERE tname = ? AND isdeleted = 0 LIMIT 1");
    if ($stmt_tid) {
        $stmt_tid->bind_param('s', $table_name);
        $stmt_tid->execute();
        $row_tid = $stmt_tid->get_result()->fetch_assoc();
        if ($row_tid) {
            $table_id = intval($row_tid['id']);
        }
        $stmt_tid->close();
    }
}
if (!empty($table_name)) {
    $info = empty($info) ? "طاولة: $table_name" : "$info - طاولة: $table_name";
}

$ajax_save = isset($_POST['ajax_save']) && $_POST['ajax_save'] == '1';
// إغلاق طلب الطاولة بعد الدفع (مسار الدفع الوحيد)
$finalize_order = isset($_POST['finalize_order']) && $_POST['finalize_order'] == '1';

$is_table_order = (
    $pro_tybe == InvoiceProcessor::INVOICE_TYPES['POS']
    && $order_type == 2
    && $table_id > 0
);

// قاعدة موحدة: الدفع = إغلاق. أي سداد يغطي صافي طلب الطاولة يغلقه فوراً،
// فلا يبقى الطلب مفتوحاً ليُدفع مرة ثانية من مكان آخر.
if (!$finalize_order && $is_table_order && $headnet > 0 && ($paid_cash + $paid_bank) >= $headnet) {
    $finalize_order = true;
}

/**
 * طلب طاولة مفتوح = طلب معلّق: تُسجَّل حركة المخزون فقط،
 * ولا تُنشأ أي قيود محاسبية إلا عند السداد (الإيراد يُعترف به وقت الإغلاق).
 */
$is_pending_table_order = ($is_table_order && !$finalize_order);

// تأكد من وجود عمود table_id
$col_chk = $conn->query("SHOW COLUMNS FROM ot_head LIKE 'table_id'");
if ($col_chk && $col_chk->num_rows == 0) {
    $conn->query("ALTER TABLE ot_head ADD COLUMN table_id INT DEFAULT NULL");
}

// تحديد المبلغ المدفوع حسب نوع الفاتورة
// أوامر الشراء والبيع وعروض الأسعار لا تحتاج مدفوعات
if(in_array($pro_tybe, [InvoiceProcessor::INVOICE_TYPES['PURCHASE_ORDER'], InvoiceProcessor::INVOICE_TYPES['SALES_ORDER'], InvoiceProcessor::INVOICE_TYPES['OFFER']])) {
    $paid = 0;
} elseif($pro_tybe == InvoiceProcessor::INVOICE_TYPES['POS']){
    // If paid amount is sent (which is true for our new POS), use it. 
    // Otherwise calculate it (fallback for old behavior)
    if(isset($_POST['paid'])) {
        $paid = floatval($_POST['paid']);
    } else {
        $paid = $headnet;
    }
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
    $err_msg = 'خطأ: بيانات مطلوبة مفقودة - ' . implode(', ', $missing);
    if ($ajax_save) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'message' => $err_msg], JSON_UNESCAPED_UNICODE);
        exit;
    }
    die($err_msg);
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







// الحصول على إعدادات الفاتورة
$config = InvoiceProcessor::getInvoiceConfig($pro_tybe);
error_log('Invoice config for pro_tybe ' . $pro_tybe . ': ' . print_r($config, true));
if (!$config) {
    error_log('VALIDATION FAILED: Invalid invoice type');
    die('خطأ: نوع فاتورة غير صحيح');
}

// تحديد الحسابات المحاسبية
$accounts = InvoiceProcessor::getAccountingAccounts($pro_tybe, $store_id, $acc2_id, $fund_id);
error_log('Accounting accounts: ' . print_r($accounts, true));






// الحصول على أرقام العمليات
try {
    $pro_id = InvoiceProcessor::getNextInvoiceNumber($conn, $pro_tybe);
    $disc_op_id = InvoiceProcessor::getNextInvoiceNumber($conn, $config['disc_type']);
    $paid_op_id = InvoiceProcessor::getNextInvoiceNumber($conn, $config['paid_type']);
} catch (Exception $e) {
    die('خطأ في الحصول على أرقام العمليات: ' . $e->getMessage());
}
// حساب النسب المئوية للخصم والإضافي
$fat_disc_per = isset($_POST['headdisc_pct']) ? floatval($_POST['headdisc_pct']) : (($headtotal > 0 && $headdisc > 0) ? number_format($headdisc/$headtotal*100, 2) : 0);
$fat_plus_per = ($headtotal > 0 && $headplus > 0) ? number_format($headplus/$headtotal*100, 2) : 0;

// التحقق من وجود متغير الطاولة (اختياري)
$table = isset($_POST['table']) ? intval($_POST['table']) : 0;

// بدء المعاملة لضمان تماسك البيانات
error_log('Starting database transaction');
try {
    error_log('Starting database transaction');
    $conn->begin_transaction();
    error_log('Database transaction started successfully');

    // طلب نوع "طاولة" لا يُحفظ بدون طاولة محددة
    if (
        $pro_tybe == InvoiceProcessor::INVOICE_TYPES['POS']
        && $order_type == 2
        && $table_id <= 0
    ) {
        throw new Exception('لا يمكن حفظ طلب نوع "طاولة" بدون تحديد طاولة');
    }

    // الطلب المعلّق لا يقبل سداداً جزئياً: إما يبقى مفتوحاً بلا نقدية،
    // أو يُسدَّد كاملاً فيُغلق. السداد الجزئي يتم عبر "دفع أصناف".
    if ($is_pending_table_order && ($paid_cash + $paid_bank) > 0) {
        throw new Exception('السداد الجزئي غير مسموح لطلب الطاولة — سدّد المبلغ كاملاً أو استخدم "دفع أصناف"');
    }

    // إغلاق بصافي صفر يُنتج قيداً بقيمة صفر ويقفل الطاولة بلا إيراد
    if ($finalize_order && $headnet <= 0) {
        throw new Exception('لا يمكن الدفع والإغلاق بصافي صفر — تأكد من تحميل أصناف الطلب');
    }
    
    $edit_id = isset($_REQUEST['edit_id']) ? intval($_REQUEST['edit_id']) : 0;

    // الطلب المسدَّد والمغلق (pro_tybe = 2) لا يُعاد حفظه، وإلا حُذفت قيوده وأُعيد إنشاؤها
    if ($edit_id > 0) {
        $stmt_state = $conn->prepare("SELECT pro_tybe FROM ot_head WHERE id = ? LIMIT 1");
        $stmt_state->bind_param('i', $edit_id);
        $stmt_state->execute();
        $row_state = $stmt_state->get_result()->fetch_assoc();
        $stmt_state->close();

        if ($row_state && intval($row_state['pro_tybe']) == 2) {
            throw new Exception('هذا الطلب مسدَّد ومغلق بالفعل — لا يمكن حفظه مرة أخرى');
        }
    }

    if ($edit_id > 0) {
        // --- تحديث فاتورة موجودة (UPDATE) ---
        error_log('Updating existing order ID: ' . $edit_id);
        
        // تحديث رأس الفاتورة (مع الحفاظ على تاريخ الإنشاء الأصلي pro_date)
        $stmt = $conn->prepare(
            "UPDATE ot_head SET 
                pro_tybe = ?, info = ?, accural_date = ?, 
                pro_serial = ?, store_id = ?, emp_id = ?, emp2_id = ?, 
                acc1 = ?, acc2 = ?, pro_value = ?, fat_total = ?, 
                fat_disc = ?, fat_disc_per = ?, fat_plus = ?, fat_plus_per = ?, 
                fat_net = ?, user = ?, jal_name = ?, jal_notes = ?, jal_amount = ? 
            WHERE id = ?"
        );
        
        if (!$stmt) {
            throw new Exception('فشل في تحضير استعلام تحديث الفاتورة: ' . $conn->error);
        }
        
        $total_paid = $paid_cash + $paid_bank;
        $change_amount = max(0, $total_paid - $headnet);
        $payment_status = ($total_paid >= $headnet) ? 'paid' : (($total_paid > 0) ? 'partial' : 'unpaid');
        $payment_notes_json = json_encode([
            'paid_cash' => $paid_cash,
            'paid_bank' => $paid_bank,
            'payment_fund_id' => $payment_fund_id,
            'payment_bank_id' => $payment_bank_id,
            'change_amount' => $change_amount
        ]);

        $stmt->bind_param(
            "sssssssssssssssssssss",
            $pro_tybe, $info, $accural_date, 
            $pro_serial, $store_id, $emp_id, $emp_id, 
            $accounts['acc1'], $accounts['acc2'], $headtotal, $headtotal, 
            $headdisc, $fat_disc_per, $headplus, $fat_plus_per, 
            $headnet, $usid, $jal_name, $jal_notes, $jal_amount, $edit_id
        );
        
        if (!$stmt->execute()) {
            throw new Exception('فشل في تحديث الفاتورة: ' . $stmt->error);
        }
        $stmt->close();
        
        // تحديث بيانات الدفع في رأس الفاتورة
        $stmt_update_payment = $conn->prepare(
            "UPDATE ot_head SET 
                paid_amount = ?, remaining_amount = ?, payment_status = ?, payment_notes = ?
             WHERE id = ?"
        );
        $stmt_update_payment->bind_param("ddssi", $total_paid, $change_amount, $payment_status, $payment_notes_json, $edit_id);
        $stmt_update_payment->execute();
        $stmt_update_payment->close();

        if ($table_id > 0 && $order_type == 2) {
            $stmt_tbl = $conn->prepare("UPDATE ot_head SET table_id = ? WHERE id = ?");
            $stmt_tbl->bind_param('ii', $table_id, $edit_id);
            $stmt_tbl->execute();
            $stmt_tbl->close();
        }
        
        // حذف التفاصيل القديمة
        // Note: Assuming fat_details.pro_id links to ot_head.pro_id (invoice number), not ot_head.id (primary key)
        // If fat_details.pro_id links to ot_head.id, then use $edit_id directly.
        // For now, we need to fetch the pro_id (invoice number) from ot_head using $edit_id (primary key).
        $stmt_fetch_pro_id = $conn->prepare("SELECT pro_id FROM ot_head WHERE id = ?");
        $stmt_fetch_pro_id->bind_param("i", $edit_id);
        $stmt_fetch_pro_id->execute();
        $result_pro_id = $stmt_fetch_pro_id->get_result();
        $row_pro_id = $result_pro_id->fetch_assoc();
        $stmt_fetch_pro_id->close();

        if ($row_pro_id) {
            $original_pro_id = $row_pro_id['pro_id'];
            $conn->query("DELETE FROM fat_details WHERE fatid = '$edit_id'");

            /**
             * حذف قيود الفاتورة القديمة قبل إعادة إنشائها.
             * تشمل قيد الفاتورة نفسه (op_id = الفاتورة) وقيود سنداتها (op2 = الفاتورة)،
             * لأن ترك قيود السندات يتيمة يُضخّم أرصدة الحسابات مع كل تعديل
             * (triggers جدول journal_entries تُحدّث acc_head.balance).
             * وتُحذف التفاصيل أولاً لأن journal_entries.journal_id مقيّد بـ journal_heads.id.
             */
            $journal_query = $conn->query(
                "SELECT id FROM journal_heads WHERE op_id = '$edit_id' OR op2 = '$edit_id'"
            );
            $journal_ids = [];
            if ($journal_query) {
                while ($journal_row = $journal_query->fetch_assoc()) {
                    $journal_ids[] = intval($journal_row['id']);
                }
            }

            if (!empty($journal_ids)) {
                $jid_list = implode(',', $journal_ids);
                $conn->query("DELETE FROM journal_entries WHERE journal_id IN ($jid_list)");
                $conn->query("DELETE FROM journal_heads WHERE id IN ($jid_list)");
            }

            // سندات الدفع/الخصم المرتبطة بالفاتورة
            $conn->query("DELETE FROM ot_head WHERE op2 = '$edit_id'");
        } else {
            throw new Exception('فشل في العثور على رقم الفاتورة الأصلي للتحديث.');
        }
        
        $last_op = $edit_id; // last_op now refers to the primary key of the updated ot_head record
        error_log('Order header updated successfully for ID: ' . $last_op);
        
    } else {
        // --- إدخال فاتورة جديدة (INSERT) ---
        $stmt = $conn->prepare(
            "INSERT INTO ot_head (
                pro_id, pro_tybe, is_stock, is_journal, journal_tybe, info, pro_date, 
                accural_date, pro_pattren, pro_serial, price_list, store_id, emp_id, 
                emp2_id, acc1, acc2, pro_value, fat_cost, cost_center, profit, 
                fat_total, fat_disc, fat_disc_per, fat_plus, fat_plus_per, 
                fat_tax, fat_tax_per, fat_net, user, jal_name, jal_notes, jal_amount
            ) VALUES (
                ?, ?, 1, 1, ?, ?, ?, ?, 1, ?, 1, ?, ?, ?, ?, ?, ?, 0, 1, 0, 
                ?, ?, ?, ?, ?, 0, 0, ?, ?, ?, ?, ?
            )"
        );
        
        if (!$stmt) {
            throw new Exception('فشل في تحضير استعلام إدخال الفاتورة: ' . $conn->error);
        }
        
        // bind_param يتطلب references - تحويل NULL إلى قيم فارغة لتجنب ArgumentCountError
        $bind_jal_name   = $jal_name   ?? '';
        $bind_jal_notes  = $jal_notes  ?? '';
        $bind_jal_amount = $jal_amount ?? 0;

        $stmt->bind_param(
            "sssssssssssssssssssssss",
            $pro_id, $pro_tybe, $pro_tybe, $info, $pro_date, $accural_date, 
            $pro_serial, $store_id, $emp_id, $emp_id, $accounts['acc1'], 
            $accounts['acc2'], $headtotal, $headtotal, $headdisc, 
            $fat_disc_per, $headplus, $fat_plus_per, $headnet, $usid, $bind_jal_name, $bind_jal_notes, $bind_jal_amount
        );
        
        error_log('Executing order header insert');
        if (!$stmt->execute()) {
            error_log('FAILED to insert order header: ' . $stmt->error);
            throw new Exception('فشل في إدخال الفاتورة: ' . $stmt->error);
        }
        
        $last_op = $conn->insert_id; // last_op now refers to the primary key of the new ot_head record
        error_log('Order header inserted successfully with ID: ' . $last_op);
        $stmt->close();
        
        // حفظ بيانات الدفع للفاتورة الجديدة
        $total_paid_new = $paid_cash + $paid_bank;
        $change_amount_new = max(0, $total_paid_new - $headnet);
        $payment_status_new = ($total_paid_new >= $headnet) ? 'paid' : (($total_paid_new > 0) ? 'partial' : 'unpaid');
        $payment_notes_json_new = json_encode([
            'paid_cash' => $paid_cash,
            'paid_bank' => $paid_bank,
            'payment_fund_id' => $payment_fund_id,
            'payment_bank_id' => $payment_bank_id,
            'change_amount' => $change_amount_new
        ]);
        $stmt_update_payment_new = $conn->prepare(
            "UPDATE ot_head SET 
                paid_amount = ?, remaining_amount = ?, payment_status = ?, payment_notes = ?
             WHERE id = ?"
        );
        $stmt_update_payment_new->bind_param("ddssi", $total_paid_new, $change_amount_new, $payment_status_new, $payment_notes_json_new, $last_op);
        $stmt_update_payment_new->execute();
        $stmt_update_payment_new->close();

        if ($table_id > 0 && $order_type == 2) {
            $stmt_tbl = $conn->prepare("UPDATE ot_head SET table_id = ? WHERE id = ?");
            $stmt_tbl->bind_param('ii', $table_id, $last_op);
            $stmt_tbl->execute();
            $stmt_tbl->close();
        }
    }

    if ($table_id > 0 && $pro_tybe == InvoiceProcessor::INVOICE_TYPES['POS'] && $order_type == 2) {
        // المجموعة المدمجة تُشغل كوحدة واحدة مع فاتورتها
        $stmt_tc = $conn->prepare(
            "UPDATE tables SET table_case = 1 WHERE id = ? OR parent_table_id = ?"
        );
        $stmt_tc->bind_param('ii', $table_id, $table_id);
        $stmt_tc->execute();
        $stmt_tc->close();
    }
    
    // علامة الترحيل: الطلب المعلّق غير مُرحَّل حتى السداد
    $stmt_jf = $conn->prepare("UPDATE ot_head SET is_journal = ? WHERE id = ?");
    $journal_flag = $is_pending_table_order ? 0 : 1;
    $stmt_jf->bind_param('ii', $journal_flag, $last_op);
    $stmt_jf->execute();
    $stmt_jf->close();

    // إنشاء القيود المحاسبية (فقط للفواتير الفعلية، ليس للأوامر أو العروض أو الطلبات المعلّقة)
    if (!$is_pending_table_order && !in_array($pro_tybe, [InvoiceProcessor::INVOICE_TYPES['PURCHASE_ORDER'], InvoiceProcessor::INVOICE_TYPES['SALES_ORDER'], InvoiceProcessor::INVOICE_TYPES['OFFER']])) {
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
        $stmt->bind_param("sdssss", $journal_id, $headnet, $pro_date, $details, $usid, $last_op);
        
        if (!$stmt->execute()) {
            throw new Exception('فشل في إدخال رأس القيد: ' . $stmt->error);
        }
        
        $journal_lastid = $conn->insert_id;
        $stmt->close();
        
        // القيد الأساسي للفاتورة (حسب نوع الفاتورة)
        if(in_array($pro_tybe, [InvoiceProcessor::INVOICE_TYPES['SALES'], InvoiceProcessor::INVOICE_TYPES['POS']])) {
            // فاتورة مبيعات: مدين العميل / دائن المبيعات
            
            // المدين: العميل
            $stmt = $conn->prepare(
                "INSERT INTO journal_entries (journal_id, account_id, debit, credit, tybe, op_id) 
                 VALUES (?, ?, ?, 0, 0, ?)"
            );
            $stmt->bind_param("ssds", $journal_lastid, $acc2_id, $headnet, $last_op);
            
            if (!$stmt->execute()) {
                throw new Exception('فشل في إدخال القيد المدين: ' . $stmt->error);
            }
            $stmt->close();
            
            // الدائن: المبيعات — يُقرأ من شجرة الحسابات (أول حساب طرفي تحت 32)
            // إذا لم يُوجد يُستخدم الحساب 32101 (id=99) كقيمة افتراضية
            $sales_account = 99; // id=99 → code=32101 (إيرادات من التأجير)
            $stmt_sa = $conn->prepare(
                "SELECT ah.id FROM acc_head ah
                 INNER JOIN acc_head parent ON ah.parent_id = parent.id
                 WHERE parent.code LIKE '32%' AND ah.is_basic = 0 AND ah.isdeleted = 0
                 ORDER BY ah.code LIMIT 1"
            );
            if ($stmt_sa) {
                $stmt_sa->execute();
                $res_sa = $stmt_sa->get_result()->fetch_assoc();
                if ($res_sa) { $sales_account = $res_sa['id']; }
                $stmt_sa->close();
            }
            $stmt = $conn->prepare(
                "INSERT INTO journal_entries (journal_id, account_id, debit, credit, tybe, op_id) 
                 VALUES (?, ?, 0, ?, 1, ?)"
            );
            $stmt->bind_param("ssds", $journal_lastid, $sales_account, $headnet, $last_op);
            
            if (!$stmt->execute()) {
                throw new Exception('فشل في إدخال القيد الدائن: ' . $stmt->error);
            }
            $stmt->close();
            
        } else {
            // فواتير أخرى (مشتريات، مردودات، إلخ)
            
            // إدخال تفاصيل القيد (المدين)
            $stmt = $conn->prepare(
                "INSERT INTO journal_entries (journal_id, account_id, debit, credit, tybe, op_id) 
                 VALUES (?, ?, ?, 0, 0, ?)"
            );
            $stmt->bind_param("ssds", $journal_lastid, $accounts['acc1'], $headnet, $last_op);
            
            if (!$stmt->execute()) {
                throw new Exception('فشل في إدخال القيد المدين: ' . $stmt->error);
            }
            $stmt->close();
            
            // إدخال تفاصيل القيد (الدائن)
            $stmt = $conn->prepare(
                "INSERT INTO journal_entries (journal_id, account_id, debit, credit, tybe, op_id) 
                 VALUES (?, ?, 0, ?, 1, ?)"
            );
            $stmt->bind_param("ssds", $journal_lastid, $accounts['acc2'], $headnet, $last_op);
            
            if (!$stmt->execute()) {
                throw new Exception('فشل في إدخال القيد الدائن: ' . $stmt->error);
            }
            $stmt->close();
        }
    }
    
    // معالجة المدفوعات إذا وجدت (فقط للفواتير الفعلية، ليس للأوامر أو العروض)
    // الدفع المقسم: كاش + صرافة
    if (!$is_pending_table_order && !in_array($pro_tybe, [InvoiceProcessor::INVOICE_TYPES['PURCHASE_ORDER'], InvoiceProcessor::INVOICE_TYPES['SALES_ORDER'], InvoiceProcessor::INVOICE_TYPES['OFFER']])) {
        
        // حساب المبلغ الفعلي الداخل للصندوق (المدفوع - الباقي)
        $total_paid = $paid_cash + $paid_bank;
        $change = max(0, $total_paid - $headnet); // الباقي (المرتجع للعميل)
        
        // المبلغ الفعلي الداخل = المدفوع - الباقي
        $actual_cash_received = max(0, $paid_cash - $change);
        $actual_bank_received = $paid_bank; // البنك لا يتأثر بالباقي (الباقي يُرد من الكاش فقط)
        
        // إذا كان الباقي أكبر من الكاش المدفوع، نخصم الفرق من البنك
        if ($change > $paid_cash) {
            $remaining_change = $change - $paid_cash;
            $actual_cash_received = 0;
            $actual_bank_received = max(0, $paid_bank - $remaining_change);
        }
        
        error_log('=== PAYMENT CALCULATION ===');
        error_log('Total paid: ' . $total_paid);
        error_log('Net amount: ' . $headnet);
        error_log('Change (return): ' . $change);
        error_log('Actual cash received: ' . $actual_cash_received);
        error_log('Actual bank received: ' . $actual_bank_received);
        error_log('==========================');
        
        // معالجة الدفع الكاش (فقط إذا كان هناك مبلغ فعلي داخل)
        if ($actual_cash_received > 0 && $payment_fund_id > 0) {
            error_log('Processing cash payment: ' . $actual_cash_received . ' to fund: ' . $payment_fund_id);
            
            // إدخال عملية الدفع الكاش
            $cash_op_id = InvoiceProcessor::getNextInvoiceNumber($conn, $config['paid_type']);
            $stmt = $conn->prepare(
                "INSERT INTO ot_head (
                    pro_id, pro_tybe, is_journal, journal_tybe, info, pro_date, 
                    emp_id, acc1, acc2, pro_value, cost_center, profit, user, op2
                ) VALUES (?, ?, 1, ?, ?, ?, ?, ?, ?, ?, 1, 0, ?, ?)"
            );
            
            $cash_info = $info . " - دفع كاش";
            $stmt->bind_param(
                "ssssssdssss",
                $cash_op_id, $config['paid_type'], $config['paid_type'], $cash_info, $pro_date,
                $emp_id, $payment_fund_id, $acc2_id, $actual_cash_received, $usid, $last_op
            );
            
            if (!$stmt->execute()) {
                throw new Exception('فشل في إدخال عملية الدفع الكاش: ' . $stmt->error);
            }
            
            $last_cash_paid = $conn->insert_id;
            $stmt->close();
            
            // إدخال قيد الدفع الكاش
            $stmt = $conn->prepare("SELECT MAX(journal_id) as max_id FROM journal_heads");
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $journal_id = $row && $row['max_id'] ? ($row['max_id'] + 1) : 1;
            $stmt->close();
            
            // رأس قيد الدفع الكاش
            $stmt = $conn->prepare(
                "INSERT INTO journal_heads (journal_id, op_id, total, jdate, details, user, op2) 
                 VALUES (?, ?, ?, ?, ?, ?, ?)"
            );
            
            $cash_details = $config['paid_note'] . " كاش _ " . $pro_id;
            $stmt->bind_param("ssdssss", $journal_id, $last_cash_paid, $actual_cash_received, $pro_date, $cash_details, $usid, $last_op);
            $stmt->execute();
            $journal_lastid = $conn->insert_id;
            $stmt->close();
            
            // تفاصيل قيد الدفع الكاش (مدين - الصندوق)
            $stmt = $conn->prepare(
                "INSERT INTO journal_entries (journal_id, account_id, debit, credit, tybe, op2) 
                 VALUES (?, ?, ?, 0, 0, ?)"
            );
            $stmt->bind_param("ssds", $journal_lastid, $payment_fund_id, $actual_cash_received, $last_op);
            $stmt->execute();
            $stmt->close();
            
            // تفاصيل قيد الدفع الكاش (دائن - العميل)
            $stmt = $conn->prepare(
                "INSERT INTO journal_entries (journal_id, account_id, debit, credit, tybe, op2) 
                 VALUES (?, ?, 0, ?, 1, ?)"
            );
            $stmt->bind_param("ssds", $journal_lastid, $acc2_id, $actual_cash_received, $last_op);
            $stmt->execute();
            $stmt->close();
            
            error_log('Cash payment processed successfully');
        }
        
        // معالجة الدفع الصرافة (البنك)
        if ($actual_bank_received > 0 && $payment_bank_id > 0) {
            error_log('Processing bank payment: ' . $actual_bank_received . ' to bank: ' . $payment_bank_id);
            
            // إدخال عملية الدفع الصرافة
            $bank_op_id = InvoiceProcessor::getNextInvoiceNumber($conn, $config['paid_type']);
            $stmt = $conn->prepare(
                "INSERT INTO ot_head (
                    pro_id, pro_tybe, is_journal, journal_tybe, info, pro_date, 
                    emp_id, acc1, acc2, pro_value, cost_center, profit, user, op2
                ) VALUES (?, ?, 1, ?, ?, ?, ?, ?, ?, ?, 1, 0, ?, ?)"
            );
            
            $bank_info = $info . " - دفع صرافة";
            $stmt->bind_param(
                "ssssssdssss",
                $bank_op_id, $config['paid_type'], $config['paid_type'], $bank_info, $pro_date,
                $emp_id, $payment_bank_id, $acc2_id, $actual_bank_received, $usid, $last_op
            );
            
            if (!$stmt->execute()) {
                throw new Exception('فشل في إدخال عملية الدفع الصرافة: ' . $stmt->error);
            }
            
            $last_bank_paid = $conn->insert_id;
            $stmt->close();
            
            // إدخال قيد الدفع الصرافة
            $stmt = $conn->prepare("SELECT MAX(journal_id) as max_id FROM journal_heads");
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $journal_id = $row && $row['max_id'] ? ($row['max_id'] + 1) : 1;
            $stmt->close();
            
            // رأس قيد الدفع الصرافة
            $stmt = $conn->prepare(
                "INSERT INTO journal_heads (journal_id, op_id, total, jdate, details, user, op2) 
                 VALUES (?, ?, ?, ?, ?, ?, ?)"
            );
            
            $bank_details = $config['paid_note'] . " صرافة _ " . $pro_id;
            $stmt->bind_param("ssdssss", $journal_id, $last_bank_paid, $actual_bank_received, $pro_date, $bank_details, $usid, $last_op);
            $stmt->execute();
            $journal_lastid = $conn->insert_id;
            $stmt->close();
            
            // تفاصيل قيد الدفع الصرافة (مدين - البنك)
            $stmt = $conn->prepare(
                "INSERT INTO journal_entries (journal_id, account_id, debit, credit, tybe, op2) 
                 VALUES (?, ?, ?, 0, 0, ?)"
            );
            $stmt->bind_param("ssds", $journal_lastid, $payment_bank_id, $actual_bank_received, $last_op);
            $stmt->execute();
            $stmt->close();
            
            // تفاصيل قيد الدفع الصرافة (دائن - العميل)
            $stmt = $conn->prepare(
                "INSERT INTO journal_entries (journal_id, account_id, debit, credit, tybe, op2) 
                 VALUES (?, ?, 0, ?, 1, ?)"
            );
            $stmt->bind_param("ssds", $journal_lastid, $acc2_id, $actual_bank_received, $last_op);
            $stmt->execute();
            $stmt->close();
            
            error_log('Bank payment processed successfully');
        }
    }

    // معالجة تفاصيل الفواتير باستخدام Prepared Statements
    error_log('Processing order items');
    if (isset($_POST['itmname'], $_POST['itmqty'], $_POST['itmprice'], $_POST['itmdisc'])) {
        error_log('All item arrays are set');
        // تحضير استعلام إدخال تفاصيل الفاتورة
        $stmt_details = $conn->prepare(
            "INSERT INTO fat_details (
                pro_tybe, pro_id, item_id, u_val, qty_in, qty_out, price, 
                discount, disc_pct, det_value, fatid, fat_tybe, det_store, cost_price, profit
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        
        if (!$stmt_details) {
            throw new Exception('فشل في تحضير استعلام تفاصيل الفاتورة: ' . $conn->error);
        }
        
        // تحضير استعلام الحصول على بيانات الصنف
        $stmt_item = $conn->prepare("SELECT cost_price, itmqty, price1 FROM myitems WHERE id = ?");
        if (!$stmt_item) {
            throw new Exception('فشل في تحضير استعلام بيانات الصنف: ' . $conn->error);
        }
        
        // تحضير استعلام تحديث بيانات الصنف (يشمل سعر البيع price1)
        $stmt_update = $conn->prepare("UPDATE myitems SET last_price = ?, cost_price = ?, price1 = ? WHERE id = ?");
        if (!$stmt_update) {
            throw new Exception('فشل في تحضير استعلام تحديث الصنف: ' . $conn->error);
        }
        
        // معالجة كل صنف
        foreach ($_POST['itmname'] as $index => $itmname) {
            if (empty($itmname)) continue;
            
            $itmname = intval($itmname);
            $itmqty  = floatval($_POST['itmqty'][$index]  ?? 1);
            $itmprice = floatval($_POST['itmprice'][$index] ?? 0);
            $itmdisc  = floatval($_POST['itmdisc'][$index]  ?? 0);
            $itmdisc_pct = floatval($_POST['itmdisc_pct'][$index] ?? 0);
            $itmsellprice = isset($_POST['itmsellprice'][$index]) ? floatval($_POST['itmsellprice'][$index]) : 0; // سعر البيع (فاتورة مشتريات)
            $u_val   = floatval($_POST['u_val'][$index]   ?? 1);
            if ($u_val <= 0) $u_val = 1; // حماية من القسمة على صفر
            
            // تحديد الكميات حسب نوع الفاتورة
            // أوامر الشراء (12) وأوامر البيع (13) وعروض الأسعار (14) لا تؤثر على المخزون
            if(in_array($pro_tybe, [InvoiceProcessor::INVOICE_TYPES['PURCHASE_ORDER'], InvoiceProcessor::INVOICE_TYPES['SALES_ORDER'], InvoiceProcessor::INVOICE_TYPES['OFFER']])) {
                // أوامر الشراء والبيع وعروض الأسعار → لا تؤثر على المخزون
                $qty_in = 0;
                $qty_out = 0;
            } elseif(in_array($pro_tybe, [InvoiceProcessor::INVOICE_TYPES['PURCHASE'], InvoiceProcessor::INVOICE_TYPES['SALES_RETURN']])) {
                // مشتريات ومردود مبيعات → كمية واردة
                $qty_in = $itmqty * $u_val;
                $qty_out = 0;
            } elseif($pro_tybe === InvoiceProcessor::INVOICE_TYPES['PURCHASE_RETURN']) {
                // مردود مشتريات → كمية منصرفة
                $qty_in = 0;
                $qty_out = $itmqty * $u_val;
            } elseif(in_array($pro_tybe, [InvoiceProcessor::INVOICE_TYPES['SALES'], InvoiceProcessor::INVOICE_TYPES['POS']])) {
                // مبيعات، كاشير → كمية منصرفة
                $qty_in = 0;
                $qty_out = $itmqty * $u_val;
            } else {
                $qty_in = 0;
                $qty_out = 0;
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
            $oldqty = InvoiceProcessor::getRealStockQuantity($conn, $itmname);
            $existing_price1 = floatval($rowbl['price1']);
            $cost_price = $oldprice;
            $itmprofit = 0;
            
            // حساب التكلفة والربح
            if(in_array($pro_tybe, [InvoiceProcessor::INVOICE_TYPES['PURCHASE'], InvoiceProcessor::INVOICE_TYPES['PURCHASE_ORDER']])) {
                // حساب سعر التكلفة المتوسط
                $unit_price = $itmprice / $u_val;
                $oldbalance = $oldprice * $oldqty;
                $newbalance = $qty_in * $unit_price;
                $total_balance = $oldbalance + $newbalance;
                $total_qty = $oldqty + $qty_in;
                
                if($total_qty > 0) {
                    $cost_price = $total_balance / $total_qty;
                }
                
                // سعر البيع للوحدة الأساسية (price1) — يُحفظ فقط إذا أُدخل، وإلا يبقى كما هو
                $sell_unit_price = ($itmsellprice > 0) ? ($itmsellprice / $u_val) : $existing_price1;
                
                // تحديث بيانات الصنف
                $stmt_update->bind_param("ssss", $unit_price, $cost_price, $sell_unit_price, $itmname);
                if (!$stmt_update->execute()) {
                    throw new Exception('فشل في تحديث بيانات الصنف ' . $itmname);
                }
                
                $itmprice = $unit_price;
                
            } elseif (in_array($pro_tybe, [InvoiceProcessor::INVOICE_TYPES['SALES'], InvoiceProcessor::INVOICE_TYPES['POS'], InvoiceProcessor::INVOICE_TYPES['OFFER']])) {
                // حساب الربح للمبيعات
                $unit_price = $itmprice / $u_val;
                $itmprofit = $itmqty * $u_val * ($unit_price - $oldprice);
                $itmprice = $unit_price;
            }
            
            // إدخال تفاصيل الفاتورة
            $stmt_details->bind_param(
                "sssssssssssssss",
                $pro_tybe, $last_op, $itmname, $u_val, $qty_in, $qty_out,
                $itmprice, $itmdisc, $itmdisc_pct, $det_value, $last_op, $pro_tybe,
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
    if(in_array($pro_tybe, [InvoiceProcessor::INVOICE_TYPES['SALES'], InvoiceProcessor::INVOICE_TYPES['POS'], InvoiceProcessor::INVOICE_TYPES['OFFER']])) {
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
    
    // إغلاق طلب الطاولة وتفريغها (يعمل بعد إنشاء قيود الدفع أعلاه، فلا تتكرر القيود)
    if ($finalize_order && $pro_tybe == InvoiceProcessor::INVOICE_TYPES['POS']) {
        $stmt_fin = $conn->prepare("UPDATE ot_head SET pro_tybe = 2 WHERE id = ?");
        $stmt_fin->bind_param('i', $last_op);
        $stmt_fin->execute();
        $stmt_fin->close();

        if ($table_id > 0) {
            /**
             * الطاولات المدمجة: التحرير يبدأ من الطاولة الرئيسية،
             * وإلا حُرِّرت الطاولة التابعة وحدها وبقيت باقي المجموعة مشغولة بلا فاتورة.
             */
            $primary_tid = $table_id;
            $stmt_par = $conn->prepare("SELECT parent_table_id FROM tables WHERE id = ? LIMIT 1");
            $stmt_par->bind_param('i', $table_id);
            $stmt_par->execute();
            $row_par = $stmt_par->get_result()->fetch_assoc();
            $stmt_par->close();
            if ($row_par && intval($row_par['parent_table_id'] ?? 0) > 0) {
                $primary_tid = intval($row_par['parent_table_id']);
            }

            $group_ids = [$primary_tid];
            $stmt_grp = $conn->prepare(
                "SELECT id FROM tables WHERE parent_table_id = ? AND isdeleted = 0"
            );
            $stmt_grp->bind_param('i', $primary_tid);
            $stmt_grp->execute();
            $res_grp = $stmt_grp->get_result();
            while ($row_grp = $res_grp->fetch_assoc()) {
                $group_ids[] = intval($row_grp['id']);
            }
            $stmt_grp->close();

            $in_list = implode(',', array_map('intval', array_unique($group_ids)));
            $res_rem = $conn->query(
                "SELECT COUNT(*) AS c FROM ot_head
                 WHERE table_id IN ($in_list) AND pro_tybe = 9 AND isdeleted = 0
                   AND id <> " . intval($last_op)
            );
            $remaining_open = $res_rem ? intval($res_rem->fetch_assoc()['c'] ?? 0) : 0;

            if ($remaining_open === 0) {
                $stmt_free = $conn->prepare(
                    "UPDATE tables SET is_merged = 0, parent_table_id = NULL, table_case = 0
                     WHERE id = ? OR parent_table_id = ?"
                );
                $stmt_free->bind_param('ii', $primary_tid, $primary_tid);
                $stmt_free->execute();
                $stmt_free->close();
            }
        }
    }

    // إتمام المعاملة
    error_log('Committing transaction');
    $conn->commit();
    error_log('Transaction committed successfully');
    
    // تحديث كميات الأصناف في جدول myitems بعد المعاملة
    error_log('Updating item quantities in myitems table');
    $update_qty_query = "
        UPDATE myitems mi
        SET itmqty = (
            SELECT COALESCE(SUM(qty_in) - SUM(qty_out), 0)
            FROM fat_details fd
            WHERE fd.item_id = mi.id AND fd.isdeleted = 0
        )
        WHERE mi.id IN (
            SELECT DISTINCT item_id
            FROM fat_details
            WHERE fatid = ? AND isdeleted = 0
        )
    ";
    $stmt_qty = $conn->prepare($update_qty_query);
    if ($stmt_qty) {
        $stmt_qty->bind_param("i", $last_op);
        $stmt_qty->execute();
        $stmt_qty->close();
        error_log('Item quantities updated successfully');
    }
    
    // تسجيل العملية
    $process_types = [
        InvoiceProcessor::INVOICE_TYPES['PURCHASE'] => 'add buy',
        InvoiceProcessor::INVOICE_TYPES['SALES'] => 'add sales',
        InvoiceProcessor::INVOICE_TYPES['POS'] => 'add cash'
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
    if (!empty($ajax_save)) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
        exit;
    }
    die('حدث خطأ أثناء معالجة الفاتورة: ' . $e->getMessage());
}

if (!empty($ajax_save)) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => true,
        'order_id' => $last_op,
        'table_id' => $table_id,
        'pro_id' => $pro_id,
        'finalized' => $finalize_order,
        'pending' => $is_pending_table_order,
        'message' => $finalize_order
            ? 'تم الدفع وإغلاق الطاولة'
            : ($is_pending_table_order ? 'تم الحفظ كطلب معلّق — بلا قيود حتى السداد' : 'تم الحفظ بنجاح'),
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// إعادة التوجيه حسب نوع العملية
error_log('=== REDIRECT START ===');
error_log('Submit value: ' . $submit);
error_log('Submit raw from POST: ' . (isset($_POST['submit']) ? $_POST['submit'] : 'NOT SET'));
error_log('Invoice type (pro_tybe): ' . $pro_tybe);
error_log('Last operation ID: ' . $last_op);
error_log('All POST keys: ' . implode(', ', array_keys($_POST)));

if ($submit == 'print') {
    if (isset($_POST['from_mobile']) && $_POST['from_mobile'] == '1') {
        $_SESSION['pos_back_page'] = '../pos_mobile.php';
    } elseif (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'pos_') !== false) {
        $_SESSION['pos_back_page'] = $_SERVER['HTTP_REFERER'];
    }
    error_log('CONDITION MATCHED: submit == print');
    $redirect_url = "../print/print_sales.php?id=$last_op";
    error_log('Redirecting to: ' . $redirect_url);
    header("Location: $redirect_url");
    error_log('Header sent - this should not appear if redirect works');
    exit;
} elseif ($submit == 'cash') {
    if (isset($_POST['from_mobile']) && $_POST['from_mobile'] == '1') {
        $_SESSION['pos_back_page'] = '../pos_mobile.php';
    } elseif (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'pos_') !== false) {
        $_SESSION['pos_back_page'] = $_SERVER['HTTP_REFERER'];
    }
    error_log('CONDITION MATCHED: submit == cash');
    $redirect_url = "../print/receipt.php?id=$last_op";
    error_log('Redirecting to: ' . $redirect_url);
    
    // التحقق من طلب القفل بعد الحفظ والطباعة
    if (isset($_POST['lock_after_save']) && $_POST['lock_after_save'] == '1') {
        error_log('Lock after save and print requested');
        $_SESSION['lock_after_print'] = true;
    }
    
    header("Location: $redirect_url");
    error_log('Header sent - this should not appear if redirect works');
    exit;
} elseif ($submit == 'save') {
    error_log('Redirecting with save action');
    // For save action, redirect back to POS for POS invoices, or to sales page for others
    if ($pro_tybe == InvoiceProcessor::INVOICE_TYPES['POS']) {
        error_log('Redirecting to POS page');
        
        // التحقق من طلب القفل بعد الحفظ
        if (isset($_POST['lock_after_save']) && $_POST['lock_after_save'] == '1') {
            error_log('Lock after save requested - redirecting to logout');
            header("Location: ../pos_barcode.php?logout=1");
        } else {
            if (isset($_POST['from_mobile']) && $_POST['from_mobile'] == '1') {
                error_log('Redirecting to mobile POS');
                header("Location: ../pos_mobile.php?r=" . time());
            } else {
                error_log('Redirecting to POS barcode page');
                header("Location: ../pos_barcode.php?r=" . time());
            }
        }
    } else {
        $redirects = [
            InvoiceProcessor::INVOICE_TYPES['PURCHASE'] => '../sales.php?q=purchase',  // مشتريات
            InvoiceProcessor::INVOICE_TYPES['SALES'] => '../sales.php?q=sale',      // مبيعات
            InvoiceProcessor::INVOICE_TYPES['PURCHASE_RETURN'] => '../sales.php?q=resale',  // مردود مشتريات
            InvoiceProcessor::INVOICE_TYPES['SALES_RETURN'] => '../sales.php?q=rebuy'       // مردود مبيعات
        ];
        $redirect = $redirects[$pro_tybe] ?? '../sales.php';
        $redirect .= (strpos($redirect, '?') !== false ? '&' : '?') . 'success=1';
        error_log('Redirecting to: ' . $redirect);
        error_log('Header: Location: ' . $redirect);
        header("Location: $redirect");
    }
} else {
    error_log('Redirecting with default action');
    // إعادة توجيه افتراضية حسب نوع الفاتورة
    $redirects = [
        InvoiceProcessor::INVOICE_TYPES['PURCHASE'] => '../sales.php?q=purchase',
        InvoiceProcessor::INVOICE_TYPES['SALES'] => '../sales.php?q=sale',
        InvoiceProcessor::INVOICE_TYPES['POS'] => (isset($_POST['from_mobile']) && $_POST['from_mobile'] == '1') ? '../pos_mobile.php' : '../pos_barcode.php',
        InvoiceProcessor::INVOICE_TYPES['PURCHASE_RETURN'] => '../sales.php?q=resale',
        InvoiceProcessor::INVOICE_TYPES['SALES_RETURN'] => '../sales.php?q=rebuy'
    ];
    
    $redirect = $redirects[$pro_tybe] ?? '../sales.php';
    $redirect .= (strpos($redirect, '?') !== false ? '&' : '?') . 'success=1';
    error_log('Redirecting to default: ' . $redirect);
    error_log('Header: Location: ' . $redirect);
    header("Location: $redirect");
}
error_log('Exiting script');
exit;
?>
