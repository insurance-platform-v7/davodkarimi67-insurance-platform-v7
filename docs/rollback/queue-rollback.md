# Queue Rollback Procedure

## Purpose

Restore queue processing after failure.

## Rollback Steps

1. Stop current queue workers.
2. Review pending and failed jobs.
3. Restart queue processing.
4. Validate job execution.

Commands:

php artisan queue:restart

php artisan queue:work

## Validation

Check:

php artisan queue:failed

Expected:

Queue processing is restored.

## Completion

Record rollback time and queue recovery result.