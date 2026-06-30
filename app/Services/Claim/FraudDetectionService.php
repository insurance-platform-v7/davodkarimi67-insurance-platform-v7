<?php

namespace App\Services\Claim;

use App\Models\Claim;

class FraudDetectionService
{
    public function analyze(
        Claim $claim
    ): array {

        $score = 0;

        if (
            $claim->requested_amount > 100000000
        ) {
            $score += 40;
        }

        if (
            strlen(
                $claim->description ?? ''
            ) < 20
        ) {
            $score += 20;
        }

        return [
            'risk_score' => $score,
            'fraud_suspected' => $score >= 50,
            'factors' => [
                'amount_check' => $claim->requested_amount,
            ],
        ];
    }
}
