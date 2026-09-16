<?php

namespace Tests\Feature;

use App\Enums\PolicyStatus;
use App\Models\Policy;
use App\Services\Policy\PolicyWorkflowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class PolicyWorkflowServiceCoverageTest extends TestCase
{
    use RefreshDatabase;

    public function test_same_status_returns_policy_without_update(): void
    {
        $policy = Policy::factory()->create([
            'status' => PolicyStatus::PAYMENT_PENDING,
        ]);

        $result = app(PolicyWorkflowService::class)->transition(
            $policy->id,
            PolicyStatus::PAYMENT_PENDING
        );

        $this->assertTrue($result->is($policy));
        $this->assertSame(PolicyStatus::PAYMENT_PENDING, $result->status);
    }

    public function test_invalid_transition_throws_exception(): void
    {
        $policy = Policy::factory()->create([
            'status' => PolicyStatus::PAID,
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'Invalid policy transition from [paid] to [canceled].'
        );

        app(PolicyWorkflowService::class)->transition(
            $policy->id,
            PolicyStatus::CANCELED
        );
    }

    public function test_policy_can_move_from_quote_created_to_underwriting_pending(): void
    {
        $policy = Policy::factory()->create([
            'status' => PolicyStatus::QUOTE_CREATED,
        ]);

        $result = app(PolicyWorkflowService::class)->transition(
            $policy->id,
            PolicyStatus::UNDERWRITING_PENDING
        );

        $this->assertSame(PolicyStatus::UNDERWRITING_PENDING, $result->status);
    }

    public function test_policy_can_move_from_underwriting_pending_to_rejected(): void
    {
        $policy = Policy::factory()->create([
            'status' => PolicyStatus::UNDERWRITING_PENDING,
        ]);

        $result = app(PolicyWorkflowService::class)->transition(
            $policy->id,
            PolicyStatus::REJECTED
        );

        $this->assertSame(PolicyStatus::REJECTED, $result->status);
    }

    public function test_policy_can_move_from_payment_pending_to_paid(): void
    {
        $policy = Policy::factory()->create([
            'status' => PolicyStatus::PAYMENT_PENDING,
        ]);

        $result = app(PolicyWorkflowService::class)->markPaid($policy->id);

        $this->assertSame(PolicyStatus::PAID, $result->status);
    }

    public function test_paid_policy_can_be_issued(): void
    {
        $policy = Policy::factory()->create([
            'status' => PolicyStatus::PAID,
        ]);

        $result = app(PolicyWorkflowService::class)->issue($policy->id);

        $this->assertSame(PolicyStatus::ISSUED, $result->status);
    }
}