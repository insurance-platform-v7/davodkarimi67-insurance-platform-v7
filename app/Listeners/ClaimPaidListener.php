<?php

namespace App\Listeners;

use App\Events\ClaimPaid;

class ClaimPaidListener
{
    public function handle(
        ClaimPaid $event
    ): void {

        logger()->info(
            'Claim paid',
            [
                'claim_id' => $event->claim->id,
            ]
        );
    }
}
