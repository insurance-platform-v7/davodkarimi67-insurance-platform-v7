<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'tenant_id',
        'role_id',
        'first_name',
        'last_name',
        'mobile',
        'email',
        'national_code',
        'password',
        'status',
        'last_login_at',
    ];


    // ...


    protected $hidden = [
        'password',
        'remember_token',
    ];
}
