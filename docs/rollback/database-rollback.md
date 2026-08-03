# Database Rollback Procedure

## Purpose

Restore database availability after failure.

## Rollback Steps

1. Stop affected application operations.
2. Verify database backup availability.
3. Restore database if required.
4. Validate database connection.

## Validation

Run:

php artisan migrate:status

Expected:

Database is accessible and migrations are available.

## Completion

Record rollback time and recovery result.