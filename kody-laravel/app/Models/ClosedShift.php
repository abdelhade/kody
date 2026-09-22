<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClosedShift extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'closed_orders';
    protected $primaryKey = 'id';

    const CREATED_AT = 'crtime';
    const UPDATED_AT = 'mdtime';

    protected $guarded = ['id'];

    protected $casts = [
        'date'           => 'date',
        'total_sales'    => 'float',
        'total_cash'     => 'float',
        'total_visa'     => 'float',
        'total_discount' => 'float',
        'total_returns'  => 'float',
        'expenses'       => 'float',
        'start_cash'     => 'float',
        'actual_cash'    => 'float',
        'deficit'        => 'float',
        'fund_before'    => 'float',
        'fund_after'     => 'float',
        'json_details'   => 'array',
    ];
}
