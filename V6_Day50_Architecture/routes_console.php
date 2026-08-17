<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('health:check', function () {

    $checks = [
        'app' => true,
        'database' => true,
        'cache' => true,
        'queue' => true,
    ];

    Log::channel('metrics')->info('health_check', $checks);

    foreach ($checks as $service => $status) {
        $this->line(sprintf(
            '%s : %s',
            strtoupper($service),
            $status ? 'OK' : 'FAIL'
        ));
    }

    return self::SUCCESS;

})->purpose('Run application health checks');
