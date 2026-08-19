<?php

namespace Tests\Feature;

use App\Application\Quote\QuoteApplicationService;
use App\Models\Quote;
use App\Services\Quote\OfferRankingService;
use App\Services\Quote\OfferRecommendationService;
use App\Services\Quote\QuoteEngine;
use Mockery;
use Tests\TestCase;

class QuoteApplicationServiceTest extends TestCase
{
    public function test_quote_application_service_returns_ranked_recommendations(): void
    {
        $quote = Mockery::mock(Quote::class);

        $offers = [
            (object) [
                'id' => 1,
                'premium' => 1000000,
                'meta' => [
                    'company_score' => 80,
                    'coverage_score' => 85,
                ],
                'insurance_company_id' => 10,
                'company_product_id' => 20,
            ],
            (object) [
                'id' => 2,
                'premium' => 900000,
                'meta' => [
                    'company_score' => 90,
                    'coverage_score' => 90,
                ],
                'insurance_company_id' => 11,
                'company_product_id' => 21,
            ],
        ];

        $engine = Mockery::mock(QuoteEngine::class);
        $engine->shouldReceive('generateOffers')
            ->once()
            ->with($quote)
            ->andReturn($offers);

        $service = new QuoteApplicationService(
            $engine,
            app(OfferRankingService::class),
            app(OfferRecommendationService::class),
        );

        $result = $service->execute($quote);

        $this->assertArrayHasKey('recommendations', $result);
        $this->assertEquals(2, $result['recommendations']['best_offer']['id']);
        $this->assertEquals(2, $result['recommendations']['cheapest_offer']['id']);
        $this->assertEquals(2, $result['recommendations']['balanced_offer']['id']);
    }
}
