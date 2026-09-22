<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Builder;

class Invoice extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'ot_head';
    protected $primaryKey = 'id';

    const CREATED_AT = 'crtime';
    const UPDATED_AT = 'mdtime';

    protected $guarded = ['id'];

    // Invoice Types Constants
    const TYPE_SALE            = 3;   // فاتورة مبيعات
    const TYPE_PURCHASE        = 4;   // فاتورة مشتريات
    const TYPE_POS             = 9;   // كاشير
    const TYPE_PURCHASE_RETURN = 10;  // مردود مشتريات
    const TYPE_SALE_RETURN     = 11;  // مردود مبيعات
    const TYPE_PURCHASE_ORDER  = 12;  // أمر شراء
    const TYPE_SALE_ORDER      = 13;  // أمر بيع
    const TYPE_PRICE_OFFER     = 14;  // عرض سعر

    protected $casts = [
        'isdeleted' => 'boolean',
        'pro_tybe' => 'integer',
        'store_id' => 'integer',
        'emp_id' => 'integer',
        'acc1' => 'integer',
        'acc2' => 'integer',
        'fat_total' => 'float',
        'fat_disc' => 'float',
        'fat_plus' => 'float',
        'fat_tax' => 'float',
        'fat_net' => 'float',
        'paid_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'pro_date' => 'date',
        'accural_date' => 'date',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('isdeleted', 0);
    }

    public function scopeSales(Builder $query): Builder
    {
        return $query->active()->where('pro_tybe', self::TYPE_SALE);
    }

    public function scopePurchases(Builder $query): Builder
    {
        return $query->active()->where('pro_tybe', self::TYPE_PURCHASE);
    }

    /**
     * Details / items of this invoice
     */
    public function items()
    {
        return $this->hasMany(InvoiceItem::class, 'pro_id', 'id')->where('isdeleted', 0);
    }

    /**
     * Client or Supplier account (acc1 or acc2 depending on legacy usage)
     */
    public function partyAccount()
    {
        return $this->belongsTo(Account::class, 'acc2', 'id');
    }

    public function customer()
    {
        return $this->belongsTo(Account::class, 'acc1', 'id');
    }

    public function supplier()
    {
        return $this->belongsTo(Account::class, 'acc2', 'id');
    }

    /**
     * Treasury / Fund / Bank account (acc_fund / acc1)
     */
    public function fundAccount()
    {
        return $this->belongsTo(Account::class, 'acc_fund', 'id');
    }

    /**
     * Store where goods are received / dispatched
     */
    public function store()
    {
        return $this->belongsTo(Account::class, 'store_id', 'id');
    }

    /**
     * User who created the invoice
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'user', 'id');
    }
}
