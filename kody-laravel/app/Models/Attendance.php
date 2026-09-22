<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class Attendance extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'attandance';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $guarded = ['id'];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'empid', 'id');
    }
}
