<?php
session_start();
include('../includes/connect.php');
require_once __DIR__ . '/../classes/InvoiceProcessor.php';

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
UserDefaults::enforceInvoice($conn, $_POST, $pro_tybe);
$store_id = isset($_POST['store_id']) ? intval($_POST['store_id']) : 0;
$pro_date = isset($_POST['pro_date']) && !empty($_POST['pro_date']) ? $_POST['pro_date'] : date('Y-m-d');
$accural_date = isset($_POST['accural_date']) && !empty($_POST['accural_date']) ? $_POST['accural_date'] : date('Y-m-d');
$acc2_id = isset($_POST['acc2_id']) ? intval($_POST['acc2_id']) : 0;
$emp_id = isset($_POST['emp_id']) ? intval($_POST['emp_id']) : 0;
$headtotal = isset($_POST['headtotal']) ? floatval($_POST['headtotal']) : 0;
$headdisc = isset($_POST['headdisc']) ? floatval($_POST['headdisc']) : 0;
$headnet = isset($_POST['headnet']) ? floatval($_POST['headnet']) : 0;
$fund_id = isset($_POST['fund_id']) ? intval($_POST['fund_id']) : 0;
$info = isset($_POST['info']) ? trim($_POST['info']) : '';
$submit = isset($_POST['submit']) && !empty($_POST['submit']) ? $_POST['submit'] : 'save';
$paid = isset($_POST['paid']) ? floatval($_POST['paid']) : 0;

// دفع مقسّم (كاش / شبكة / مختلط) — نفس منطق pos_barcode
$pay_method = isset($_POST['pay_method']) ? trim((string) $_POST['pay_method']) : 'cash';
$paid_cash = isset($_POST['paid_cash']) ? floatval($_POST['paid_cash']) : 0;
$paid_bank = isset($_POST['paid_bank']) ? floatval($_POST['paid_bank']) : 0;
$payment_fund_id = isset($_POST['payment_fund_id']) ? intval($_POST['payment_fund_id']) : 0;
$payment_bank_id = isset($_POST['payment_bank_id']) ? intval($_POST['payment_bank_id']) : 0;

if ($pay_method === 'cash') {
    $paid_bank = 0;
    $payment_bank_id = 0;
} elseif ($pay_method === 'bank') {
    $paid_cash = 0;
    $payment_fund_id = 0;
}

if ($paid_cash == 0 && $paid_bank == 0) {
    $paid_cash = $paid;
    if ($payment_fund_id <= 0) {
        $payment_fund_id = $fund_id;
    }
}
if ($payment_fund_id <= 0 && $pay_method !== 'bank') {
    $payment_fund_id = $fund_id;
}
$paid = $paid_cash + $paid_bank;

// للصندوق المحاسبي على رأس الفاتورة: الصندوق إن وُجد، وإلا البنك
if ($fund_id <= 0) {
    $fund_id = $payment_fund_id > 0 ? $payment_fund_id : $payment_bank_id;
}

// إضافة نوع الطلب
$order_type = isset($_POST['age']) ? intval($_POST['age']) : 1;
$order_types = [1 => 'بيع مباشر', 2 => 'حجز', 3 => 'توصيل', 4 => 'مردود مبيعات'];
$order_type_text = $order_types[$order_type] ?? 'بيع مباشر';
$is_return = ($order_type == 4);
if ($is_return) {
    // مردود مبيعات (11) — مش مردود مشتريات (10)
    $pro_tybe = InvoiceProcessor::INVOICE_TYPES['SALES_RETURN'];
}
$info = empty($info) ? "نوع الطلب: $order_type_text" : "$info - نوع الطلب: $order_type_text";

// التحقق من البيانات
$has_payment_account = ($payment_fund_id > 0 && $paid_cash > 0) || ($payment_bank_id > 0 && $paid_bank > 0) || $fund_id > 0;
if (!$store_id || !$acc2_id || !$emp_id || !$has_payment_account) {
    echo '<pre>';
    echo "store_id: " . ($store_id ?: 'MISSING') . "\n";
    echo "acc2_id: " . ($acc2_id ?: 'MISSING') . "\n";
    echo "emp_id: " . ($emp_id ?: 'MISSING') . "\n";
    echo "fund_id: " . ($fund_id ?: 'MISSING') . "\n";
    echo "payment_fund_id: " . ($payment_fund_id ?: '0') . "\n";
    echo "payment_bank_id: " . ($payment_bank_id ?: '0') . "\n";
    echo "\nPOST Data:\n";
    print_r($_POST);
    echo '</pre>';
    die('خطأ: بيانات مطلوبة مفقودة');
}
if ($paid_cash > 0 && $payment_fund_id <= 0) {
    die('خطأ: يجب اختيار الصندوق عند الدفع كاش');
}
if ($paid_bank > 0 && $payment_bank_id <= 0) {
    die('خطأ: يجب اختيار البنك عند الدفع شبكة');
}

if (!isset($_POST['itmname']) || !is_array($_POST['itmname']) || empty(array_filter($_POST['itmname']))) {
    die('خطأ: يجب إضافة صنف واحد على الأقل');
}

try {
    $conn->begin_transaction();
    
    // إضافة حماية ضد تكرار الفاتورة (النقر المزدوج)
    $stmt_check = $conn->prepare("
        SELECT id, TIME_TO_SEC(TIMEDIFF(NOW(), crtime)) as time_diff 
        FROM ot_head 
        WHERE user = ? AND pro_tybe = ? AND fat_net = ?
          AND (acc1 = ? OR acc2 = ?)
        ORDER BY id DESC LIMIT 1
    ");
    $stmt_check->bind_param("iidii", $usid, $pro_tybe, $headnet, $acc2_id, $acc2_id);
    $stmt_check->execute();
    $res_check = $stmt_check->get_result();
    if ($row_check = $res_check->fetch_assoc()) {
        if (isset($row_check['time_diff']) && $row_check['time_diff'] !== null && $row_check['time_diff'] < 15) {
            $conn->rollback();
            $_SESSION['success_message'] = 'تم حفظ الفاتورة بنجاح (تم منع التكرار)';
            if ($submit == 'cash') {
                if (!function_exists('kody_receipt_design_load')) {
                    require_once __DIR__ . '/../includes/receipt_design.php';
                }
                $cashDesignDup = kody_receipt_design_load($rowstg ?? []);
                if (!empty($cashDesignDup['direct_print'])) {
                    header('Location: ../pos_clothes.php?print_id=' . (int) $row_check['id'] . '&r=' . time());
                } else {
                    header('Location: ../print/choose.php?id=' . (int) $row_check['id']);
                }
            } else {
                $stmt_sett = $conn->prepare("SELECT pos_type FROM settings LIMIT 1");
                $stmt_sett->execute();
                $settings = $stmt_sett->get_result()->fetch_assoc();
                $pos_page = (isset($settings['pos_type']) && $settings['pos_type'] === 'clothes') ? '../pos_clothes.php' : '../pos_barcode.php';
                header("Location: $pos_page?r=" . time());
            }
            exit;
        }
    }
    $stmt_check->close();

    // الحصول على رقم الفاتورة التالي
    $stmt = $conn->prepare("SELECT MAX(CAST(pro_id AS UNSIGNED)) as max_id FROM ot_head WHERE pro_tybe = ? FOR UPDATE");
    $stmt->bind_param("i", $pro_tybe);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $pro_id = $row && $row['max_id'] ? ($row['max_id'] + 1) : 1;
    $stmt->close();
    
    // إدخال رأس الفاتورة مع قيد محاسبي (is_journal = 1)
    $stmt = $conn->prepare("
        INSERT INTO ot_head (
            pro_id, pro_tybe, is_stock, is_journal, journal_tybe, info, pro_date, 
            accural_date, pro_serial, store_id, emp_id, emp2_id, acc1, acc2, 
            pro_value, fat_total, fat_disc, fat_plus, fat_net, user
        ) VALUES (
            ?, ?, 1, 1, ?, ?, ?, ?, '0', ?, ?, ?, ?, ?, ?, ?, ?, 0, ?, ?
        )
    ");

    // كاشير: acc1=صندوق / acc2=عميل — المخزن في store_id فقط (حتى في المردود)
    $accounts = InvoiceProcessor::getAccountingAccounts(
        InvoiceProcessor::INVOICE_TYPES['POS'],
        $store_id,
        $acc2_id,
        $fund_id
    );
    $acc1_val = (int) $accounts['acc1'];
    $acc2_val = (int) $accounts['acc2'];

    $stmt->bind_param(
        "iiissssiiiiidddi",
        $pro_id, $pro_tybe, $pro_tybe, $info, $pro_date, $accural_date,
        $store_id, $emp_id, $emp_id, $acc1_val, $acc2_val, $headtotal,
        $headtotal, $headdisc, $headnet, $usid
    );
    
    if (!$stmt->execute()) {
        throw new Exception('فشل في إدخال الفاتورة: ' . $stmt->error);
    }
    
    $last_op = $conn->insert_id;
    $stmt->close();
    $price_list = isset($_POST['price_list']) ? max(1, intval($_POST['price_list'])) : 1;
    $stmtList = $conn->prepare('UPDATE ot_head SET price_list = ? WHERE id = ?');
    if ($stmtList) {
        $stmtList->bind_param('ii', $price_list, $last_op);
        $stmtList->execute();
        $stmtList->close();
    }
    
    // إدخال تفاصيل الفاتورة
    $stmt_details = $conn->prepare("
        INSERT INTO fat_details (
            pro_tybe, pro_id, item_id, u_val, qty_in, qty_out, price, 
            discount, det_value, fatid, fat_tybe, det_store, cost_price
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt_cost = $conn->prepare("SELECT cost_price FROM myitems WHERE id = ?");
    
    foreach ($_POST['itmname'] as $index => $itmname) {
        if (empty($itmname)) continue;
        
        $itmname = intval($itmname);
        $itmqty = floatval($_POST['itmqty'][$index]);
        $itmprice = floatval($_POST['itmprice'][$index]);
        $itmdisc = floatval($_POST['itmdisc'][$index]);
        $u_val = floatval($_POST['u_val'][$index]);
        $det_value = $itmqty * ($itmprice - $itmdisc);
        $qty_in = $is_return ? ($itmqty * $u_val) : 0;
        $qty_out = $is_return ? 0 : ($itmqty * $u_val);
        $stmt_cost->bind_param("i", $itmname);
        $stmt_cost->execute();
        $cost_price = (float) ($stmt_cost->get_result()->fetch_assoc()['cost_price'] ?? 0);
        
        $stmt_details->bind_param(
            "iiiddddddiiid",
            $pro_tybe, $last_op, $itmname, $u_val, $qty_in, $qty_out,
            $itmprice, $itmdisc, $det_value, $last_op, $pro_tybe, $store_id, $cost_price
        );
        
        if (!$stmt_details->execute()) {
            throw new Exception('فشل في إدخال تفاصيل الصنف ' . $itmname);
        }
    }
    
    $stmt_details->close();
    $stmt_cost->close();
    
    // إنشاء القيد المحاسبي للفاتورة
    // الحصول على رقم القيد التالي
    $stmt = $conn->prepare("SELECT MAX(journal_id) as max_id FROM journal_heads");
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $journal_id = $row && $row['max_id'] ? ($row['max_id'] + 1) : 1;
    $stmt->close();
    
    // إدخال رأس القيد
    $stmt = $conn->prepare("
        INSERT INTO journal_heads (journal_id, total, jdate, details, user, op_id) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    
    $journal_details = ($is_return ? "فاتورة مردود مبيعات رقم " : "فاتورة مبيعات POS رقم ") . $pro_id;
    $stmt->bind_param("idssii", $journal_id, $headnet, $pro_date, $journal_details, $usid, $last_op);
    
    if (!$stmt->execute()) {
        throw new Exception('فشل في إدخال رأس القيد: ' . $stmt->error);
    }
    
    $journal_lastid = $conn->insert_id;
    $stmt->close();
    
    // القيد الرئيسي: بيع مدين العميل/دائن المبيعات — مردود العكس (المخزن يتحرك بقيد التكلفة فقط)
    $sales_account = InvoiceProcessor::resolveSalesAccountId($conn);
    if ($sales_account <= 0) {
        throw new Exception('لا يوجد حساب فرعي تحت 3111 (المبيعات) — شغّل تحديثات قاعدة البيانات أولاً');
    }
    $acc1_journal = $is_return ? $sales_account : $acc2_id;
    $acc2_journal = $is_return ? $acc2_id : $sales_account;

    $stmt = $conn->prepare("
        INSERT INTO journal_entries (journal_id, account_id, debit, credit, tybe, op_id) 
        VALUES (?, ?, ?, 0, 0, ?)
    ");
    $stmt->bind_param("iidi", $journal_lastid, $acc1_journal, $headnet, $last_op);
    
    if (!$stmt->execute()) {
        throw new Exception('فشل في إدخال القيد المدين: ' . $stmt->error);
    }
    $stmt->close();
    
    $stmt = $conn->prepare("
        INSERT INTO journal_entries (journal_id, account_id, debit, credit, tybe, op_id) 
        VALUES (?, ?, 0, ?, 1, ?)
    ");
    $stmt->bind_param("iidi", $journal_lastid, $acc2_journal, $headnet, $last_op);
    
    if (!$stmt->execute()) {
        throw new Exception('فشل في إدخال القيد الدائن: ' . $stmt->error);
    }
    $stmt->close();
    
    // سندات الدفع (كاش / شبكة / مختلط)
    InvoiceProcessor::updatePaymentFields(
        $conn,
        (int) $last_op,
        (float) $paid_cash,
        (float) $paid_bank,
        (int) $payment_fund_id,
        (int) $payment_bank_id,
        (float) $headnet
    );

    // بيع = سند قبض | مردود = سند دفع (رد للعميل من الصندوق/البنك)
    $pay_config = InvoiceProcessor::getInvoiceConfig($is_return
        ? InvoiceProcessor::INVOICE_TYPES['SALES_RETURN']
        : InvoiceProcessor::INVOICE_TYPES['POS']
    );
    if ($is_return && $pay_config) {
        $pay_config['paid_type'] = InvoiceProcessor::ACCOUNTING_TYPES['PAYMENT'];
        $pay_config['paid_note'] = 'سند دفع';
    }
    if ($pay_config && ($paid_cash > 0 || $paid_bank > 0)) {
        InvoiceProcessor::createSplitPaymentVouchers(
            $conn,
            (int) $last_op,
            $pay_config,
            (string) $info,
            (string) $pro_date,
            (int) $emp_id,
            (int) $usid,
            (int) $acc2_id,
            (float) $paid_cash,
            (float) $paid_bank,
            (int) $payment_fund_id,
            (int) $payment_bank_id,
            (float) $headnet,
            $pro_id
        );
    }

    InvoiceProcessor::syncCostOfSalesJournal($conn, (int) $last_op);
    
    $conn->commit();
    
    $success_msg_prefix = $is_return ? 'تم حفظ فاتورة المردود' : 'تم حفظ الفاتورة';
    $_SESSION['success_message'] = $success_msg_prefix . ' وإنشاء القيد المحاسبي بنجاح - رقم الفاتورة: ' . $pro_id;
    
} catch (Exception $e) {
    $conn->rollback();
    die('حدث خطأ أثناء معالجة الفاتورة: ' . $e->getMessage());
}

// إعادة التوجيه
if ($submit == 'cash') {
    if (!function_exists('kody_receipt_design_load')) {
        require_once __DIR__ . '/../includes/receipt_design.php';
    }
    $cashDesign = kody_receipt_design_load($rowstg ?? []);
    if (!empty($cashDesign['direct_print'])) {
        header('Location: ../pos_clothes.php?print_id=' . (int) $last_op . '&r=' . time());
        exit;
    }
    header("Location: ../print/choose.php?id=$last_op");
} else {
    // حفظ فقط — رجوع للـ POS
    $stmt = $conn->prepare("SELECT pos_type FROM settings LIMIT 1");
    $stmt->execute();
    $settings = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    $pos_type = $settings['pos_type'] ?? 'barcode';
    $pos_page = ($pos_type === 'clothes') ? '../pos_clothes.php' : '../pos_barcode.php';
    
    header("Location: $pos_page?r=" . time());
}
exit;
?>