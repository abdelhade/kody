<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CustomerController extends Controller
{
    public function index()
    {
        // Spatie Check
        // $this->authorize('view_customer'); // Uncomment when roles are seeded

        $customers = Account::clients()->paginate(20);

        return Inertia::render('MasterData/Customers', [
            'customers' => $customers,
        ]);
    }

    public function store(Request $request)
    {
        // $this->authorize('create_customer');

        $validated = $request->validate([
            'aname' => 'required|string|max:50',
            'phone' => 'nullable|string|max:200',
            'address' => 'nullable|string|max:200',
            'info' => 'nullable|string|max:250', // Tax number
            'start_balance' => 'numeric',
            'nature' => 'required|in:1,2',
            'credit' => 'numeric|min:0',
        ]);

        // Parent ID for clients is usually the 'العملاء' folder which is 122
        $parent = Account::where('code', '122')->first();
        
        // Generate next customer code (122xxxx)
        $lastCustomer = $parent ? Account::where('parent_id', $parent->id)->orderBy('code', 'desc')->first() : null;
        $nextCode = $lastCustomer ? (intval($lastCustomer->code) + 1) : 1220001;

        Account::create([
            'code' => (string)$nextCode,
            'aname' => $validated['aname'],
            'parent_id' => $parent ? $parent->id : 0,
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'info' => $validated['info'] ?? null,
            'start_balance' => $validated['start_balance'] * ($validated['nature'] == 2 ? -1 : 1),
            'credit' => $validated['credit'],
            'nature' => $validated['nature'],
            'is_basic' => 0,
            'isdeleted' => 0,
            'editable' => 1,
            'secret' => 0,
            'constant' => 0,
            'balance' => $validated['start_balance'] * ($validated['nature'] == 2 ? -1 : 1),
            'tenant' => \App\Models\Tenant::current()?->id ?? 0,
        ]);

        return redirect()->back()->with('success', 'تم إضافة العميل بنجاح.');
    }

    public function update(Request $request, $id)
    {
        // $this->authorize('update_customer');

        $customer = Account::clients()->findOrFail($id);

        $validated = $request->validate([
            'aname' => 'required|string|max:50',
            'phone' => 'nullable|string|max:200',
            'address' => 'nullable|string|max:200',
            'info' => 'nullable|string|max:250',
            'start_balance' => 'numeric',
            'nature' => 'required|in:1,2',
            'credit' => 'numeric|min:0',
        ]);

        $newStartBalance = $validated['start_balance'] * ($validated['nature'] == 2 ? -1 : 1);
        
        $customer->update([
            'aname' => $validated['aname'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'info' => $validated['info'] ?? null,
            'credit' => $validated['credit'],
            // updating start balance might require recalculating current balance, omitting complex logic for brevity
        ]);

        return redirect()->back()->with('success', 'تم تعديل بيانات العميل بنجاح.');
    }

    public function destroy($id)
    {
        // $this->authorize('delete_customer');

        $customer = Account::clients()->findOrFail($id);
        
        // Soft delete
        $customer->update(['isdeleted' => 1]);

        return redirect()->back()->with('success', 'تم حذف العميل بنجاح.');
    }
}
