<?php

namespace App\Models;

use App\Enums\ClaimStatus;
use Database\Factories\ClaimFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Claim extends BaseTenantModel
{
    /** @use HasFactory<ClaimFactory> */
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'policy_id',
        'claim_number',
        'status',
        'requested_amount',
        'approved_amount',
        'description',
        'meta',
    ];

    protected $casts = [
        'status' => ClaimStatus::class,
        'meta' => 'array',
    ];

    /** @return BelongsTo<Policy, $this> */
    public function policy(): BelongsTo
    {
        return $this->belongsTo(Policy::class);
    }

    /** @return HasOne<ClaimAssessment, $this> */
    public function assessment(): HasOne
    {
        return $this->hasOne(ClaimAssessment::class);
    }

    /** @return HasMany<ClaimDocument, $this> */
    public function documents(): HasMany
    {
        return $this->hasMany(ClaimDocument::class);
    }

    /** @return HasMany<ClaimPayment, $this> */
    public function payments(): HasMany
    {
        return $this->hasMany(ClaimPayment::class);
    }
}
