<?php

namespace App\Models;

use Database\Factories\CompanyProductFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CompanyProduct extends BaseTenantModel
{
    /** @use HasFactory<CompanyProductFactory> */
    use HasFactory;

    protected $table = 'company_product';

    protected $fillable = [
        'tenant_id',
        'insurance_company_id',
        'insurance_product_id',
        'is_active',
        'config',
    ];

    protected $casts = [
        'config' => 'array',
        'is_active' => 'boolean',
    ];

    /** @return BelongsTo<InsuranceCompany, $this> */
    public function company(): BelongsTo
    {
        return $this->belongsTo(
            InsuranceCompany::class,
            'insurance_company_id'
        );
    }

    /** @return BelongsTo<InsuranceProduct, $this> */
    public function product(): BelongsTo
    {
        return $this->belongsTo(
            InsuranceProduct::class,
            'insurance_product_id'
        );
    }

    /** @return HasOne<ProductFormula, $this> */
    public function productFormula(): HasOne
    {
        return $this->hasOne(
            ProductFormula::class,
            'insurance_product_id',
            'insurance_product_id'
        )
            ->where(
                'insurance_company_id',
                $this->insurance_company_id
            )
            ->where('is_active', true);
    }

    /** @return HasMany<QuoteOffer, $this> */
    public function quoteOffers(): HasMany
    {
        return $this->hasMany(
            QuoteOffer::class,
            'company_product_id'
        );
    }
}
