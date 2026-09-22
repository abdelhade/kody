<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Invoice;
use App\Models\Item;
use App\Models\ItemGroup;
use App\Models\ItemUnit;
use App\Services\InvoiceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PosController extends Controller
{
    public function __construct(
        protected InvoiceService $invoiceService
    ) {}

    /**
     * Render the Vue 3 POS screen.
     */
    public function index(): Response
    {
        $categories = ItemGroup::where('isdeleted', 0)->orderBy('gname')->get(['id', 'gname']);

        $recentItems = Item::with(['units.unit', 'group'])
            ->where('isdeleted', 0)
            ->latest('id')
            ->take(30)
            ->get();

        $funds = Account::funds()->get(['id', 'code', 'aname']);
        $clients = Account::clients()->take(20)->get(['id', 'code', 'aname']);

        return Inertia::render('Pos/Index', [
            'categories'  => $categories,
            'recentItems' => $recentItems,
            'funds'       => $funds,
            'clients'     => $clients,
            'defaultFund' => $funds->first(),
        ]);
    }

    /**
     * Fast item lookup by barcode or keyword.
     */
    public function search(Request $request): JsonResponse
    {
        $q = trim($request->input('q', ''));
        if (empty($q)) {
            return response()->json([]);
        }

        // Search in items and item_units
        $items = Item::with(['units.unit', 'group'])
            ->where('isdeleted', 0)
            ->where(function ($query) use ($q) {
                $query->where('barcode', $q)
                      ->orWhere('iname', 'LIKE', "%{$q}%")
                      ->orWhere('code', $q)
                      ->orWhereHas('units', function ($uq) use ($q) {
                          $uq->where('unit_barcode', $q);
                      });
            })
            ->take(20)
            ->get();

        return response()->json($items);
    }

    /**
     * Instant POS checkout.
     */
    public function checkout(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items'            => 'required|array|min:1',
            'items.*.item_id'  => 'required|integer',
            'items.*.qty'      => 'required|numeric|min:0.01',
            'items.*.u_val'    => 'required|numeric|min:0.001',
            'items.*.price'    => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'fat_disc'         => 'nullable|numeric|min:0',
            'fat_tax'          => 'nullable|numeric|min:0',
            'paid_amount'      => 'nullable|numeric|min:0',
            'acc_fund'         => 'nullable|integer',
            'acc2'             => 'nullable|integer',
            'order_type'       => 'nullable|string',
            'info'             => 'nullable|string',
        ]);

        $header = [
            'pro_tybe'       => Invoice::TYPE_POS,
            'pro_date'       => date('Y-m-d'),
            'store_id'       => 1,
            'acc2'           => $validated['acc2'] ?? 0,
            'acc_fund'       => $validated['acc_fund'] ?? 0,
            'fat_disc'       => $validated['fat_disc'] ?? 0,
            'fat_tax'        => $validated['fat_tax'] ?? 0,
            'paid_amount'    => $validated['paid_amount'] ?? 0,
            'order_type'     => $validated['order_type'] ?? 'takeaway',
            'info'           => $validated['info'] ?? 'فاتورة نقطة بيع (POS)',
            'invoice_status' => 'completed',
        ];

        $invoice = $this->invoiceService->createInvoice($header, $validated['items'], auth()->id() ?? 1);

        return response()->json([
            'success' => true,
            'message' => 'تم حفظ الفاتورة بنجاح!',
            'invoice' => $invoice,
        ]);
    }
}
