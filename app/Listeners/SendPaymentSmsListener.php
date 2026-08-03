<?php

namespace App\Listeners;

use App\Events\PaymentSucceeded;
use App\Services\Notification\NotificationService;

class SendPaymentSmsListener
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    public function handle(
        PaymentSucceeded $event
    ): void {

        $mobile = $event->policy->customer_mobile
            ?? '09120000000';

        $this->notificationService->sms(
            $mobile,
            'Payment completed successfully.'
        );
    }
}
