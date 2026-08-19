<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class ReinsuranceContract extends BaseTenantModel
{
    protected $fillable = [
        'tenant_id',
        'name',
        'type',
        'retention_limit',
        'cession_rate',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'retention_limit' => 'decimal:2',
        'cession_rate' => 'decimal:2',
    ];

    public function allocations(): HasMany
    {
        return $this->hasMany(
            ReinsuranceAllocation::class,
            'reinsurance_contract_id'
        );
    }
}
