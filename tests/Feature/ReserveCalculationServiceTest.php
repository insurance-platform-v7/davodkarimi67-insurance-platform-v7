<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\Actuarial\ReserveCalculationService;

class ReserveCalculationServiceTest extends TestCase
{
    public function test_service_exists()
    {
        $service = app(
            ReserveCalculationService::class
        );

        $this->assertInstanceOf(
            ReserveCalculationService::class,
            $service
        );
    }
}
