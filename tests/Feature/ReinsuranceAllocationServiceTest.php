<?php

namespace Tests\Feature;

use App\Services\Reinsurance\ReinsuranceAllocationService;
use Tests\TestCase;

class ReinsuranceAllocationServiceTest extends TestCase
{
    public function test_service_exists()
    {
        $service = app(
            ReinsuranceAllocationService::class
        );

        $this->assertInstanceOf(
            ReinsuranceAllocationService::class,
            $service
        );
    }
}
