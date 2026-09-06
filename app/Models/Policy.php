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

    /** @return BelongsTo<Quote, $this> */
    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    /** @return BelongsTo<QuoteOffer, $this> */
    public function offer(): BelongsTo
    {
        return $this->belongsTo(
            QuoteOffer::class,
            'quote_offer_id'
        );
    }

    /** @return BelongsTo<Customer, $this> */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /** @return HasMany<Payment, $this> */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /** @return HasMany<Claim, $this> */
    public function claims(): HasMany
    {
        return $this->hasMany(Claim::class);
    }

    /** @return HasMany<ReinsuranceAllocation, $this> */
    public function reinsuranceAllocations(): HasMany
    {
        return $this->hasMany(ReinsuranceAllocation::class);
    }

    /** @return HasMany<Reserve, $this> */
    public function reserves(): HasMany
    {
        return $this->hasMany(
            Reserve::class,
            'policy_id'
        );
    }
}
