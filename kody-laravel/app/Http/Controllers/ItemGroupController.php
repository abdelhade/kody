<?php

namespace App\Http\Controllers;

use App\Models\ItemGroup;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ItemGroupController extends Controller
{
    public function index()
    {
        $groups = ItemGroup::active()->with('parentGroup')->paginate(20);

        return Inertia::render('MasterData/ItemGroups', [
            'groups' => $groups,
            'parentGroups' => ItemGroup::active()->where('parent', 0)->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'gname' => 'required|string|max:100',
            'info' => 'nullable|string|max:200',
            'parent' => 'nullable|integer',
        ]);

        ItemGroup::create([
            'gname' => $validated['gname'],
            'info' => $validated['info'] ?? null,
            'parent' => $validated['parent'] ?? 0,
            'isdeleted' => 0,
            'tenant' => \App\Models\Tenant::current()?->id ?? 0,
            'user' => auth()->id() ?? 1,
        ]);

        return redirect()->back()->with('success', 'تم إضافة المجموعة بنجاح.');
    }

    public function update(Request $request, $id)
    {
        $group = ItemGroup::active()->findOrFail($id);

        $validated = $request->validate([
            'gname' => 'required|string|max:100',
            'info' => 'nullable|string|max:200',
            'parent' => 'nullable|integer',
        ]);
        
        $group->update([
            'gname' => $validated['gname'],
            'info' => $validated['info'] ?? null,
            'parent' => $validated['parent'] ?? 0,
        ]);

        return redirect()->back()->with('success', 'تم تعديل المجموعة بنجاح.');
    }

    public function destroy($id)
    {
        $group = ItemGroup::active()->findOrFail($id);
        $group->update(['isdeleted' => 1]);

        return redirect()->back()->with('success', 'تم حذف المجموعة بنجاح.');
    }
}
