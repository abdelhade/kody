<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Unit extends Model
{
    use HasFactory;

    protected $table = 'myunits';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'uname',
        'isdeleted',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('isdeleted', 0);
    }

    public function itemUnits()
    {
        return $this->hasMany(ItemUnit::class, 'unit_id', 'id');
    }
}
