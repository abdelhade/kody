<?php

namespace App\Services;

use App\Models\JournalEntry;
use App\Models\Account;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function getAccountStatement($accountId, $fromDate, $toDate)
    {
        $account = Account::find($accountId);
        if (!$account) return null;

        // Opening balance
        $openingQuery = JournalEntry::active()
            ->where('account_id', $accountId);
        
        if ($fromDate) {
            $openingQuery->whereDate('crtime', '<', $fromDate);
        }

        $openingDebit = $openingQuery->sum('debit');
        $openingCredit = $openingQuery->sum('credit');
        $openingBalance = $openingDebit - $openingCredit;

        // Transactions
        $transactionsQuery = JournalEntry::active()
            ->where('account_id', $accountId)
            ->orderBy('crtime', 'asc');

        if ($fromDate) {
            $transactionsQuery->whereDate('crtime', '>=', $fromDate);
        }
        if ($toDate) {
            $transactionsQuery->whereDate('crtime', '<=', $toDate);
        }

        $transactions = $transactionsQuery->get();

        // Calculate running balance
        $runningBalance = $openingBalance;
        $formattedTransactions = $transactions->map(function ($t) use (&$runningBalance) {
            $runningBalance += ($t->debit - $t->credit);
            $t->running_balance = $runningBalance;
            return $t;
        });

        return [
            'account' => $account,
            'opening_balance' => $openingBalance,
            'transactions' => $formattedTransactions,
            'closing_balance' => $runningBalance,
            'total_debit' => $transactions->sum('debit'),
            'total_credit' => $transactions->sum('credit')
        ];
    }

    public function getTrialBalance($fromDate = null, $toDate = null)
    {
        $query = DB::table('journal_entries')
            ->join('acc_head', 'journal_entries.account_id', '=', 'acc_head.id')
            ->select(
                'acc_head.id',
                'acc_head.aname',
                DB::raw('SUM(journal_entries.debit) as total_debit'),
                DB::raw('SUM(journal_entries.credit) as total_credit')
            )
            ->where(function($q) {
                $q->where('journal_entries.isdeleted', 0)
                  ->orWhereNull('journal_entries.isdeleted');
            })
            ->groupBy('acc_head.id', 'acc_head.aname');

        if ($fromDate) {
            $query->whereDate('journal_entries.crtime', '>=', $fromDate);
        }
        if ($toDate) {
            $query->whereDate('journal_entries.crtime', '<=', $toDate);
        }

        $balances = $query->get()->map(function ($row) {
            $net = $row->total_debit - $row->total_credit;
            $row->balance = $net;
            return $row;
        });

        return $balances;
    }

    public function getProfitLoss($fromDate = null, $toDate = null)
    {
        // This is a simplified P&L based on account types
        // Revenue (Sales) vs Expenses/COGS
        // In Kody, accounts have typ or we can use specific IDs. 
        // For demonstration, we aggregate Trial Balance and group by a hypothetical type.

        $trialBalance = $this->getTrialBalance($fromDate, $toDate);

        // Assume account typ=3 is Revenue, typ=4 is Expense, etc.
        // For now we just return mock aggregated data based on standard setups
        $revenues = $trialBalance->filter(function($a) {
            return strpos($a->aname, 'مبيعات') !== false || strpos($a->aname, 'ايرادات') !== false;
        });

        $expenses = $trialBalance->filter(function($a) {
            return strpos($a->aname, 'مصروفات') !== false || strpos($a->aname, 'تكلفة') !== false;
        });

        $totalRevenue = abs($revenues->sum('balance')); // usually credit balance
        $totalExpense = $expenses->sum('balance'); // usually debit balance

        return [
            'revenues' => $revenues->values(),
            'expenses' => $expenses->values(),
            'total_revenue' => $totalRevenue,
            'total_expense' => $totalExpense,
            'net_profit' => $totalRevenue - $totalExpense
        ];
    }
}
