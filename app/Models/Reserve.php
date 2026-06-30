<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reserve extends Model
{
    protected $fillable = [
        'policy_id',
        'reserve_amount',
        'reserve_type',
        'valuation_date',
        'meta',
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
