<?php

namespace Tests\Feature;

use App\Services\Claim\FraudDetectionService;
use Tests\TestCase;

class FraudDetectionServiceTest extends TestCase
{
    public function test_service_exists()
    {
        $service = app(
            FraudDetectionService::class
        );

        $this->assertInstanceOf(
            FraudDetectionService::class,
            $service
        );
    }
}
