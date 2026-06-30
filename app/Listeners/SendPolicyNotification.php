<?php

namespace App\Listeners;

use App\Events\PolicyIssued;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SendPolicyNotification implements ShouldQueue
{
    public function handle(PolicyIssued $event): void
    {
        $policy = $event->policy;

        Log::info('Policy issued notification', [
            'policy_id' => $policy->id,
            'policy_number' => $policy->policy_number,
        ]);
    }
}
