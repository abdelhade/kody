<?php
include('../includes/connect.php');
require_once('../includes/payroll_calcs_helper.php');
ensure_payroll_calcs_schema($conn);

$id = (int) ($_GET['edit'] ?? 0);
$snd_id = (int) ($_POST['snd_id'] ?? $id);
$date = $_POST['date'] ?? date('Y-m-d');
$info = $conn->real_escape_string($_POST['info'] ?? '');
$user = $conn->real_escape_string($_SESSION['login'] ?? '');

$tybe = (int) ($_POST['calc_tybe'] ?? 1);
if ($tybe < 1 || $tybe > 4) {
    $tybe = 1;
}

$emp_ids = $_POST['emp_id'] ?? [];
$amounts = $_POST['amount'] ?? [];
$percents = $_POST['percent'] ?? [];
$info2s = $_POST['info2'] ?? [];

$conn->query("DELETE FROM payroll_calcs WHERE snd_id = $id");

$count = count($emp_ids);
for ($i = 0; $i < $count; $i++) {
    $empId = (int) ($emp_ids[$i] ?? 0);
    if ($empId <= 0) {
        continue;
    }
    $rowEmp = $conn->query("SELECT name FROM employees WHERE id = $empId")->fetch_assoc();
    $empName = $conn->real_escape_string($rowEmp['name'] ?? '');
    $amount = (float) ($amounts[$i] ?? 0);
    $percent = (float) ($percents[$i] ?? 0);
    $info2 = $conn->real_escape_string($info2s[$i] ?? '');

    $sql = "INSERT INTO payroll_calcs (snd_id, calc_tybe, date, emp_id, emp_name, amount, percent, info, info2, user)
            VALUES ($snd_id, $tybe, '$date', $empId, '$empName', $amount, $percent, '$info', '$info2', '$user')";
    $conn->query($sql);
}

header('location:../payroll_calcs.php');
