<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Quote extends BaseTenantModel
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'tenant_id',
        'customer_id',
        'insurance_product_id',
        'quote_number',
        'input_data',
        'status',
    ];

    protected $casts = [
        'input_data' => 'array',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(
            Customer::class,
            'customer_id'
        );
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(
            InsuranceProduct::class,
            'insurance_product_id'
        );
    }

    public function offers(): HasMany
    {
        return $this->hasMany(
            QuoteOffer::class,
            'quote_id'
        );
    }

    public function policy(): HasOne
    {
        return $this->hasOne(
            Policy::class,
            'quote_id'
        );
    }
}
