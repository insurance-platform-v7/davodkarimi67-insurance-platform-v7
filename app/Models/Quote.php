<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Relations\HasOne;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Quote extends BaseTenantModel
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'insurance_product_id',
        'quote_number',
        'input_data',
        'status',
    ];

    protected $casts = [
        'input_data' => 'array',
    ];


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
            QuoteOffer::class
        );
    }

    public function policy(): HasOne
    {
        return $this->hasOne(
            Policy::class
        );
    }


}

