<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $table = 'usr_pwrs';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $guarded = ['id'];

    public function users()
    {
        return $this->hasMany(User::class, 'userrole', 'id');
    }

    /**
     * Check if role has a specific permission
     */
    public function hasPermission(string $permission): bool
    {
        return !empty($this->{$permission});
    }
}
