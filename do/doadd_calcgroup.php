<?php
include('../includes/connect.php');
require_once('../includes/salary_rates.php');
require_once('../includes/payroll_calcs_helper.php');
require_once('../includes/shift_attendance.php');
ensure_payroll_calcs_schema($conn);
ensure_shift_single_fp_schema($conn);

$department = (int) $_POST['department'];
$startdate = $_POST['startdate'];
$enddate = $_POST['enddate'];

$rowDept = $conn->query("SELECT name FROM departments WHERE id = $department")->fetch_assoc();
$departmentName = $rowDept['name'] ?? ('إدارة #' . $department);

$processedEmployees = [];

$sqlemps = "SELECT * FROM `employees` WHERE `department` = '$department' AND isdeleted != 1 ORDER BY name";
$resemps = $conn->query($sqlemps);
while ($rowemps = $resemps->fetch_assoc()) {
  
$employeeid = $rowemps['id'];
$startnum = new DateTime($startdate);
$endnum = new DateTime($enddate);

// التحقق من وجود سجلات في الفترة المحددة
$sqlchkdur = "SELECT * FROM attlog WHERE employee = $employeeid AND day >= '$startdate' AND day < '$enddate'";
$rowchkdur = $conn->query($sqlchkdur)->fetch_assoc();
if (isset($rowchkdur)) {
    $_SESSION['calcsalary_flash'] = [
        'type' => 'danger',
        'title' => 'توقفت المعالجة',
        'source' => 'معالجة البصمة — حسب الإدارة',
        'lines' => [
            'الإدارة: ' . $departmentName,
            'الفترة: من ' . $startdate . ' إلى ' . $enddate,
            'الموظف المتوقف: ' . $rowemps['name'],
            'يوجد سجلات محسوبة مسبقاً لهذا الموظف في الفترة المحددة.',
        ],
    ];
    if (!empty($processedEmployees)) {
        $_SESSION['calcsalary_flash']['lines'][] = 'تم احتساب ' . count($processedEmployees) . ' موظف قبل التوقف.';
        $_SESSION['calcsalary_flash']['employees'] = $processedEmployees;
    }
    header('Location: ../add_calcsalary.php');
    exit;
}

// حساب عدد الأيام في الفترة المحددة
$interval = $startnum->diff($endnum);
$dayscount = $interval->days + 1; // يجب إضافة 1 لأن الفرق يشمل يوم البداية أيضًا

// استرجاع بيانات الموظف
$rowemp = $conn->query("SELECT * FROM employees WHERE id = $employeeid")->fetch_assoc();
$ent_tybe = (int)$rowemp['ent_tybe'];
if ($ent_tybe < 1 || $ent_tybe > 5) {
    $ent_tybe = 1;
}
$hour_extra = (float)$rowemp['hour_extra'];
if ($hour_extra <= 0) {
    $hour_extra = 1;
}
$day_extra = $rowemp['day_extra'];

// استرجاع بيانات الشيفت (إذا لم تُعيَّن وردية للموظف نستخدم أول وردية في النظام)
$shift = (int)$rowemp['shift'];
$rowshft = $conn->query("SELECT * FROM shifts WHERE id = $shift AND (isdeleted = 0 OR isdeleted IS NULL)")->fetch_assoc();
if (!$rowshft) {
    $rowshft = $conn->query("SELECT * FROM shifts WHERE (isdeleted = 0 OR isdeleted IS NULL) ORDER BY id ASC LIMIT 1")->fetch_assoc();
}
if (!$rowshft) {
    $_SESSION['calcsalary_flash'] = [
        'type' => 'danger',
        'title' => 'توقفت المعالجة',
        'source' => 'معالجة البصمة — حسب الإدارة',
        'lines' => [
            'الإدارة: ' . $departmentName,
            'الموظف المتوقف: ' . $rowemps['name'],
            'لا توجد وردية معرّفة في النظام.',
        ],
    ];
    header('Location: ../add_calcsalary.php');
    exit;
}

$shiftstart = $rowshft['shiftstart'];
$shiftend = $rowshft['shiftend'];
$instart = $rowshft['instart'];
$inend = $rowshft['inend'];
$outstart = $rowshft['outstart'];
$outend = $rowshft['outend'];
$workingdays = $rowshft['workingdays'];
$wdarray = array_map('trim', explode(",", (string)$workingdays));
$single_fp_rule = normalize_single_fp_rule($rowshft['single_fp_rule'] ?? 'half');

// حلقة لمعالجة كل يوم في الفترة المحددة
for ($i = 0; $i < $dayscount; $i++) {
    $curday = $startnum->format('Y-m-d');
    $cdate = new DateTime($curday);
    $dayofweek = $cdate->format('N');

    // حساب ساعات العمل المتوقعة (شيفت ليلي: نهاية الشيفت قبل بدايته على الساعة)
    $time1 = strtotime($shiftend);
    $time2 = strtotime($shiftstart);
    $time_difference_in_seconds = $time1 - $time2;
    if ($time_difference_in_seconds <= 0) {
        $time_difference_in_seconds += 24 * 3600;
    }
    $time_difference_hours = floor($time_difference_in_seconds / 3600);
    $time_difference_minutes = floor(($time_difference_in_seconds % 3600) / 60);
    $time_difference_seconds = $time_difference_in_seconds % 60;

    // تحديد حالة اليوم (عمل أم لا)
    $baseStatue = in_array($dayofweek, $wdarray) ? 1 : 0;
    $statue = $baseStatue;

    $fpin = null;
    $fpout = null;

    // التحقق من بصمة الدخول
    $sqlfpin = "SELECT MIN(time) AS fpin FROM attandance WHERE employee = '$employeeid' AND fpdate = '$curday' AND time >= '$instart' AND time <= '$inend'";

    $rowfpin = $conn->query($sqlfpin)->fetch_assoc();
    $fpin = $rowfpin['fpin'] ?? null;

    $shiftstart_time = new DateTime($shiftstart);
    $shiftend_time = new DateTime($shiftend);
   
    
    if ($shiftend_time > $shiftstart_time) {
        // Case 1: Shift does not cross midnight
        $sqlfpout = "SELECT MAX(time) AS fpout FROM attandance WHERE employee = '$employeeid' AND fpdate = '$curday' AND time >= '$outstart' AND time <= '$outend'";
        $rowfpout = $conn->query($sqlfpout)->fetch_assoc();
        $fpout = $rowfpout['fpout'] ?? null;
    } elseif ($shiftend_time <= $shiftstart_time) {
        // Case 2: Shift crosses midnight
        $curday = (new DateTime($curday))->modify('+1 day')->format('Y-m-d');
        
        $sqlfpout = "SELECT MAX(time) AS fpout FROM attandance WHERE employee = '$employeeid' AND fpdate = '$curday' AND time >= '$outstart' AND time <= '$outend'";
        $rowfpout = $conn->query($sqlfpout)->fetch_assoc();
        $fpout = $rowfpout['fpout'] ?? null;

        if ($fpout) {
            $fpout_time = new DateTime($fpout);
            $fpout_time->modify('+24 hours');
            $hours = (int)$fpout_time->format('H');
            $minutes_seconds = $fpout_time->format(':i:s');
            $fpout = ($hours + 24) . $minutes_seconds;
        }
        $curday = $startnum->format('Y-m-d');
    }

    $has_fpin = ($fpin !== null && $fpin !== '');
    $has_fpout = ($fpout !== null && $fpout !== '');
       
    







    if($fpout > 24){
        $time_difference_in_seconds = $time1 + (24*3600) - $time2;
        $time_difference_hours = floor($time_difference_in_seconds / 3600);
        $time_difference_minutes = floor(($time_difference_in_seconds % 3600) / 60);
        $time_difference_seconds = $time_difference_in_seconds % 60;
    }
    $time_difference_hours2 = 0;
    if ($has_fpin && $has_fpout) {
        $statue = 2;
        list($hours, $minutes, $seconds) = array_pad(explode(':', (string)$fpout), 3, '00');
        $hours = (int)$hours;
        if ($hours >= 24) {
            $hours -= 24;
            $fpout_normalized = sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
            $time3 = strtotime($fpout_normalized) + 86400;
        } else {
            $time3 = strtotime($fpout);
        }
        $time4 = strtotime($fpin);
        $time_difference2 = $time3 - $time4;
        $time_difference_hours2 = round(($time_difference2 / 3600), 2);
    } else {
        $resolved = resolve_single_fp_attendance(
            $has_fpin,
            $has_fpout,
            (float) $time_difference_hours,
            $single_fp_rule,
            $baseStatue
        );
        $statue = $resolved['statue'];
        $time_difference_hours2 = $resolved['curhours'];
    }

    // حساب المستحقات المالية
    $defhours_for_rate = $time_difference_hours > 0 ? $time_difference_hours : 1;
    $dueforhour = round(employee_hourly_rate($rowemp, $defhours_for_rate, $curday), 2);
    $realdue = floor($dueforhour * $time_difference_hours2);




    // إدخال البيانات في جدول سجلات الحضور
    $sqllog = ("INSERT INTO attlog 
    (employee, day, starttime, endtime, fpin, fpout, defhours, curhours, dueforhour, realdue, statue)
     VALUES 
     ('$employeeid','$curday','$shiftstart','$shiftend','$fpin','$fpout','$time_difference_hours ','$time_difference_hours2','$dueforhour','$realdue','$statue')");
    $startnum->add(new DateInterval('P1D'));





    "INSERT INTO attlog 
    (employee, day        , starttime, endtime  , fpin     , fpout    , defhours , curhours, dueforhour, realdue, statue) VALUES 
    ('130'   ,'2024-08-01','16:00:00','02:00:00','16:00:00','02:00:00','-14'     ,'10'     ,'-4.76'    ,'-48'   ,'2'    )";



    $conn->query($sqllog);
}
$sqlgetatt = "SELECT COUNT(*) AS holidays FROM attlog WHERE statue = '0' AND employee = '$employeeid'  AND day >= '$startdate' AND day <= '$enddate'";
$reshol = $conn->query($sqlgetatt);
$rowhol = $reshol->fetch_assoc();
$holidays = $rowhol['holidays'];
$holidays = $rowhol['holidays'];
$workdays = $dayscount - $holidays;
$exphours = $time_difference_hours * $workdays;

$sqlacchours = "SELECT SUM(curhours) AS curhours FROM attlog WHERE statue = '2' AND employee = '$employeeid'  AND day >= '$startdate' AND day <= '$enddate'";
$rowacchours = $conn->query($sqlacchours)->fetch_assoc();
$accualhours = round($rowacchours['curhours'], 2);

$sqlcountatt = "SELECT COUNT(*) AS attdays FROM attlog WHERE statue = '2' AND employee = '$employeeid'  AND day >= '$startdate' AND day <= '$enddate'";
$rowcountatt = $conn->query($sqlcountatt)->fetch_assoc();
$attdays = $rowcountatt['attdays'];


$rowcountabs = $conn->query("SELECT COUNT(*) AS absdays FROM attlog WHERE statue = '2' AND employee = '$employeeid'  AND day > '$startdate' AND day < '$enddate'")->fetch_assoc();
$absdays = $rowcountabs['absdays'];

// إنشاء ملخص الحضور
$info = " احتساب الرواتب من يوم " . $startdate . " الي يوم " . $enddate;

// اجر الساعة حسب فترة الراتب (يومي / شهري)
$shift_hours_for_rate = $time_difference_hours > 0 ? $time_difference_hours : 1;
$titleperhour = round(employee_hourly_rate($rowemp, $shift_hours_for_rate, $startdate), 2);

// exphours عدد الساعات المتوقعة
// اجر اليوم dayhours
        $extrasql="SELECT SUM(curhours - defhours) AS total_hours FROM attlog WHERE curhours > defhours AND statue != 0 AND employee = '$employeeid'  AND day >= '$startdate' AND day <= '$enddate'";
      

        $extra_time_hours =$conn->query($extrasql)->fetch_assoc();
        



        $extra_time_period = $conn->query("SELECT SUM(curhours) - SUM(defhours) AS total_difference FROM attlog where statue != 0 AND employee = '$employeeid'  AND day >= '$startdate' AND day <= '$enddate'")->fetch_assoc();
        

        $ext_hours =  $extra_time_hours['total_hours'];
        $ext_period = $extra_time_period['total_difference'];
        $basic_hours = $accualhours - $ext_hours;
        $basic_period = $accualhours - $ext_period;
        $ext_hours_ent = $ext_hours * $titleperhour *  $hour_extra ; 
        $ext_hours_basic = $ext_hours * $titleperhour;
        $basic_hours_ent = ($basic_hours * $titleperhour );



        // ايام العمل الفعلية
        $workdays = $dayscount - $holidays;
        // راتب اليوم
        $day_hours = employee_daily_rate($rowemp, $startdate);



if ($ent_tybe == 1) {
    $info = " احتساب الرواتب من يوم " . $startdate . " الي يوم " . $enddate . " بنظام الاستحقاق بالساعات فقط";
    $entitle =  round($titleperhour * $accualhours ,2 );

}elseif ($ent_tybe == 2) {
    $info = " احتساب الرواتب من يوم " . $startdate . " الي يوم " . $enddate . " بنظام الاستحقاق ساعات عمل و اضافي يومي";
    $entitle = round($titleperhour * $accualhours ,2 ) + $ext_hours_ent - $ext_hours_basic ;

}elseif ($ent_tybe == 3){
    $info = " احتساب الرواتب من يوم " . $startdate . " الي يوم " . $enddate . " بنظام الاستحقاق ساعات عمل و اضافي خلال الفترة";
    if ($ext_period <= 0) {
        $entitle = round($accualhours * $titleperhour, 2);
    } else {
        $entitle = round((($accualhours - $ext_period) * $titleperhour) + ($ext_period * $titleperhour * $hour_extra), 2);
    }
}elseif ($ent_tybe == 4){
    $info = " احتساب الرواتب من يوم " . $startdate . " الي يوم " . $enddate . " بنظام الاستحقاق بناء علي الحضور";
    $entitle = round($attdays * employee_daily_rate($rowemp, $startdate), 2);

}elseif ($ent_tybe == 5){
    $info = " احتساب الرواتب من يوم " . $startdate . " الي يوم " . $enddate . " بنظام الاستحقاق بالانتاجية";
    $empname = $conn->real_escape_string($rowemp['name']);
    $rowprod = $conn->query("SELECT COALESCE(SUM(value), 0) AS prod_val FROM productions WHERE emp_name = '$empname' AND date >= '$startdate' AND date <= '$enddate'")->fetch_assoc();
    $entitle = round((float)$rowprod['prod_val'], 2);
} else {
    $entitle = round($titleperhour * $accualhours, 2);
}







$payrollSums = payroll_sum_for_period($conn, (int) $employeeid, $rowemp['name'], $startdate, $enddate, $entitle);
$bonus = $payrollSums['bonus'];
$insurance = $payrollSums['insurance'];
$tax = $payrollSums['tax'];
$deduction = $payrollSums['deduction'];
$netPay = payroll_net_pay($entitle, $payrollSums);

$sqlattdocs = "INSERT INTO attdocs 
(empid,fromdate,todate,alldays, workdays, exphours, accualhours, attdays, absdays, holidays, earlyminits, info , entitle, bonus, insurance, tax, deduction, net_pay) VALUES ('$employeeid','$startdate','$enddate','$dayscount','$workdays','$exphours','$accualhours','$attdays','$absdays','$holidays','0','$info' , '$entitle', '$bonus', '$insurance', '$tax', '$deduction', '$netPay')";


$conn->query($sqlattdocs);
$docid = $conn->insert_id;

// تحديث سجلات الحضور بمعرف الملخص
$sqlupdate = "UPDATE attlog SET attdoc = '$docid' WHERE day >= '$startdate'  AND day <= '$enddate' And employee = $employeeid";
$conn->query($sqlupdate);

if ($titleperhour > 0) {
    $conn->query("UPDATE attlog SET dueforhour = $titleperhour, realdue = FLOOR($titleperhour * curhours) WHERE attdoc = '$docid' AND employee = '$employeeid'");
}

// تسجيل العملية
$conn->query("INSERT INTO `process`(`type`) VALUES ('add calcsalary')");

$processedEmployees[] = [
    'name' => $rowemp['name'],
    'attdays' => $attdays,
    'hours' => $accualhours,
    'entitle' => $entitle,
    'net_pay' => $netPay,
    'docid' => $docid,
];

}    
$conn->query("INSERT INTO `process`(`type`) VALUES ('add calcgroup')");

if (empty($processedEmployees)) {
    $_SESSION['calcsalary_flash'] = [
        'type' => 'warning',
        'title' => 'لم يتم احتساب أي موظف',
        'source' => 'معالجة البصمة — حسب الإدارة',
        'lines' => [
            'الإدارة: ' . $departmentName,
            'الفترة: من ' . $startdate . ' إلى ' . $enddate,
            'لا يوجد موظفون نشطون في هذه الإدارة.',
        ],
    ];
} else {
    $_SESSION['calcsalary_flash'] = [
        'type' => 'success',
        'title' => 'تمت المعالجة بنجاح',
        'source' => 'معالجة البصمة — حسب الإدارة',
        'lines' => [
            'الإدارة: ' . $departmentName,
            'الفترة: من ' . $startdate . ' إلى ' . $enddate,
            'عدد الموظفين المحتسبين: ' . count($processedEmployees),
        ],
        'employees' => $processedEmployees,
        'link' => 'calcsalary.php?from=' . urlencode($startdate) . '&to=' . urlencode($enddate),
    ];
}

header('Location: ../add_calcsalary.php');
exit;