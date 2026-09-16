<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClaimAssessment extends BaseTenantModel
{
    protected $fillable = [
        'claim_id',
        'risk_score',
        'fraud_suspected',
        'notes',
        'factors',
        'tenant_id',
    ];

    protected $casts = [
        'fraud_suspected' => 'boolean',
        'factors' => 'array',
        'risk_score' => 'integer',
    ];

    /** @return BelongsTo<Claim, $this> */
    public function claim(): BelongsTo
    {
        return $this->belongsTo(Claim::class);
    }
}
