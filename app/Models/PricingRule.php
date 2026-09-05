<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PricingRule extends BaseTenantModel
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'tenant_id',
        'insurance_company_id',
        'insurance_product_id',
        'name',
        'code',
        'priority',
        'conditions',
        'actions',
        'starts_at',
        'ends_at',
        'is_active',
    ];

    protected $casts = [
        'conditions' => 'array',
        'actions' => 'array',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_active' => 'boolean',
    ];
}
