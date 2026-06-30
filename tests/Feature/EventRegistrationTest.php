<?php

namespace Tests\Feature;

use App\Events\PolicyIssued;
use App\Listeners\SendPolicyEmailListener;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class EventRegistrationTest extends TestCase
{
    public function test_policy_issued_event_exists()
    {
        Event::fake();

        Event::assertListening(
            PolicyIssued::class,
            SendPolicyEmailListener::class
        );

        $this->assertTrue(true);
    }
}
