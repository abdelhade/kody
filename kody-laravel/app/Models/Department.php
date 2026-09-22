<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class Department extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'departments';
    protected $primaryKey = 'id';
    public $timestamps = false; // Check if crtime/mdtime exists

    protected $guarded = ['id'];
}
