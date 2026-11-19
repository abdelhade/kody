<?php
session_start();
include('../includes/connect.php');
    $usid = $_SESSION['userid'];

// استرجاع بيانات الإدخال وتنظيفها
$journal_id = $_POST["journal_id"] ?? '';
$jdate = $_POST["jdate"] ?? '';
$details = $_POST["details"] ?? '';
$total = $_POST["total"] ?? 0;
$depitVals = (isset($_POST['depitval']) && is_array($_POST['depitval'])) ? $_POST['depitval'] : [];
$depitNames = (isset($_POST['depitname']) && is_array($_POST['depitname'])) ? $_POST['depitname'] : [];
$creditVals = (isset($_POST['creditval']) && is_array($_POST['creditval'])) ? $_POST['creditval'] : [];
$creditNames = (isset($_POST['creditname']) && is_array($_POST['creditname'])) ? $_POST['creditname'] : [];

// إدخال البيانات في جدول journal_heads
$sql1 = "INSERT INTO journal_heads (journal_id, total , jdate , details , user) VALUES ('$journal_id','$total', '$jdate', '$details', '$usid')";
$conn->query($sql1);

// الحصول على معرف journal_head الأخير للإشارة إليه في journal_entries
$journal_lastid = $conn->insert_id;

// التكرار عبر مصفوفات depitval و depitname لإدخال الإدخالات المدينة
foreach ($depitVals as $key => $depitValue) {
    if ($depitValue === '' || (float) $depitValue == 0) {
        continue;
    }
    $account_id = $depitNames[$key] ?? 0;
    if ((int) $account_id < 1) {
        continue;
    }
    $details = "إدخال دين"; // قم بتعديل هذا إذا كان لديك معلومات محددة

    $sql2 = "INSERT INTO journal_entries 
    (journal_id       , account_id   , debit        , credit, tybe, info) VALUES 
    ('$journal_lastid', '$account_id', '$depitValue', '0'   , '0'  , '$details')";
    $conn->query($sql2);
}

// التكرار عبر مصفوفات creditval و creditname لإدخال الإدخالات الدائنة
foreach ($creditVals as $key => $creditValue) {
    if ($creditValue === '' || (float) $creditValue == 0) {
        continue;
    }
    $account_id = $creditNames[$key] ?? 0;
    if ((int) $account_id < 1) {
        continue;
    }
    $info = "إدخال ائتمان"; // قم بتعديل هذا إذا كان لديك معلومات محددة

    $sql3 = "INSERT INTO journal_entries (journal_id, account_id, debit, credit, tybe, info) 
             VALUES ('$journal_lastid', '$account_id', '0', '$creditValue', '1', '$info')";
    $conn->query($sql3);
}

// تسجيل العملية للتدقيق أو السجل
$conn->query("INSERT INTO `process`(`type`) VALUES ('إضافة قيد')");

    header("location:../daily_journal.php");

