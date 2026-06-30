<?php

namespace Tests\Feature;

use App\Services\Claim\ClaimSettlementService;
use Tests\TestCase;

class ClaimSettlementServiceTest extends TestCase
{
    public function test_service_exists()
    {
        $service = app(
            ClaimSettlementService::class
        );

        $this->assertInstanceOf(
            ClaimSettlementService::class,
            $service
        );
    }
}
