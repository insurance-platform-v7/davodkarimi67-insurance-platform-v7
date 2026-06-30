<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditChannel extends Model
{
    protected $fillable = [
        'name',
        'driver',
        'is_active',
        'config',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'config' => 'array',
    ];
}
