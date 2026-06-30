<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\Reinsurance\ReinsuranceReportingService;

class ReinsuranceReportingServiceTest extends TestCase
{
    public function test_service_exists()
    {
        $service = app(
            ReinsuranceReportingService::class
        );

        $this->assertInstanceOf(
            ReinsuranceReportingService::class,
            $service
        );
    }
}
