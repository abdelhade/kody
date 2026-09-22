<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Invoice;
use App\Models\Item;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class SystemStatusController extends Controller
{
    /**
     * Check system connectivity and summarize database entities.
     */
    public function status(): JsonResponse
    {
        try {
            DB::connection()->getPdo();
            $dbConnected = true;
            $dbName = DB::connection()->getDatabaseName();
        } catch (\Exception $e) {
            $dbConnected = false;
            $dbName = null;
            $dbError = $e->getMessage();
        }

        if (!$dbConnected) {
            return response()->json([
                'status' => 'error',
                'message' => 'تعذر الاتصال بقاعدة البيانات',
                'error' => $dbError ?? 'Unknown error',
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'نظام Kody Laravel يعمل ويتصل بقاعدة البيانات بنجاح',
            'database' => $dbName,
            'counts' => [
                'users' => User::count(),
                'accounts' => Account::where('isdeleted', 0)->count(),
                'clients' => Account::clients()->count(),
                'suppliers' => Account::suppliers()->count(),
                'items' => Item::where('isdeleted', 0)->count(),
                'invoices' => Invoice::where('isdeleted', 0)->count(),
            ],
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Get paginated items with units.
     */
    public function items(): JsonResponse
    {
        $items = Item::with(['units.unit', 'group'])
            ->where('isdeleted', 0)
            ->latest('id')
            ->paginate(20);

        return response()->json([
            'status' => 'success',
            'data' => $items,
        ]);
    }

    /**
     * Get accounts grouped by type.
     */
    public function accounts(): JsonResponse
    {
        $accounts = Account::where('isdeleted', 0)
            ->whereIn('parent_id', [0, 1, 2, 3, 4, 5, 7, 8, 13, 15, 18, 19, 20, 33, 37])
            ->select('id', 'code', 'aname', 'parent_id', 'balance')
            ->orderBy('code')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $accounts,
        ]);
    }
}
