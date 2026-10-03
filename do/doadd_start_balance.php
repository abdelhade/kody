<?php
include('../includes/connect.php');
session_start();
$user = isset($_SESSION['userid']) ? $_SESSION['userid'] : 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['acc_id']) && isset($_POST['newbalance'])) {
        $acc_ids = $_POST['acc_id'];
        $new_balances = $_POST['newbalance'];

        // الحصول على أول تاريخ في النظام
        $res_date = $conn->query("SELECT MIN(jdate) AS first_date FROM journal_heads WHERE isdeleted = 0 AND details != 'قيد الأرصدة الافتتاحية'");
        $row_date = $res_date->fetch_assoc();
        $first_date = !empty($row_date['first_date']) ? $row_date['first_date'] : date('Y-m-d');

        // 1. البحث عن القيد المجمع للأرصدة الافتتاحية أو إنشاؤه
        $j_name = "قيد الأرصدة الافتتاحية";
        $stmt_check = $conn->prepare("SELECT id FROM journal_heads WHERE details = ? AND isdeleted = 0 LIMIT 1");
        $stmt_check->bind_param("s", $j_name);
        $stmt_check->execute();
        $res = $stmt_check->get_result();
        
        if ($res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $journal_head_id = $row['id'];
            
            // تحديث التاريخ ليكون أول تاريخ في النظام دائماً
            $stmt_update_date = $conn->prepare("UPDATE journal_heads SET jdate = ? WHERE id = ?");
            $stmt_update_date->bind_param("si", $first_date, $journal_head_id);
            $stmt_update_date->execute();
            $stmt_update_date->close();
        } else {
            $stmt_insert_jh = $conn->prepare("INSERT INTO journal_heads (journal_id, total, details, user, jdate) VALUES ('0', 0, ?, ?, ?)");
            $stmt_insert_jh->bind_param("sis", $j_name, $user, $first_date);
            $stmt_insert_jh->execute();
            $journal_head_id = $conn->insert_id;
            $stmt_insert_jh->close();
        }
        $stmt_check->close();

        // 2. تحديث الحسابات وقيود اليومية
        $stmt_acc = $conn->prepare("UPDATE acc_head SET balance = ? WHERE id = ?");
        $stmt_check_je = $conn->prepare("SELECT id FROM journal_entries WHERE journal_id = ? AND account_id = ? AND isdeleted = 0 LIMIT 1");
        $stmt_update_je = $conn->prepare("UPDATE journal_entries SET debit = ?, credit = ?, tybe = ? WHERE id = ?");
        $stmt_insert_je = $conn->prepare("INSERT INTO journal_entries (journal_id, account_id, debit, credit, tybe, info) VALUES (?, ?, ?, ?, ?, ?)");
        
        $info = "رصيد افتتاحي";

        for ($i = 0; $i < count($acc_ids); $i++) {
            $id = (int)$acc_ids[$i];
            
            if (isset($new_balances[$i])) {
                $balance = (float)$new_balances[$i];
                
                // تحديث جدول الحسابات
                $stmt_acc->bind_param("di", $balance, $id);
                $stmt_acc->execute();
                
                // تحديد المدين والدائن
                $debit = $balance > 0 ? $balance : 0;
                $credit = $balance < 0 ? abs($balance) : 0;
                $tybe = $balance >= 0 ? 0 : 1;
                
                // البحث في قيود اليومية
                $stmt_check_je->bind_param("ii", $journal_head_id, $id);
                $stmt_check_je->execute();
                $res_je = $stmt_check_je->get_result();
                
                if ($res_je->num_rows > 0) {
                    // تحديث القيد الموجود
                    $row_je = $res_je->fetch_assoc();
                    $je_id = $row_je['id'];
                    $stmt_update_je->bind_param("ddii", $debit, $credit, $tybe, $je_id);
                    $stmt_update_je->execute();
                } else {
                    // إنشاء قيد جديد (فقط إذا كان الرصيد لا يساوي صفر لعدم تكديس الداتابيز بقيود فارغة)
                    if ($balance != 0) {
                        $stmt_insert_je->bind_param("iiddis", $journal_head_id, $id, $debit, $credit, $tybe, $info);
                        $stmt_insert_je->execute();
                    }
                }
            }
        }
        
        $stmt_acc->close();
        $stmt_check_je->close();
        $stmt_update_je->close();
        $stmt_insert_je->close();
        
        // تسجيل العملية
        $process_stmt = $conn->prepare("INSERT INTO `process`(`type`) VALUES (?)");
        $process_type = "تحديث الأرصدة الافتتاحية";
        $process_stmt->bind_param("s", $process_type);
        $process_stmt->execute();
        $process_stmt->close();
    }
    
    header("Location: ../start_balance.php");
    exit();
}

