<?php

namespace Database\Factories;

use App\Models\Formula;
use App\Models\FormulaVersion;
use App\Models\ProductFormula;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFormulaFactory extends Factory
{
    protected $model = ProductFormula::class;

    public function definition(): array
    {
        $formula = Formula::factory()->create();

        $version = FormulaVersion::factory()->create([
            'formula_id' => $formula->id,
        ]);

        return [

            'formula_id' => $formula->id,

            'formula_version_id' => $version->id,

            'insurance_company_id' => 1,

            'insurance_product_id' => 1,

            'name' => 'Default',

            'version' => 1,

            'formula_json' => $version->formula_json,

            'is_active' => true,

        ];
    }
}
