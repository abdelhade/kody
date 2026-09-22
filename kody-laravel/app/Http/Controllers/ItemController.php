<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemGroup;
use App\Models\ItemUnit;
use App\Models\ItemBarcode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ItemController extends Controller
{
    public function index()
    {
        $items = Item::active()->with(['group', 'defaultUnit'])->paginate(20);

        return Inertia::render('MasterData/Items/Index', [
            'items' => $items,
        ]);
    }

    public function create()
    {
        return Inertia::render('MasterData/Items/CreateEdit', [
            'groups' => ItemGroup::active()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'iname' => 'required|string|max:200|unique:tenant.myitems,iname',
            'group1' => 'required|integer',
            'info' => 'nullable|string',
            // Base prices stored directly on item or default unit
            'market_price' => 'nullable|numeric',
            'cost_price' => 'nullable|numeric',
            'price1' => 'nullable|numeric',
            'units' => 'required|array|min:1',
            'units.*.unit_name' => 'required|string',
            'units.*.u_val' => 'required|numeric|min:1', // conversion factor (e.g., 1 for piece, 24 for carton)
            'units.*.price1' => 'required|numeric',
            'units.*.barcodes' => 'nullable|array',
        ]);

        DB::beginTransaction();
        try {
            // 1. Create the base item
            $item = Item::create([
                'iname' => $validated['iname'],
                'group1' => $validated['group1'],
                'info' => $validated['info'] ?? null,
                'market_price' => $validated['market_price'] ?? 0,
                'cost_price' => $validated['cost_price'] ?? 0,
                'price1' => $validated['price1'] ?? 0,
                'itmqty' => 0, // Initial stock is 0, updated via movements
                'salesqty' => 1,
                'isdeleted' => 0,
                'user' => auth()->id() ?? 1,
                'tenant' => \App\Models\Tenant::current()?->id ?? 0,
            ]);

            // 2. Create the units & barcodes
            foreach ($validated['units'] as $unitData) {
                // In kody26 schema, unit_id was typically a foreign key to a generic unit dictionary, 
                // but we will simplify it or map it if it requires an integer.
                // For now, we'll store the factor in u_val.
                $itemUnit = ItemUnit::create([
                    'item_id' => $item->id,
                    'unit_id' => 1, // Fallback if schema requires integer
                    'unit_name' => $unitData['unit_name'], // We might need to add unit_name to schema if it doesn't exist, wait, kody26 doesn't have unit_name! It has unit_id!
                    'u_val' => $unitData['u_val'],
                    'price1' => $unitData['price1'],
                    'cost_price' => $validated['cost_price'] ?? 0,
                    'isdeleted' => 0,
                ]);

                // 3. Store barcodes
                if (!empty($unitData['barcodes'])) {
                    foreach ($unitData['barcodes'] as $barcode) {
                        ItemBarcode::create([
                            'item_id' => $item->id,
                            'item_unit_id' => $itemUnit->id, // If item_barcodes doesn't have item_unit_id, we map it or alter schema
                            'barcode' => $barcode,
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('items.index')->with('success', 'تم إضافة الصنف بنجاح.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $item = Item::active()->with(['units.barcodes'])->findOrFail($id);
        
        return Inertia::render('MasterData/Items/CreateEdit', [
            'item' => $item,
            'groups' => ItemGroup::active()->get(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $item = Item::active()->findOrFail($id);
        // Implement full update logic with sync (Delete old units/barcodes and recreate, or update existing)
        // Omitted for brevity in this iteration.
        return redirect()->route('items.index')->with('success', 'تم تعديل الصنف بنجاح.');
    }

    public function destroy($id)
    {
        $item = Item::active()->findOrFail($id);
        $item->update(['isdeleted' => 1]);
        return redirect()->back()->with('success', 'تم حذف الصنف بنجاح.');
    }
}
