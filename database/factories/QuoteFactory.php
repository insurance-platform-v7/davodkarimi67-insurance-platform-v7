<?php

namespace Database\Factories;

use App\Models\Quote;
use App\Models\Tenant;
use App\Models\InsuranceProduct;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuoteFactory extends Factory
{
    protected $model = Quote::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'insurance_product_id' => InsuranceProduct::factory(),
            'quote_number' => 'Q-' . uniqid(),
            'input_data' => [
                'driver_age' => 30,
                'car_value' => 50000,
            ],
            'status' => 'draft',
        ];
    }
}
