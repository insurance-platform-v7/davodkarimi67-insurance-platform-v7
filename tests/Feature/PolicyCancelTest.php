<?php

namespace Tests\Feature;

use App\Enums\PolicyStatus;
use App\Models\Policy;
use App\Services\Policy\PolicyWorkflowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PolicyCancelTest extends TestCase
{
    use RefreshDatabase;

    public function test_policy_can_be_cancelled(): void
    {
        $policy = Policy::factory()->create([
            'status' => PolicyStatus::PAYMENT_PENDING,
        ]);

        $service = app(PolicyWorkflowService::class);

        $result = $service->cancel($policy->id);

        $this->assertTrue(
            $result->is($policy)
        );

        $this->assertSame(
            PolicyStatus::CANCELED,
            $result->status
        );
    }
}
