<?php
// File: app/Models/InsuranceCompany.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InsuranceCompany extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'name',
        'code',
        'active',
        'meta',
    ];

    protected $attributes = [
        'active' => true,
    ];

    protected $casts = [
        'meta' => 'array',
        'active' => 'boolean',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
