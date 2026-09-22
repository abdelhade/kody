<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InvoiceController extends Controller
{
    public function __construct(
        protected InvoiceService $invoiceService
    ) {}

    /**
     * Display a listing of invoices.
     */
    public function index()
    {
        // Fetch B2B Sales Invoices
        $invoices = Invoice::where('pro_tybe', Invoice::TYPE_SALE)
            ->with(['customer', 'warehouse'])
            ->orderBy('id', 'desc')
            ->paginate(10);

        return Inertia::render('Invoices/Index', [
            'invoices' => $invoices,
        ]);
    }

    /**
     * Show the form for creating a new invoice.
     */
    public function create()
    {
        return Inertia::render('Invoices/CreateEdit');
    }

    /**
     * Store a newly created invoice in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'acc1'             => 'required|integer', // Customer ID
            'store_id'         => 'required|integer',
            'pro_date'         => 'required|date',
            'order_type'       => 'nullable|string',
            'info'             => 'nullable|string',
            
            // Totals
            'fat_disc'         => 'nullable|numeric|min:0',
            'fat_tax'          => 'nullable|numeric|min:0',
            'paid_amount'      => 'nullable|numeric|min:0',
            
            // Items
            'items'            => 'required|array|min:1',
            'items.*.item_id'  => 'required|integer',
            'items.*.qty'      => 'required|numeric|min:0.01', // qty_out
            'items.*.u_val'    => 'required|numeric|min:0.001',
            'items.*.price'    => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
        ]);

        $header = [
            'pro_tybe'       => Invoice::TYPE_SALE,
            'pro_date'       => $validated['pro_date'],
            'store_id'       => $validated['store_id'],
            'acc1'           => $validated['acc1'],
            'fat_disc'       => $validated['fat_disc'] ?? 0,
            'fat_tax'        => $validated['fat_tax'] ?? 0,
            'paid_amount'    => $validated['paid_amount'] ?? 0,
            'order_type'     => $validated['order_type'] ?? 'takeaway',
            'info'           => $validated['info'] ?? 'فاتورة مبيعات B2B',
            'invoice_status' => 'completed',
        ];

        $invoice = $this->invoiceService->createInvoice($header, $validated['items'], auth()->id() ?? 1);

        return redirect()->route('invoices.index')->with('success', 'تم إنشاء الفاتورة بنجاح');
    }
}
