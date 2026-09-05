<?php

namespace App\Listeners;

use App\Events\ClaimApproved;
use App\Services\Notification\NotificationService;

class ClaimApprovedListener
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    public function handle(
        ClaimApproved $event
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
            'Insurance Claim Approved',
            'Your insurance claim has been approved.'
        );
    }
}
