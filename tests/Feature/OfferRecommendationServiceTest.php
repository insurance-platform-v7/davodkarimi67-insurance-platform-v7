<?php

namespace Tests\Feature;

use App\Services\Quote\OfferRecommendationService;
use Tests\TestCase;

class OfferRecommendationServiceTest extends TestCase
{
    public function test_offer_recommendation()
    {
        $service = app(
            OfferRecommendationService::class
        );

        $result = $service->recommend([
            [
                'id' => 1,
                'premium' => 1000000,
                'rank_score' => 80,
            ],
            [
                'id' => 2,
                'premium' => 800000,
                'rank_score' => 75,
            ],
            [
                'id' => 3,
                'premium' => 1200000,
                'rank_score' => 95,
            ],
        ]);

        $this->assertEquals(
            3,
            $result['best_offer']['id']
        );

        $this->assertEquals(
            2,
            $result['cheapest_offer']['id']
        );

        $this->assertNotNull(
            $result['balanced_offer']
        );
    }
}
