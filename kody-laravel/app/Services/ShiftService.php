<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\ClosedShift;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ShiftService
{
    public function getCurrentShiftData($userId)
    {
        $lastShift = ClosedShift::where('user', auth()->user()->uname ?? $userId)
            ->orderBy('crtime', 'desc')
            ->first();

        $lastClosingTime = $lastShift ? $lastShift->crtime : Carbon::today();

        $sales = Invoice::where('user', $userId)
            ->where('isdeleted', 0)
            ->whereIn('pro_tybe', [Invoice::TYPE_SALE, Invoice::TYPE_POS])
            ->where('crtime', '>', $lastClosingTime)
            ->get();

        $returns = Invoice::where('user', $userId)
            ->where('isdeleted', 0)
            ->where('pro_tybe', Invoice::TYPE_SALE_RETURN)
            ->where('crtime', '>', $lastClosingTime)
            ->get();

        $totalSales = $sales->sum('fat_net');
        $totalReturns = $returns->sum('fat_net');
        
        $totalCash = $sales->where('acc1', '!=', 2)->sum('fat_net'); 
        $totalVisa = $sales->where('acc1', 2)->sum('fat_net'); 

        return [
            'total_sales' => $totalSales,
            'total_returns' => $totalReturns,
            'total_cash' => $totalCash,
            'total_visa' => $totalVisa,
            'net_cash' => $totalCash - $totalReturns,
            'last_closed_at' => $lastClosingTime,
            'orders_count' => $sales->count(),
        ];
    }

    public function closeShift($userId, array $data)
    {
        return DB::transaction(function () use ($userId, $data) {
            $shiftData = $this->getCurrentShiftData($userId);

            $shiftNumber = date('Ymd') . '_' . $userId;
            
            $expenses = floatval($data['expenses'] ?? 0);
            $actualCash = floatval($data['actual_cash'] ?? 0);
            $actualVisa = floatval($data['actual_visa'] ?? 0);
            
            $expectedCash = $shiftData['net_cash'] - $expenses;
            $cashDeficit = $actualCash - $expectedCash;
            
            $expectedVisa = $shiftData['total_visa'];
            $visaDeficit = $actualVisa - $expectedVisa;
            
            $totalDeficit = $cashDeficit + $visaDeficit;

            $jsonDetails = [
                'sys_cash' => $shiftData['net_cash'],
                'sys_visa' => $shiftData['total_visa'],
                'sys_expenses' => $expenses,
                'actual_cash' => $actualCash,
                'actual_visa' => $actualVisa,
                'cash_diff' => $cashDeficit,
                'visa_diff' => $visaDeficit,
            ];

            return ClosedShift::create([
                'shift' => $shiftNumber,
                'date' => Carbon::today(),
                'user' => auth()->user()->uname ?? (string)$userId,
                'endtime' => Carbon::now()->format('H:i:s'),
                'total_sales' => $shiftData['total_sales'],
                'total_cash' => $shiftData['total_cash'],
                'total_visa' => $shiftData['total_visa'],
                'total_discount' => 0,
                'total_returns' => $shiftData['total_returns'],
                'expenses' => $expenses,
                'start_cash' => 0,
                'actual_cash' => $actualCash,
                'actual_visa' => $actualVisa,
                'deficit' => $totalDeficit,
                'fund_before' => 0,
                'fund_after' => $actualCash,
                'status' => 1,
                'json_details' => $jsonDetails,
                'exp_notes' => $data['exp_notes'] ?? '',
                'info' => $data['info'] ?? '',
            ]);
        });
    }
}
