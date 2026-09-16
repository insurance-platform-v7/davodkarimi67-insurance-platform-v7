<?php

namespace Database\Factories;

use App\Enums\PolicyStatus;
use App\Models\Customer;
use App\Models\Policy;
use App\Models\Quote;
use App\Models\QuoteOffer;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Policy>
 */
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

            'customer_id' => Customer::factory()->state([
                'mobile' => '09120000000',
            ]),

            'policy_number' => 'P-' . $this->faker
                    ->unique()
                    ->bothify('########'),

            'premium' => 1000,

            'status' => PolicyStatus::ISSUED,

            'meta' => [],
        ];
    }
}
