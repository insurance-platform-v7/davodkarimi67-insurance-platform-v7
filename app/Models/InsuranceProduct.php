<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InsuranceProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'name',
        'code',
        'category',
        'is_active',
        'schema',
        'meta',
    ];

    protected $casts = [
        'schema' => 'array',
        'meta' => 'array',
        'is_active' => 'boolean',
    ];
}
