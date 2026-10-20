# Database Failure Runbook

## Incident

Database connection failure.

## Detection

Laravel database connection errors.

Example:
SQLSTATE connection refused.

## Recovery Steps

1. Verify PostgreSQL service status.
2. Verify database host and port configuration.
3. Restore database connectivity.
4. Clear Laravel configuration cache.

Commands:

php artisan config:clear

## Validation

Run:

php artisan migrate:status

Expected:
Migration list is available.
