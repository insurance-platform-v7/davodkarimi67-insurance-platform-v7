<?php
// File: app/Services/Issuance/IssuanceService.php

namespace App\Services\Issuance;

use App\Enums\PolicyStatus;
use App\Models\Policy;
use App\Services\Audit\AuditService;
use App\Services\Issuance\Contracts\IssuanceProviderInterface;
use App\Services\Policy\PolicyWorkflowService;
use Illuminate\Support\Facades\DB;

class IssuanceService
{
    public function __construct(
        private IssuanceProviderInterface $provider,
        private PolicyWorkflowService $workflow,
        private AuditService $audit
    ) {}

    public function issue(int $policyId): Policy
    {
        return DB::transaction(function () use ($policyId) {
            $policy = Policy::query()
                ->lockForUpdate()
                ->findOrFail($policyId);

            if ($policy->status->value === PolicyStatus::ISSUED->value) {
                return $policy;
            }

            $result = $this->provider->issue($policy);

            $policy->update([
                'policy_number' => $result['policy_number'],
                'meta' => array_merge($policy->meta ?? [], $result),
            ]);

            $this->workflow->transition($policy, PolicyStatus::ISSUED);

            $this->audit->log(
                'policy',
                $policy->id,
                'issued',
                $result
            );

            return $policy->refresh();
        });
    }
}
