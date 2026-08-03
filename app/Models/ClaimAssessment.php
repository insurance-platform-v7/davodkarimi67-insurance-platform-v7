<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\BelongsToTenant;
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
    ];

    public function claim(): BelongsTo
    {
        return $this->belongsTo(
            Claim::class
        );
    }
}
