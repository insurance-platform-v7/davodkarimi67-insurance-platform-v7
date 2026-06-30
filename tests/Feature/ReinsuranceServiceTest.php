<?php

namespace Tests\Feature;

use App\Services\Reinsurance\ReinsuranceService;
use Tests\TestCase;

class ReinsuranceServiceTest extends TestCase
{
    public function test_service_exists()
    {
        $service = app(
            ReinsuranceService::class
        );

        $this->assertInstanceOf(
            ReinsuranceService::class,
            $service
        );
    }
}
