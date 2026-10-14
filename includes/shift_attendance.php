<?php
/**
 * قواعد الوردية: البصمة الواحدة، وفترات السماح (1 و 2) وإجراءات الخصم (وقت عادي / نصف يوم / يوم).
 */

function shift_single_fp_rule_label(string $rule): string
{
    switch ($rule) {
        case 'cancel':
            return 'إلغاء اليوم';
        case 'none':
            return 'لا تعليق';
        case 'half':
        default:
            return 'احتساب نصف يوم';
    }
}

function normalize_single_fp_rule(?string $rule): string
{
    $rule = strtolower(trim((string)$rule));
    if ($rule === 'cancel') {
        return 'cancel';
    }
    if ($rule === 'none' || $rule === 'no_action' || $rule === 'no_comment') {
        return 'none';
    }
    return 'half';
}

/**
 * @return array{is_flexible:int, required_hours:float, day_cutoff:string, dup_fp_minutes:int}
 */
function shift_flexible_input(array $post): array
{
    $cutoff = (string) ($post['day_cutoff'] ?? '');
    if (!preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $cutoff)) {
        $cutoff = '06:00';
    }
    return [
        'is_flexible' => !empty($post['is_flexible']) ? 1 : 0,
        'required_hours' => max(0.0, round((float) ($post['required_hours'] ?? 8), 2)),
        'day_cutoff' => strlen($cutoff) === 5 ? $cutoff . ':00' : $cutoff,
        'dup_fp_minutes' => max(0, (int) ($post['dup_fp_minutes'] ?? 2)),
    ];
}

function normalize_grace_action(?string $action, string $default = 'actual'): string
{
    $allowed = ['actual', 'half', 'full'];
    $action = strtolower(trim((string)$action));
    return in_array($action, $allowed, true) ? $action : $default;
}

function shift_grace_action_label(string $action): string
{
    switch ($action) {
        case 'half':
            return 'خصم نصف يوم';
        case 'full':
            return 'خصم يوم كامل';
        case 'actual':
        default:
            return 'خصم وقت عادي';
    }
}

/**
 * تطبيق البصمة التلقائية عند اختيار "لا تعليق":
 * - عند تسجيل حضور فقط: تُحدد بصمة الانصراف عند نهاية الانصراف (outend).
 * - عند تسجيل انصراف فقط: تُحدد بصمة الحضور عند نهاية الحضور (inend).
 *
 * @param string|null $fpin
 * @param string|null $fpout
 * @param array $shiftRow
 * @param bool $isCrossMidnight
 * @return array{fpin: string|null, fpout: string|null, filled: bool}
 */
function apply_single_fp_no_comment_fallback(
    ?string $fpin,
    ?string $fpout,
    array $shiftRow,
    bool $isCrossMidnight = false
): array {
    $hasIn = ($fpin !== null && $fpin !== '');
    $hasOut = ($fpout !== null && $fpout !== '');

    if ($hasIn xor $hasOut) {
        if ($hasIn && !$hasOut) {
            $fallbackOut = !empty($shiftRow['outend']) ? $shiftRow['outend'] : ($shiftRow['shiftend'] ?? '');
            if ($isCrossMidnight && !empty($fallbackOut)) {
                $fpoDt = new DateTime($fallbackOut);
                $hours = (int)$fpoDt->format('H');
                $minutesSeconds = $fpoDt->format(':i:s');
                $fpout = ($hours + 24) . $minutesSeconds;
            } else {
                $fpout = $fallbackOut;
            }
            return ['fpin' => $fpin, 'fpout' => $fpout, 'filled' => true];
        } else {
            $fallbackIn = !empty($shiftRow['inend']) ? $shiftRow['inend'] : ($shiftRow['shiftstart'] ?? '');
            return ['fpin' => $fallbackIn, 'fpout' => $fpout, 'filled' => true];
        }
    }

    return ['fpin' => $fpin, 'fpout' => $fpout, 'filled' => false];
}

/**
 * @return array{statue: int, curhours: float}
 */
function resolve_single_fp_attendance(
    bool $hasFpin,
    bool $hasFpout,
    float $defHours,
    string $singleFpRule,
    int $baseStatue
): array {
    if ($hasFpin && $hasFpout) {
        return ['statue' => 2, 'curhours' => -1.0];
    }
    if (!$hasFpin && !$hasFpout) {
        return ['statue' => $baseStatue, 'curhours' => 0.0];
    }
    $normRule = normalize_single_fp_rule($singleFpRule);
    if ($normRule === 'cancel') {
        return ['statue' => $baseStatue, 'curhours' => 0.0];
    }
    if ($normRule === 'none') {
        return ['statue' => 2, 'curhours' => round($defHours, 2)];
    }
    return ['statue' => 2, 'curhours' => round($defHours / 2, 2)];
}

/**
 * يوم عمل في وردية مرنة.
 * البصمات اللي بين بعض أقل من $dupMinutes بتتعد بصمة واحدة، والباقي أزواج دخول/خروج وتتجمع مددها.
 * بصمة واحدة بس → قاعدة البصمة الواحدة. عدد فردي أكبر من 1 → آخر بصمة بدون زوج بتتجاهل.
 *
 * @param list<int> $timestamps بصمات يوم العمل مرتبة تصاعديًا
 * @return array{statue: int, curhours: float, first: ?int, last: ?int}
 */
function resolve_flexible_attendance(
    array $timestamps,
    float $requiredHours,
    int $dupMinutes,
    string $singleFpRule,
    int $baseStatue
): array {
    $punches = [];
    foreach ($timestamps as $ts) {
        if (!empty($punches) && ($ts - end($punches)) < max(0, $dupMinutes) * 60) {
            continue;
        }
        $punches[] = $ts;
    }

    $count = count($punches);
    if ($count === 0) {
        return ['statue' => $baseStatue, 'curhours' => 0.0, 'first' => null, 'last' => null];
    }
    if ($count === 1) {
        $single = resolve_single_fp_attendance(true, false, $requiredHours, $singleFpRule, $baseStatue);
        return $single + ['first' => $punches[0], 'last' => null];
    }

    $seconds = 0;
    for ($i = 0; $i + 1 < $count; $i += 2) {
        $seconds += $punches[$i + 1] - $punches[$i];
    }
    $hours = round($seconds / 3600, 2);
    $lastPaired = $punches[$count - ($count % 2) - 1];

    return [
        'statue' => $hours > 0 ? 2 : $baseStatue,
        'curhours' => $hours,
        'first' => $punches[0],
        'last' => $lastPaired,
    ];
}

/**
 * حساب حالة وساعات حضور اليوم عند وجود بصمتي دخول وخروج مع فترات السماح (1 و 2) وإجراءات الخصم.
 *
 * @param string $fpin بصمة الدخول
 * @param string $fpout بصمة الخروج
 * @param int $expected_start وقت بداية الوردية المتوقع (timestamp)
 * @param int $expected_end وقت نهاية الوردية المتوقع (timestamp)
 * @param float $defHours ساعات العمل الرسمية للوردية
 * @param array $shiftRow بيانات الوردية من جدول shifts
 * @param int $baseStatue الحالة الافتراضية لليوم (1 ليوم عمل، 0 لعطلة)
 * @return array{statue: int, curhours: float, in_action: string, out_action: string}
 */
function resolve_shift_presence_with_grace(
    string $fpin,
    string $fpout,
    int $expected_start,
    int $expected_end,
    float $defHours,
    array $shiftRow,
    int $baseStatue
): array {
    list($h, $m, $s) = array_pad(explode(':', (string)$fpout), 3, '00');
    $h = (int)$h;
    if ($h >= 24) {
        $h -= 24;
        $fpout_normalized = sprintf("%02d:%02d:%02d", $h, $m, $s);
        $time3 = strtotime($fpout_normalized) + 86400;
    } else {
        $time3 = strtotime($fpout);
    }
    $time4 = strtotime($fpin);

    if ($expected_end <= $expected_start) {
        $expected_end += 86400;
    }

    $latelimit_1 = max(0, (int)($shiftRow['latelimit'] ?? 0));
    $earlylimit_1 = max(0, (int)($shiftRow['earlylimit'] ?? 0));
    $grace1_action = normalize_grace_action($shiftRow['grace1_action'] ?? 'actual', 'actual');

    $latelimit_2 = max(0, (int)($shiftRow['latelimit_2'] ?? 0));
    $earlylimit_2 = max(0, (int)($shiftRow['earlylimit_2'] ?? 0));
    $grace2_action = normalize_grace_action($shiftRow['grace2_action'] ?? 'half', 'half');

    $ignore_early = !empty($shiftRow['ignore_early_in']);
    $ignore_late = !empty($shiftRow['ignore_late_out']);

    // ── 1. معالجة بصمة الحضور (In) ──
    $diff_in = $time4 - $expected_start; // موجب = تأخير
    $effective_time4 = $time4;
    $in_action = 'none';

    if ($diff_in > 0) {
        $late_min = $diff_in / 60;
        if ($latelimit_1 > 0 && $late_min <= $latelimit_1) {
            // ضمن فترة السماح الأولى: لا خصم
            $effective_time4 = $expected_start;
            $in_action = 'none';
        } elseif ($latelimit_2 > 0 && $late_min > $latelimit_2) {
            // تجاوز فترة السماح الثانية
            $in_action = $grace2_action;
            if ($in_action === 'actual') {
                $effective_time4 = $time4;
            }
        } else {
            // تجاوز فترة السماح الأولى (ضمن فترة السماح الثانية أو لا توجد فترة ثانية)
            $in_action = $grace1_action;
            if ($in_action === 'actual') {
                $effective_time4 = $time4;
            }
        }
    } else {
        // حضور مبكر أو على الوقت
        if ($ignore_early || abs($diff_in) <= ($earlylimit_1 * 60)) {
            $effective_time4 = $expected_start;
        } else {
            $effective_time4 = $time4;
        }
        $in_action = 'none';
    }

    // ── 2. معالجة بصمة الانصراف (Out) ──
    $diff_out = $expected_end - $time3; // موجب = انصراف مبكر
    $effective_time3 = $time3;
    $out_action = 'none';

    if ($diff_out > 0) {
        $early_out_min = $diff_out / 60;
        if ($earlylimit_1 > 0 && $early_out_min <= $earlylimit_1) {
            // ضمن فترة السماح الأولى للانصراف: لا خصم
            $effective_time3 = $expected_end;
            $out_action = 'none';
        } elseif ($earlylimit_2 > 0 && $early_out_min > $earlylimit_2) {
            // تجاوز فترة السماح الثانية للانصراف
            $out_action = $grace2_action;
            if ($out_action === 'actual') {
                $effective_time3 = $time3;
            }
        } else {
            // تجاوز فترة السماح الأولى للانصراف
            $out_action = $grace1_action;
            if ($out_action === 'actual') {
                $effective_time3 = $time3;
            }
        }
    } else {
        // انصراف متأخر أو على الوقت
        if ($ignore_late || abs($diff_out) <= ($latelimit_1 * 60)) {
            $effective_time3 = $expected_end;
        } else {
            $effective_time3 = $time3;
        }
        $out_action = 'none';
    }

    // ── 3. تطبيق الإجراءات والخصم ──
    // إذا كان أحد الإجراءين خصم يوم كامل:
    if ($in_action === 'full' || $out_action === 'full') {
        return [
            'statue' => $baseStatue,
            'curhours' => 0.0,
            'in_action' => $in_action,
            'out_action' => $out_action,
        ];
    }

    // إذا كان كلاهما خصم نصف يوم: نص يوم + نص يوم = خصم يوم كامل
    if ($in_action === 'half' && $out_action === 'half') {
        return [
            'statue' => $baseStatue,
            'curhours' => 0.0,
            'in_action' => $in_action,
            'out_action' => $out_action,
        ];
    }

    // الساعات الفعلية بعد خصم الدقائق (إن وجدت)
    $actual_worked_hours = max(0.0, round(($effective_time3 - $effective_time4) / 3600, 2));

    // إذا كان أحدهما خصم نصف يوم:
    if ($in_action === 'half' || $out_action === 'half') {
        $half_day_hours = round($defHours / 2, 2);
        $curhours = min($half_day_hours, $actual_worked_hours);
        return [
            'statue' => 2,
            'curhours' => $curhours,
            'in_action' => $in_action,
            'out_action' => $out_action,
        ];
    }

    // الحالة الطبيعية (خصم وقت عادي أو لا خصم):
    return [
        'statue' => 2,
        'curhours' => $actual_worked_hours,
        'in_action' => $in_action,
        'out_action' => $out_action,
    ];
}
