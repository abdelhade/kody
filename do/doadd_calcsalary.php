<?php
include('../includes/connect.php');
require_once('../includes/payroll_engine.php');

$employeeid = (int) ($_POST['employee'] ?? 0);
$startdate = (string) ($_POST['startdate'] ?? '');
$enddate = (string) ($_POST['enddate'] ?? '');

$res = payroll_process_employee($conn, $employeeid, $startdate, $enddate);

if (!$res['ok']) {
    $_SESSION['calcsalary_flash'] = [
        'type' => 'danger',
        'title' => 'لم تتم المعالجة',
        'source' => 'معالجة البصمة — موظف واحد',
        'lines' => [
            'الموظف: ' . $res['name'],
            'الفترة: من ' . $startdate . ' إلى ' . $enddate,
            $res['error'],
        ],
    ];
    header('Location: ../add_calcsalary.php');
    exit;
}

$lines = [
    'الموظف: ' . $res['name'],
    'الفترة: من ' . $startdate . ' إلى ' . $enddate,
    'عدد أيام الفترة: ' . $res['days'],
    'أيام الحضور الفعلي: ' . $res['attdays'] . ' — أيام الغياب: ' . $res['absdays'],
    'إجمالي الساعات المحتسبة: ' . $res['hours'],
];
if ($res['holiday_pay'] > 0) {
    $lines[] = 'أجر الإجازات المدفوعة: ' . number_format((float) $res['holiday_pay'], 2);
}
$lines[] = 'الاستحقاق: ' . number_format((float) $res['entitle'], 2) . ' — الصافي: ' . number_format((float) $res['net_pay'], 2);
$lines[] = 'رقم مستند المعالجة: #' . $res['docid'];

$_SESSION['calcsalary_flash'] = [
    'type' => 'success',
    'title' => 'تمت المعالجة بنجاح',
    'source' => 'معالجة البصمة — موظف واحد',
    'lines' => $lines,
    'link' => 'calcsalary.php?emp=' . $employeeid . '&from=' . urlencode($startdate) . '&to=' . urlencode($enddate),
];

header('Location: ../add_calcsalary.php');
exit;
