<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reserve extends BaseTenantModel
{
    protected $fillable = [
        'tenant_id',
        'policy_id',
        'reserve_amount',
        'reserve_type',
        'valuation_date',
        'meta',
    ];

    protected $casts = [
        'reserve_amount' => 'decimal:2',
        'valuation_date' => 'date',
        'meta' => 'array',
    ];

    public function policy(): BelongsTo
    {
        return $this->belongsTo(
            Policy::class
        );
    }
}
