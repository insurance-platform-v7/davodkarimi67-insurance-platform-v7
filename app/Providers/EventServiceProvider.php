<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\PolicyIssued;
use App\Listeners\SendPolicyNotification;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        \App\Events\PolicyIssued::class => [
            \App\Listeners\SendPolicyEmailListener::class,
        ],

        \App\Events\PaymentSucceeded::class => [
            \App\Listeners\SendPaymentSmsListener::class,
        ],

        PolicyIssued::class => [
            SendPolicyNotification::class,
        ],


        \App\Events\PolicyExpiringSoon::class => [
            \App\Listeners\SendRenewalReminderListener::class,
        ],


        \App\Events\ClaimApproved::class => [
            \App\Listeners\ClaimApprovedListener::class,
        ],

        \App\Events\ClaimRejected::class => [
            \App\Listeners\ClaimRejectedListener::class,
        ],

        \App\Events\ClaimPaid::class => [
            \App\Listeners\ClaimPaidListener::class,
        ],



    ];
}
