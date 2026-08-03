# Payment Failure Runbook

## Incident

Payment provider failure or payment callback failure.

## Detection

Symptoms:

- Payment callback validation failure.
- Payment verification failure.
- Payment workflow interruption.

## Recovery Steps

1. Verify payment gateway availability.
2. Validate callback payload.
3. Check payment transaction status.
4. Retry failed payment workflow if required.

## Validation

Verify:

- Payment status consistency.
- Payment workflow state.

Expected:

Payment flow can continue after provider recovery.