<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InsuranceCompany extends BaseTenantModel
{
    use HasFactory, BelongsToTenant;

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

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function companyProducts(): HasMany
    {
        return $this->hasMany(
            CompanyProduct::class,
            'insurance_company_id'
        );
    }
}
