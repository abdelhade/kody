<?php

namespace App\Services;

use App\Models\Payroll;
use App\Models\Employee;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PayrollService
{
    /**
     * Calculate payroll for a specific employee and period
     */
    public function calculatePayroll(Employee $employee, $startDate, $endDate)
    {
        // 1. Get attendance records for the period
        // For now, this is a placeholder structure based on typical kody logic
        
        $baseSalary = (float)$employee->salary;
        $workdaysExpected = 26; // Default standard, can be dynamic
        $dailyRate = $workdaysExpected > 0 ? $baseSalary / $workdaysExpected : 0;
        
        // Count actual attendance days
        $actualDays = DB::table('attandance')
            ->where('empid', $employee->id)
            ->whereBetween('fpdate', [$startDate, $endDate])
            ->where('isdeleted', '!=', 1)
            ->distinct('fpdate')
            ->count('fpdate');

        // Allowances and deductions
        // In the old system, this comes from attdocs or allowences table
        // We calculate it dynamically here for preview
        
        $entitle = $dailyRate * $actualDays;
        
        // Sample values
        $bonus = 0;
        $insurance = 0;
        $tax = 0;
        $deduction = 0;

        $netPay = $entitle + $bonus - $insurance - $tax - $deduction;

        return [
            'employee_id' => $employee->id,
            'employee_name' => $employee->name,
            'base_salary' => $baseSalary,
            'workdays_expected' => $workdaysExpected,
            'actual_days' => $actualDays,
            'entitle' => $entitle,
            'bonus' => $bonus,
            'insurance' => $insurance,
            'tax' => $tax,
            'deduction' => $deduction,
            'net_pay' => $netPay,
        ];
    }

    /**
     * Process and save payroll document (attdocs)
     */
    public function processPayroll($employeeId, $startDate, $endDate, $data)
    {
        return DB::transaction(function () use ($employeeId, $startDate, $endDate, $data) {
            
            return Payroll::create([
                'empid' => $employeeId,
                'fromdate' => $startDate,
                'todate' => $endDate,
                'workdays' => $data['actual_days'] ?? 0,
                'alldays' => $data['workdays_expected'] ?? 26,
                'entitle' => $data['entitle'] ?? 0,
                'bonus' => $data['bonus'] ?? 0,
                'insurance' => $data['insurance'] ?? 0,
                'tax' => $data['tax'] ?? 0,
                'deduction' => $data['deduction'] ?? 0,
                'net_pay' => $data['net_pay'] ?? 0,
                'info' => 'تم المعالجة الآلية عبر مسير الرواتب',
            ]);
        });
    }
}
