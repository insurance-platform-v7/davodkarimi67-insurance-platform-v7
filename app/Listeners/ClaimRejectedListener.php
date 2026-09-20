<?php

namespace App\Listeners;

use App\Events\ClaimRejected;
use App\Services\Notification\NotificationService;

class ClaimRejectedListener
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    public function handle(ClaimRejected $event): void
    {
        $claim = $event->claim;

        $email = $claim->policy?->customer?->email;

        if (! is_string($email) || $email === '') {
            return;
        }

        $meta = $claim->meta;

        $reason = is_array($meta)
            ? ($meta['rejection_reason'] ?? 'No reason was provided.')
            : 'No reason was provided.';

        if (! is_string($reason)) {
            $reason = 'No reason was provided.';
        }

        $message =
            'Your insurance claim '.
            $claim->claim_number.
            ' has been rejected.'.
            PHP_EOL.
            'Reason: '.
            $reason;

        $this->notificationService->email(
            $email,
            'Insurance Claim Rejected',
            $message
        );
    }
}
