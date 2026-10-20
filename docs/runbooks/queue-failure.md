# Queue Failure Runbook

## Incident

Queue worker failure or queue processing interruption.

## Detection

Symptoms:

- Jobs remain in queue.
- Queue backlog increases.
- Workers are not processing jobs.

## Recovery Steps

1. Check queue worker status.
2. Restart queue workers.
3. Verify pending jobs processing.

Commands:

php artisan queue:work

php artisan queue:restart

## Validation

Check failed jobs:

php artisan queue:failed

Expected:

Queue processes jobs successfully.
