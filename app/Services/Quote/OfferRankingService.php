<?php

namespace App\Services\Quote;

class OfferRankingService
{
    public function rank(array $offers): array
    {
        foreach ($offers as &$offer) {

            $premium = (float) ($offer['premium'] ?? 0);
            $companyScore = (float) ($offer['company_score'] ?? 0);
            $coverageScore = (float) ($offer['coverage_score'] ?? 0);

            $premiumScore = $premium > 0
                ? (1000000 / $premium)
                : 0;

            $offer['rank_score'] =
                ($premiumScore * 0.3)
                + ($companyScore * 0.4)
                + ($coverageScore * 0.3);
        }

        return $offers;
    }

    public function sort(array $offers): array
    {
        usort(
            $offers,
            fn ($a, $b) =>
                ($b['rank_score'] ?? 0)
                <=>
                ($a['rank_score'] ?? 0)
        );

        return $offers;
    }

    public function recommend(array $offers): ?int
    {
        if (empty($offers)) {
            return null;
        }

        $offers = $this->rank($offers);
        $offers = $this->sort($offers);

        return $offers[0]['id'] ?? null;
    }
}
