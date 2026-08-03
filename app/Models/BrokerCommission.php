<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToTenant;
class BrokerCommission extends BaseTenantModel
{

    protected $fillable = [
        'broker_id',
        'policy_id',
        'premium',
        'rate',
        'commission_amount',
        'tenant_id'
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
