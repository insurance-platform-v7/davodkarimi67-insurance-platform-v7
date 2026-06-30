<?php
// File: database/factories/FormulaVersionFactory.php

namespace Database\Factories;

use App\Models\FormulaVersion;
use Illuminate\Database\Eloquent\Factories\Factory;

class FormulaVersionFactory extends Factory
{
    protected $model = FormulaVersion::class;

    public function definition(): array
    {
        return [
            'formula_id' => 1,
            'version' => 1,
            'formula_json' => [],
            'is_active' => true,
            'activated_at' => now(),
        ];
    }
}
