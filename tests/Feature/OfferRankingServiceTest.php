<?php

namespace Tests\Feature;

use App\Services\Quote\OfferRankingService;
use Tests\TestCase;

class OfferRankingServiceTest extends TestCase
{
    public function test_offer_ranking()
    {
        $service = app(
            OfferRankingService::class
        );

        $recommendedId = $service->recommend([
            [
                'id' => 1,
                'premium' => 1000000,
                'company_score' => 80,
                'coverage_score' => 85,
            ],
            [
                'id' => 2,
                'premium' => 900000,
                'company_score' => 90,
                'coverage_score' => 90,
            ],
        ]);

        $this->assertEquals(
            2,
            $recommendedId
        );
    }
}
