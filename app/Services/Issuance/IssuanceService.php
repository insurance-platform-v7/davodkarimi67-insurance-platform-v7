<?php

namespace App\Services\Issuance;

use App\Enums\PolicyStatus;
use App\Models\Policy;
use App\Services\Audit\AuditService;
use App\Services\Issuance\Contracts\IssuanceProviderInterface;
use App\Services\Policy\PolicyWorkflowService;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class IssuanceService
{
    public function __construct(
        private IssuanceProviderInterface $provider,
        private PolicyWorkflowService $workflow,
        private AuditService $audit,
    ) {}

    public function issue(int $policyId): Policy
    {
        return DB::transaction(function () use ($policyId): Policy {

            $policy = Policy::query()
                ->lockForUpdate()
                ->findOrFail($policyId);

            if ($policy->status === PolicyStatus::ISSUED) {
                return $policy;
            }

            $result = $this->provider->issue($policy);

            if (! isset($result['policy_number'])) {
                throw new RuntimeException(
                    'Issuance provider did not return a policy number.'
                );
            }

            $policy->update([
                'policy_number' => $result['policy_number'],
                'meta' => array_merge(
                    $policy->meta ?? [],
                    $result
                ),
            ]);

            $this->workflow->transition(
                $policy->id,
                PolicyStatus::ISSUED
            );

            $this->audit->log(
                'policy',
                $policy->id,
                'issued',
                $result
            );

            return $policy->fresh();
        });
    }
}
