<?php
/**
 * قواعد الوردية: البصمة الواحدة، وفترات السماح (1 و 2) وإجراءات الخصم (وقت عادي / نصف يوم / يوم).
 */

function ensure_shift_single_fp_schema(mysqli $conn): void
{
    static $done = false;
    if ($done) {
        return;
    }
    $done = true;
    // الاعتماد الأساسي على MigrationRunner (011_add_single_fp_rule_to_shifts.sql).
    $check = @$conn->query("SHOW COLUMNS FROM shifts LIKE 'single_fp_rule'");
    if ($check && $check->num_rows === 0) {
        @$conn->query("ALTER TABLE shifts ADD COLUMN single_fp_rule VARCHAR(20) NOT NULL DEFAULT 'half' AFTER workingdays");
    }
}

function ensure_shift_grace_rules_schema(mysqli $conn): void
{
    static $done = false;
    if ($done) {
        return;
    }
    $done = true;
    // الاعتماد الأساسي على MigrationRunner (022_add_shift_grace_rules.sql).
    $check = @$conn->query("SHOW COLUMNS FROM shifts LIKE 'grace1_action'");
    if ($check && $check->num_rows === 0) {
        @$conn->query("ALTER TABLE shifts 
            ADD COLUMN grace1_action VARCHAR(20) NOT NULL DEFAULT 'actual' AFTER earlylimit,
            ADD COLUMN latelimit_2 INT DEFAULT 0 AFTER grace1_action,
            ADD COLUMN earlylimit_2 INT DEFAULT 0 AFTER latelimit_2,
            ADD COLUMN grace2_action VARCHAR(20) NOT NULL DEFAULT 'half' AFTER earlylimit_2");
    }
}

function shift_single_fp_rule_label(string $rule): string
{
    return $rule === 'cancel' ? 'إلغاء اليوم' : 'احتساب نصف يوم';
}

function normalize_single_fp_rule(?string $rule): string
{
    return ($rule === 'cancel') ? 'cancel' : 'half';
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
    if (normalize_single_fp_rule($singleFpRule) === 'cancel') {
        return ['statue' => $baseStatue, 'curhours' => 0.0];
    }
    return ['statue' => 2, 'curhours' => round($defHours / 2, 2)];
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
