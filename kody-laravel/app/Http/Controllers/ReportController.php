<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use App\Models\Account;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function accountStatement(Request $request)
    {
        $accounts = Account::active()->get();
        $accountId = $request->input('account_id');
        $fromDate = $request->input('from_date', Carbon::now()->startOfMonth()->toDateString());
        $toDate = $request->input('to_date', Carbon::now()->toDateString());

        $statement = null;
        if ($accountId) {
            $statement = $this->reportService->getAccountStatement($accountId, $fromDate, $toDate);
        }

        return Inertia::render('Reports/AccountStatement', [
            'accounts' => $accounts,
            'filters' => [
                'account_id' => $accountId,
                'from_date' => $fromDate,
                'to_date' => $toDate,
            ],
            'statement' => $statement
        ]);
    }

    public function trialBalance(Request $request)
    {
        $fromDate = $request->input('from_date', Carbon::now()->startOfYear()->toDateString());
        $toDate = $request->input('to_date', Carbon::now()->toDateString());

        $balances = $this->reportService->getTrialBalance($fromDate, $toDate);

        return Inertia::render('Reports/TrialBalance', [
            'balances' => $balances,
            'filters' => [
                'from_date' => $fromDate,
                'to_date' => $toDate,
            ]
        ]);
    }

    public function profitLoss(Request $request)
    {
        $fromDate = $request->input('from_date', Carbon::now()->startOfMonth()->toDateString());
        $toDate = $request->input('to_date', Carbon::now()->toDateString());

        $data = $this->reportService->getProfitLoss($fromDate, $toDate);

        return Inertia::render('Reports/ProfitLoss', [
            'reportData' => $data,
            'filters' => [
                'from_date' => $fromDate,
                'to_date' => $toDate,
            ]
        ]);
    }
}
