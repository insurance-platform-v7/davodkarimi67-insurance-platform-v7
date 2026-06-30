<?php

namespace App\Services\Quote;

class OfferRecommendationService
{
    public function recommend(array $offers): array
    {
        if (empty($offers)) {
            return [
                'best_offer' => null,
                'cheapest_offer' => null,
                'balanced_offer' => null,
            ];
        }

        $bestOffer = collect($offers)
            ->sortByDesc('rank_score')
            ->first();

        $cheapestOffer = collect($offers)
            ->sortBy('premium')
            ->first();

        $balancedOffer = collect($offers)
            ->sortByDesc(function ($offer) {

                $rankScore = $offer['rank_score'] ?? 0;
                $premium = $offer['premium'] ?? 1;

                return $rankScore / max($premium, 1);
            })
            ->first();

        return [
            'best_offer' => $bestOffer,
            'cheapest_offer' => $cheapestOffer,
            'balanced_offer' => $balancedOffer,
        ];
    }
}
