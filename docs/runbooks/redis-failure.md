# Redis Failure Runbook

## Incident

Redis service failure or cache availability issue.

## Detection

Symptoms:

- Cache operations fail.
- Redis connection errors.
- Cache-dependent operations are unavailable.

## Recovery Steps

1. Verify Redis service availability.
2. Restore Redis service.
3. Validate Redis connection.
4. Clear application cache if required.

Commands:

php artisan config:clear

## Validation

Verify:

- Application connectivity.
- Cache operations.

Expected:

Application continues after Redis recovery.
