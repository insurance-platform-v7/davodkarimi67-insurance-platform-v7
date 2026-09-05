<?php

namespace Tests\Feature;

use App\Events\ClaimApproved;
use App\Events\ClaimPaid;
use App\Events\ClaimRejected;
use App\Listeners\ClaimApprovedListener;
use App\Listeners\ClaimPaidListener;
use App\Listeners\ClaimRejectedListener;
use App\Mail\ClaimNotificationMail;
use App\Models\Claim;
use App\Models\Customer;
use App\Models\Policy;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ClaimNotificationTest extends TestCase
{
    use RefreshDatabase;

    private function createClaim(): Claim
    {
        $tenant = Tenant::factory()->create();

        app()->instance('tenant', $tenant);

        $customer = Customer::factory()->create([
            'tenant_id' => $tenant->id,
            'email' => 'customer@example.com',
        ]);

        $policy = Policy::factory()->create([
            'tenant_id' => $tenant->id,
            'customer_id' => $customer->id,
        ]);

        return Claim::create([
            'tenant_id' => $tenant->id,
            'policy_id' => $policy->id,
            'claim_number' => 'CLM-' . uniqid(),
            'status' => 'under_review',
            'requested_amount' => 1000000,
            'approved_amount' => null,
            'description' => 'Test claim description',
            'meta' => [],
        ]);
    }

    public function test_approved_claim_sends_email_notification(): void
    {
        Mail::fake();

        $claim = $this->createClaim();

        $listener = app(ClaimApprovedListener::class);

        $listener->handle(
            new ClaimApproved($claim)
        );

        Mail::assertSent(
            ClaimNotificationMail::class,
            function (ClaimNotificationMail $mail): bool {
                return $mail->subjectText === 'Insurance Claim Approved';
            }
        );

        Mail::assertSentCount(1);
    }

    public function test_rejected_claim_sends_email_notification(): void
    {
        Mail::fake();

        $claim = $this->createClaim();

        $claim->update([
            'meta' => [
                'rejection_reason' => 'Insufficient documentation',
            ],
        ]);

        $listener = app(ClaimRejectedListener::class);

        $listener->handle(
            new ClaimRejected($claim)
        );

        Mail::assertSent(
            ClaimNotificationMail::class,
            function (ClaimNotificationMail $mail): bool {
                return $mail->subjectText === 'Insurance Claim Rejected';
            }
        );

        Mail::assertSentCount(1);
    }

    public function test_paid_claim_sends_email_notification(): void
    {
        Mail::fake();

        $claim = $this->createClaim();

        $listener = app(ClaimPaidListener::class);

        $listener->handle(
            new ClaimPaid($claim)
        );

        Mail::assertSent(
            ClaimNotificationMail::class,
            function (ClaimNotificationMail $mail): bool {
                return $mail->subjectText === 'Insurance Claim Payment Completed';
            }
        );

        Mail::assertSentCount(1);
    }
}
