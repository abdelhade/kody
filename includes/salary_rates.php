<?php

/**
 * Salary rate helpers based on employee calc_type (monthly | daily).
 * Monthly: daily rate = salary / days in reference month.
 * Daily:    daily rate = salary (entered amount is already per day).
 */

function employee_is_salary_daily(array $employee): bool
{
    return isset($employee['calc_type']) && $employee['calc_type'] === 'daily';
}

function employee_is_salary_monthly(array $employee): bool
{
    return !employee_is_salary_daily($employee);
}

function employee_month_days(?string $referenceDate = null): int
{
    if ($referenceDate) {
        return (int) date('t', strtotime($referenceDate));
    }

    return (int) date('t');
}

function employee_daily_rate(array $employee, ?string $referenceDate = null): float
{
    $salary = (float) ($employee['salary'] ?? 0);

    if (employee_is_salary_daily($employee)) {
        return $salary;
    }

    $monthDays = employee_month_days($referenceDate);

    return $monthDays > 0 ? $salary / $monthDays : 0.0;
}

function employee_hourly_rate(array $employee, float $shiftHours, ?string $referenceDate = null): float
{
    if ($shiftHours <= 0) {
        $shiftHours = 1;
    }

    return employee_daily_rate($employee, $referenceDate) / $shiftHours;
}

function employee_salary_period_label(array $employee): string
{
    return employee_is_salary_daily($employee) ? 'يومي' : 'شهري';
}
