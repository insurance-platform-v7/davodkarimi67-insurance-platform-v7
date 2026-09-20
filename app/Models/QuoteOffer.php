<?php

namespace App\Models;

use Database\Factories\QuoteOfferFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class QuoteOffer extends BaseTenantModel
{
    /** @use HasFactory<QuoteOfferFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'tenant_id',
        'quote_id',
        'insurance_company_id',
        'company_product_id',
        'formula_version_id',
        'premium',
        'present_value',
        'profit',
        'rank',
        'breakdown',
        'meta',
        'status',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'premium' => 'decimal:2',
        'present_value' => 'decimal:2',
        'profit' => 'decimal:2',
        'breakdown' => 'array',
        'meta' => 'array',
    ];

    /**
     * @return HasOne<Policy, $this>
     */
    public function policy(): HasOne
    {
        return $this->hasOne(
            Policy::class,
            'quote_offer_id'
        );
    }

    /**
     * @return BelongsTo<CompanyProduct, $this>
     */
    public function companyProduct(): BelongsTo
    {
        return $this->belongsTo(
            CompanyProduct::class,
            'company_product_id'
        );
    }

    /**
     * @return BelongsTo<InsuranceCompany, $this>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(
            InsuranceCompany::class,
            'insurance_company_id'
        );
    }

    /**
     * @return BelongsTo<Quote, $this>
     */
    public function quote(): BelongsTo
    {
        return $this->belongsTo(
            Quote::class,
            'quote_id'
        );
    }
}
