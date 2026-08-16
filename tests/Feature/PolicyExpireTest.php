<?php

namespace Tests\Feature;

use App\Enums\PolicyStatus;
use App\Models\Policy;
use App\Services\Policy\PolicyWorkflowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PolicyExpireTest extends TestCase
{
    use RefreshDatabase;

    public function test_policy_can_expire(): void
    {
        $policy = Policy::factory()->create([
            'status' => PolicyStatus::ISSUED,
        ]);

        $service = app(PolicyWorkflowService::class);

        $result = $service->expire($policy->id);

        $this->assertTrue(
            $result->is($policy)
        );

        $this->assertSame(
            PolicyStatus::EXPIRED,
            $result->status
        );
    }
}
