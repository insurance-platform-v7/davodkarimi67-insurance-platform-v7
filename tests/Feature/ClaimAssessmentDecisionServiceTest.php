<?php

namespace Tests\Feature;

use App\Enums\ClaimStatus;
use App\Models\Claim;
use App\Models\ClaimAssessment;
use App\Models\Policy;
use App\Models\Tenant;
use App\Services\Claim\ClaimAssessmentDecisionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class ClaimAssessmentDecisionServiceTest extends TestCase
{
    use RefreshDatabase;

    private function createClaim(
        Tenant $tenant
    ): Claim {
        app()->instance(
            'tenant',
            $tenant
        );

        $policy = Policy::factory()->create([
            'tenant_id' => $tenant->id,
        ]);

        return Claim::create([
            'tenant_id' => $tenant->id,
            'policy_id' => $policy->id,
            'claim_number' => 'CLM-' . uniqid(),
            'status' => ClaimStatus::UNDER_REVIEW,
            'requested_amount' => 1000000,
            'approved_amount' => null,
            'description' => 'Test insurance claim description',
            'meta' => [],
        ]);
    }

    public function test_service_exists(): void
    {
        $service = app(
            ClaimAssessmentDecisionService::class
        );

        $this->assertInstanceOf(
            ClaimAssessmentDecisionService::class,
            $service
        );
    }

    public function test_normal_assessment_can_proceed(): void
    {
        $tenant = Tenant::factory()->create();

        $claim = $this->createClaim(
            $tenant
        );

        ClaimAssessment::create([
            'tenant_id' => $tenant->id,
            'claim_id' => $claim->id,
            'risk_score' => 20,
            'fraud_suspected' => false,
            'factors' => [
                'amount_check' => 1000000,
            ],
        ]);

        $service = app(
            ClaimAssessmentDecisionService::class
        );

        $this->assertSame(
            'proceed',
            $service->decide($claim)
        );
    }

    public function test_fraud_assessment_requires_review(): void
    {
        $tenant = Tenant::factory()->create();

        $claim = $this->createClaim(
            $tenant
        );

        ClaimAssessment::create([
            'tenant_id' => $tenant->id,
            'claim_id' => $claim->id,
            'risk_score' => 70,
            'fraud_suspected' => true,
            'factors' => [
                'amount_check' => 150000000,
            ],
        ]);

        $service = app(
            ClaimAssessmentDecisionService::class
        );

        $this->assertSame(
            'review',
            $service->decide($claim)
        );
    }

    public function test_claim_without_assessment_is_rejected(): void
    {
        $tenant = Tenant::factory()->create();

        $claim = $this->createClaim(
            $tenant
        );

        $service = app(
            ClaimAssessmentDecisionService::class
        );

        $this->expectException(
            RuntimeException::class
        );

        $service->decide($claim);
    }

    public function test_decision_uses_claim_assessment(): void
    {
        $tenant = Tenant::factory()->create();

        $claim = $this->createClaim(
            $tenant
        );

        $assessment = ClaimAssessment::create([
            'tenant_id' => $tenant->id,
            'claim_id' => $claim->id,
            'risk_score' => 50,
            'fraud_suspected' => true,
            'factors' => [
                'amount_check' => 150000000,
            ],
        ]);

        $service = app(
            ClaimAssessmentDecisionService::class
        );

        $result = $service->decide(
            $claim->fresh()
        );

        $this->assertSame(
            $assessment->claim_id,
            $claim->id
        );

        $this->assertSame(
            'review',
            $result
        );
    }
}
