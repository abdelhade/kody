<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WarehouseController extends Controller
{
    public function index()
    {
        $warehouses = Account::stores()->paginate(20);

        return Inertia::render('MasterData/Warehouses', [
            'warehouses' => $warehouses,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'aname' => 'required|string|max:50',
            'address' => 'nullable|string|max:200',
        ]);

        $parent = Account::where('code', '123')->first();
        $lastWarehouse = $parent ? Account::where('parent_id', $parent->id)->orderBy('code', 'desc')->first() : null;
        $nextCode = $lastWarehouse ? (intval($lastWarehouse->code) + 1) : 1230001;

        Account::create([
            'code' => (string)$nextCode,
            'aname' => $validated['aname'],
            'parent_id' => $parent ? $parent->id : 0,
            'address' => $validated['address'] ?? null,
            'is_stock' => 1,
            'nature' => 1, // Stores are debit (Asset)
            'is_basic' => 0,
            'isdeleted' => 0,
            'editable' => 1,
            'secret' => 0,
            'constant' => 0,
            'balance' => 0,
            'tenant' => \App\Models\Tenant::current()?->id ?? 0,
        ]);

        return redirect()->back()->with('success', 'تم إضافة المستودع بنجاح.');
    }

    public function update(Request $request, $id)
    {
        $warehouse = Account::stores()->findOrFail($id);

        $validated = $request->validate([
            'aname' => 'required|string|max:50',
            'address' => 'nullable|string|max:200',
        ]);
        
        $warehouse->update([
            'aname' => $validated['aname'],
            'address' => $validated['address'] ?? null,
        ]);

        return redirect()->back()->with('success', 'تم تعديل بيانات المستودع بنجاح.');
    }

    public function destroy($id)
    {
        $warehouse = Account::stores()->findOrFail($id);
        $warehouse->update(['isdeleted' => 1]);

        return redirect()->back()->with('success', 'تم حذف المستودع بنجاح.');
    }
}
