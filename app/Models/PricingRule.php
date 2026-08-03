<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToTenant;
class PricingRule extends BaseTenantModel
{
    use HasFactory, BelongsToTenant;
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
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];
}
