<?php

namespace App\Events;

use App\Models\Claim;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ClaimApproved
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public Claim $claim
    ) {}
}
