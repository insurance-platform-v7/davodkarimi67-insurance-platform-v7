<?php

namespace App\Events;

use App\Models\Policy;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PolicyExpiringSoon
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public Policy $policy
    ) {}
}
