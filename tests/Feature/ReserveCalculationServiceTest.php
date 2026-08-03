<?php

namespace Tests\Feature;

use App\Services\Actuarial\ReserveCalculationService;
use Tests\TestCase;

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
