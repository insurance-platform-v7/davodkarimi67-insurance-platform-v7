# V7 Module Ownership

## Ownership Map

| Module        | Owner Team            | Primary Responsibility                                      |
|---------------|-----------------------|-------------------------------------------------------------|
| FormulaEngine | FormulaEngine Team    | Formula evaluation, conditions, execution and formula-related processing |
| Issuance      | Issuance Team         | Policy issuance processing                                  |
| Notifications | Notifications Team    | Notification jobs, listeners, models and services           |
| Payments      | Payments Team         | Payment processing and gateway integration                  |
| Policies      | Policies Team         | Policy lifecycle and policy-related processing              |
| Products      | Products Team         | Product-related processing                                  |
| Quotes        | Quotes Team           | Quote calculation and quote processing                      |
| Reports       | Reports Team          | Reporting and report-related processing                     |
| Users         | Users Team            | User-related processing                                     |

## Shared Application Areas

- `app/Services`: shared application/domain services that are not isolated to one module.
- `app/Domain`: domain repositories and domain-specific components.
- `app/Http`: API controllers, middleware, requests and resources.
- `app/Models`: shared Eloquent models and cross-module persistence concerns.

## Boundary Rule

Module-specific business logic should remain within the owning module where practical. Shared services and infrastructure may be used when responsibilities cross module boundaries.

## Ownership Definition

Each module under `app/Modules` has an explicitly assigned Owner Team.
The Owner Team is accountable for the module's primary responsibility, boundaries,
maintenance and architecture compliance.

The Owner Team names in this document are governance ownership assignments.
They do not imply that corresponding GitHub Organization Teams currently exist.

## Evidence

Ownership scope is based on the actual module inventory under `app/Modules`:
- `FormulaEngine`
- `Issuance`
- `Notifications`
- `Payments`
- `Policies`
- `Products`
- `Quotes`
- `Reports`
- `Users`

All modules currently present under `app/Modules` are represented in the Ownership Map above.
