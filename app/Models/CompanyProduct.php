<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyProduct extends Model
{
    protected $table = 'company_product';

    protected $fillable = [
        'insurance_company_id',
        'insurance_product_id',
        'is_active',
        'config',
    ];

    protected $casts = ['config' => 'array'];

    public function company()
    {
        return $this->belongsTo(InsuranceCompany::class, 'insurance_company_id');
    }

    public function product()
    {
        return $this->belongsTo(InsuranceProduct::class, 'insurance_product_id');
    }
}
