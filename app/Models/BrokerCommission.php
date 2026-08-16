<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BrokerCommission extends BaseTenantModel
{
    protected $fillable = [
        'broker_id',
        'policy_id',
        'premium',
        'rate',
        'commission_amount',
        'tenant_id',
    ];

    public function broker(): BelongsTo
    {
        return $this->belongsTo(
            Broker::class
        );
    }

    public function policy(): BelongsTo
    {
        return $this->belongsTo(
            Policy::class
        );
    }
}
