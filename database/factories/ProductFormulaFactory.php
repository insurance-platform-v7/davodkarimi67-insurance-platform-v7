<?php

namespace Database\Factories;

use App\Models\Formula;
use App\Models\ProductFormula;
use App\Models\InsuranceProduct;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFormulaFactory extends Factory
{
    protected $model = ProductFormula::class;

    public function definition(): array
    {
        return [
            'insurance_product_id' => InsuranceProduct::factory(),
            'formula_id' => Formula::factory(),
            'name' => 'Test Formula',
            'is_active' => true,
        ];
    }
}
