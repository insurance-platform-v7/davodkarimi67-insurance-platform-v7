<?php

namespace App\Application\Quote;

use App\Models\Quote;
use App\Services\Quote\OfferRankingService;
use App\Services\Quote\OfferRecommendationService;
use App\Services\Quote\QuoteEngine;

class QuoteApplicationService
{
    public function __construct(
        protected QuoteEngine $quoteEngine,
        protected OfferRankingService $rankingService,
        protected OfferRecommendationService $recommendationService,
    ) {}

    /**
     * @return array{
     *     offers: array<int, array{
     *         id: int,
     *         premium?: int|float,
     *         company_score?: int|float,
     *         coverage_score?: int|float,
     *         insurance_company_id: int,
     *         company_product_id: int,
     *         rank_score?: int|float
     *     }>,
     *     recommendations: array{
     *         best_offer: array<string, mixed>|null,
     *         cheapest_offer: array<string, mixed>|null,
     *         balanced_offer: array<string, mixed>|null
     *     }
     * }
     */
    public function execute(Quote $quote): array
    {
        $offers = $this->quoteEngine->generateOffers($quote);

        $offerData = array_map(
            static function ($offer): array {
                /**
                 * @var array{
                 *     company_score?: int|float,
                 *     coverage_score?: int|float
                 * } $meta
                 */
                $meta = $offer->meta ?? [];

                return [
                    'id' => $offer->id,
                    'premium' => (float) $offer->premium,
                    'company_score' => (float) ($meta['company_score'] ?? 0),
                    'coverage_score' => (float) ($meta['coverage_score'] ?? 0),
                    'insurance_company_id' => $offer->insurance_company_id,
                    'company_product_id' => $offer->company_product_id,
                ];
            },
            $offers
        );

        $rankedOffers = $this->rankingService->sort(
            $this->rankingService->rank($offerData)
        );

        $recommendations = $this->recommendationService->recommend(
            $rankedOffers
        );

        return [
            'offers' => $rankedOffers,
            'recommendations' => $recommendations,
        ];
    }
}
