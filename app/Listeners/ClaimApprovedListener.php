<?php

namespace App\Listeners;

use App\Events\ClaimApproved;
use App\Services\Notification\NotificationService;

class ClaimApprovedListener
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    public function handle(ClaimApproved $event): void
    {
        $claim = $event->claim;

        $email = $claim->policy?->customer?->email;

        if (! is_string($email) || $email === '') {
            return;
        }

        $message =
            'Your insurance claim '.
            $claim->claim_number.
            ' has been approved.';

        $this->notificationService->email(
            $email,
            'Insurance Claim Approved',
            $message
        );
    }
}
