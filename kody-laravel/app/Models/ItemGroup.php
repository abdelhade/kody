<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ItemGroup extends Model
{
    use HasFactory;

    protected $table = 'item_group';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'gname',
        'info',
        'parent',
        'isdeleted',
        'tenant',
        'user',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('isdeleted', 0);
    }

    public function items()
    {
        return $this->hasMany(Item::class, 'group1', 'id');
    }

    public function parentGroup()
    {
        return $this->belongsTo(ItemGroup::class, 'parent', 'id');
    }
}
