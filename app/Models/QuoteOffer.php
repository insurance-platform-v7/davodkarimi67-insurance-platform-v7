<?php
// File: app/Models/QuoteOffer.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QuoteOffer extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'quote_id',
        'insurance_company_id',
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
}
