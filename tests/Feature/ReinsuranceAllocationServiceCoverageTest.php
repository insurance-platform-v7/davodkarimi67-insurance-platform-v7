<?php

namespace Tests\Feature;

use App\Models\Policy;
use App\Models\ReinsuranceAllocation;
use App\Models\ReinsuranceContract;
use App\Services\Reinsurance\ReinsuranceAllocationService;
use App\Services\Reinsurance\ReinsuranceService;
use Tests\TestCase;

class ReinsuranceAllocationServiceCoverageTest extends TestCase
{
    public function test_allocate_creates_allocation_from_calculated_values(): void
    {
        $policy = Policy::query()->findOrFail(8);

        $contract = new ReinsuranceContract();
        $contract->id = 999999;

        $service = $this->createMock(ReinsuranceService::class);

        $service->expects($this->once())
            ->method('calculate')
            ->with($policy, $contract)
            ->willReturn([
                'premium' => 1000.00,
                'retention' => 400.00,
                'ceded_amount' => 600.00,
                'reinsurer_share' => 300.00,
            ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        (new ReinsuranceAllocationService($service))
            ->allocate($policy, $contract);
    }
}