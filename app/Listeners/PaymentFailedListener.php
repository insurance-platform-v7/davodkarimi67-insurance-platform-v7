<?php

namespace App\Listeners;

use App\Events\PaymentFailed;
use Illuminate\Support\Facades\Log;

class PaymentFailedListener
{
    /**
     * Handle the event.
     */
    public function handle(PaymentFailed $event): void
    {
        Log::warning('Payment failed', [
            'policy_id' => $event->policy->id,
        ]);
    }
}
