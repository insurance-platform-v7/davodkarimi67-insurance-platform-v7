<?php

namespace App\Models;

use Database\Factories\InsuranceCompanyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InsuranceCompany extends BaseTenantModel
{
    /** @use HasFactory<InsuranceCompanyFactory> */
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

    /** @return BelongsTo<Tenant, $this> */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    /** @return HasMany<CompanyProduct, $this> */
    public function companyProducts(): HasMany
    {
        return $this->hasMany(
            CompanyProduct::class,
            'insurance_company_id'
        );
    }
}
