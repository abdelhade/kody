<?php
/**
 * Payroll adjustments: bonus, insurance, income tax, deduction.
 * Applied during attendance processing by date range (like productions).
 */

/** المكافأة تُضاف للراتب؛ التأمين والضريبة والخصم تُطرح منه */
function payroll_calc_is_addition(int $tybe): bool
{
    return $tybe === 1;
}

function payroll_calc_type_label(int $tybe, bool $withSign = false): string
{
    $labels = [
        1 => 'مكافأة',
        2 => 'تأمين',
        3 => 'ضريبة دخل',
        4 => 'خصم',
    ];
    $label = $labels[$tybe] ?? '—';
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
 * Sum payroll_calcs for an employee in a date range, grouped by type.
 *
 * @return array{bonus: float, insurance: float, tax: float, deduction: float}
 */
function payroll_sum_for_period(mysqli $conn, int $empId, string $empName, string $startDate, string $endDate, float $baseEntitle): array
{
    $empNameEsc = $conn->real_escape_string($empName);
    $sql = "SELECT calc_tybe, amount, percent FROM payroll_calcs
            WHERE isdeleted = 0
              AND date >= '$startDate' AND date <= '$endDate'
              AND (emp_id = $empId OR emp_name = '$empNameEsc')
            ORDER BY date ASC, id ASC";
    $res = $conn->query($sql);
    $sums = ['bonus' => 0.0, 'insurance' => 0.0, 'tax' => 0.0, 'deduction' => 0.0];
    if (!$res) {
        return $sums;
    }
    while ($row = $res->fetch_assoc()) {
        $val = payroll_calc_line_amount($row, $baseEntitle);
        $tybe = (int) $row['calc_tybe'];
        if ($tybe === 1) {
            $sums['bonus'] += $val;
        } elseif ($tybe === 2) {
            $sums['insurance'] += $val;
        } elseif ($tybe === 3) {
            $sums['tax'] += $val;
        } elseif ($tybe === 4) {
            $sums['deduction'] += $val;
        }
    }
    foreach ($sums as $k => $v) {
        $sums[$k] = round($v, 2);
    }
    return $sums;
}

/**
 * الراتب الصافي = المستحق الأساسي + مكافأة − تأمين − ضريبة − خصم
 */
function payroll_net_pay(float $entitle, array $sums): float
{
    $bonus = abs((float) ($sums['bonus'] ?? 0));
    $insurance = abs((float) ($sums['insurance'] ?? 0));
    $tax = abs((float) ($sums['tax'] ?? 0));
    $deduction = abs((float) ($sums['deduction'] ?? 0));

    return round($entitle + $bonus - $insurance - $tax - $deduction, 2);
}

function ensure_payroll_calcs_schema(mysqli $conn): void
{
    static $done = false;
    if ($done) {
        return;
    }
    $done = true;

    $conn->query("CREATE TABLE IF NOT EXISTS `payroll_calcs` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `snd_id` int(11) NOT NULL,
      `calc_tybe` tinyint(4) NOT NULL DEFAULT 1,
      `date` date NOT NULL,
      `emp_id` int(11) DEFAULT NULL,
      `emp_name` varchar(100) DEFAULT NULL,
      `amount` double NOT NULL DEFAULT 0,
      `percent` double NOT NULL DEFAULT 0,
      `info` varchar(250) DEFAULT NULL,
      `info2` varchar(250) DEFAULT NULL,
      `user` varchar(50) DEFAULT NULL,
      `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
      `isdeleted` tinyint(1) DEFAULT 0,
      `tenant` int(11) DEFAULT 0,
      `branch` int(11) DEFAULT 0,
      PRIMARY KEY (`id`),
      KEY `snd_id` (`snd_id`),
      KEY `emp_date` (`emp_id`, `date`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

    $cols = ['bonus', 'insurance', 'tax', 'deduction', 'net_pay'];
    $check = $conn->query("SHOW COLUMNS FROM attdocs LIKE 'bonus'");
    if ($check && $check->num_rows === 0) {
        $conn->query("ALTER TABLE attdocs
            ADD COLUMN bonus double NOT NULL DEFAULT 0 AFTER entitle,
            ADD COLUMN insurance double NOT NULL DEFAULT 0 AFTER bonus,
            ADD COLUMN tax double NOT NULL DEFAULT 0 AFTER insurance,
            ADD COLUMN deduction double NOT NULL DEFAULT 0 AFTER tax,
            ADD COLUMN net_pay double NOT NULL DEFAULT 0 AFTER deduction");
    }
}
