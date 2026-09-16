<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Broker extends Model
{
    protected $fillable = [
        'name',
        'code',
        'commission_rate',
        'is_active',
    ];

    protected $casts = [
        'commission_rate' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * @return HasMany<BrokerCommission, $this>
     */
    public function commissions(): HasMany
    {
        return $this->hasMany(
            BrokerCommission::class
        );
    }
}
