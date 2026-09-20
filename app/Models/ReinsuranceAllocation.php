<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReinsuranceAllocation extends BaseTenantModel
{
    protected $fillable = [
        'policy_id',
        'reinsurance_contract_id',
        'premium',
        'retention',
        'ceded_amount',
        'reinsurer_share',
        'tenant_id',
    ];

    /** @return BelongsTo<Policy, $this> */
    public function policy(): BelongsTo
    {
        return $this->belongsTo(Policy::class);
    }

    /** @return BelongsTo<ReinsuranceContract, $this> */
    public function contract(): BelongsTo
    {
        return $this->belongsTo(
            ReinsuranceContract::class,
            'reinsurance_contract_id'
        );
    }
}
