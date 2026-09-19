<?php

use Spatie\Backup\Notifications\Notifiable;
use Spatie\Backup\Notifications\Notifications\BackupHasFailedNotification;
use Spatie\Backup\Notifications\Notifications\BackupWasSuccessfulNotification;
use Spatie\Backup\Notifications\Notifications\CleanupHasFailedNotification;
use Spatie\Backup\Notifications\Notifications\CleanupWasSuccessfulNotification;
use Spatie\Backup\Notifications\Notifications\HealthyBackupWasFoundNotification;
use Spatie\Backup\Notifications\Notifications\UnhealthyBackupWasFoundNotification;
use Spatie\Backup\Tasks\Cleanup\Strategies\DefaultStrategy;
use Spatie\Backup\Tasks\Monitor\HealthChecks\MaximumAgeInDays;
use Spatie\Backup\Tasks\Monitor\HealthChecks\MaximumStorageInMegabytes;

return [

    'backup' => [

        'name' => env('APP_NAME', 'insurance-platform'),

        'source' => [

            'files' => [

                'include' => [
                    base_path(),
                ],

                'exclude' => [
                    base_path('vendor'),
                    base_path('node_modules'),
                    storage_path('app/backup-temp'),
                ],

                'follow_links' => false,

                'ignore_unreadable_directories' => false,

                'relative_path' => null,

            ],

            'databases' => [
                'pgsql',
            ],

        ],

        'database_dump_compressor' => null,

        'database_dump_file_extension' => '',

        'destination' => [

            'filename_prefix' => '',

            'disks' => [
                'local',
            ],

        ],

        'temporary_directory' => storage_path('app/backup-temp'),

        'password' => env('BACKUP_ARCHIVE_PASSWORD'),

        'encryption' => 'default',

    ],

    'notifications' => [

        'notifications' => [
            BackupHasFailedNotification::class => ['mail'],
            UnhealthyBackupWasFoundNotification::class => ['mail'],
            CleanupHasFailedNotification::class => ['mail'],
            BackupWasSuccessfulNotification::class => [],
            HealthyBackupWasFoundNotification::class => [],
            CleanupWasSuccessfulNotification::class => [],
        ],

        'notifiable' => Notifiable::class,

        'mail' => [

            'to' => env('BACKUP_NOTIFICATION_EMAIL', 'backup@example.com'),

        ],

    ],

    'monitor_backups' => [

        [

            'name' => env('APP_NAME', 'insurance-platform'),

            'disks' => ['local'],

            'health_checks' => [

                MaximumAgeInDays::class => 2,

                MaximumStorageInMegabytes::class => 10240,

            ],

        ],

    ],

    'cleanup' => [

        'strategy' => DefaultStrategy::class,

        'default_strategy' => [

            'keep_all_backups_for_days' => 7,

            'keep_daily_backups_for_days' => 30,

            'keep_weekly_backups_for_weeks' => 8,

            'keep_monthly_backups_for_months' => 12,

            'keep_yearly_backups_for_years' => 5,

            'delete_oldest_backups_when_using_more_megabytes_than' => 10240,

        ],

    ],

];
