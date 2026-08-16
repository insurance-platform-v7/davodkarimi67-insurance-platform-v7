<?php

namespace App\Models;

use App\Enums\ClaimStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Claim extends BaseTenantModel
{
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
        'meta' => 'array',
        'status' => ClaimStatus::class,
        'requested_amount' => 'decimal:2',
        'approved_amount' => 'decimal:2',
    ];

    public function policy(): BelongsTo
    {
        return $this->belongsTo(
            Policy::class
        );
    }

    public function assessment(): HasOne
    {
        return $this->hasOne(
            ClaimAssessment::class
        );
    }

    public function payments(): HasMany
    {
        return $this->hasMany(
            ClaimPayment::class
        );
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(
            Tenant::class
        );
    }
}
