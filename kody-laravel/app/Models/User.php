<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, BelongsToTenant, HasRoles;

    protected $table = 'users';
    protected $primaryKey = 'id';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'uname',
        'email',
        'phone',
        'password',
        'usertype',
        'userrole',
        'is_waiter',
        'img',
        'tenant',
        'branch',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the password for the user.
     */
    public function getAuthPasswordName(): string
    {
        return 'password';
    }

    /**
     * Get the role associated with the user.
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'userrole', 'id');
    }

    /**
     * Check if user is a waiter.
     */
    public function isWaiter(): bool
    {
        return (bool) $this->is_waiter;
    }
}
