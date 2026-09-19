<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BrokerCommission extends Model
{
    use BelongsToTenant;

    /** @use HasFactory<Factory> */
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'broker_id',
        'policy_id',
        'premium',
        'rate',
        'commission_amount',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'premium' => 'float',
        'rate' => 'float',
        'commission_amount' => 'float',
    ];

    /** @return BelongsTo<Broker, $this> */
    public function broker(): BelongsTo
    {
        return $this->belongsTo(Broker::class);
    }

    /** @return BelongsTo<Policy, $this> */
    public function policy(): BelongsTo
    {
        return $this->belongsTo(Policy::class);
    }

    /** @return BelongsTo<Tenant, $this> */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
