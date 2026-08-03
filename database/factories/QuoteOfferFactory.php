<?php

namespace Database\Factories;

use App\Models\InsuranceCompany;
use App\Models\Quote;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuoteOfferFactory extends Factory
{
    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'quote_id' => Quote::factory(),
            'insurance_company_id' => InsuranceCompany::factory(),
            'premium' => 1000,
            'discount_amount' => 0,
            'tax_amount' => 0,
            'status' => 'offered',
        ];
    }
}
