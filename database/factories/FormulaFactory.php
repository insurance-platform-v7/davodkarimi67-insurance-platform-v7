<?php

namespace Database\Factories;

use App\Models\Formula;
use Illuminate\Database\Eloquent\Factories\Factory;

class FormulaFactory extends Factory
{
    protected $model = Formula::class;

    public function definition(): array
    {
        return [
            'name' => 'Premium Formula',
            'code' => fake()->unique()->slug(),
            'is_active' => true,
        ];
    }
}
