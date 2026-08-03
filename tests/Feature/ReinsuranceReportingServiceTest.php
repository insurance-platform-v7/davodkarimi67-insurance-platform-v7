<?php

namespace Tests\Feature;

use App\Services\Reinsurance\ReinsuranceReportingService;
use Tests\TestCase;

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
