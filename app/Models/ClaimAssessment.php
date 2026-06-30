<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClaimAssessment extends Model
{
    protected $fillable = [
        'claim_id',
        'risk_score',
        'fraud_suspected',
        'notes',
        'factors',
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
