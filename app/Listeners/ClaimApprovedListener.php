<?php

namespace App\Listeners;

use App\Events\ClaimApproved;

class ClaimApprovedListener
{
    public function handle(
        ClaimApproved $event
    ): void {

        logger()->info(
            'Claim approved',
            [
                'claim_id' => $event->claim->id,
            ]
        );
    }
}
