<?php

namespace App\Listeners;

use App\Events\ClaimPaid;
use App\Services\Notification\NotificationService;

class ClaimPaidListener
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    public function handle(ClaimPaid $event): void
    {
        $claim = $event->claim;

        $email = $claim->policy?->customer?->email;

        if (! is_string($email) || $email === '') {
            return;
        }

        $message =
            'Payment for your insurance claim ' .
            $claim->claim_number .
            ' has been completed.';

        $this->notificationService->email(
            $email,
            'Insurance Claim Payment Completed',
            $message
        );
    }
}
