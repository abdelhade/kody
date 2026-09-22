<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Item;
use App\Models\Account;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InvoiceService
{
    /**
     * Create an invoice with all its line items inside a database transaction.
     * Includes advanced accounting rules: Credit Limits & Moving Average Cost.
     *
     * @param array $header
     * @param array $items
     * @param int $userId
     * @return Invoice
     * @throws ValidationException
     */
    public function createInvoice(array $header, array $items, int $userId = 1): Invoice
    {
        $proType = (int) ($header['pro_tybe'] ?? Invoice::TYPE_SALE);
        $storeId = (int) ($header['store_id'] ?? 1);
        
        $acc1Id  = (int) ($header['acc1'] ?? 0); // Party (Customer/Supplier)
        $acc2Id  = (int) ($header['acc2'] ?? 0); // Additional account

        if (empty($items)) {
            throw ValidationException::withMessages([
                'items' => 'الرجاء إضافة صنف واحد على الأقل.'
            ]);
        }

        return DB::transaction(function () use ($header, $items, $userId, $proType, $storeId, $acc1Id, $acc2Id) {
            $computedTotal = 0.0;
            $preparedItems = [];
            
            // For moving average
            $itemsToUpdateCost = [];

            foreach ($items as $row) {
                $itemId = (int) ($row['item_id'] ?? 0);
                $qty = (float) ($row['qty'] ?? 1);
                $uVal = (float) ($row['u_val'] ?? 1);
                $price = (float) ($row['price'] ?? 0);
                $discount = (float) ($row['discount'] ?? 0);
                $plus = (float) ($row['plus'] ?? 0);

                $totalQty = $qty * ($uVal > 0 ? $uVal : 1);
                $lineTotal = ($qty * $price) - $discount + $plus;
                $computedTotal += $lineTotal;

                // Determine stock movement
                $qtyIn = 0.0;
                $qtyOut = 0.0;

                switch ($proType) {
                    case Invoice::TYPE_PURCHASE:
                    case Invoice::TYPE_SALE_RETURN:
                        $qtyIn = $totalQty;
                        $itemsToUpdateCost[] = [
                            'item_id' => $itemId,
                            'qty_in' => $qtyIn,
                            'unit_cost' => $totalQty > 0 ? ($lineTotal / $totalQty) : 0,
                        ];
                        break;
                    case Invoice::TYPE_SALE:
                    case Invoice::TYPE_POS:
                    case Invoice::TYPE_PURCHASE_RETURN:
                        $qtyOut = $totalQty;
                        break;
                }

                $preparedItems[] = [
                    'item_id'    => $itemId,
                    'det_store'  => $row['store_id'] ?? $storeId,
                    'pro_tybe'   => $proType,
                    'u_val'      => $uVal,
                    'qty_in'     => $qtyIn,
                    'qty_out'    => $qtyOut,
                    'price'      => $price,
                    'cost_price' => $row['cost_price'] ?? 0,
                    'discount'   => $discount,
                    'plus'       => $plus,
                    'det_value'  => $lineTotal,
                ];
            }

            $fatDisc = (float) ($header['fat_disc'] ?? 0);
            $fatPlus = (float) ($header['fat_plus'] ?? 0);
            $fatTax  = (float) ($header['fat_tax'] ?? 0);
            $fatNet  = $computedTotal - $fatDisc + $fatPlus + $fatTax;

            $paidAmount = (float) ($header['paid_amount'] ?? 0);
            $remainingAmount = max(0, $fatNet - $paidAmount);

            // 1. Check Customer Credit Limit for Sales
            if ($remainingAmount > 0 && in_array($proType, [Invoice::TYPE_SALE, Invoice::TYPE_POS]) && $acc1Id > 0) {
                $customer = Account::find($acc1Id);
                if ($customer && $customer->credit > 0) {
                    // Start balance plus current debt plus new invoice debt
                    $totalDebt = $customer->balance + $remainingAmount;
                    if ($totalDebt > $customer->credit) {
                        throw ValidationException::withMessages([
                            'credit_limit' => "لقد تجاوز العميل الحد الائتماني المسموح به ({$customer->credit}). إجمالي المديونية سيصبح: {$totalDebt}"
                        ]);
                    }
                }
            }

            // 2. Update Moving Average Cost for Purchases / Sales Returns
            foreach ($itemsToUpdateCost as $costData) {
                $item = Item::find($costData['item_id']);
                if ($item) {
                    $oldQty = $item->itmqty;
                    $oldAvgCost = $item->cost_price;
                    
                    $newQty = $costData['qty_in'];
                    $newUnitCost = $costData['unit_cost'];
                    
                    // Moving Weighted Average Formula
                    $totalQty = $oldQty + $newQty;
                    if ($totalQty > 0) {
                        $newAvgCost = (($oldQty * $oldAvgCost) + ($newQty * $newUnitCost)) / $totalQty;
                        $item->cost_price = $newAvgCost;
                    }
                    
                    // Update last purchase price too
                    if ($proType == Invoice::TYPE_PURCHASE) {
                        $item->price1 = $newUnitCost; // Assuming price1 or similar stores last cost, or we just rely on cost_price
                    }
                    
                    // itmqty will be updated via triggers or observers usually, but cost needs explicit math
                    $item->save();
                }
            }

            // Create Invoice Header
            $invoice = Invoice::create([
                'pro_tybe'         => $proType,
                'pro_date'         => $header['pro_date'] ?? date('Y-m-d'),
                'accural_date'     => $header['accural_date'] ?? null,
                'pro_serial'       => $header['pro_serial'] ?? null,
                'store_id'         => $storeId,
                'acc1'             => $acc1Id,
                'acc2'             => $acc2Id,
                'acc_fund'         => $header['acc_fund'] ?? $header['fund_id'] ?? 0,
                'emp_id'           => $header['emp_id'] ?? 0,
                'info'             => $header['info'] ?? null,
                'fat_total'        => $computedTotal,
                'fat_disc'         => $fatDisc,
                'fat_plus'         => $fatPlus,
                'fat_tax'          => $fatTax,
                'fat_net'          => $fatNet,
                'paid_amount'      => $paidAmount,
                'remaining_amount' => $remainingAmount,
                'payment_status'   => $remainingAmount <= 0 ? 'paid' : ($paidAmount > 0 ? 'partial' : 'unpaid'),
                'invoice_status'   => $header['invoice_status'] ?? 'completed',
                'order_type'       => $header['order_type'] ?? 'takeaway',
                'table_id'         => $header['table_id'] ?? null,
                'user'             => $userId,
            ]);

            // Save line items
            foreach ($preparedItems as $itemRow) {
                $itemRow['pro_id'] = $invoice->id;
                InvoiceItem::create($itemRow);
            }

            return $invoice->load('items.item', 'customer');
        });
    }
}
