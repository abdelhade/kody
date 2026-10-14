<?php
/**
 * Payroll adjustments: bonus, insurance, income tax, deduction, short advance.
 * Applied during attendance processing by date range (like productions).
 */

const PAYROLL_CALC_BONUS = 1;
const PAYROLL_CALC_INSURANCE = 2;
const PAYROLL_CALC_TAX = 3;
const PAYROLL_CALC_DEDUCTION = 4;
const PAYROLL_CALC_ADVANCE = 5;

/**
 * @return array<int, array{key:string, label:string}>
 */
function payroll_calc_types(): array
{
    return [
        PAYROLL_CALC_BONUS => ['key' => 'bonus', 'label' => 'مكافأة'],
        PAYROLL_CALC_INSURANCE => ['key' => 'insurance', 'label' => 'تأمين'],
        PAYROLL_CALC_TAX => ['key' => 'tax', 'label' => 'ضريبة دخل'],
        PAYROLL_CALC_DEDUCTION => ['key' => 'deduction', 'label' => 'خصم'],
        PAYROLL_CALC_ADVANCE => ['key' => 'advance', 'label' => 'سلفة قصيرة'],
    ];
}

function payroll_calc_normalize_type(int $tybe): int
{
    return isset(payroll_calc_types()[$tybe]) ? $tybe : PAYROLL_CALC_BONUS;
}

/** المكافأة تُضاف للراتب؛ التأمين والضريبة والخصم والسلفة تُطرح منه */
function payroll_calc_is_addition(int $tybe): bool
{
    return $tybe === PAYROLL_CALC_BONUS;
}

function payroll_calc_type_label(int $tybe, bool $withSign = false): string
{
    $label = payroll_calc_types()[$tybe]['label'] ?? '—';
    if (!$withSign) {
        return $label;
    }
    return payroll_calc_is_addition($tybe) ? "$label (+)" : "$label (-)";
}

function payroll_calc_signed_amount(int $tybe, float $amount): float
{
    $amount = abs($amount);
    return payroll_calc_is_addition($tybe) ? $amount : -$amount;
}

function payroll_calc_line_amount(array $row, float $baseEntitle): float
{
    $percent = (float) ($row['percent'] ?? 0);
    if ($percent > 0) {
        return round($baseEntitle * $percent / 100, 2);
    }
    return round((float) ($row['amount'] ?? 0), 2);
}

/**
 * @return array{bonus: float, insurance: float, tax: float, deduction: float, advance: float}
 */
function payroll_empty_sums(): array
{
    $sums = [];
    foreach (payroll_calc_types() as $t) {
        $sums[$t['key']] = 0.0;
    }
    return $sums;
}

/**
 * Sum payroll_calcs for an employee in a date range, grouped by type.
 *
 * @return array{bonus: float, insurance: float, tax: float, deduction: float, advance: float}
 */
function payroll_sum_for_period(mysqli $conn, int $empId, string $startDate, string $endDate, float $baseEntitle): array
{
    $types = payroll_calc_types();
    $sums = payroll_empty_sums();
    $start = $conn->real_escape_string($startDate);
    $end = $conn->real_escape_string($endDate);
    $res = $conn->query("SELECT calc_tybe, amount, percent FROM payroll_calcs
            WHERE isdeleted = 0
              AND emp_id = $empId
              AND date >= '$start' AND date <= '$end'
            ORDER BY date ASC, id ASC");
    while ($res && ($row = $res->fetch_assoc())) {
        $tybe = (int) $row['calc_tybe'];
        if (!isset($types[$tybe])) {
            continue;
        }
        $sums[$types[$tybe]['key']] += payroll_calc_line_amount($row, $baseEntitle);
    }
    foreach ($sums as $k => $v) {
        $sums[$k] = round($v, 2);
    }
    return $sums;
}

/**
 * الراتب الصافي = المستحق الأساسي + مكافأة − تأمين − ضريبة − خصم − سلف قصيرة
 */
function payroll_net_pay(float $entitle, array $sums): float
{
    $net = $entitle;
    foreach (payroll_calc_types() as $tybe => $t) {
        $val = abs((float) ($sums[$t['key']] ?? 0));
        $net += payroll_calc_is_addition($tybe) ? $val : -$val;
    }
    return round($net, 2);
}
