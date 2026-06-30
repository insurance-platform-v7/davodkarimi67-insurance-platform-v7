<?php

namespace App\Console\Commands;

use App\Events\PolicyExpiringSoon;
use App\Models\Policy;
use App\Services\Policy\RenewalService;
use Illuminate\Console\Command;

class CheckExpiringPolicies extends Command
{
    protected $signature = 'policies:check-expiring';

    protected $description =
        'Check expiring policies';

    public function handle(
        RenewalService $renewalService
    ): int {

        Policy::query()
            ->get()
            ->each(function (
                Policy $policy
            ) use (
                $renewalService
            ) {

                if (
                    $renewalService
                        ->isEligible($policy)
                ) {
                    event(
                        new PolicyExpiringSoon(
                            $policy
                        )
                    );
                }
            });

        return self::SUCCESS;
    }
}
