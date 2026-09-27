# V7 Module Ownership

## Ownership Map

| Module | Primary Responsibility |
|---|---|
| FormulaEngine | Formula evaluation, conditions, execution and formula-related processing |
| Issuance | Policy issuance processing |
| Notifications | Notification jobs, listeners, models and services |
| Payments | Payment processing and gateway integration |
| Policies | Policy lifecycle and policy-related processing |
| Products | Product-related processing |
| Quotes | Quote calculation and quote processing |
| Reports | Reporting and report-related processing |
| Users | User-related processing |

## Shared Application Areas

- `app/Services`: shared application/domain services that are not isolated to one module.
- `app/Domain`: domain repositories and domain-specific components.
- `app/Http`: API controllers, middleware, requests and resources.
- `app/Models`: shared Eloquent models and cross-module persistence concerns.

## Boundary Rule

Module-specific business logic should remain within the owning module where practical. Shared services and infrastructure may be used when responsibilities cross module boundaries.

## Evidence

Ownership is derived from the current repository structure under `app/Modules` and its existing Jobs, Listeners, Models and Services directories.
