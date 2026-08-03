<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Traits\BelongsToTenant;
class QuoteOffer extends BaseTenantModel
{



    use HasFactory;

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

    protected $casts = [
        'breakdown' => 'array',
        'meta' => 'array',
    ];

    public function policy(): HasOne
    {
        return $this->hasOne(Policy::class, 'quote_offer_id');
    }

    public function companyProduct(): BelongsTo
    {
        return $this->belongsTo(CompanyProduct::class);
    }

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }
}
