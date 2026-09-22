<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Builder;

class JournalEntry extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'journal_entries';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $guarded = ['id'];

    protected $casts = [
        'debit' => 'float',
        'credit' => 'float',
        'crtime' => 'datetime',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('isdeleted', 0)->orWhereNull('isdeleted');
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'id');
    }
}
