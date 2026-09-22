<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Department;
use App\Models\Job;
use App\Models\Attendance;
use App\Models\Payroll;
use App\Services\PayrollService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Carbon;

class HrController extends Controller
{
    protected $payrollService;

    public function __construct(PayrollService $payrollService)
    {
        $this->payrollService = $payrollService;
    }

    // --- Employees ---
    public function employees(Request $request)
    {
        $employees = Employee::with(['departmentRel', 'jobRel'])->active()->paginate(15);
        $departments = Department::where('isdeleted', '!=', 1)->orWhereNull('isdeleted')->get();
        $jobs = Job::all();

        return Inertia::render('Hr/Employees', [
            'employees' => $employees,
            'departments' => $departments,
            'jobs' => $jobs
        ]);
    }

    // --- Attendance ---
    public function attendance(Request $request)
    {
        $date = $request->input('date', Carbon::today()->toDateString());
        
        $attendance = Attendance::with('employee')
            ->whereDate('fpdate', $date)
            ->where(function($q) {
                $q->where('isdeleted', '!=', 1)->orWhereNull('isdeleted');
            })
            ->get();

        $employees = Employee::active()->get();

        return Inertia::render('Hr/Attendance', [
            'attendance' => $attendance,
            'employees' => $employees,
            'filterDate' => $date
        ]);
    }

    // --- Payroll ---
    public function payroll(Request $request)
    {
        $month = $request->input('month', Carbon::today()->format('Y-m'));
        $startDate = Carbon::parse($month . '-01')->startOfMonth()->toDateString();
        $endDate = Carbon::parse($month . '-01')->endOfMonth()->toDateString();

        $payrolls = Payroll::with('employee')
            ->whereBetween('fromdate', [$startDate, $endDate])
            ->where(function($q) {
                $q->where('isdeleted', '!=', 1)->orWhereNull('isdeleted');
            })
            ->get();

        // Calculate preview for an employee if requested
        $previewData = null;
        if ($request->has('preview_emp_id')) {
            $emp = Employee::findOrFail($request->input('preview_emp_id'));
            $previewData = $this->payrollService->calculatePayroll($emp, $startDate, $endDate);
        }

        $employees = Employee::active()->get();

        return Inertia::render('Hr/Payroll', [
            'payrolls' => $payrolls,
            'employees' => $employees,
            'filterMonth' => $month,
            'previewData' => $previewData
        ]);
    }

    public function processPayroll(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'actual_days' => 'required|numeric',
            'entitle' => 'required|numeric',
            'net_pay' => 'required|numeric',
        ]);

        $this->payrollService->processPayroll(
            $request->employee_id,
            $request->start_date,
            $request->end_date,
            $request->all()
        );

        return redirect()->back()->with('success', 'تم اعتماد مسير الراتب للموظف بنجاح.');
    }
}
