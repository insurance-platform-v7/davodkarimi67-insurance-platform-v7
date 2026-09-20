<?php

namespace App\Models;

class PricingRule extends BaseTenantModel
{
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

    /** @var array<string, string> */
    protected $casts = [
        'conditions' => 'array',
        'actions' => 'array',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_active' => 'boolean',
    ];
}
