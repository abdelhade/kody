<?php
/**
 * معالجة البصمة واحتساب الراتب لموظف واحد في فترة.
 * مشتركة بين معالجة الموظف (do/doadd_calcsalary.php) ومعالجة الإدارة (do/doadd_calcgroup.php).
 *
 * حالة اليوم في attlog.statue: 0 إجازة، 1 غياب (يوم عمل بدون حضور)، 2 حضور.
 * الراتب الشهري: الإجازات (أيام خارج workingdays للوردية) مدفوعة بأجر اليوم،
 * فالمحصلة = الراتب − خصم الغياب/النقص. الراتب اليومي: الإجازة غير مدفوعة.
 */

require_once __DIR__ . '/salary_rates.php';
require_once __DIR__ . '/payroll_calcs_helper.php';
require_once __DIR__ . '/shift_attendance.php';
require_once __DIR__ . '/EntitlesSeeder.php';
require_once __DIR__ . '/../classes/DynamicShiftDetector.php';

function payroll_valid_date(string $date): bool
{
    $dt = DateTime::createFromFormat('Y-m-d', $date);
    return $dt && $dt->format('Y-m-d') === $date;
}

/** وردية الموظف، ولو مش متحددة أول وردية في النظام */
function payroll_employee_shift(mysqli $conn, int $shiftId): ?array
{
    $row = $conn->query("SELECT * FROM shifts WHERE id = $shiftId AND (isdeleted = 0 OR isdeleted IS NULL)")->fetch_assoc();
    if (!$row) {
        $row = $conn->query("SELECT * FROM shifts WHERE (isdeleted = 0 OR isdeleted IS NULL) ORDER BY id ASC LIMIT 1")->fetch_assoc();
    }
    return $row ?: null;
}

function payroll_fp_time(mysqli $conn, int $empId, string $day, ?string $from, ?string $to, string $agg): ?string
{
    $from = $conn->real_escape_string((string) $from);
    $to = $conn->real_escape_string((string) $to);
    $row = $conn->query("SELECT $agg(time) AS t FROM attandance
        WHERE employee = '$empId' AND fpdate = '$day' AND time >= '$from' AND time <= '$to'")->fetch_assoc();
    $t = $row['t'] ?? null;
    return ($t === null || $t === '') ? null : $t;
}

/**
 * يوم في وردية بمواعيد ثابتة: بصمة دخول في نطاق الحضور وبصمة خروج في نطاق الانصراف، مع فترات السماح.
 *
 * @return array{0:int, 1:float, 2:?string, 3:?string} [statue, curhours, fpin, fpout]
 */
function payroll_fixed_day(
    mysqli $conn,
    int $employeeId,
    DateTime $day,
    array $rowshft,
    bool $crossMidnight,
    int $shiftStartTs,
    int $shiftEndTs,
    float $shiftHours,
    string $singleFpRule,
    int $baseStatue
): array {
    $curday = $day->format('Y-m-d');
    $fpin = payroll_fp_time($conn, $employeeId, $curday, $rowshft['instart'], $rowshft['inend'], 'MIN');
    $outDay = $crossMidnight ? (clone $day)->modify('+1 day')->format('Y-m-d') : $curday;
    $fpout = payroll_fp_time($conn, $employeeId, $outDay, $rowshft['outstart'], $rowshft['outend'], 'MAX');
    if ($crossMidnight && $fpout !== null) {
        $fpoutDt = new DateTime($fpout);
        $fpout = ((int) $fpoutDt->format('H') + 24) . $fpoutDt->format(':i:s');
    }

    if ($singleFpRule === 'none') {
        $filled = apply_single_fp_no_comment_fallback($fpin, $fpout, $rowshft, $crossMidnight);
        $fpin = $filled['fpin'] ?: null;
        $fpout = $filled['fpout'] ?: null;
    }

    if ($fpin !== null && $fpout !== null) {
        $resolved = resolve_shift_presence_with_grace($fpin, $fpout, $shiftStartTs, $shiftEndTs, $shiftHours, $rowshft, $baseStatue);
    } else {
        $resolved = resolve_single_fp_attendance($fpin !== null, $fpout !== null, $shiftHours, $singleFpRule, $baseStatue);
    }

    return [(int) $resolved['statue'], (float) $resolved['curhours'], $fpin, $fpout];
}

/**
 * يوم في وردية مرنة: كل البصمات من day_cutoff يوم العمل لحد day_cutoff اليوم اللي بعده.
 * الخروج بعد نص الليل بيتسجل بصيغة أكبر من 24 ساعة (زي 26:30:00) زي الوردية الليلية.
 *
 * @return array{0:int, 1:float, 2:?string, 3:?string} [statue, curhours, fpin, fpout]
 */
function payroll_flexible_day(
    mysqli $conn,
    int $employeeId,
    DateTime $day,
    string $dayCutoff,
    int $dupMinutes,
    float $requiredHours,
    string $singleFpRule,
    int $baseStatue
): array {
    $curday = $day->format('Y-m-d');
    $nextday = (clone $day)->modify('+1 day')->format('Y-m-d');
    $cutoff = $conn->real_escape_string($dayCutoff);

    $timestamps = [];
    $res = $conn->query("SELECT fpdate, time FROM attandance
        WHERE employee = '$employeeId'
          AND ((fpdate = '$curday' AND time >= '$cutoff') OR (fpdate = '$nextday' AND time < '$cutoff'))
        ORDER BY fpdate ASC, time ASC");
    while ($res && ($row = $res->fetch_assoc())) {
        $ts = strtotime($row['fpdate'] . ' ' . $row['time']);
        if ($ts !== false) {
            $timestamps[] = $ts;
        }
    }

    $resolved = resolve_flexible_attendance($timestamps, $requiredHours, $dupMinutes, $singleFpRule, $baseStatue);

    $dayStart = strtotime($curday . ' 00:00:00');
    $fmt = static function (?int $ts) use ($dayStart): ?string {
        if ($ts === null) {
            return null;
        }
        $sec = $ts - $dayStart;
        return sprintf('%02d:%02d:%02d', intdiv($sec, 3600), intdiv($sec % 3600, 60), $sec % 60);
    };

    return [(int) $resolved['statue'], (float) $resolved['curhours'], $fmt($resolved['first']), $fmt($resolved['last'])];
}

function payroll_sql_value(mysqli $conn, $value): string
{
    if ($value === null || $value === '') {
        return 'NULL';
    }
    return "'" . $conn->real_escape_string((string) $value) . "'";
}

/**
 * @return array{ok:bool, code?:string, error?:string, name:string, docid?:int, attdays?:int,
 *               absdays?:int, hours?:float, holiday_pay?:float, entitle?:float, net_pay?:float}
 */
function payroll_process_employee(mysqli $conn, int $employeeId, string $startdate, string $enddate): array
{
    $rowemp = $conn->query("SELECT * FROM employees WHERE id = $employeeId")->fetch_assoc();
    $name = $rowemp['name'] ?? ('موظف #' . $employeeId);
    $fail = fn(string $code, string $error) => ['ok' => false, 'code' => $code, 'error' => $error, 'name' => $name];

    if (!$rowemp) {
        return $fail('employee', 'الموظف غير موجود.');
    }
    if (!payroll_valid_date($startdate) || !payroll_valid_date($enddate) || $startdate > $enddate) {
        return $fail('period', 'الفترة غير صحيحة.');
    }

    $exists = $conn->query("SELECT id FROM attlog WHERE employee = $employeeId AND day >= '$startdate' AND day <= '$enddate' LIMIT 1")->fetch_assoc();
    if ($exists) {
        return $fail('exists', 'يوجد سجلات محسوبة مسبقاً في هذه الفترة. احذف المعالجة القديمة أو اختر فترة أخرى.');
    }

    $shiftGroupId = !empty($rowemp['shift_group_id']) ? (int) $rowemp['shift_group_id'] : 0;
    $groupShifts = $shiftGroupId > 0 ? DynamicShiftDetector::getGroupShifts($conn, $shiftGroupId) : [];
    $isDynamic = !empty($groupShifts);

    $rowshft = null;
    if (!$isDynamic) {
        $rowshft = payroll_employee_shift($conn, (int) $rowemp['shift']);
        if (!$rowshft) {
            return $fail('shift', 'لا توجد وردية معرّفة في النظام. أضف وردية من إعدادات الورديات ثم أعد المحاولة.');
        }
    } else {
        $rowshft = $groupShifts[0];
    }

    $entTybe = EntitlesSeeder::normalize((int) $rowemp['ent_tybe']);
    $hourExtra = (float) $rowemp['hour_extra'];
    if ($hourExtra <= 0) {
        $hourExtra = 1;
    }

    $shiftstart = $rowshft['shiftstart'];
    $shiftend = $rowshft['shiftend'];
    $shiftStartTs = strtotime($shiftstart);
    $shiftEndTs = strtotime($shiftend);
    $crossMidnight = $shiftEndTs <= $shiftStartTs;
    $shiftSeconds = $shiftEndTs - $shiftStartTs;
    if ($shiftSeconds <= 0) {
        $shiftSeconds += 24 * 3600;
    }
    $shiftHours = round($shiftSeconds / 3600, 2);
    $workingDays = array_map('trim', explode(',', (string) $rowshft['workingdays']));
    $singleFpRule = normalize_single_fp_rule($rowshft['single_fp_rule'] ?? 'half');

    $isFlexible = !empty($rowshft['is_flexible']);
    $dayCutoff = (string) ($rowshft['day_cutoff'] ?? '06:00:00');
    $dupMinutes = (int) ($rowshft['dup_fp_minutes'] ?? 2);
    if ($isFlexible) {
        $shiftHours = (float) ($rowshft['required_hours'] ?? 0) > 0 ? round((float) $rowshft['required_hours'], 2) : 8.0;
    }

    $hourRate = round(employee_hourly_rate($rowemp, $shiftHours > 0 ? $shiftHours : 1, $startdate), 2);
    $dayRate = employee_daily_rate($rowemp, $startdate);

    $t = [
        'days' => 0, 'non_working' => 0, 'holidays' => 0, 'absent' => 0, 'att' => 0,
        'hours' => 0.0, 'extra_daily' => 0.0, 'period_diff' => 0.0,
    ];

    $conn->begin_transaction();
    try {
        $day = new DateTime($startdate);
        $last = new DateTime($enddate);
        while ($day <= $last) {
            $curday = $day->format('Y-m-d');

            // تحديد الوردية لهذا اليوم بعينه (ثابتة أو ديناميكية مستنتجة من البصمة)
            $dayShift = $rowshft;
            if ($isDynamic) {
                $firstFp = payroll_fp_time($conn, $employeeId, $curday, '00:00:00', '23:59:59', 'MIN');
                if ($firstFp !== null) {
                    $detected = DynamicShiftDetector::detectShift($firstFp, $groupShifts);
                    if ($detected) {
                        $dayShift = $detected;
                    }
                } else {
                    $dayShift = $groupShifts[0];
                }
            }

            $curShiftStart = $dayShift['shiftstart'];
            $curShiftEnd = $dayShift['shiftend'];
            $curShiftStartTs = strtotime($curShiftStart);
            $curShiftEndTs = strtotime($curShiftEnd);
            $curCrossMidnight = $curShiftEndTs <= $curShiftStartTs;
            $curShiftSeconds = $curShiftEndTs - $curShiftStartTs;
            if ($curShiftSeconds <= 0) {
                $curShiftSeconds += 24 * 3600;
            }
            $curShiftHours = round($curShiftSeconds / 3600, 2);
            $curWorkingDays = array_map('trim', explode(',', (string) $dayShift['workingdays']));
            $curSingleFpRule = normalize_single_fp_rule($dayShift['single_fp_rule'] ?? 'half');
            $curIsFlexible = !empty($dayShift['is_flexible']);
            $curDayCutoff = (string) ($dayShift['day_cutoff'] ?? '06:00:00');
            $curDupMinutes = (int) ($dayShift['dup_fp_minutes'] ?? 2);
            if ($curIsFlexible) {
                $curShiftHours = (float) ($dayShift['required_hours'] ?? 0) > 0 ? round((float) $dayShift['required_hours'], 2) : 8.0;
            }

            $baseStatue = in_array($day->format('N'), $curWorkingDays) ? 1 : 0;

            if ($curIsFlexible) {
                [$statue, $curhours, $fpin, $fpout] = payroll_flexible_day($conn, $employeeId, $day, $curDayCutoff, $curDupMinutes, $curShiftHours, $curSingleFpRule, $baseStatue);
            } else {
                [$statue, $curhours, $fpin, $fpout] = payroll_fixed_day($conn, $employeeId, $day, $dayShift, $curCrossMidnight, $curShiftStartTs, $curShiftEndTs, $curShiftHours, $curSingleFpRule, $baseStatue);
            }
            $defhours = $baseStatue ? $curShiftHours : 0;

            $t['days']++;
            if ($baseStatue === 0) {
                $t['non_working']++;
            }
            if ($statue === 0) {
                $t['holidays']++;
            } elseif ($statue === 1) {
                $t['absent']++;
            } else {
                $t['att']++;
                $t['hours'] += $curhours;
            }
            if ($statue !== 0) {
                $diff = $curhours - $defhours;
                if ($diff > 0) {
                    $t['extra_daily'] += $diff;
                }
                $t['period_diff'] += $diff;
            }

            $realdue = floor($hourRate * $curhours);
            $dayShiftId = (int) $dayShift['id'];
            $conn->query("INSERT INTO attlog
                (employee, day, starttime, endtime, fpin, fpout, defhours, curhours, dueforhour, realdue, statue, shift_id)
                VALUES ($employeeId, '$curday', " . payroll_sql_value($conn, $curShiftStart) . ", " . payroll_sql_value($conn, $curShiftEnd) . ",
                " . payroll_sql_value($conn, $fpin) . ", " . payroll_sql_value($conn, $fpout) . ",
                $defhours, $curhours, $hourRate, $realdue, $statue, $dayShiftId)");

            $day->modify('+1 day');
        }

        $workdays = $t['days'] - $t['non_working'];
        $exphours = round($shiftHours * $workdays, 2);
        $accualhours = round($t['hours'], 2);
        $extDaily = round($t['extra_daily'], 2);
        $extPeriod = round($t['period_diff'], 2);

        switch ($entTybe) {
            case EntitlesSeeder::HOURS_DAILY_EXTRA:
                $entitle = $hourRate * $accualhours + $extDaily * $hourRate * ($hourExtra - 1);
                break;
            case EntitlesSeeder::HOURS_PERIOD_EXTRA:
                $entitle = $extPeriod > 0
                    ? ($accualhours - $extPeriod) * $hourRate + $extPeriod * $hourRate * $hourExtra
                    : $accualhours * $hourRate;
                break;
            case EntitlesSeeder::ATTENDANCE_DAYS:
                $entitle = $t['att'] * $dayRate;
                break;
            case EntitlesSeeder::PRODUCTION:
                $rowprod = $conn->query("SELECT COALESCE(SUM(value), 0) AS v FROM productions
                    WHERE emp_id = $employeeId AND isdeleted = 0 AND date >= '$startdate' AND date <= '$enddate'")->fetch_assoc();
                $entitle = (float) $rowprod['v'];
                break;
            case EntitlesSeeder::HOURS_ONLY:
            default:
                $entitle = $hourRate * $accualhours;
        }

        $holidayPay = (employee_is_salary_monthly($rowemp) && $entTybe !== EntitlesSeeder::PRODUCTION)
            ? round($t['non_working'] * $dayRate, 2)
            : 0.0;
        $entitle = round($entitle + $holidayPay, 2);

        $sums = payroll_sum_for_period($conn, $employeeId, $startdate, $enddate, $entitle);
        $netPay = payroll_net_pay($entitle, $sums);

        $entName = EntitlesSeeder::getPresets()[$entTybe]['tybe'];
        $info = " احتساب الرواتب من يوم $startdate الي يوم $enddate بنظام الاستحقاق $entName"
            . ($holidayPay > 0 ? ' + إجازات مدفوعة' : '');
        $infoSql = $conn->real_escape_string($info);

        $conn->query("INSERT INTO attdocs
            (empid, fromdate, todate, alldays, workdays, exphours, accualhours, attdays, absdays, holidays, earlyminits, info,
             entitle, holiday_pay, bonus, insurance, tax, deduction, advance, net_pay)
            VALUES ($employeeId, '$startdate', '$enddate', {$t['days']}, $workdays, $exphours, $accualhours, {$t['att']}, {$t['absent']}, {$t['holidays']}, 0, '$infoSql',
             $entitle, $holidayPay, {$sums['bonus']}, {$sums['insurance']}, {$sums['tax']}, {$sums['deduction']}, {$sums['advance']}, $netPay)");
        $docid = (int) $conn->insert_id;

        $conn->query("UPDATE attlog SET attdoc = $docid WHERE employee = $employeeId AND day >= '$startdate' AND day <= '$enddate'");
        $conn->query("INSERT INTO `process`(`type`) VALUES ('add calcsalary')");

        $conn->commit();
    } catch (Throwable $e) {
        $conn->rollback();
        return $fail('db', 'خطأ أثناء الحفظ: ' . $e->getMessage());
    }

    return [
        'ok' => true,
        'name' => $name,
        'docid' => $docid,
        'days' => $t['days'],
        'attdays' => $t['att'],
        'absdays' => $t['absent'],
        'hours' => $accualhours,
        'holiday_pay' => $holidayPay,
        'entitle' => $entitle,
        'net_pay' => $netPay,
    ];
}
