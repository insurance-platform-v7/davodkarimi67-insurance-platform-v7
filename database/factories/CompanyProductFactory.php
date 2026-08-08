<?php

namespace Database\Factories;

use App\Models\CompanyProduct;
use App\Models\InsuranceCompany;
use App\Models\InsuranceProduct;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyProductFactory extends Factory
{
    protected $model = CompanyProduct::class;

    public function definition(): array
    {
        return [
            'insurance_company_id' => InsuranceCompany::factory(),
            'insurance_product_id' => InsuranceProduct::factory(),
            'is_active' => true,
            'config' => [],
        ];
    }
}
