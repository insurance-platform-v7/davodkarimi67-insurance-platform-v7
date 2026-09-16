<?php

namespace Tests\Feature;

use App\Models\Policy;
use App\Models\ReinsuranceContract;
use App\Services\Reinsurance\ReinsuranceService;
use Tests\TestCase;

class ReinsuranceServiceCoverageTest extends TestCase
{
    public function test_calculate_returns_full_retention_when_premium_is_below_limit(): void
    {
        $policy = new Policy();
        $policy->premium = 1000;

        $contract = new ReinsuranceContract();
        $contract->retention_limit = 1500;
        $contract->cession_rate = 50;

        $result = (new ReinsuranceService())->calculate($policy, $contract);

        $this->assertSame(1000.0, $result['premium']);
        $this->assertSame(0, $result['ceded_amount']);
        $this->assertSame(0.0, $result['reinsurer_share']);
    }

    public function test_calculate_cedes_amount_and_applies_cession_rate(): void
    {
        $policy = new Policy();
        $policy->premium = 1000;

        $contract = new ReinsuranceContract();
        $contract->retention_limit = 400;
        $contract->cession_rate = 50;

        $result = (new ReinsuranceService())->calculate($policy, $contract);

        $this->assertSame(1000.0, $result['premium']);
        $this->assertSame(600.0, $result['ceded_amount']);
        $this->assertSame(300.0, $result['reinsurer_share']);
    }

    public function test_calculate_rounds_reinsurer_share_to_two_decimals(): void
    {
        $policy = new Policy();
        $policy->premium = 1000;

        $contract = new ReinsuranceContract();
        $contract->retention_limit = 333;
        $contract->cession_rate = 33.33;

        $result = (new ReinsuranceService())->calculate($policy, $contract);

        $this->assertSame(667.0, $result['ceded_amount']);
        $this->assertSame(222.31, $result['reinsurer_share']);
    }
}
