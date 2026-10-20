# Insurance Platform V7.0

Insurance Platform V7.0 is an insurance platform backend built with Laravel, with a Nuxt 3 frontend and PostgreSQL database.

## Technology Stack

- Backend: Laravel 12
- PHP: 8.4
- Frontend: Nuxt 3
- TypeScript
- Database: PostgreSQL

## Repository Structure

- `app/` — Laravel application code
- `routes/` — application and API routes
- `database/` — migrations, seeders and factories
- `tests/` — automated tests
- `docs/` — project, API and governance documentation
- `infrastructure/` — infrastructure-related files
- `resources/` — application resources
- `public/` — public application assets

## API

The API is versioned under:

`/api/v1`

Available API documentation:

`docs/api/openapi.yaml`

Current API areas include:

- Authentication
- Admin dashboard
- Quotes
- Payments
- Policy issuance
- Reinsurance reporting

## Development Setup

Install PHP dependencies:

`composer install`

Create or configure the local environment according to the project's environment configuration.

Run database migrations when required:

`php artisan migrate`

Start the Laravel development server:

`php artisan serve`

## Testing

Run the Laravel test suite:

`php artisan test`

Run PHPStan at maximum analysis level:

`vendor/bin/phpstan analyse --level=max --memory-limit=1G`

Run Laravel Pint:

`vendor/bin/pint`

## V7 Execution Rule

V7 work is executed sequentially:

1. Requirement
2. Implementation
3. Test
4. Evidence
5. PASS

Only the approved scope of the current V7 day is addressed.

Unrelated refactoring, dependency upgrades, database changes, feature development, or architecture changes are out of scope unless explicitly required by the current checklist.

## Documentation

Project documentation is maintained under `docs/`.

Important governance documents include:

- `docs/governance/V7-Requirements-Approval.md`
- `docs/governance/V7-Day05-Acceptance-Criteria.md`
- `docs/governance/V7-Day05-Technical-Notes.md`
- `docs/governance/V7-Code-Review-Approval.md`
- `docs/governance/V7-Versioning-Policy.md`
- `docs/governance/V7-Feature-Flags.md`
- `docs/governance/V7-Logging-Security.md`
- `docs/governance/V7-Module-Ownership.md`
- `docs/governance/V7-Technical-Debt-Backlog.md`

Additional project documentation includes:

- `docs/domain-map.md`
- `docs/formula-inventory.md`
- `docs/refactor-plan.md`
- `docs/V7-README.md`
- `docs/V7-Development-Philosophy.md`
- `docs/api/openapi.yaml`
- `docs/rollback/`
- `docs/runbooks/`

## Versioning

The project follows Semantic Versioning (SemVer):

- MAJOR — incompatible API or contract changes
- MINOR — backward-compatible functionality additions
- PATCH — backward-compatible bug fixes and corrections

Release and version claims must be supported by appropriate Git and release/deployment evidence.

## Feature Flags

The project documents feature flags under:

`docs/governance/V7-Feature-Flags.md`

The currently documented formula engine flag is:

`formula_engine_v2`

## Governance

V7 governance requires evidence-based completion of each checklist item.

A requirement is considered PASS only when the required implementation, verification, and evidence are available.

## Scope Control

Architecture changes, unrelated refactoring, dependency upgrades, and new functionality must not be introduced unless explicitly required by the approved V7 scope.
