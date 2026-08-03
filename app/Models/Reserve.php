<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\BelongsToTenant;
class Reserve extends BaseTenantModel
{

    protected $fillable = [
        'policy_id',
        'reserve_amount',
        'reserve_type',
        'valuation_date',
        'meta',
        'tenant_id'
    ];

    protected $casts = [
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
