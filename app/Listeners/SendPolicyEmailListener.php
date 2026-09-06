<?php

namespace App\Listeners;

use App\Events\PolicyIssued;
use App\Services\Notification\NotificationService;

class SendPolicyEmailListener
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    public function handle(PolicyIssued $event): void
    {
        $policy = $event->policy;
        $email = $policy->customer->email
            ?? 'test@example.com';
        $this->notificationService->email(
            $email,
            'Your insurance policy has been issued.',
            'Your insurance policy '
            .$policy->policy_number
            .' has been issued successfully.'
        );
    }
}
