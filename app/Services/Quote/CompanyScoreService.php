<?php

namespace App\Services\Quote;

class CompanyScoreService
{
    public function calculate(
        float $responseTime,
        float $claimRatio,
        float $customerRating,
        float $settlementSpeed
    ): float {

        $responseScore = max(
            0,
            min(
                100,
                100 - $responseTime
            )
        );

        $claimScore = max(
            0,
            min(
                100,
                100 - ($claimRatio * 100)
            )
        );

        $ratingScore = max(
            0,
            min(
                100,
                $customerRating * 20
            )
        );

        $settlementScore = max(
            0,
            min(
                100,
                $settlementSpeed
            )
        );

        $score =
            ($responseScore * 0.20)
            + ($claimScore * 0.30)
            + ($ratingScore * 0.30)
            + ($settlementScore * 0.20);

        return round($score, 2);
    }
}
