<?php

namespace App\Models;

use App\Enums\ClaimStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Claim extends Model
{
    protected $fillable = [
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
}
