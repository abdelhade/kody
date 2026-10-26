<?php
session_start();
if (!isset($_SESSION['login'])) {
    header('Location: ../index.php');
    exit;
}

$id_early = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id_early > 0 && isset($_COOKIE['invoice_print_cashier']) && $_COOKIE['invoice_print_cashier'] === '1') {
    header('Location: receipt.php?id=' . $id_early . '&src=invoice');
    exit;
}

require_once __DIR__ . '/includes/connect.php';
require_once __DIR__ . '/../includes/receipt_design.php';
$design = kody_receipt_design_load($rowstg ?? []);

if (!isset($_GET['id'])) {
    echo '<div style="padding:40px; text-align:center; font-family:Cairo,sans-serif;">لا يوجد فاتورة بهذا الرقم</div>';
    exit;
}

$id = intval($_GET['id']);
$rowfat = $conn->query("SELECT * FROM `ot_head` WHERE id = $id")->fetch_assoc();
if ($rowfat == null) {
    echo '<div style="padding:40px; text-align:center; font-family:Cairo,sans-serif;">لا يوجد فاتورة بهذا الرقم</div>';
    exit;
}

$tybe = (int)($rowfat['pro_tybe'] ?? 0);
$pos_type = $rowstg['pos_type'] ?? 'barcode';
$pos_back = ($pos_type === 'clothes') ? '../pos_clothes.php' : '../pos_barcode.php';
$type_map = [
    3 => '../sales.php?q=sale',
    4 => '../sales.php?q=purchase',
    9 => $pos_back,
    10 => '../sales.php?q=resale',
    11 => '../sales.php?q=rebuy',
    12 => '../sales.php?q=po',
    13 => '../sales.php?q=so',
    14 => '../sales.php?q=offer',
    18 => '../sales.php?q=transfer',
    6 => $pos_back,
];
$default_back = $type_map[$tybe] ?? '../sales.php';

if (!empty($_SESSION['pos_back_page'])) {
    $back_page = $_SESSION['pos_back_page'];
    unset($_SESSION['pos_back_page']);
    if (!in_array($tybe, [6, 9], true) && strpos($back_page, 'pos_') !== false) {
        $back_page = $default_back;
    }
} else {
    $back_page = $default_back;
}

// عنوان نوع المستند
if ($tybe == 4) {
    $doc_title = 'فاتورة مشتريات';
} elseif ($tybe == 3 || $tybe == 9) {
    $doc_title = 'فاتورة مبيعات';
} elseif ($tybe == 10) {
    $doc_title = 'مردود مشتريات';
} elseif ($tybe == 11) {
    $doc_title = 'مردود مبيعات';
} elseif ($tybe == 12) {
    $doc_title = 'أمر شراء';
} elseif ($tybe == 13) {
    $doc_title = 'أمر بيع';
} elseif ($tybe == 14) {
    $doc_title = 'عرض سعر';
} elseif ($tybe == 18) {
    $doc_title = 'إذن تحويل مخزني';
} else {
    $doc_title = 'فاتورة';
}

$is_doc_order = in_array($tybe, [12, 13, 14], true);

// تحديد الطرف (عميل / مورد / مخزن)
if ($tybe == 4 || $tybe == 10) {
    $accid = (int)$rowfat['acc1'];
    if ($tybe == 4) {
        $accid = (int)$rowfat['acc2'];
    }
    $partyLabel = 'المورد';
} elseif ($tybe == 18) {
    $accid = (int)($rowfat['acc2'] ?? 0);
    $partyLabel = 'المخزن المحول إليه';
} else {
    $accid = (int)$rowfat['acc2'];
    $partyLabel = 'العميل';
}

$rowacc1 = null;
if ($accid > 0) {
    $accStmt = $conn->prepare('SELECT aname, phone, address, code FROM acc_head WHERE id = ? LIMIT 1');
    if ($accStmt) {
        $accStmt->bind_param('i', $accid);
        $accStmt->execute();
        $rowacc1 = $accStmt->get_result()->fetch_assoc() ?: null;
        $accStmt->close();
    }
}

$customer_name = $rowacc1['aname'] ?? '';
$customer_phone = $rowacc1['phone'] ?? '';
$customer_address = $rowacc1['address'] ?? '';

// التحويل المخزني: تفاصيل المخازن
$from_store_name = '';
$to_store_name = '';
if ($tybe == 18) {
    $from_st_id = (int)($rowfat['store_id'] ?? 0);
    $to_st_id = (int)($rowfat['acc2'] ?? 0);
    if ($from_st_id > 0) {
        $r_from = $conn->query("SELECT sname FROM mystores WHERE id = $from_st_id")->fetch_assoc();
        $from_store_name = $r_from['sname'] ?? '';
    }
    if ($to_st_id > 0) {
        $r_to = $conn->query("SELECT sname FROM mystores WHERE id = $to_st_id")->fetch_assoc();
        $to_store_name = $r_to['sname'] ?? '';
    }
}

// الكاشير / الموظف
$employee_name = '';
$empid = (int)($rowfat['emp_id'] ?? 0);
if ($empid > 0) {
    $rowemp = $conn->query("SELECT aname FROM acc_head WHERE id = $empid")->fetch_assoc();
    $employee_name = trim((string)($rowemp['aname'] ?? ''));
}

// مندوب التوصيل
$driver_name = '';
$dpid = (int)($rowfat['delivery_person_id'] ?? 0);
if ($dpid <= 0) {
    $emp2id = (int)($rowfat['emp2_id'] ?? 0);
    if ($emp2id > 0 && $emp2id !== $empid) {
        $dpid = $emp2id;
    }
}
if ($dpid > 0) {
    $rowdrv = $conn->query("SELECT aname FROM acc_head WHERE id = $dpid")->fetch_assoc();
    if ($rowdrv) {
        $driver_name = trim((string)$rowdrv['aname']);
    }
}

$info_text = (string)($rowfat['info'] ?? '');
$is_delivery = (strpos($info_text, 'دليفري') !== false) || (($rowfat['order_type'] ?? '') === 'delivery');
$is_table = (strpos($info_text, 'طاولة') !== false || strpos($info_text, 'Table') !== false);
if ($is_delivery) {
    if (preg_match('/العميل:\s*(.+?)(?:\s+-\s+الهاتف:|$)/u', $info_text, $nm) && isset($nm[1])) {
        $customer_name = trim($nm[1]);
    }
    if (preg_match('/الهاتف:\s*(.+?)(?:\s+-\s+العنوان:|$)/u', $info_text, $ph) && isset($ph[1])) {
        $customer_phone = trim($ph[1]);
    }
    if (preg_match('/العنوان:\s*(.+?)(?:\s+-\s+مندوب التوصيل:|\s+-\s+دفع|$)/u', $info_text, $ad) && isset($ad[1])) {
        $customer_address = trim($ad[1]);
    }
    if ($driver_name === '' && preg_match('/مندوب التوصيل:\s*(.+?)(?:\s+-\s+|$)/u', $info_text, $dr) && isset($dr[1])) {
        $driver_name = trim($dr[1]);
    }
} elseif ($driver_name === '' && preg_match('/مندوب التوصيل:\s*(.+?)(?:\s+-\s+|$)/u', $info_text, $dr) && isset($dr[1])) {
    $driver_name = trim($dr[1]);
}

// رصيد الطرف قبل وبعد الفاتورة (من قيود اليومية)
$party_bal_before = null;
$party_bal_after = null;
$show_party_balance = $accid > 0
    && in_array($tybe, [3, 4, 9, 10, 11], true)
    && empty($rowfat['isdeleted']);
if ($show_party_balance) {
    $isSupplierAcc = (strpos((string)($rowacc1['code'] ?? ''), '211') === 0);
    $balSign = $isSupplierAcc ? -1 : 1;

    $balAfter = 0.0;
    $stmtAfter = $conn->prepare(
        'SELECT COALESCE(SUM(debit), 0) - COALESCE(SUM(credit), 0) AS bal
         FROM journal_entries
         WHERE account_id = ? AND isdeleted = 0'
    );
    if ($stmtAfter) {
        $stmtAfter->bind_param('i', $accid);
        $stmtAfter->execute();
        $balAfter = (float)(($stmtAfter->get_result()->fetch_assoc()['bal'] ?? 0));
        $stmtAfter->close();
    }

    $balBefore = $balAfter;
    $stmtBefore = $conn->prepare(
        'SELECT COALESCE(SUM(debit), 0) - COALESCE(SUM(credit), 0) AS bal
         FROM journal_entries
         WHERE account_id = ? AND isdeleted = 0
           AND NOT (COALESCE(op2, 0) = ?
                OR journal_id IN (SELECT id FROM journal_heads WHERE op_id = ? OR op2 = ?))'
    );
    if ($stmtBefore) {
        $stmtBefore->bind_param('iiii', $accid, $id, $id, $id);
        $stmtBefore->execute();
        $balBefore = (float)(($stmtBefore->get_result()->fetch_assoc()['bal'] ?? 0));
        $stmtBefore->close();
    }

    $party_bal_before = round($balBefore * $balSign, 2);
    $party_bal_after = round($balAfter * $balSign, 2);
}

// بيانات اللوجو
$_logo_file = !empty($rowstg['company_logo']) ? $rowstg['company_logo'] : (!empty($rowstg['logo']) ? $rowstg['logo'] : 'logo.jpg');
$logo_path = '../assets/logo/' . $_logo_file;
if (!file_exists($logo_path)) {
    $logo_path = '../assets/logo/logo.jpg';
}
$show_logo = file_exists($logo_path);

// جلب أصناف الفاتورة
$items = [];
$total_qty = 0;
$total_item_disc = 0.0;
$has_any_item_disc = false;

$detWhere = "(fatid = $id OR pro_id = $id) AND isdeleted = 0";
if ((int)$tybe === 18) {
    $detWhere .= ' AND qty_out > 0';
}
$resdet = $conn->query("SELECT * FROM fat_details WHERE $detWhere ORDER BY id");
while ($rowdet = $resdet->fetch_assoc()) {
    $itmid = (int)$rowdet['item_id'];
    $rowitm = $conn->query("SELECT iname, barcode FROM myitems WHERE id = $itmid")->fetch_assoc();
    $u = (float)$rowdet['u_val'];
    if ($u <= 0) {
        $u = 1;
    }
    if ($tybe == 4 || $tybe == 10) {
        $qty = (float)$rowdet['qty_in'] / $u;
    } elseif ($tybe == 3 || $tybe == 9 || $tybe == 11 || $tybe == 18) {
        $qty = (float)$rowdet['qty_out'] / $u;
    } elseif (in_array($tybe, [12, 13, 14], true)) {
        $qty = (float)($rowdet['doc_qty'] ?? 0) / $u;
    } else {
        $qty = 0;
    }
    $total_qty += $qty;

    $unit_name = '';
    $itmunt = $conn->query("SELECT unit_id FROM item_units WHERE item_id = $itmid AND u_val = $u LIMIT 1")->fetch_assoc();
    if ($itmunt && !empty($itmunt['unit_id'])) {
        $unitid = (int)$itmunt['unit_id'];
        $unitRow = $conn->query("SELECT uname FROM myunits WHERE id = $unitid")->fetch_assoc();
        $unit_name = $unitRow['uname'] ?? '';
    }

    $item_disc = (float)($rowdet['discount'] ?? 0);
    if ($item_disc > 0) {
        $has_any_item_disc = true;
        $total_item_disc += $item_disc;
    }

    $items[] = [
        'id' => $itmid,
        'code' => $rowitm['barcode'] ?? $itmid,
        'name' => $rowitm['iname'] ?? 'صنف غير محدد',
        'qty' => $qty,
        'unit' => $unit_name,
        'price' => (float)$rowdet['price'] * $u,
        'discount' => $item_disc,
        'total' => (float)$rowdet['det_value'],
    ];
}

// الحسابات المالية الإجمالية
$fat_total = (float)($rowfat['fat_total'] ?? 0);
$fat_net = (float)($rowfat['fat_net'] ?? 0);
$disc_val = (float)($rowfat['fat_disc'] ?? 0);
$disc_pct_head = !empty($rowfat['fat_disc_per']) ? (float)$rowfat['fat_disc_per'] : (($fat_total > 0 && $disc_val > 0) ? round(($disc_val / $fat_total) * 100, 2) : 0);
$plus_val = (float)($rowfat['fat_plus'] ?? 0);
$plus_pct_head = !empty($rowfat['fat_plus_per']) ? (float)$rowfat['fat_plus_per'] : (($fat_total > 0 && $plus_val > 0) ? round(($plus_val / $fat_total) * 100, 2) : 0);

$paid_res = $conn->query("SELECT SUM(pro_value) AS paid_amount FROM ot_head WHERE (pro_tybe = 1 OR pro_tybe = 2) AND op2 = $id AND isdeleted = 0");
$paid_row = $paid_res ? $paid_res->fetch_assoc() : null;
if ($paid_row && $paid_row['paid_amount'] !== null) {
    $paidval = (float)$paid_row['paid_amount'];
} else {
    if (isset($rowfat['paid_amount']) && (float)$rowfat['paid_amount'] > 0) {
        $paidval = (float)$rowfat['paid_amount'];
    } else {
        $paidval = $fat_net;
    }
}
$remaining = $fat_net - $paidval;

$fmtMoney = static function ($v) {
    $v = (float)$v;
    if (abs($v - round($v)) < 0.0001) {
        return number_format($v, 0);
    }
    return number_format($v, 2);
};

$fmtBal = static function ($v) use ($fmtMoney) {
    if ($v === null) return '0';
    $v = (float)$v;
    $abs = $fmtMoney(abs($v));
    if ($v > 0.001) {
        return $abs . ' (عليه)';
    } elseif ($v < -0.001) {
        return $abs . ' (له)';
    }
    return '0.00';
};

$total_before_disc = $fat_total + $total_item_disc;
if ($disc_val > 0 && empty($total_item_disc) && abs($fat_total - ($fat_net + $disc_val)) < 0.01) {
    $total_before_disc = $fat_total + $disc_val;
}

$company_name = !empty($rowstg['company_name']) ? $rowstg['company_name'] : 'Kody POS';
$company_tel  = !empty($rowstg['company_tel']) ? $rowstg['company_tel'] : '';
$company_add  = !empty($rowstg['company_add']) ? $rowstg['company_add'] : '';
$tax_no = $design['header']['tax_no'] ?? '';
$cr_no = $design['header']['cr_no'] ?? '';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($doc_title, ENT_QUOTES, 'UTF-8') ?> #<?= htmlspecialchars((string)$rowfat['pro_id'], ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap">
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Cairo', Arial, Tahoma, sans-serif;
            background: #f1f5f9;
            margin: 0;
            padding: 24px 12px;
            direction: rtl;
            color: #0f172a;
            font-size: 13.5px;
            line-height: 1.4;
            -webkit-font-smoothing: antialiased;
        }
        .invoice-sheet {
            max-width: 860px;
            margin: 0 auto;
            background: #ffffff;
            padding: 28px 32px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
        }

        /* رأس الفاتورة */
        .inv-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 16px;
            margin-bottom: 16px;
        }
        .inv-brand {
            flex: 1;
            text-align: right;
        }
        .inv-brand .brand-logo img {
            max-width: 140px;
            max-height: 65px;
            height: auto;
            display: inline-block;
            margin-bottom: 6px;
            object-fit: contain;
        }
        .inv-brand .company-title {
            font-size: 20px;
            font-weight: 800;
            color: #0f172a;
            margin: 2px 0;
            line-height: 1.2;
        }
        .inv-brand .company-details {
            font-size: 12px;
            color: #475569;
            margin-top: 4px;
            line-height: 1.6;
        }
        .inv-meta-card {
            flex: 0 0 280px;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 12px 14px;
            text-align: right;
        }
        .inv-meta-card .doc-badge {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1.5px solid #0f172a;
            padding-bottom: 6px;
            margin-bottom: 8px;
        }
        .inv-meta-card .doc-title-text {
            font-size: 15px;
            font-weight: 800;
            color: #0f172a;
        }
        .inv-meta-card .inv-number {
            font-size: 15px;
            font-weight: 800;
            background: #0f172a;
            color: #fff;
            padding: 2px 10px;
            border-radius: 4px;
        }
        .inv-meta-row {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            color: #334155;
            margin: 3px 0;
        }
        .inv-meta-row strong {
            color: #0f172a;
        }
        .order-badge-banner {
            margin-top: 6px;
            padding: 4px 8px;
            background: #e2e8f0;
            border-radius: 4px;
            font-size: 11.5px;
            font-weight: 700;
            text-align: center;
            color: #1e293b;
        }
        .barcode-box {
            margin-top: 8px;
            display: flex;
            justify-content: center;
            border-top: 1px dashed #cbd5e1;
            padding-top: 6px;
        }
        .barcode-box svg {
            display: block;
            max-width: 100%;
        }

        /* بيانات العميل / المورد */
        .party-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-right: 4px solid #0f172a;
            border-radius: 6px;
            padding: 10px 16px;
            margin-bottom: 16px;
        }
        .party-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 8px 24px;
            align-items: center;
            font-size: 13px;
        }
        .party-grid-item {
            display: inline-flex;
            gap: 6px;
        }
        .party-grid-item strong {
            color: #0f172a;
            white-space: nowrap;
        }
        .party-grid-item span {
            color: #334155;
        }

        /* جدول الأصناف */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 14px 0;
            font-size: 13px;
        }
        .items-table th, .items-table td {
            padding: 8px 8px;
            border: 1px solid #cbd5e1;
            text-align: center;
            vertical-align: middle;
        }
        .items-table th {
            background: #f1f5f9;
            color: #0f172a;
            font-weight: 700;
            font-size: 12.5px;
            border-bottom: 2px solid #0f172a;
        }
        .items-table td.item-name {
            text-align: right;
            font-weight: 600;
            color: #0f172a;
        }
        .items-table tr:nth-child(even) td {
            background: #fafafa;
        }
        .items-table tr.summary-row td {
            background: #f8fafc;
            font-weight: 700;
            border-top: 2px solid #0f172a;
            border-bottom: 2px solid #0f172a;
        }

        /* قسم المجاميع والحساب أسفل الفاتورة */
        .invoice-bottom-grid {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
            margin-top: 16px;
            page-break-inside: avoid;
        }
        .summary-box-side {
            width: 380px;
        }
        .terms-box-side {
            flex: 1;
        }
        .financial-table {
            width: 100%;
            border-collapse: collapse;
            background: #ffffff;
            border: 1px solid #0f172a;
            border-radius: 6px;
            overflow: hidden;
        }
        .financial-table td {
            padding: 7px 12px;
            border: 1px solid #e2e8f0;
            font-size: 13px;
        }
        .financial-table td.lbl {
            text-align: right;
            font-weight: 600;
            color: #334155;
            background: #f8fafc;
            width: 55%;
        }
        .financial-table td.val {
            text-align: center;
            font-weight: 700;
            font-family: Arial, Tahoma, sans-serif;
            font-size: 13.5px;
            color: #0f172a;
            width: 45%;
            white-space: nowrap;
        }
        .financial-table tr.net-highlight td {
            background: #0f172a;
            color: #ffffff !important;
            font-weight: 800;
            font-size: 14.5px;
            border-color: #0f172a;
        }
        .financial-table tr.net-highlight td.lbl {
            background: #0f172a;
            color: #ffffff !important;
        }
        .financial-table tr.net-highlight td.val {
            background: #0f172a;
            color: #ffffff !important;
        }

        /* كشف الحساب */
        .balance-table {
            width: 100%;
            border-collapse: collapse;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            margin-bottom: 12px;
            border-radius: 6px;
            overflow: hidden;
        }
        .balance-table th {
            background: #f1f5f9;
            color: #0f172a;
            font-size: 12.5px;
            font-weight: 700;
            padding: 6px 10px;
            text-align: center;
            border: 1px solid #cbd5e1;
            border-bottom: 2px solid #0f172a;
        }
        .balance-table td {
            padding: 6px 10px;
            border: 1px solid #e2e8f0;
            font-size: 12.5px;
        }
        .balance-table td.lbl {
            text-align: right;
            font-weight: 600;
            color: #475569;
            background: #f8fafc;
            width: 55%;
        }
        .balance-table td.val {
            text-align: center;
            font-weight: 700;
            font-family: Arial, Tahoma, sans-serif;
            color: #0f172a;
            width: 45%;
            white-space: nowrap;
        }
        .balance-table tr.balance-net td {
            font-weight: 800;
            background: #f1f5f9;
            border-top: 1.5px solid #0f172a;
        }

        /* الملاحظات والشروط */
        .invoice-terms-box {
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 6px;
            padding: 10px 14px;
            font-size: 11.5px;
            color: #475569;
            line-height: 1.6;
        }
        .invoice-terms-box strong {
            color: #0f172a;
            display: block;
            margin-bottom: 3px;
            font-size: 12px;
        }

        /* توقيعات أسفل الفاتورة */
        .signatures-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 24px;
            padding-top: 14px;
            border-top: 1px dashed #cbd5e1;
            font-size: 12.5px;
            font-weight: 700;
            color: #334155;
            page-break-inside: avoid;
        }

        /* تذييل الصفحة */
        .invoice-footer-banner {
            text-align: center;
            margin-top: 14px;
            padding-top: 8px;
            font-size: 11px;
            color: #64748b;
            font-weight: 600;
            page-break-inside: avoid;
        }

        /* شريط أزرار التحكم (يختفي في الطباعة) */
        .actions-bar {
            max-width: 860px;
            margin: 16px auto 0;
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }
        .actions-bar button, .actions-bar a.back-btn {
            background: #0f172a;
            color: #ffffff;
            border: none;
            padding: 9px 22px;
            font-size: 14px;
            font-weight: 700;
            font-family: inherit;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: background 0.15s ease;
        }
        .actions-bar button:hover {
            background: #1e293b;
        }
        .actions-bar a.back-btn {
            background: #64748b;
        }
        .actions-bar a.back-btn:hover {
            background: #475569;
        }
        .actions-bar label.cashier-toggle {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin: 0;
            cursor: pointer;
            font-weight: 700;
            color: #334155;
            background: #ffffff;
            padding: 8px 14px;
            border-radius: 6px;
            border: 1px solid #cbd5e1;
            user-select: none;
        }

        /* تنسيق الطباعة الفعلي A4 */
        @page {
            size: A4 portrait;
            margin: 12mm 12mm 12mm 12mm;
        }
        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
                color: #000000 !important;
                font-size: 12px !important;
            }
            .invoice-sheet {
                max-width: 100% !important;
                width: 100% !important;
                box-shadow: none !important;
                border: none !important;
                border-radius: 0 !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .items-table th {
                background: #f1f5f9 !important;
                color: #000000 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .financial-table tr.net-highlight td {
                background: #0f172a !important;
                color: #ffffff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .financial-table td, .financial-table th,
            .items-table td, .items-table th,
            .balance-table td, .balance-table th {
                border-color: #94a3b8 !important;
            }
        }
    </style>
</head>
<body>

<div class="invoice-sheet" id="printed">
    <!-- رأس الفاتورة -->
    <div class="inv-header">
        <div class="inv-brand">
            <?php if ($show_logo): ?>
            <div class="brand-logo"><img src="<?= htmlspecialchars($logo_path, ENT_QUOTES, 'UTF-8') ?>" alt=""></div>
            <?php endif; ?>
            <div class="company-title"><?= htmlspecialchars($company_name, ENT_QUOTES, 'UTF-8') ?></div>
            <div class="company-details">
                <?php if ($company_tel !== ''): ?>
                <div><strong>الهاتف:</strong> <?= htmlspecialchars($company_tel, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
                <?php if ($company_add !== ''): ?>
                <div><strong>العنوان:</strong> <?= htmlspecialchars($company_add, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
                <?php if ($tax_no !== ''): ?>
                <div><strong>الرقم الضريبي:</strong> <?= htmlspecialchars($tax_no, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
                <?php if ($cr_no !== ''): ?>
                <div><strong>السجل التجاري:</strong> <?= htmlspecialchars($cr_no, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="inv-meta-card">
            <div class="doc-badge">
                <span class="doc-title-text"><?= htmlspecialchars($doc_title, ENT_QUOTES, 'UTF-8') ?></span>
                <span class="inv-number">#<?= htmlspecialchars((string)$rowfat['pro_id'], ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <?php
            $serial = trim((string)($rowfat['pro_serial'] ?? ''));
            if ($serial !== '' && $serial !== (string)$rowfat['pro_id']):
            ?>
            <div class="inv-meta-row">
                <span>الرقم التسلسلي:</span>
                <strong><?= htmlspecialchars($serial, ENT_QUOTES, 'UTF-8') ?></strong>
            </div>
            <?php endif; ?>
            <div class="inv-meta-row">
                <span>التاريخ:</span>
                <strong><?= htmlspecialchars((string)$rowfat['pro_date'], ENT_QUOTES, 'UTF-8') ?></strong>
            </div>
            <?php if (!empty($rowfat['crtime'])): ?>
            <div class="inv-meta-row">
                <span>الوقت:</span>
                <span><?= htmlspecialchars(substr((string)$rowfat['crtime'], 11, 8) ?: (string)$rowfat['crtime'], ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <?php endif; ?>
            <?php if ($employee_name !== ''): ?>
            <div class="inv-meta-row">
                <span>المستخدم / الكاشير:</span>
                <strong><?= htmlspecialchars($employee_name, ENT_QUOTES, 'UTF-8') ?></strong>
            </div>
            <?php endif; ?>
            <?php if (!empty($rowfat['accural_date']) && $rowfat['accural_date'] !== '0000-00-00'): ?>
            <div class="inv-meta-row">
                <span>تاريخ الاستحقاق:</span>
                <strong><?= htmlspecialchars((string)$rowfat['accural_date'], ENT_QUOTES, 'UTF-8') ?></strong>
            </div>
            <?php endif; ?>

            <?php if ($is_delivery): ?>
            <div class="order-badge-banner">طلب توصيل (دليفري)<?= $driver_name !== '' ? ' — مندوب: ' . htmlspecialchars($driver_name, ENT_QUOTES, 'UTF-8') : '' ?></div>
            <?php elseif ($is_table): ?>
            <div class="order-badge-banner">طلب طاولة (صالة)</div>
            <?php endif; ?>

            <div class="barcode-box">
                <svg id="invoiceBarcodeSvg"></svg>
            </div>
        </div>
    </div>

    <!-- بيانات الطرف (العميل أو المورد أو المخازن) -->
    <?php if ($tybe == 18): ?>
    <div class="party-card">
        <div class="party-grid">
            <div class="party-grid-item">
                <strong>من مخزن:</strong>
                <span><?= htmlspecialchars($from_store_name ?: 'المخزن الرئيسي', ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <div class="party-grid-item">
                <strong>إلى مخزن:</strong>
                <span><?= htmlspecialchars($to_store_name ?: 'المخزن المحول إليه', ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <?php if (!empty($rowfat['info'])): ?>
            <div class="party-grid-item">
                <strong>البيان:</strong>
                <span><?= htmlspecialchars((string)$rowfat['info'], ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php elseif ($customer_name !== '' || $customer_phone !== '' || $customer_address !== ''): ?>
    <div class="party-card">
        <div class="party-grid">
            <?php if ($customer_name !== ''): ?>
            <div class="party-grid-item">
                <strong>بيانات <?= htmlspecialchars($partyLabel, ENT_QUOTES, 'UTF-8') ?>:</strong>
                <span><?= htmlspecialchars($customer_name, ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <?php endif; ?>
            <?php if ($customer_phone !== ''): ?>
            <div class="party-grid-item">
                <strong>الهاتف:</strong>
                <span><?= htmlspecialchars($customer_phone, ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <?php endif; ?>
            <?php if ($customer_address !== ''): ?>
            <div class="party-grid-item">
                <strong>العنوان:</strong>
                <span><?= htmlspecialchars($customer_address, ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <?php endif; ?>
            <?php if ($driver_name !== '' && !$is_delivery): ?>
            <div class="party-grid-item">
                <strong>مندوب التوصيل:</strong>
                <span><?= htmlspecialchars($driver_name, ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- جدول أصناف الفاتورة -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 15%;">كود الصنف</th>
                <th class="item-name" style="width: <?= $has_any_item_disc ? '35%' : '43%' ?>;">اسم الصنف</th>
                <th style="width: 10%;">الكمية</th>
                <th style="width: 9%;">الوحدة</th>
                <th style="width: 11%;">السعر</th>
                <?php if ($has_any_item_disc): ?>
                <th style="width: 8%;">الخصم</th>
                <?php endif; ?>
                <th style="width: 14%;">الإجمالي</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $idx = 0;
            foreach ($items as $itm):
                $idx++;
            ?>
            <tr>
                <td><?= $idx ?></td>
                <td><?= htmlspecialchars((string)$itm['code'], ENT_QUOTES, 'UTF-8') ?></td>
                <td class="item-name"><?= htmlspecialchars((string)$itm['name'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= number_format($itm['qty'], 2) ?></td>
                <td><?= htmlspecialchars((string)$itm['unit'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= number_format($itm['price'], 2) ?></td>
                <?php if ($has_any_item_disc): ?>
                <td style="color: <?= $itm['discount'] > 0 ? '#b91c1c' : 'inherit' ?>;">
                    <?= $itm['discount'] > 0 ? number_format($itm['discount'], 2) : '-' ?>
                </td>
                <?php endif; ?>
                <td><strong><?= number_format($itm['total'], 2) ?></strong></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($items)): ?>
            <tr>
                <td colspan="<?= $has_any_item_disc ? 8 : 7 ?>" style="padding: 16px; color:#64748b;">لا توجد أصناف مسجلة في هذه الفاتورة</td>
            </tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr class="summary-row">
                <td colspan="3" style="text-align: right; padding-right: 14px;">
                    إجمالي الكميات (عدد الأصناف: <?= count($items) ?>)
                </td>
                <td><?= number_format($total_qty, 2) ?></td>
                <td colspan="<?= $has_any_item_disc ? 4 : 3 ?>"></td>
            </tr>
        </tfoot>
    </table>

    <!-- أسفل الفاتورة: المجاميع وكشف الحساب والشروط -->
    <div class="invoice-bottom-grid">
        <!-- قسم الشروط وكشف الحساب (اليمين) -->
        <div class="terms-box-side">
            <?php if ($show_party_balance && $party_bal_before !== null && $party_bal_after !== null): ?>
            <table class="balance-table">
                <thead>
                    <tr>
                        <th colspan="2">كشف حساب <?= htmlspecialchars($partyLabel, ENT_QUOTES, 'UTF-8') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="lbl">الرصيد السابق للفاتورة:</td>
                        <td class="val"><?= htmlspecialchars($fmtBal($party_bal_before), ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                    <tr class="balance-net">
                        <td class="lbl">إجمالي الرصيد الحالي بعد الفاتورة:</td>
                        <td class="val"><?= htmlspecialchars($fmtBal($party_bal_after), ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                </tbody>
            </table>
            <?php endif; ?>

            <div class="invoice-terms-box">
                <strong>ملاحظات وسياسة الفاتورة:</strong>
                <div>• البضاعة المباعة ترد وتستبدل خلال 14 يوماً مع إحضار أصل الفاتورة.</div>
                <div>• الرصيد المذكور في الفاتورة يعتبر صحيحاً ومعتمداً ما لم يتم الاعتراض عليه كتابياً خلال 15 يوماً.</div>
                <?php if (!empty($design['footer']['notes_text'])): ?>
                <div style="margin-top: 4px;"><?= nl2br(htmlspecialchars($design['footer']['notes_text'], ENT_QUOTES, 'UTF-8')) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- جدول المجاميع المالية (اليسار) -->
        <div class="summary-box-side">
            <table class="financial-table">
                <tbody>
                    <tr>
                        <td class="lbl">إجمالي الأصناف:</td>
                        <td class="val"><?= htmlspecialchars($fmtMoney($total_before_disc), ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                    <?php if ($total_item_disc > 0): ?>
                    <tr>
                        <td class="lbl">إجمالي خصومات الأصناف:</td>
                        <td class="val" style="color: #b91c1c;">- <?= htmlspecialchars($fmtMoney($total_item_disc), ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if ($disc_val > 0): ?>
                    <tr>
                        <td class="lbl">خصم الفاتورة <?= $disc_pct_head > 0 ? '(' . $disc_pct_head . '%)' : '' ?>:</td>
                        <td class="val" style="color: #b91c1c;">- <?= htmlspecialchars($fmtMoney($disc_val), ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if ($plus_val > 0): ?>
                    <tr>
                        <td class="lbl">الإضافي <?= $plus_pct_head > 0 ? '(' . $plus_pct_head . '%)' : '' ?>:</td>
                        <td class="val" style="color: #15803d;">+ <?= htmlspecialchars($fmtMoney($plus_val), ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr class="net-highlight">
                        <td class="lbl">صافي القيمة المطلوب:</td>
                        <td class="val"><?= htmlspecialchars($fmtMoney($fat_net), ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                    <?php if (!$is_doc_order): ?>
                    <tr>
                        <td class="lbl">المدفوع من الفاتورة:</td>
                        <td class="val" style="color: #15803d;"><?= htmlspecialchars($fmtMoney($paidval), ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                    <?php if (abs($remaining) > 0.001): ?>
                    <tr>
                        <td class="lbl"><?= $remaining < 0 ? 'المتبقي للعميل (باقي):' : 'المتبقي على العميل (آجل):' ?></td>
                        <td class="val" style="color: <?= $remaining > 0 ? '#b91c1c' : '#15803d' ?>;">
                            <?= htmlspecialchars($fmtMoney(abs($remaining)), ENT_QUOTES, 'UTF-8') ?>
                        </td>
                    </tr>
                    <?php endif; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- التوقيعات -->
    <div class="signatures-section">
        <div>توقيع المستلم: ....................................................</div>
        <div>توقيع المسؤول / المحاسب: ....................................................</div>
    </div>

    <!-- شكر / فوتر أخير -->
    <?php if (!empty($design['footer']['footer_text'])): ?>
    <div class="invoice-footer-banner">
        <?= nl2br(htmlspecialchars($design['footer']['footer_text'], ENT_QUOTES, 'UTF-8')) ?>
    </div>
    <?php endif; ?>
</div>

<!-- شريط الأزرار للتحكم والشاشات (لا يظهر عند الطباعة) -->
<div class="actions-bar no-print">
    <button type="button" id="printButton">
        طباعة الفاتورة
    </button>
    <a href="<?= htmlspecialchars($back_page, ENT_QUOTES, 'UTF-8') ?>" id="back" class="back-btn">
        عودة
    </a>
    <label for="printCashierCheck" class="cashier-toggle">
        <input type="checkbox" id="printCashierCheck">
        طباعة كاشير (بون حراري)
    </label>
</div>

<script src="code.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. إنشاء باركود الفاتورة
    if (window.JsBarcode) {
        try {
            window.JsBarcode('#invoiceBarcodeSvg', <?= json_encode((string)$rowfat['pro_id']) ?>, {
                format: 'CODE128',
                height: 30,
                width: 1.5,
                displayValue: false,
                margin: 0
            });
        } catch (e) {}
    }

    // 2. زر أمر الطباعة
    var printButton = document.getElementById('printButton');
    if (printButton) {
        printButton.addEventListener('click', function() {
            window.print();
        });
    }

    // 3. التحويل لبون الكاشير عند تحديد الخيار
    var cashierCheck = document.getElementById('printCashierCheck');
    if (cashierCheck) {
        cashierCheck.addEventListener('change', function() {
            if (!cashierCheck.checked) return;
            var exp = new Date();
            exp.setFullYear(exp.getFullYear() + 1);
            document.cookie = 'invoice_print_cashier=1; expires=' + exp.toUTCString() + '; path=/; SameSite=Lax';
            var params = new URLSearchParams(window.location.search);
            var id = params.get('id');
            if (id) {
                window.location.href = 'receipt.php?id=' + encodeURIComponent(id) + '&src=invoice';
            }
        });
    }

    // 4. وظيفة الرجوع
    function doGoBack() {
        if (window.opener) {
            window.close();
            return;
        }
        if (window.history.length > 1) {
            window.history.back();
            return;
        }
        var backBtn = document.getElementById('back');
        if (backBtn && backBtn.getAttribute('href')) {
            window.location.href = backBtn.getAttribute('href');
        }
    }

    var backBtn = document.getElementById('back');
    if (backBtn) {
        backBtn.addEventListener('click', function(e) {
            e.preventDefault();
            doGoBack();
        });
    }

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            doGoBack();
        }
    });

    // 5. فتح أمر الطباعة تلقائياً بمجرد تحميل الصفحة
    setTimeout(function() {
        window.print();
    }, 350);

    // ملاحظة هامة: تم إلغاء ربط afterprint بـ doGoBack()
    // حتى إذا قام المستخدم بالضغط على Cancel، تُغلق نافذة الطباعة فقط وتبقى صفحة الفاتورة مفتوحة أمامه.
});
</script>
</body>
</html>
