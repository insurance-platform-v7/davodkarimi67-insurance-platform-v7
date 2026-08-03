<?php

namespace Database\Factories;

use App\Models\Formula;
use App\Models\FormulaVersion;
use Illuminate\Database\Eloquent\Factories\Factory;

class FormulaVersionFactory extends Factory
{
    protected $model = FormulaVersion::class;

    public function definition(): array
    {
        return [
            'formula_id' => Formula::factory(),
            'version' => 1,
            'is_active' => true,
            'activated_at' => now(),

            'formula_json' => [
                'operator' => '*',
                'left' => [
                    'var' => 'car_value',
                ],
                'right' => 0.03,
            ],
        ];
    }
}
