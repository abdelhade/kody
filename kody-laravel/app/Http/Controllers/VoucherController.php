<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Carbon\Carbon;
use App\Models\Account; // Assuming we use AccHead mapped to Account model

class VoucherController extends Controller
{
    /**
     * Display a listing of the vouchers.
     */
    public function index(Request $request)
    {
        $type = $request->query('type', 1); // 1 = Receipt, 2 = Payment
        $vouchers = DB::table('myvouchers')
            ->where('tybe', $type)
            ->where('isdeleted', 0)
            ->orderBy('id', 'desc')
            ->paginate(10);

        // Map account names
        $accountIds = collect($vouchers->items())->pluck('account')->merge(collect($vouchers->items())->pluck('fund_account'))->unique();
        $accounts = DB::table('acc_head')->whereIn('id', $accountIds)->pluck('aname', 'id');

        $mappedVouchers = collect($vouchers->items())->map(function ($v) use ($accounts) {
            $v->account_name = $accounts[$v->account] ?? 'Unknown';
            $v->fund_account_name = $accounts[$v->fund_account] ?? 'Unknown';
            return $v;
        });

        return Inertia::render('Accounting/Vouchers/Index', [
            'vouchers' => [
                'data' => $mappedVouchers,
                'links' => $vouchers->linkCollection(),
                'current_page' => $vouchers->currentPage(),
                'last_page' => $vouchers->lastPage(),
            ],
            'type' => $type,
        ]);
    }

    /**
     * Store a newly created voucher in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tybe' => 'required|in:1,2', // 1=Receipt (سند قبض), 2=Payment (سند صرف)
            'val' => 'required|numeric|min:0.01',
            'account' => 'required|integer',       // External party (Customer/Supplier/Expense)
            'fund_account' => 'required|integer',  // Treasury/Bank
            'vdate' => 'required|date',
            'info' => 'nullable|string|max:200',
        ]);

        DB::transaction(function () use ($validated, $request) {
            // Create Voucher
            $voucherId = DB::table('myvouchers')->insertGetId([
                'vdate' => $validated['vdate'],
                'tybe' => $validated['tybe'],
                'val' => $validated['val'],
                'account' => $validated['account'],
                'fund_account' => $validated['fund_account'],
                'voucher_id' => 'V-' . time(), // Unique Code
                'info' => $validated['info'],
                'user' => auth()->id() ?? 1,
                'tenant' => \App\Models\Tenant::current()?->id ?? 0,
            ]);

            // Add Journal Entries (القيود المحاسبية)
            // If Receipt (1): Fund Account (Debit) / Party (Credit)
            // If Payment (2): Party (Debit) / Fund Account (Credit)
            
            $debitAcc  = $validated['tybe'] == 1 ? $validated['fund_account'] : $validated['account'];
            $creditAcc = $validated['tybe'] == 1 ? $validated['account'] : $validated['fund_account'];

            // Debit Entry
            DB::table('journal_entries')->insert([
                'jdate' => $validated['vdate'],
                'account_id' => $debitAcc,
                'debit' => $validated['val'],
                'credit' => 0,
                'info' => ($validated['tybe'] == 1 ? 'سند قبض' : 'سند صرف') . ' #' . $voucherId . ($validated['info'] ? ' - ' . $validated['info'] : ''),
                'user' => auth()->id() ?? 1,
            ]);

            // Credit Entry
            DB::table('journal_entries')->insert([
                'jdate' => $validated['vdate'],
                'account_id' => $creditAcc,
                'debit' => 0,
                'credit' => $validated['val'],
                'info' => ($validated['tybe'] == 1 ? 'سند قبض' : 'سند صرف') . ' #' . $voucherId . ($validated['info'] ? ' - ' . $validated['info'] : ''),
                'user' => auth()->id() ?? 1,
            ]);
        });

        return redirect()->back()->with('success', 'تم حفظ السند وإنشاء القيود بنجاح.');
    }
}
