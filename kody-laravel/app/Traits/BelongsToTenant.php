<?php

namespace App\Traits;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Builder;

trait BelongsToTenant
{
    /**
     * Boot the trait to attach global scope and auto-assign tenant id on creation.
     */
    protected static function bootBelongsToTenant(): void
    {
        // Global scope to isolate records by current active tenant
        // Removed: We are now using Database-per-tenant, so tables are naturally isolated.
        /*
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (Tenant::checkCurrent()) {
                $table = $builder->getModel()->getTable();
                $currentId = Tenant::current()->id;
                $builder->where(function ($q) use ($table, $currentId) {
                    $q->where("{$table}.tenant", $currentId)
                      ->orWhere("{$table}.tenant", 0);
                });
            }
        });
        */

        // Auto-fill tenant id on creating if not explicitly provided
        static::creating(function ($model) {
            if (Tenant::checkCurrent() && empty($model->tenant)) {
                $model->tenant = Tenant::current()->id;
            }
        });
    }

    /**
     * Relationship to the Tenant model
     */
    public function tenantModel()
    {
        return $this->belongsTo(Tenant::class, 'tenant', 'id');
    }
}
