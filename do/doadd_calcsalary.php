<?php
include('../includes/connect.php');

$employeeid = $_POST['employee'];
$startdate = $_POST['startdate'];
$startnum = new DateTime($startdate);
$enddate = $_POST['enddate'];
$endnum = new DateTime($enddate);

// التحقق من وجود سجلات في الفترة المحددة
$sqlchkdur = "SELECT * FROM attlog WHERE employee = $employeeid AND day >= '$startdate' AND day < '$enddate'";
$rowchkdur = $conn->query($sqlchkdur)->fetch_assoc();
if (isset($rowchkdur)) {
    echo "<h1> يوجد سجلات في الفتره المحدده من فضلك تأكد من الفتره<button style='font-size:40px'><a href='../add_calcsalary.php'>رجوع</a></button></h1> ";
    die;
}

// حساب عدد الأيام في الفترة المحددة
$interval = $startnum->diff($endnum);
$dayscount = $interval->days + 1; // يجب إضافة 1 لأن الفرق يشمل يوم البداية أيضًا

// استرجاع بيانات الموظف
$rowemp = $conn->query("SELECT * FROM employees WHERE id = $employeeid")->fetch_assoc();
$calc_type = $rowemp['calc_type'] ?? 'monthly';
if ($calc_type === 'daily') {
    $emp_daily_rate = (float)$rowemp['salary'];
} elseif ($calc_type === 'weekly') {
    $emp_daily_rate = (float)$rowemp['salary'] / 7;
} else {
    $emp_daily_rate = (float)$rowemp['salary'] / 30;
}
$period_base_salary = $emp_daily_rate * $dayscount;

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
    echo "<h1>لا توجد وردية معرّفة في النظام. أضف وردية من إعدادات الورديات ثم أعد المحاولة.<button style='font-size:40px'><a href='../add_calcsalary.php'>رجوع</a></button></h1>";
    die;
}

$shiftstart = $rowshft['shiftstart'];
$shiftend = $rowshft['shiftend'];
$instart = $rowshft['instart'];
$inend = $rowshft['inend'];
$outstart = $rowshft['outstart'];
$outend = $rowshft['outend'];
$workingdays = $rowshft['workingdays'];
$wdarray = array_map('trim', explode(",", (string)$workingdays));

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
    $is_workday = in_array($dayofweek, $wdarray);
    if ($is_workday) {
        $statue = 1;
    } else {
        $statue = 0;
    }

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
    
    $holiday_calc = isset($rowstg['holiday_work_calc']) ? (int)$rowstg['holiday_work_calc'] : 1;
    $defhours_inserted = $time_difference_hours;
    
    if ($has_fpin || $has_fpout) {
        if (!$is_workday) { // holiday
            if ($holiday_calc === 0) {
                // Ignore work on holiday
                $statue = 0;
            } else {
                $statue = 2;
                if ($holiday_calc === 2) {
                    // Overtime: defhours becomes 0
                    $defhours_inserted = 0;
                }
            }
        } else {
            // Workday
            $statue = 2;
        }
    }
       
    







    if($fpout > 24){
        $time_difference_in_seconds = $time1 + (24*3600) - $time2;
        $time_difference_hours = floor($time_difference_in_seconds / 3600);
        $time_difference_minutes = floor(($time_difference_in_seconds % 3600) / 60);
        $time_difference_seconds = $time_difference_in_seconds % 60;
    }

    $time_difference_hours2 = 0;
    if ($has_fpin && $has_fpout) {
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
    } elseif (!$has_fpin && !$has_fpout) {
        $time_difference_hours2 = 0;
    } else {
        $missing_fp_calc = isset($rowstg['missing_fingerprint_calc']) ? (float)$rowstg['missing_fingerprint_calc'] : 0.5;
        $time_difference_hours2 = $time_difference_hours * $missing_fp_calc;
    }

    if (!$is_workday && ($has_fpin || $has_fpout) && $holiday_calc === 0) {
        $time_difference_hours2 = 0;
    }

    // حساب المستحقات المالية
    $defhours_for_rate = $time_difference_hours > 0 ? $time_difference_hours : 1;
    $dfh = $emp_daily_rate / $defhours_for_rate;
    $dueforhour = round($dfh, 2);
    $realdue = floor($dueforhour * $time_difference_hours2);




    // إدخال البيانات في جدول سجلات الحضور
    $sqllog = ("INSERT INTO attlog 
    (employee, day, starttime, endtime, fpin, fpout, defhours, curhours, dueforhour, realdue, statue)
     VALUES 
     ('$employeeid','$curday','$shiftstart','$shiftend','$fpin','$fpout','$defhours_inserted','$time_difference_hours2','$dueforhour','$realdue','$statue')");
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

// اجر الساعه اليومي
$titleperhour = $exphours > 0 ? round($period_base_salary / $exphours, 2) : 0;
// اجر الساعه اليومي

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
        $day_hours = $workdays > 0 ? $period_base_salary / $workdays : 0;



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
    if ($calc_type === 'daily') {
        $daily_pay_rate = (float)$rowemp['salary'];
    } elseif ($calc_type === 'weekly') {
        $daily_pay_rate = (float)$rowemp['salary'] / 7;
    } else {
        $daily_pay_rate = $workdays > 0 ? ((float)$rowemp['salary'] / $workdays) : 0;
    }
    $entitle = round($attdays * $daily_pay_rate, 2);

}elseif ($ent_tybe == 5){
    $info = " احتساب الرواتب من يوم " . $startdate . " الي يوم " . $enddate . " بنظام الاستحقاق بالانتاجية";
    $empname = $conn->real_escape_string($rowemp['name']);
    $rowprod = $conn->query("SELECT COALESCE(SUM(value), 0) AS prod_val FROM productions WHERE emp_name = '$empname' AND date >= '$startdate' AND date <= '$enddate'")->fetch_assoc();
    $entitle = round((float)$rowprod['prod_val'], 2);
}

$empname_esc = $conn->real_escape_string($rowemp['name']);
$rowextra = $conn->query("SELECT COALESCE(SUM(amount), 0) AS extra_val FROM financial_transactions WHERE emp_name = '$empname_esc' AND type = 1 AND date >= '$startdate' AND date <= '$enddate'")->fetch_assoc();
$rowdeduct = $conn->query("SELECT COALESCE(SUM(amount), 0) AS deduct_val FROM financial_transactions WHERE emp_name = '$empname_esc' AND type = 0 AND date >= '$startdate' AND date <= '$enddate'")->fetch_assoc();
$extraVal = (float)$rowextra['extra_val'];
$deductVal = (float)$rowdeduct['deduct_val'];

$entitle = $entitle + $extraVal - $deductVal;

if ($extraVal > 0 || $deductVal > 0) {
    $info .= " (إضافي: " . number_format($extraVal, 2) . " ج.م، خصم: " . number_format($deductVal, 2) . " ج.م)";
}








$sqlattdocs = "INSERT INTO attdocs 
(empid,fromdate,todate,alldays, workdays, exphours, accualhours, attdays, absdays, holidays, earlyminits, info , entitle) VALUES ('$employeeid','$startdate','$enddate','$dayscount','$workdays','$exphours','$accualhours','$attdays','$absdays','$holidays','0','$info' , '$entitle')";


$conn->query($sqlattdocs);
$docid = $conn->insert_id;

// تحديث سجلات الحضور بمعرف الملخص
$sqlupdate = "UPDATE attlog SET attdoc = '$docid' WHERE day >= '$startdate'  AND day <= '$enddate' And employee = $employeeid";
$conn->query($sqlupdate);

// توحيد المستحق اليومي مع أجر الساعة المحسوب للفترة
if ($titleperhour > 0) {
    $conn->query("UPDATE attlog SET dueforhour = $titleperhour, realdue = FLOOR($titleperhour * curhours) WHERE attdoc = '$docid' AND employee = '$employeeid'");
}


// تسجيل العملية
$conn->query("INSERT INTO `process`(`type`) VALUES ('add calcsalary')");

// إعادة التوجيه إلى صفحة حساب الرواتب
header('location:../calcsalary.php');

include('../includes/footer.php');
