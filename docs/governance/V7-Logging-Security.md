# V7 Logging and Security Conventions

## Logging

- Use Laravel `Log` levels appropriate to the event: `info` for operational/business events and `warning` for recoverable warning conditions.
- Request logs include correlation/request identifiers, tenant context, HTTP method/path, status, and timing metadata.
- Business logs use non-sensitive identifiers such as policy IDs and policy numbers where required for traceability.
- Credentials, access tokens, passwords, secrets, authorization headers, and payment-card data must not be logged.
- Notification logging must not contain raw mobile numbers or message contents; notification SMS logs use a truncated SHA-256 mobile hash and message length.

## Security Scan Evidence

- Source review searched application code for credential and payment-sensitive terms.
- Application logging locations were reviewed individually for sensitive payloads.
