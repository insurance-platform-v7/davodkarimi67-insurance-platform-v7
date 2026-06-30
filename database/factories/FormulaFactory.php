<?php

namespace Database\Factories;

use App\Models\Formula;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class FormulaFactory extends Factory
{
    protected $model = Formula::class;

    public function definition(): array
    {
        return [
            'name' => 'Test Formula',
            'code' => Str::slug(fake()->unique()->words(3, true)),
            'is_active' => true,
        ];
    }
}
