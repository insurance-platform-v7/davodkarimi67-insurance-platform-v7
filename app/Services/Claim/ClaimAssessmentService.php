<?php

namespace App\Services\Claim;

use App\Models\Claim;
use App\Models\ClaimAssessment;

class ClaimAssessmentService
{
    public function __construct(
        protected FraudDetectionService $fraudService
    ) {}

    public function assess(
        Claim $claim
    ): ClaimAssessment {

        $result =
            $this->fraudService
                ->analyze($claim);

        return ClaimAssessment::create([
            'claim_id' => $claim->id,
            'risk_score' => $result['risk_score'],
            'fraud_suspected' => $result['fraud_suspected'],
            'factors' => $result['factors'],
        ]);
    }
}
