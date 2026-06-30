<?php
// File: app/Console/Commands/HealthCheckCommand.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Policy;
use App\Models\Payment;

class HealthCheckCommand extends Command
{
    protected $signature = 'app:health-check';
    protected $description = 'Health check for insurance platform';

    public function handle(): int
    {
        $this->info('Policies: '.Policy::count());
        $this->info('Payments: '.Payment::count());

        return self::SUCCESS;
    }
}
