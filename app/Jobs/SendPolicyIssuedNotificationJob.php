<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendPolicyIssuedNotificationJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $policyId
    ) {}

    public function handle(): void
    {
        //
    }
}
