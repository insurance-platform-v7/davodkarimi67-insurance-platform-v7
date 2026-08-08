<?php

namespace Database\Factories;

use App\Models\CompanyProduct;
use App\Models\Quote;
use App\Models\QuoteOffer;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuoteOfferFactory extends Factory
{
    protected $model = QuoteOffer::class;

    public function definition(): array
    {
        $companyProduct = CompanyProduct::factory()->create();

        return [

            'tenant_id' => app()->bound('tenant')
                ? app('tenant')->id
                : Tenant::factory(),

            'quote_id' => Quote::factory(),

            'company_product_id' => $companyProduct->id,

            'insurance_company_id' => $companyProduct->insurance_company_id,

            'formula_version_id' => null,

            'premium' => 1000,

            'present_value' => 1000,

            'profit' => 100,

            'rank' => 1,

            'breakdown' => [],

            'meta' => [],

            'status' => 'available',
        ];
    }
}
