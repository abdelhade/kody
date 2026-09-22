<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Builder;

class Account extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'acc_head';
    protected $primaryKey = 'id';
    
    const CREATED_AT = 'crtime';
    const UPDATED_AT = 'mdtime';

    protected $guarded = ['id'];

    protected $casts = [
        'isdeleted' => 'boolean',
        'is_stock' => 'boolean',
        'is_fund' => 'boolean',
        'is_basic' => 'boolean',
        'start_balance' => 'decimal:3',
        'credit' => 'decimal:3',
        'debit' => 'decimal:3',
        'balance' => 'decimal:3',
    ];

    /**
     * Parent account in the chart of accounts
     */
    public function parent()
    {
        return $this->belongsTo(Account::class, 'parent_id', 'id');
    }

    /**
     * Child accounts under this account
     */
    public function children()
    {
        return $this->hasMany(Account::class, 'parent_id', 'id')->where('isdeleted', 0);
    }

    // --- Scopes for convenient querying ---

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('isdeleted', 0);
    }

    public function scopeClients(Builder $query): Builder
    {
        return $query->active()->where('code', 'LIKE', '122%');
    }

    public function scopeSuppliers(Builder $query): Builder
    {
        return $query->active()->where('code', 'LIKE', '211%');
    }

    public function scopeFunds(Builder $query): Builder
    {
        return $query->active()->where('code', 'LIKE', '121%');
    }

    public function scopeBanks(Builder $query): Builder
    {
        return $query->active()->where('code', 'LIKE', '124%');
    }

    public function scopeStores(Builder $query): Builder
    {
        return $query->active()->where(function($q) {
            $q->where('code', 'LIKE', '123%')->orWhere('is_stock', 1);
        });
    }

    public function scopeExpenses(Builder $query): Builder
    {
        return $query->active()->where('code', 'LIKE', '44%');
    }

    public function scopeRevenues(Builder $query): Builder
    {
        return $query->active()->where(function($q) {
            $q->where('code', 'LIKE', '31%')->orWhere('code', 'LIKE', '32%');
        });
    }

    public function scopeEmployees(Builder $query): Builder
    {
        return $query->active()->where('code', 'LIKE', '213%');
    }
}
