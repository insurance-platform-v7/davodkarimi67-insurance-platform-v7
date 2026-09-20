<?php

namespace Tests\Feature;

use App\Enums\PolicyStatus;
use App\Events\PolicyIssued;
use App\Listeners\SendPolicyEmailListener;
use App\Models\Customer;
use App\Models\Policy;
use App\Services\Notification\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class PolicyIssuedNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_policy_issued_event_sends_email_notification(): void
    {
        $customer = Customer::factory()->create([
            'email' => 'customer@example.com',
        ]);

        $policy = Policy::factory()->create([
            'customer_id' => $customer->id,
            'status' => PolicyStatus::ISSUED,
            'policy_number' => 'P-TEST-001',
        ]);

        $notificationService = Mockery::mock(
            NotificationService::class
        );

        $notificationService
            ->expects('email')
            ->once()
            ->with(
                'customer@example.com',
                'Your insurance policy has been issued.',
                'Your insurance policy P-TEST-001 has been issued successfully.'
            )
            ->andReturn(true);

        $this->app->instance(
            NotificationService::class,
            $notificationService
        );

        $listener = app(
            SendPolicyEmailListener::class
        );

        $listener->handle(
            new PolicyIssued($policy)
        );

        $this->assertTrue(true);
    }
}
