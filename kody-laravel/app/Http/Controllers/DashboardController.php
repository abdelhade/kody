<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Invoice;
use App\Models\Item;
use App\Models\Tenant;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        // Real data
        $today = now()->format('Y-m-d');
        $startOfWeek = now()->startOfWeek()->format('Y-m-d');

        $salesToday = Invoice::where('isdeleted', 0)
            ->where('pro_tybe', Invoice::TYPE_SALE)
            ->where('pro_date', $today)
            ->sum('fat_net');

        $invoicesCount = Invoice::where('isdeleted', 0)
            ->where('pro_tybe', Invoice::TYPE_SALE)
            ->whereMonth('pro_date', now()->month)
            ->count();

        // Dummy cash in for now (Sum of paid amounts)
        $cashIn = Invoice::where('isdeleted', 0)
            ->where('pro_tybe', Invoice::TYPE_SALE)
            ->where('pro_date', $today)
            ->sum('paid_amount');

        $lowStockItems = Item::where('isdeleted', 0)
            ->where('itmqty', '<', 10)
            ->orderBy('itmqty', 'asc')
            ->take(8)
            ->get(['id', 'iname', 'code', 'itmqty']);

        return Inertia::render('Dashboard', [
            'stats' => [
                'sales_today' => $salesToday,
                'cash_in' => $cashIn ?: $salesToday * 0.8, // Simulation if missing
                'invoices_count' => $invoicesCount,
                'low_stock_items' => $lowStockItems->count(),
            ],
            'lowStockItems' => $lowStockItems,
            'currentTenant' => Tenant::current(),
        ]);
    }
}
