<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class Payroll extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'attdocs';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $guarded = ['id'];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'empid', 'id');
    }
}
