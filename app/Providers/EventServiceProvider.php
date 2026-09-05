<?php

namespace App\Providers;

use App\Events\ClaimApproved;
use App\Events\ClaimPaid;
use App\Events\ClaimRejected;
use App\Events\PaymentFailed;
use App\Events\PaymentSucceeded;
use App\Events\PolicyExpiringSoon;
use App\Events\PolicyIssued;
use App\Events\QuoteOfferCreated;

use App\Listeners\ClaimApprovedListener;
use App\Listeners\ClaimPaidListener;
use App\Listeners\ClaimRejectedListener;
use App\Listeners\PaymentFailedListener;
use App\Listeners\SendPaymentSmsListener;
use App\Listeners\SendPolicyEmailListener;
use App\Listeners\SendPolicyNotification;
use App\Listeners\SendQuoteOfferNotification;
use App\Listeners\SendRenewalReminderListener;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        PolicyIssued::class => [
            SendPolicyEmailListener::class,
            SendPolicyNotification::class,
        ],

        PaymentSucceeded::class => [
            SendPaymentSmsListener::class,
        ],

        PaymentFailed::class => [
            PaymentFailedListener::class,
        ],

        QuoteOfferCreated::class => [
            SendQuoteOfferNotification::class,
        ],

        PolicyExpiringSoon::class => [
            SendRenewalReminderListener::class,
        ],

        ClaimApproved::class => [
            ClaimApprovedListener::class,
        ],

        ClaimRejected::class => [
            ClaimRejectedListener::class,
        ],

        ClaimPaid::class => [
            ClaimPaidListener::class,
        ],
    ];
}
