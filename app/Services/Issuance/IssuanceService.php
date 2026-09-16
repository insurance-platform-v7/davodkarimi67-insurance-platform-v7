<?php

namespace App\Services\Issuance;

use App\Enums\PolicyStatus;
use App\Events\PolicyIssued;
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

            if (!app()->bound('tenant') && $policy->tenant_id) {
                $tenant = $policy->tenant;

                if ($tenant !== null) {
                    app()->instance('tenant', $tenant);
                }
            }

            if ($policy->status !== PolicyStatus::PAID) {
                throw new RuntimeException(
                    'Policy must be paid before issuance.'
                );
            }

            $result = $this->provider->issue($policy);

            $policy->update([
                'policy_number' => $result['policy_number']
                    ?? $policy->policy_number,

                'meta' => array_merge(
                    $policy->meta ?? [],
                    [
                        'issuance' => $result,
                    ]
                ),
            ]);

            $policy = $this->workflow->issue($policy->id);

            $this->audit->log(
                'policy',
                $policy->id,
                'issued',
                [
                    'policy_number' => $policy->policy_number,
                    'provider' => $result['provider'] ?? null,
                    'issued_at' => $result['issued_at'] ?? null,
                ]
            );

            $policy = $policy->refresh();

            PolicyIssued::dispatch($policy);

            return $policy;
        });
    }
}
