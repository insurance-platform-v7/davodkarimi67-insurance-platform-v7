<?php

namespace App\Listeners;

use App\Events\PolicyIssued;
use App\Services\Notification\NotificationService;

class SendPolicyEmailListener
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    public function handle(
        PolicyIssued $event
    ): void {

        $email = $event->policy->customer_email
            ?? 'test@example.com';

        $this->notificationService->email(
            $email,
            'Policy Issued',
            'Your insurance policy has been issued.'
        );
    }
}
