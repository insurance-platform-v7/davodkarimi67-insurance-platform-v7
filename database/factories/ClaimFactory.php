<?php

namespace Database\Factories;

use App\Enums\ClaimStatus;
use App\Models\Claim;
use App\Models\Policy;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClaimFactory extends Factory
{
    protected $model = Claim::class;

    public function definition(): array
    {
        return [
            'tenant_id' => null,
            'policy_id' => Policy::factory(),
            'claim_number' => 'CLM-' . strtoupper($this->faker->unique()->bothify('##########')),
            'status' => ClaimStatus::SUBMITTED,
            'requested_amount' => 1000000,
            'approved_amount' => null,
            'description' => 'Test claim description',
            'meta' => [],
        ];
    }
}
