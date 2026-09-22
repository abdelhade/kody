<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Builder;

class Employee extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'employees';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $guarded = ['id'];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('isdeleted', '!=', 1)->orWhereNull('isdeleted');
    }

    public function departmentRel()
    {
        return $this->belongsTo(Department::class, 'department', 'id');
    }

    public function jobRel()
    {
        return $this->belongsTo(Job::class, 'jop', 'id');
    }
}
