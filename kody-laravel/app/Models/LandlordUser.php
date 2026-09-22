<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class LandlordUser extends Authenticatable
{
    use Notifiable;

    protected $connection = 'landlord';
    protected $table = 'landlord_users';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
    ];

    protected $hidden = [
        'password',
    ];
}
