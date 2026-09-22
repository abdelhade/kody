<?php

namespace App\Services;

use App\Models\Item;
use App\Models\ItemUnit;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ItemService
{
    /**
     * Create a new item with its units within a transaction.
     *
     * @param array $data
     * @param int $userId
     * @return Item
     * @throws ValidationException
     */
    public function createItem(array $data, int $userId = 1): Item
    {
        $barcode = trim($data['barcode'] ?? '');
        $iname = trim($data['iname'] ?? '');

        // 1. Verify name uniqueness
        if (Item::where('iname', $iname)->where('isdeleted', 0)->exists()) {
            throw ValidationException::withMessages([
                'iname' => 'اسم الصنف موجود بالفعل مسبقاً.'
            ]);
        }

        // 2. Barcode auto-generation or validation
        if (empty($barcode)) {
            $lastBarcode = Item::orderByDesc('id')->value('barcode');
            $barcode = is_numeric($lastBarcode) ? (string) ((int) $lastBarcode + 1) : (string) (Item::count() + 1);
        } else {
            $this->validateBarcodeUnique($barcode);
        }

        // 3. Units validation
        $units = $data['units'] ?? [];
        if (empty($units)) {
            throw ValidationException::withMessages([
                'units' => 'يجب تحديد وحدة واحدة على الأقل للصنف.'
            ]);
        }

        return DB::transaction(function () use ($data, $barcode, $iname, $userId, $units) {
            $firstUnit = $units[0] ?? [];

            // Create Item in myitems
            $item = Item::create([
                'iname'        => $iname,
                'name2'        => $data['name2'] ?? null,
                'code'         => $data['code'] ?? null,
                'barcode'      => $barcode,
                'info'         => $data['info'] ?? null,
                'market_price' => $firstUnit['market_price'] ?? ($data['market_price'] ?? 0),
                'cost_price'   => $firstUnit['cost_price'] ?? ($data['cost_price'] ?? 0),
                'price1'       => $firstUnit['price1'] ?? ($data['price1'] ?? 0),
                'price2'       => $firstUnit['price2'] ?? ($data['price2'] ?? 0),
                'price3'       => $firstUnit['price3'] ?? ($data['price3'] ?? 0),
                'group1'       => $data['group1'] ?? 0,
                'group2'       => $data['group2'] ?? 0,
                'group3'       => $data['group3'] ?? 0,
                'user'         => $userId,
                'tenant'       => $data['tenant'] ?? 0,
                'branch'       => $data['branch'] ?? 0,
                'isdeleted'    => 0,
            ]);

            // Create Item Units in item_units
            foreach ($units as $index => $u) {
                $unitBarcode = trim($u['unit_barcode'] ?? '');
                if (empty($unitBarcode)) {
                    $unitBarcode = ($index === 0) ? $barcode : '99' . $index . $barcode;
                } else if ($index > 0) {
                    $this->validateBarcodeUnique($unitBarcode);
                }

                ItemUnit::create([
                    'item_id'      => $item->id,
                    'unit_id'      => $u['unit_id'],
                    'u_val'        => $u['u_val'] ?? 1,
                    'unit_barcode' => $unitBarcode,
                    'cost_price'   => $u['cost_price'] ?? 0,
                    'price1'       => $u['price1'] ?? 0,
                    'price2'       => $u['price2'] ?? 0,
                    'price3'       => $u['price3'] ?? 0,
                    'price4'       => $u['price4'] ?? 0,
                    'def_sale'     => !empty($u['def_sale']) ? 1 : ($index === 0 ? 1 : 0),
                    'def_buy'      => !empty($u['def_buy']) ? 1 : ($index === 0 ? 1 : 0),
                    'def_stock'    => !empty($u['def_stock']) ? 1 : ($index === 0 ? 1 : 0),
                    'tenant'       => $data['tenant'] ?? 0,
                    'branch'       => $data['branch'] ?? 0,
                    'isdeleted'    => 0,
                ]);
            }

            return $item->load('units.unit', 'group');
        });
    }

    /**
     * Validate barcode is unique across items and item units.
     */
    public function validateBarcodeUnique(string $barcode, ?int $ignoreItemId = null): void
    {
        $itemExists = Item::where('barcode', $barcode)
            ->when($ignoreItemId, fn($q) => $q->where('id', '!=', $ignoreItemId))
            ->where('isdeleted', 0)
            ->exists();

        $unitExists = ItemUnit::where('unit_barcode', $barcode)
            ->when($ignoreItemId, fn($q) => $q->where('item_id', '!=', $ignoreItemId))
            ->where('isdeleted', 0)
            ->exists();

        if ($itemExists || $unitExists) {
            throw ValidationException::withMessages([
                'barcode' => "الباركود ({$barcode}) مسجل ومستخدم مسبقاً."
            ]);
        }
    }
}
