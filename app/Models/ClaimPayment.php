<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClaimPayment extends Model
{
    protected $fillable = [
        'claim_id',
        'amount',
        'reference_number',
        'paid_at',
        'meta',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'meta' => 'array',
    ];

    public function claim(): BelongsTo
    {
        return $this->belongsTo(
            Claim::class
        );
    }
}
