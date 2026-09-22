<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Builder;

class InvoiceItem extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'fat_details';
    protected $primaryKey = 'id';

    const CREATED_AT = 'crtime';
    const UPDATED_AT = null;

    protected $guarded = ['id'];

    protected $casts = [
        'isdeleted' => 'boolean',
        'item_id' => 'integer',
        'pro_id' => 'integer',
        'det_store' => 'integer',
        'pro_tybe' => 'integer',
        'u_val' => 'decimal:3',
        'qty_in' => 'float',
        'qty_out' => 'float',
        'price' => 'float',
        'cost_price' => 'float',
        'discount' => 'float',
        'plus' => 'float',
        'det_value' => 'float',
        'profit' => 'float',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('isdeleted', 0);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'pro_id', 'id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }

    public function store()
    {
        return $this->belongsTo(Account::class, 'det_store', 'id');
    }
}
