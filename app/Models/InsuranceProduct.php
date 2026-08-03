<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Traits\BelongsToTenant;
class InsuranceProduct extends BaseTenantModel
{

    use HasFactory, BelongsToTenant;


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
        return $this->hasMany(CompanyProduct::class);
    }

    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class);
    }
}
