<?php

namespace Tests\Feature;

use App\Services\Claim\ClaimService;
use Tests\TestCase;

class ClaimServiceTest extends TestCase
{
    public function test_claim_service_exists()
    {
        $service = app(
            ClaimService::class
        );

        $this->assertInstanceOf(
            ClaimService::class,
            $service
        );
    }
}
