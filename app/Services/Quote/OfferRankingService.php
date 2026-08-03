<?php

namespace App\Services\Quote;

class OfferRankingService
{
    public function rank(array $offers): array
    {
        return array_map(function (array $offer): array {

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

            return $offer;

        }, $offers);
    }

    public function sort(array $offers): array
    {
        usort(
            $offers,
            static fn (array $a, array $b): int => ($b['rank_score'] ?? 0)
                <=>
                ($a['rank_score'] ?? 0)
        );

        return $offers;
    }

    public function recommend(array $offers): ?int
    {
        if ($offers === []) {
            return null;
        }

        $offers = $this->sort(
            $this->rank($offers)
        );

        return $offers[0]['id'] ?? null;
    }
}
