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

    public function execute(Quote $quote): array
    {
        $offers = $this->quoteEngine->generateOffers($quote);

        $offerData = array_map(
            static function ($offer): array {
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
