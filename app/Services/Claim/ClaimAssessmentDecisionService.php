<?php

namespace App\Services\Claim;

use App\Models\Claim;
use RuntimeException;

class ClaimAssessmentDecisionService
{
    public function decide(Claim $claim): string
    {
        $assessment = $claim->assessment;

        if (! $assessment) {
            throw new RuntimeException(
                'Claim assessment is required before making a decision.'
            );
        }

        if ($assessment->fraud_suspected) {
            return 'review';
        }

        if ($assessment->risk_score >= 50) {
            return 'review';
        }

        return 'proceed';
    }
}
