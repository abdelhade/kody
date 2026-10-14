<?php
include('../includes/connect.php');
require_once('../includes/payroll_engine.php');

$department = (int) ($_POST['department'] ?? 0);
$startdate = (string) ($_POST['startdate'] ?? '');
$enddate = (string) ($_POST['enddate'] ?? '');

$rowDept = $conn->query("SELECT name FROM departments WHERE id = $department")->fetch_assoc();
$departmentName = $rowDept['name'] ?? ('إدارة #' . $department);

$processedEmployees = [];
$failedLines = [];

$resemps = $conn->query("SELECT id FROM `employees` WHERE `department` = $department AND isdeleted != 1 ORDER BY name");
while ($resemps && ($rowemps = $resemps->fetch_assoc())) {
    $res = payroll_process_employee($conn, (int) $rowemps['id'], $startdate, $enddate);
    if (!$res['ok']) {
        $failedLines[] = $res['name'] . ': ' . $res['error'];
        continue;
    }
    $processedEmployees[] = [
        'name' => $res['name'],
        'attdays' => $res['attdays'],
        'hours' => $res['hours'],
        'entitle' => $res['entitle'],
        'net_pay' => $res['net_pay'],
        'docid' => $res['docid'],
    ];
}
$conn->query("INSERT INTO `process`(`type`) VALUES ('add calcgroup')");

$baseLines = [
    'الإدارة: ' . $departmentName,
    'الفترة: من ' . $startdate . ' إلى ' . $enddate,
];

if (empty($processedEmployees)) {
    $_SESSION['calcsalary_flash'] = [
        'type' => empty($failedLines) ? 'warning' : 'danger',
        'title' => 'لم يتم احتساب أي موظف',
        'source' => 'معالجة البصمة — حسب الإدارة',
        'lines' => array_merge($baseLines, $failedLines ?: ['لا يوجد موظفون نشطون في هذه الإدارة.']),
    ];
} else {
    $lines = array_merge($baseLines, ['عدد الموظفين المحتسبين: ' . count($processedEmployees)]);
    if (!empty($failedLines)) {
        $lines[] = 'تم تخطي ' . count($failedLines) . ' موظف:';
        $lines = array_merge($lines, $failedLines);
    }
    $_SESSION['calcsalary_flash'] = [
        'type' => empty($failedLines) ? 'success' : 'warning',
        'title' => empty($failedLines) ? 'تمت المعالجة بنجاح' : 'تمت المعالجة مع تخطي بعض الموظفين',
        'source' => 'معالجة البصمة — حسب الإدارة',
        'lines' => $lines,
        'employees' => $processedEmployees,
        'link' => 'calcsalary.php?dept=' . $department . '&from=' . urlencode($startdate) . '&to=' . urlencode($enddate),
    ];
}

header('Location: ../add_calcsalary.php');
exit;
