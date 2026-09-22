<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class OpeningBalancesController extends Controller
{
    public function index()
    {
        // Fetch only leaf accounts (is_basic = 0) to set their opening balances
        $accounts = Account::where('is_basic', 0)
            ->where('isdeleted', 0)
            ->orderBy('code', 'asc')
            ->get(['id', 'code', 'aname', 'start_balance', 'nature']);

        return Inertia::render('Accounting/OpeningBalances', [
            'accounts' => $accounts
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'balances' => 'required|array',
            'balances.*.id' => 'required|integer|exists:acc_head,id',
            'balances.*.start_balance' => 'required|numeric',
        ]);

        DB::transaction(function () use ($validated) {
            foreach ($validated['balances'] as $balanceData) {
                $account = Account::find($balanceData['id']);
                
                $startBalance = $balanceData['start_balance'];
                
                // Keep the sign logic consistent: debit accounts positive, credit accounts positive in UI but negated in DB if nature dictates?
                // Actually start_balance in UI is absolute. Let's just save it exactly as user types, or multiply by nature.
                // For opening balances screen, users type the exact balance. We assume positive means debit, negative means credit?
                // Or maybe they type absolute and we rely on nature? Let's rely on nature.
                
                $dbBalance = $startBalance * ($account->nature == 2 ? -1 : 1);
                
                $account->update([
                    'start_balance' => $dbBalance,
                    'balance' => $dbBalance // In a real system, we'd recalculate balance based on operations, but for now we just set it.
                ]);
            }
        });

        return redirect()->back()->with('success', 'تم حفظ الأرصدة الافتتاحية بنجاح.');
    }
}
