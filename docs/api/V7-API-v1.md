# Insurance Platform V7.0 — API v1 Documentation

## Scope

This document describes the currently registered API v1 routes verified from the Laravel route registry.

## Base Path

`/api/v1`

## Authentication

Authentication requirements must follow the middleware attached to each route.

## Tenant Context

Where TenantMiddleware is applied, the request requires:

- `X-Tenant-ID` header
- Numeric positive tenant ID
- Active tenant
- Authenticated user's tenant must match the requested tenant when a user is present

Documented middleware responses include:

- `400` — `X-Tenant-ID` header is required
- `400` — Invalid `X-Tenant-ID` header
- `404` — Tenant not found
- `403` — Tenant mismatch

## Endpoints

| Method | Endpoint | Controller / Action |
|---|---|---|
| GET | `/api/v1/admin/dashboard` | `Api\AdminDashboardController@index` |
| POST | `/api/v1/auth/login` | `Api\AuthController@login` |
| POST | `/api/v1/auth/logout` | `Api\AuthController@logout` |
| GET | `/api/v1/auth/me` | `Api\AuthController@me` |
| POST | `/api/v1/claims` | `Api\ClaimController@store` |
| POST | `/api/v1/issuance/{policyId}` | `Api\IssuanceController@issue` |
| POST | `/api/v1/payments/callback` | `Api\PaymentController@callback` |
| POST | `/api/v1/payments/create` | `Api\PaymentController@create` |
| POST | `/api/v1/payments/initiate` | `Api\PaymentController@create` |
| POST | `/api/v1/policies/issue` | `Api\PolicyController@issue` |
| POST | `/api/v1/quotes` | `Api\QuoteController@store` |
| GET | `/api/v1/reinsurance/report` | `Api\ReinsuranceReportController@index` |

## API Contract Status

The route registry confirms the endpoints above.

Request schemas, response schemas, validation rules, permissions, and endpoint-specific error contracts are documented only where verified from repository evidence. No undocumented request or response fields are inferred here.

## Verification Source

The endpoint list is derived from the current Laravel route registry using:

`php artisan route:list --path=api/v1`

This document is limited to the current repository state and does not claim historical API behavior.
