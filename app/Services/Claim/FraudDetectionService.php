<?php

namespace App\Services\Claim;

use App\Models\Claim;

class FraudDetectionService
{
    /**
     * @return array<string, mixed>
     */
    public function analyze(Claim $claim): array
    {
        $score = 0;

        $factors = [
            'amount_check' => $claim->requested_amount,
        ];

        if ($claim->requested_amount > 100000000) {
            $score += 40;

            $factors['high_amount'] = true;
        }

        if (strlen($claim->description ?? '') < 20) {
            $score += 20;
            $factors['short_description'] = true;
        }

        return [
            'risk_score' => $score,
            'fraud_suspected' => $score >= 50,
            'factors' => $factors,
        ];
    }
}
