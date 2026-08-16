<?php

namespace Tests\Feature;

use App\Models\Claim;
use App\Models\Tenant;
use App\Services\Claim\FraudDetectionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FraudDetectionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function createClaim(array $attributes = []): Claim
    {
        $tenant = Tenant::factory()->create();

        return Claim::factory()->create(
            array_merge(
                [
                    'tenant_id' => $tenant->id,
                ],
                $attributes
            )
        );
    }

    public function test_service_exists(): void
    {
        $service = app(
            FraudDetectionService::class
        );

        $this->assertInstanceOf(
            FraudDetectionService::class,
            $service
        );
    }

    public function test_low_risk_claim_returns_low_score(): void
    {
        $claim = $this->createClaim([
            'requested_amount' => 50000000,
            'description' => 'This is a normal insurance claim description.',
        ]);

        $service = app(
            FraudDetectionService::class
        );

        $result = $service->analyze($claim);

        $this->assertSame(
            0,
            $result['risk_score']
        );

        $this->assertFalse(
            $result['fraud_suspected']
        );
    }

    public function test_high_amount_increases_risk_score(): void
    {
        $claim = $this->createClaim([
            'requested_amount' => 150000000,
            'description' => 'This is a normal insurance claim description.',
        ]);

        $service = app(
            FraudDetectionService::class
        );

        $result = $service->analyze($claim);

        $this->assertSame(
            40,
            $result['risk_score']
        );

        $this->assertFalse(
            $result['fraud_suspected']
        );

        $this->assertTrue(
            $result['factors']['high_amount']
        );
    }

    public function test_short_description_increases_risk_score(): void
    {
        $claim = $this->createClaim([
            'requested_amount' => 50000000,
            'description' => 'Short',
        ]);

        $service = app(
            FraudDetectionService::class
        );

        $result = $service->analyze($claim);

        $this->assertSame(
            20,
            $result['risk_score']
        );

        $this->assertFalse(
            $result['fraud_suspected']
        );

        $this->assertTrue(
            $result['factors']['short_description']
        );
    }

    public function test_claim_is_flagged_when_risk_score_reaches_fifty(): void
    {
        $claim = $this->createClaim([
            'requested_amount' => 150000000,
            'description' => 'Short',
        ]);

        $service = app(
            FraudDetectionService::class
        );

        $result = $service->analyze($claim);

        $this->assertSame(
            60,
            $result['risk_score']
        );

        $this->assertTrue(
            $result['fraud_suspected']
        );

        $this->assertTrue(
            $result['factors']['high_amount']
        );

        $this->assertTrue(
            $result['factors']['short_description']
        );
    }
}
