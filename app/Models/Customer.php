<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'tenant_id',
        'first_name',
        'last_name',
        'mobile',
        'email',
        'national_code',
        'birth_date',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
        'birth_date' => 'date',
    ];
}
