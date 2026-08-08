<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Customer>
 */
class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tenant_id' => null,
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'mobile' => fake()->unique()->numerify('09#########'),
            'email' => fake()->unique()->safeEmail(),
            'national_code' => fake()->unique()->numerify('##########'),
            'birth_date' => fake()->date(),
            'status' => 'active',
            'meta' => [],
        ];
    }
}
