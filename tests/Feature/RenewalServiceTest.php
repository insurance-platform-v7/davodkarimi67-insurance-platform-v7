<?php

namespace Tests\Feature;

use App\Services\Policy\RenewalService;
use Tests\TestCase;

class RenewalServiceTest extends TestCase
{
    public function test_service_exists()
    {
        $service = app(
            RenewalService::class
        );

        $this->assertInstanceOf(
            RenewalService::class,
            $service
        );
    }
}
