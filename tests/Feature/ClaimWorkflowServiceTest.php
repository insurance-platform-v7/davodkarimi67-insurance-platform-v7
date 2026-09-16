<?php

namespace Tests\Feature;

use App\Enums\ClaimStatus;
use App\Events\ClaimApproved;
use App\Events\ClaimPaid;
use App\Events\ClaimRejected;
use App\Models\Claim;
use App\Models\Policy;
use App\Models\Tenant;
use App\Services\Claim\ClaimWorkflowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use RuntimeException;
use Tests\TestCase;

class ClaimWorkflowServiceTest extends TestCase
{
    use RefreshDatabase;

    private function createClaim(
        Tenant $tenant,
        ClaimStatus $status
    ): Claim {
        app()->instance('tenant', $tenant);

        $policy = Policy::factory()->create([
            'tenant_id' => $tenant->id,
        ]);

        return Claim::create([
            'tenant_id' => $tenant->id,
            'policy_id' => $policy->id,
            'claim_number' => 'CLM-'.uniqid(),
            'status' => $status,
            'requested_amount' => 1000000,
            'approved_amount' => null,
            'description' => 'Test claim description',
            'meta' => [],
        ]);
    }

    public function test_claim_can_move_from_submitted_to_under_review(): void
    {
        $tenant = Tenant::factory()->create();

        $claim = $this->createClaim(
            $tenant,
            ClaimStatus::SUBMITTED
        );

        $service = app(ClaimWorkflowService::class);

        $result = $service->review($claim);

        $this->assertSame(
            ClaimStatus::UNDER_REVIEW,
            $result->status
        );
    }

    public function test_claim_can_be_approved_and_event_is_dispatched(): void
    {
        Event::fake();

        $tenant = Tenant::factory()->create();

        $claim = $this->createClaim(
            $tenant,
            ClaimStatus::UNDER_REVIEW
        );

        $service = app(ClaimWorkflowService::class);

        $result = $service->approve(
            $claim,
            500000
        );

        $this->assertSame(
            ClaimStatus::APPROVED,
            $result->status
        );

        $this->assertEquals(
            500000,
            (float) $result->approved_amount
        );

        Event::assertDispatched(
            ClaimApproved::class
        );
    }

    public function test_claim_can_be_rejected_and_event_is_dispatched(): void
    {
        Event::fake();

        $tenant = Tenant::factory()->create();

        $claim = $this->createClaim(
            $tenant,
            ClaimStatus::UNDER_REVIEW
        );

        $service = app(ClaimWorkflowService::class);

        $result = $service->reject(
            $claim,
            'Insufficient documentation'
        );

        $this->assertSame(
            ClaimStatus::REJECTED,
            $result->status
        );

        $this->assertSame(
            'Insufficient documentation',
            $result->meta['rejection_reason']
        );

        Event::assertDispatched(
            ClaimRejected::class
        );
    }

    public function test_approved_claim_can_be_paid_and_event_is_dispatched(): void
    {
        Event::fake();

        $tenant = Tenant::factory()->create();

        $claim = $this->createClaim(
            $tenant,
            ClaimStatus::APPROVED
        );

        $claim->update([
            'approved_amount' => 500000,
        ]);

        $service = app(ClaimWorkflowService::class);

        $result = $service->pay($claim);

        $this->assertSame(
            ClaimStatus::PAID,
            $result->status
        );

        Event::assertDispatched(
            ClaimPaid::class
        );
    }

    public function test_invalid_claim_transition_is_rejected(): void
    {
        $tenant = Tenant::factory()->create();

        $claim = $this->createClaim(
            $tenant,
            ClaimStatus::SUBMITTED
        );

        $service = app(ClaimWorkflowService::class);

        $this->expectException(
            RuntimeException::class
        );

        $service->pay($claim);
    }

    public function test_claim_can_be_submitted(): void
    {
        $tenant = Tenant::factory()->create();

        $claim = $this->createClaim(
            $tenant,
            ClaimStatus::SUBMITTED
        );

        $service = app(ClaimWorkflowService::class);

        $result = $service->submit($claim);

        $this->assertSame(
            ClaimStatus::SUBMITTED,
            $result->status
        );
    }

    public function test_same_claim_status_returns_without_update(): void
    {
        $tenant = Tenant::factory()->create();

        $claim = $this->createClaim(
            $tenant,
            ClaimStatus::UNDER_REVIEW
        );

        $service = app(ClaimWorkflowService::class);

        $result = $service->review($claim);

        $this->assertSame($claim->id, $result->id);
        $this->assertSame(
            ClaimStatus::UNDER_REVIEW,
            $result->status
        );
    }

    public function test_negative_approved_amount_is_rejected(): void
    {
        $tenant = Tenant::factory()->create();

        $claim = $this->createClaim(
            $tenant,
            ClaimStatus::UNDER_REVIEW
        );

        $service = app(ClaimWorkflowService::class);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'Approved amount cannot be negative.'
        );

        $service->approve($claim, -1);
    }


    public function test_rejected_claim_cannot_be_paid(): void
    {
        $tenant = Tenant::factory()->create();

        $claim = $this->createClaim(
            $tenant,
            ClaimStatus::REJECTED
        );

        $service = app(ClaimWorkflowService::class);

        $this->expectException(
            RuntimeException::class
        );

        $service->pay($claim);
    }
}
