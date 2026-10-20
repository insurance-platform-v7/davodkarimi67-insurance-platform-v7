# Queue Incident Playbook

## Incident Type

Queue processing failure.

## Impact

Background processing is interrupted.

Affected areas:

- Payment processing
- Notifications
- Reports
- Async workflows

## Response

1. Check queue status.
2. Check worker availability.
3. Restart queue workers.
4. Monitor job processing.

## Communication

Record:

- Queue failure time.
- Number of pending jobs.
- Recovery time.

## Resolution

Verify:

- Jobs are processed.
- Failed jobs are reviewed.

## Post Incident

Document cause and recovery result.
