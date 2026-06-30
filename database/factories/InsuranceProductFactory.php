<?php

namespace Database\Factories;

use App\Models\InsuranceProduct;
use Illuminate\Database\Eloquent\Factories\Factory;

class InsuranceProductFactory extends Factory
{
    protected $model = InsuranceProduct::class;

    public function definition(): array
    {
        return [
            'tenant_id' => null,
            'name' => 'Test Product',
            'code' => fake()->unique()->slug(),
            'category' => 'car',
            'is_active' => true,
            'schema' => [],
            'meta' => [],
        ];
    }
}
