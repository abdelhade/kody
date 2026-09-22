<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Services\AccountService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AccountController extends Controller
{
    public function __construct(
        protected AccountService $accountService
    ) {}

    /**
     * Display chart of accounts and parties.
     */
    public function index(Request $request): Response
    {
        $tab = $request->input('tab', 'tree');
        $search = trim($request->input('search', ''));

        $query = Account::where('isdeleted', 0);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('aname', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }

        $clients = (clone $query)->clients()->orderBy('code')->paginate(20, ['*'], 'clients_page');
        $suppliers = (clone $query)->suppliers()->orderBy('code')->paginate(20, ['*'], 'suppliers_page');
        $funds = (clone $query)->funds()->orderBy('code')->get();
        $banks = (clone $query)->banks()->orderBy('code')->get();

        // Hierarchical Tree for main chart
        $treeAccounts = Account::where('isdeleted', 0)
            ->where('parent_id', 0)
            ->with(['children.children.children'])
            ->orderBy('code')
            ->get();

        $stats = [
            'total_clients'    => Account::clients()->count(),
            'total_suppliers'  => Account::suppliers()->count(),
            'total_funds_cash' => (float) Account::funds()->sum('balance'),
            'total_banks_cash' => (float) Account::banks()->sum('balance'),
        ];

        // Parent accounts for dropdown in create modal
        $parentAccounts = Account::where('isdeleted', 0)
            ->whereIn('parent_id', [0, 1, 2, 3, 4, 5, 7, 8, 18, 19, 20, 33, 37])
            ->orderBy('code')
            ->get(['id', 'code', 'aname']);

        return Inertia::render('Accounts/Index', [
            'tab'            => $tab,
            'clients'        => $clients,
            'suppliers'      => $suppliers,
            'funds'          => $funds,
            'banks'          => $banks,
            'treeAccounts'   => $treeAccounts,
            'stats'          => $stats,
            'parentAccounts' => $parentAccounts,
            'filters'        => [
                'search' => $search,
                'tab'    => $tab,
            ],
        ]);
    }

    /**
     * Store a new account or client/supplier.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'aname'         => 'required|string|max:100',
            'code'          => 'nullable|string|max:30',
            'parent_id'     => 'required|integer',
            'phone'         => 'nullable|string|max:50',
            'address'       => 'nullable|string|max:200',
            'start_balance' => 'nullable|numeric',
            'info'          => 'nullable|string|max:250',
        ], [
            'aname.required'     => 'اسم الحساب أو العميل مطلوب.',
            'parent_id.required' => 'يرجى اختيار الحساب الرئيسي التابع له.',
        ]);

        $this->accountService->createAccount($validated);

        return redirect()->back()->with('success', 'تم إنشاء الحساب بنجاح في دليل الحسابات.');
    }
}
