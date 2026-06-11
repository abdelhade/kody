<?php
/**
 * قواعد الوردية عند وجود بصمة حضور أو انصراف فقط (بدون البصمتين).
 */

function ensure_shift_single_fp_schema(mysqli $conn): void
{
    static $done = false;
    if ($done) {
        return;
    }
    $done = true;
    $check = $conn->query("SHOW COLUMNS FROM shifts LIKE 'single_fp_rule'");
    if ($check && $check->num_rows === 0) {
        $conn->query("ALTER TABLE shifts ADD COLUMN single_fp_rule VARCHAR(20) NOT NULL DEFAULT 'half' AFTER workingdays");
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
