<?php

namespace Database\Factories;

use App\Enums\PolicyStatus;
use App\Models\Policy;
use App\Models\Quote;
use App\Models\QuoteOffer;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

class PolicyFactory extends Factory
{
    protected $model = Policy::class;

    public function definition(): array
    {
        return [

            'tenant_id' => app()->bound('tenant')
                ? app('tenant')->id
                : Tenant::factory(),

            'quote_id' => Quote::factory(),

            'quote_offer_id' => QuoteOffer::factory(),

            'policy_number' => 'P-' . $this->faker->unique()->bothify('########'),

            'premium' => 1000,

            'status' => PolicyStatus::ISSUED,

            'meta' => [],
        ];
    }
}
