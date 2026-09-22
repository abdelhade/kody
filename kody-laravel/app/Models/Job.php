<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class Job extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'jops';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $guarded = ['id'];
}
