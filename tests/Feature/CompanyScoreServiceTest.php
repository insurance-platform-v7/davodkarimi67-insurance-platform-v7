<?php

namespace Tests\Feature;

use App\Services\Quote\CompanyScoreService;
use Tests\TestCase;

class CompanyScoreServiceTest extends TestCase
{
    public function test_company_score_is_calculated_correctly(): void
    {
        $service = app(CompanyScoreService::class);

        $score = $service->calculate(
            responseTime: 10,
            claimRatio: 0.15,
            customerRating: 4.5,
            settlementSpeed: 90
        );

        /*
         * responseScore:
         * 100 - 10 = 90
         *
         * claimScore:
         * 100 - (0.15 * 100) = 85
         *
         * ratingScore:
         * 4.5 * 20 = 90
         *
         * settlementScore:
         * 90
         *
         * final:
         * (90 * 0.20)
         * + (85 * 0.30)
         * + (90 * 0.30)
         * + (90 * 0.20)
         * = 88.5
         */

        $this->assertSame(88.5, $score);
    }

    public function test_company_score_is_bounded_between_zero_and_one_hundred(): void
    {
        $service = app(CompanyScoreService::class);

        $score = $service->calculate(
            responseTime: 500,
            claimRatio: 2,
            customerRating: 10,
            settlementSpeed: -100
        );

        $this->assertGreaterThanOrEqual(0, $score);
        $this->assertLessThanOrEqual(100, $score);
    }
}
