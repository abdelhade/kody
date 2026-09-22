<?php

namespace App\Http\Controllers;

use App\Services\ShiftService;
use App\Models\ClosedShift;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ShiftController extends Controller
{
    protected $shiftService;

    public function __construct(ShiftService $shiftService)
    {
        $this->shiftService = $shiftService;
    }

    public function index(Request $request)
    {
        $userId = $request->user()->id;
        
        $currentShiftStats = $this->shiftService->getCurrentShiftData($userId);
        
        $closedShifts = ClosedShift::orderBy('crtime', 'desc')->paginate(10);

        return Inertia::render('Shifts/Index', [
            'currentShiftStats' => $currentShiftStats,
            'closedShifts' => $closedShifts
        ]);
    }

    public function close(Request $request)
    {
        $request->validate([
            'expenses' => 'numeric|min:0',
            'actual_cash' => 'required|numeric',
            'actual_visa' => 'nullable|numeric',
            'exp_notes' => 'nullable|string',
            'info' => 'nullable|string'
        ]);

        $userId = $request->user()->id;
        
        $this->shiftService->closeShift($userId, $request->only([
            'expenses', 'actual_cash', 'actual_visa', 'exp_notes', 'info'
        ]));

        return redirect()->back()->with('success', 'تم إغلاق الوردية بنجاح.');
    }
}
