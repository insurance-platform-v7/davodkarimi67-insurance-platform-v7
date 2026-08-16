<?php

namespace App\Services\Policy;

use App\Enums\PolicyStatus;
use RuntimeException;

class PolicyStateMachine
{
    public function canTransition(
        PolicyStatus $from,
        PolicyStatus $to
    ): bool {
        return match ($from) {
            PolicyStatus::QUOTE_CREATED => in_array($to, [
                PolicyStatus::UNDERWRITING_PENDING,
                PolicyStatus::PAYMENT_PENDING,
                PolicyStatus::CANCELED,
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

            PolicyStatus::ISSUED => in_array($to, [
                PolicyStatus::EXPIRED,
            ], true),

            PolicyStatus::CANCELED,
            PolicyStatus::REJECTED,
            PolicyStatus::EXPIRED => false,
        };
    }

    public function validate(
        PolicyStatus $from,
        PolicyStatus $to
    ): void {
        if ($from === $to) {
            return;
        }

        if (! $this->canTransition($from, $to)) {
            throw new RuntimeException(
                sprintf(
                    'Invalid policy transition from [%s] to [%s].',
                    $from->value,
                    $to->value
                )
            );
        }
    }
}
