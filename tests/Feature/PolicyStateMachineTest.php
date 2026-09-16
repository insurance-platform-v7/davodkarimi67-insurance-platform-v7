<?php

namespace Tests\Feature;

use App\Enums\PolicyStatus;
use App\Services\Policy\PolicyStateMachine;
use RuntimeException;
use Tests\TestCase;

class PolicyStateMachineTest extends TestCase
{
    public function test_all_allowed_transitions_are_accepted(): void
    {
        $machine = app(PolicyStateMachine::class);

        $allowed = [
            [PolicyStatus::QUOTE_CREATED, PolicyStatus::UNDERWRITING_PENDING],
            [PolicyStatus::QUOTE_CREATED, PolicyStatus::PAYMENT_PENDING],
            [PolicyStatus::QUOTE_CREATED, PolicyStatus::CANCELED],
            [PolicyStatus::UNDERWRITING_PENDING, PolicyStatus::PAYMENT_PENDING],
            [PolicyStatus::UNDERWRITING_PENDING, PolicyStatus::REJECTED],
            [PolicyStatus::PAYMENT_PENDING, PolicyStatus::PAID],
            [PolicyStatus::PAYMENT_PENDING, PolicyStatus::CANCELED],
            [PolicyStatus::PAID, PolicyStatus::ISSUED],
            [PolicyStatus::ISSUED, PolicyStatus::EXPIRED],
        ];

        foreach ($allowed as [$from, $to]) {
            $this->assertTrue($machine->canTransition($from, $to));
        }
    }

    public function test_terminal_states_cannot_transition(): void
    {
        $machine = app(PolicyStateMachine::class);

        foreach ([
            PolicyStatus::CANCELED,
            PolicyStatus::REJECTED,
            PolicyStatus::EXPIRED,
        ] as $from) {
            $this->assertFalse(
                $machine->canTransition($from, PolicyStatus::ISSUED)
            );
        }
    }

    public function test_invalid_transition_is_rejected(): void
    {
        $machine = app(PolicyStateMachine::class);

        $this->assertFalse(
            $machine->canTransition(
                PolicyStatus::QUOTE_CREATED,
                PolicyStatus::ISSUED
            )
        );
    }

    public function test_validate_allows_same_state(): void
    {
        $machine = app(PolicyStateMachine::class);

        $machine->validate(
            PolicyStatus::PAID,
            PolicyStatus::PAID
        );

        $this->assertTrue(true);
    }

    public function test_validate_accepts_allowed_transition(): void
    {
        $machine = app(PolicyStateMachine::class);

        $machine->validate(
            PolicyStatus::PAID,
            PolicyStatus::ISSUED
        );

        $this->assertTrue(true);
    }

    public function test_validate_throws_for_invalid_transition(): void
    {
        $machine = app(PolicyStateMachine::class);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'Invalid policy transition from [paid] to [canceled].'
        );

        $machine->validate(
            PolicyStatus::PAID,
            PolicyStatus::CANCELED
        );
    }
}