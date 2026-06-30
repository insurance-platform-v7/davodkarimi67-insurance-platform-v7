<?php

namespace Tests\Feature;

use App\Services\Quote\CompanyScoreService;
use Tests\TestCase;

class CompanyScoreServiceTest extends TestCase
{
    public function test_company_score()
    {
        $service = app(
            CompanyScoreService::class
        );

        $score = $service->calculate(
            responseTime: 10,
            claimRatio: 0.15,
            customerRating: 4.5,
            settlementSpeed: 90
        );

        $this->assertGreaterThan(
            0,
            $score
        );
    }
}
