<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Builder;

class Item extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'myitems';
    protected $primaryKey = 'id';

    const CREATED_AT = 'crtime';
    const UPDATED_AT = 'mdtime';

    protected $guarded = ['id'];

    protected $casts = [
        'isdeleted' => 'boolean',
        'market_price' => 'float',
        'cost_price' => 'float',
        'price1' => 'float',
        'price2' => 'float',
        'price3' => 'float',
        'salesqty' => 'float',
        'itmqty' => 'float',
        'manual_price_edit' => 'boolean',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('isdeleted', 0);
    }

    /**
     * Item units (with conversion factor u_val and unit barcodes)
     */
    public function units()
    {
        return $this->hasMany(ItemUnit::class, 'item_id', 'id')->where('isdeleted', 0);
    }

    /**
     * Default primary unit
     */
    public function defaultUnit()
    {
        return $this->hasOne(ItemUnit::class, 'item_id', 'id')
                    ->where('isdeleted', 0)
                    ->orderBy('id', 'asc');
    }

    /**
     * Primary category / group
     */
    public function group()
    {
        return $this->belongsTo(ItemGroup::class, 'group1', 'id');
    }

    /**
     * User who created the item
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'user', 'id');
    }
}
