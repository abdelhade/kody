<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Account::suppliers()->paginate(20);

        return Inertia::render('MasterData/Suppliers', [
            'suppliers' => $suppliers,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'aname' => 'required|string|max:50',
            'phone' => 'nullable|string|max:200',
            'address' => 'nullable|string|max:200',
            'info' => 'nullable|string|max:250', // Tax number
            'start_balance' => 'numeric',
            'nature' => 'required|in:1,2',
        ]);

        $parent = Account::where('code', '211')->first();
        $lastSupplier = $parent ? Account::where('parent_id', $parent->id)->orderBy('code', 'desc')->first() : null;
        $nextCode = $lastSupplier ? (intval($lastSupplier->code) + 1) : 2110001;

        Account::create([
            'code' => (string)$nextCode,
            'aname' => $validated['aname'],
            'parent_id' => $parent ? $parent->id : 0,
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'info' => $validated['info'] ?? null,
            'start_balance' => $validated['start_balance'] * ($validated['nature'] == 2 ? -1 : 1),
            'nature' => $validated['nature'],
            'is_basic' => 0,
            'isdeleted' => 0,
            'editable' => 1,
            'secret' => 0,
            'constant' => 0,
            'balance' => $validated['start_balance'] * ($validated['nature'] == 2 ? -1 : 1),
            'tenant' => \App\Models\Tenant::current()?->id ?? 0,
        ]);

        return redirect()->back()->with('success', 'تم إضافة المورد بنجاح.');
    }

    public function update(Request $request, $id)
    {
        $supplier = Account::suppliers()->findOrFail($id);

        $validated = $request->validate([
            'aname' => 'required|string|max:50',
            'phone' => 'nullable|string|max:200',
            'address' => 'nullable|string|max:200',
            'info' => 'nullable|string|max:250',
        ]);
        
        $supplier->update([
            'aname' => $validated['aname'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'info' => $validated['info'] ?? null,
        ]);

        return redirect()->back()->with('success', 'تم تعديل بيانات المورد بنجاح.');
    }

    public function destroy($id)
    {
        $supplier = Account::suppliers()->findOrFail($id);
        $supplier->update(['isdeleted' => 1]);

        return redirect()->back()->with('success', 'تم حذف المورد بنجاح.');
    }
}
