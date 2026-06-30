<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuoteOption extends Model
{
    protected $fillable = [
        'quote_request_id',
        'insurance_company_id',
        'base_price',
        'final_price',
        'discount_percent',
        'surcharge_percent',
        'details',
        'currency'
    ];

    protected $casts = [
        'details' => 'array',
        'base_price' => 'decimal:2',
        'final_price' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'surcharge_percent' => 'decimal:2',
    ];

    public function quoteRequest()
    {
        return $this->belongsTo(QuoteRequest::class);
    }

    public function company()
    {
        return $this->belongsTo(InsuranceCompany::class, 'insurance_company_id');
    }
}
