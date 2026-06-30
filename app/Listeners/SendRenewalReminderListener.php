<?php

namespace App\Listeners;

use App\Events\PolicyExpiringSoon;
use App\Services\Notification\NotificationService;

class SendRenewalReminderListener
{
    public function __construct(
        protected NotificationService $notificationService
    ) {
    }

    public function handle(
        PolicyExpiringSoon $event
    ): void {

        $mobile = $event->policy->customer_mobile
            ?? '09120000000';

        $this->notificationService->sms(
            $mobile,
            'Your insurance policy will expire soon.'
        );
    }
}
