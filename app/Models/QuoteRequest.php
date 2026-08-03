<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuoteRequest extends Model
{
    protected $fillable = [
        'insurance_type_id',
        'user_id',
        'customer_name',
        'customer_mobile',
        'vehicle_type',
        'vehicle_year',
        'usage_type',
        'no_claim_years',
        'has_previous_claim',
        'status',
    ];

    public function options()
    {
        return $this->hasMany(QuoteOption::class);
    }

    public function insuranceType()
    {
        return $this->belongsTo(InsuranceType::class);
    }
}
