<?php

namespace App\Listeners;

use App\Events\ClaimPaid;
use App\Services\Notification\NotificationService;

class ClaimPaidListener
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    public function handle(
        ClaimPaid $event
    ): void {
        $claim = $event->claim;

        $email = $claim->policy
            ?->customer
            ?->email;

        if (! $email) {
            return;
        }

        $this->notificationService->email(
            $email,
            'Insurance Claim Payment Completed',
            'Your insurance claim payment has been completed successfully.'
        );
    }
}
