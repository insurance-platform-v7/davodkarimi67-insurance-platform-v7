<?php

namespace Tests\Feature;

use App\Enums\PolicyStatus;
use App\Models\Policy;
use App\Services\Issuance\IssuanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IssuanceServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_paid_policy_can_be_issued(): void
    {
        $policy = Policy::factory()->create([
            'status' => PolicyStatus::PAID,
        ]);

        $service = app(IssuanceService::class);

        $result = $service->issue($policy->id);

        $this->assertTrue(
            $result->status === PolicyStatus::ISSUED
        );

        $this->assertNotEmpty(
            $result->policy_number
        );

        $this->assertDatabaseHas('policies', [
            'id' => $policy->id,
            'status' => PolicyStatus::ISSUED->value,
        ]);
    }

    public function test_unpaid_policy_cannot_be_issued(): void
    {
        $policy = Policy::factory()->create([
            'status' => PolicyStatus::QUOTE_CREATED,
        ]);

        $service = app(IssuanceService::class);

        $this->expectException(\RuntimeException::class);

        $service->issue($policy->id);
    }

    public function test_policy_issuance_creates_audit_log(): void
    {
        $policy = Policy::factory()->create([
            'status' => PolicyStatus::PAID,
        ]);

        $service = app(IssuanceService::class);

        $service->issue($policy->id);

        $this->assertDatabaseHas('policy_audit_logs', [
            'entity_type' => 'policy',
            'entity_id' => $policy->id,
            'action' => 'issued',
        ]);
    }
}
