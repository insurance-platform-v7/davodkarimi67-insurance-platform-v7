<?php

namespace Tests\Feature;

use App\Services\Reporting\ReportingService;
use Tests\TestCase;

class ReportingServiceTest extends TestCase
{
    public function test_reporting_summary_exists()
    {
        $service = app(
            ReportingService::class
        );

        $summary = $service->summary();

        $this->assertArrayHasKey(
            'premium_total',
            $summary
        );

        $this->assertArrayHasKey(
            'policy_count',
            $summary
        );

        $this->assertArrayHasKey(
            'payment_success_rate',
            $summary
        );
    }
}
