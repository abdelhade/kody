<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ItemUnit extends Model
{
    use HasFactory;

    protected $table = 'item_units';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $guarded = ['id'];

    protected $casts = [
        'isdeleted' => 'boolean',
        'u_val' => 'decimal:3',
        'cost_price' => 'float',
        'price1' => 'decimal:3',
        'price2' => 'decimal:3',
        'price3' => 'decimal:3',
        'price4' => 'decimal:3',
        'def_sale' => 'boolean',
        'def_buy' => 'boolean',
        'def_stock' => 'boolean',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('isdeleted', 0);
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }
}
