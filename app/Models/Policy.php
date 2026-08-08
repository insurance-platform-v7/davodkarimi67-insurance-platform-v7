<?php

namespace App\Models;

use App\Enums\PolicyStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Policy extends BaseTenantModel
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'quote_id',
        'quote_offer_id',
        'customer_id',
        'policy_number',
        'status',
        'premium',
        'starts_at',
        'ends_at',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
        'status' => PolicyStatus::class,
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'premium' => 'decimal:2',
    ];

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(
            QuoteOffer::class,
            'quote_offer_id'
        );
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function claims(): HasMany
    {
        return $this->hasMany(Claim::class);
    }

    public function reinsuranceAllocations(): HasMany
    {
        return $this->hasMany(ReinsuranceAllocation::class);
    }

    public function reserves(): HasMany
    {
        return $this->hasMany(Reserve::class);
    }
}
