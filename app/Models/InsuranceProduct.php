<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InsuranceProduct extends BaseTenantModel
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

    public function companyProducts(): HasMany
    {
        return $this->hasMany(
            CompanyProduct::class,
            'insurance_product_id'
        );
    }

    public function quotes(): HasMany
    {
        return $this->hasMany(
            Quote::class,
            'insurance_product_id'
        );
    }
}
