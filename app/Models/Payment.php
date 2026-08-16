<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends BaseTenantModel
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'policy_id',
        'gateway',
        'transaction_id',
        'authority',
        'amount',
        'status',
        'callback_data',
        'paid_at',
    ];

    protected $casts = [
        'callback_data' => 'array',
        'status' => PaymentStatus::class,
        'paid_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function policy(): BelongsTo
    {
        return $this->belongsTo(Policy::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
