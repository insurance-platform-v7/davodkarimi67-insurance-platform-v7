<?php

namespace App\Services\Quote;

class OfferRecommendationService
{
    /**
     * @param array<int, array{
     *     rank_score?: int|float,
     *     premium?: int|float,
     *     ...
     * }> $offers
     * @return array{
     *     best_offer: array<string, mixed>|null,
     *     cheapest_offer: array<string, mixed>|null,
     *     balanced_offer: array<string, mixed>|null
     * }
     */
    public function recommend(array $offers): array
    {
        if ($offers === []) {
            return [
                'best_offer' => null,
                'cheapest_offer' => null,
                'balanced_offer' => null,
            ];
        }

        $bestOffer = null;
        $cheapestOffer = null;
        $balancedOffer = null;

        $bestRank = PHP_FLOAT_MIN;
        $lowestPremium = PHP_FLOAT_MAX;
        $bestBalancedScore = PHP_FLOAT_MIN;

        foreach ($offers as $offer) {
            $rankScore = (float) ($offer['rank_score'] ?? 0);
            $premium = max((float) ($offer['premium'] ?? 0), 1.0);

            if ($rankScore > $bestRank) {
                $bestRank = $rankScore;
                $bestOffer = $offer;
            }

            if ($premium < $lowestPremium) {
                $lowestPremium = $premium;
                $cheapestOffer = $offer;
            }

            $balancedScore = $rankScore / $premium;

            if ($balancedScore > $bestBalancedScore) {
                $bestBalancedScore = $balancedScore;
                $balancedOffer = $offer;
            }
        }

        return [
            'best_offer' => $bestOffer,
            'cheapest_offer' => $cheapestOffer,
            'balanced_offer' => $balancedOffer,
        ];
    }
}
