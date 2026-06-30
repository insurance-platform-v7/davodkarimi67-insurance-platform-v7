<?php

namespace App\Listeners;

use App\Events\ClaimRejected;

class ClaimRejectedListener
{
    public function handle(
        ClaimRejected $event
    ): void {

        logger()->info(
            'Claim rejected',
            [
                'claim_id' => $event->claim->id,
            ]
        );
    }
}
