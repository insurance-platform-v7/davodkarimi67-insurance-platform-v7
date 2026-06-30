<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BrokerCommission extends Model
{
    protected $fillable = [
        'broker_id',
        'policy_id',
        'premium',
        'rate',
        'commission_amount',
    ];
}
