<?php

namespace App\Services\Policy;

use App\Domain\Policy\PolicyRepository;
use App\Enums\PolicyStatus;
use App\Models\Policy;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PolicyWorkflowService
{
    public function __construct(
        private PolicyRepository $policies,
    ) {}

    public function issue(int $policyId): Policy
    {
        return $this->transition($policyId, PolicyStatus::ISSUED);
    }

    public function markPaymentPending(int $policyId): Policy
    {
        return $this->transition($policyId, PolicyStatus::PAYMENT_PENDING);
    }

    public function markPaid(int $policyId): Policy
    {
        return $this->transition($policyId, PolicyStatus::PAID);
    }

    public function transition(
        int $policyId,
        PolicyStatus $toStatus
    ): Policy {
        return DB::transaction(function () use (
            $policyId,
            $toStatus
        ): Policy {
            $policy = $this->policies->findOrFail($policyId);

            // Idempotent transition
            if ($policy->status === $toStatus) {
                return $policy;
            }

            if (! $this->isValid($policy->status, $toStatus)) {
                throw new RuntimeException('Invalid transition.');
            }

            return $this->policies->updateStatus(
                $policy,
                $toStatus
            );
        });
    }

    protected function isValid(
        PolicyStatus $from,
        PolicyStatus $to
    ): bool {
        return match ($from) {
            PolicyStatus::QUOTE_CREATED => in_array($to, [
                PolicyStatus::PAYMENT_PENDING,
                PolicyStatus::UNDERWRITING_PENDING,
            ], true),

            PolicyStatus::UNDERWRITING_PENDING => in_array($to, [
                PolicyStatus::PAYMENT_PENDING,
                PolicyStatus::REJECTED,
            ], true),

            PolicyStatus::PAYMENT_PENDING => in_array($to, [
                PolicyStatus::PAID,
                PolicyStatus::CANCELED,
            ], true),

            PolicyStatus::PAID => in_array($to, [
                PolicyStatus::ISSUED,
            ], true),

            default => false,
        };
    }

    public function cancel(): void
    {
        // Reserved for future implementation.
    }

    public function expire(): void
    {
        // Reserved for future implementation.
    }
}
