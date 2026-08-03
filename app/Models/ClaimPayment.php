<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\BelongsToTenant;
class ClaimPayment extends BaseTenantModel
{

    protected $fillable = [
        'claim_id',
        'amount',
        'reference_number',
        'paid_at',
        'meta',
        'tenant_id'
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
