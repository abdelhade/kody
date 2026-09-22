<?php

namespace App\Models;

use Spatie\Multitenancy\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant
{
    protected $connection = 'landlord';

    protected $table = 'tenants';

    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'array',
    ];

    /**
     * Users belonging to this tenant
     */
    public function users()
    {
        return $this->hasMany(User::class, 'tenant', 'id');
    }

    /**
     * Items belonging to this tenant
     */
    public function items()
    {
        return $this->hasMany(Item::class, 'tenant', 'id');
    }

    /**
     * Accounts belonging to this tenant
     */
    public function accounts()
    {
        return $this->hasMany(Account::class, 'tenant', 'id');
    }

    /**
     * Invoices belonging to this tenant
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'tenant', 'id');
    }
}
