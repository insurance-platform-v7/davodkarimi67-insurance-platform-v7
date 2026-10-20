<?php

namespace Tests\Integration;

use App\Enums\PolicyStatus;
use App\Events\PolicyIssued;
use App\Models\Customer;
use App\Models\Policy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class PolicyIssuedQueueIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_policy_issued_event_is_queued_and_processed_by_real_worker(): void
    {
        $this->withoutExceptionHandling();

        $customer = Customer::factory()->create([
            'email' => 'v7-worker@example.com',
        ]);

        $policy = Policy::factory()->create([
            'customer_id' => $customer->id,
            'status' => PolicyStatus::ISSUED,
        ]);

        Log::info('V7 DAY 02 TEST MARKER', [
            'policy_number' => $policy->policy_number,
        ]);

        $jobsBefore = DB::table('jobs')->count();

        event(new PolicyIssued($policy));

        $jobsAfterDispatch = DB::table('jobs')->count();

        $this->assertSame(
            $jobsBefore + 1,
            $jobsAfterDispatch,
            'PolicyIssued must create exactly one queued job.'
        );

        $queuedJob = DB::table('jobs')
            ->where('id', DB::table('jobs')->max('id'))
            ->first();

        $this->assertNotNull($queuedJob);

        $payload = json_decode($queuedJob->payload, true);

        $this->assertSame(
            'App\\Listeners\\SendPolicyNotification',
            $payload['displayName'] ?? null
        );
        $exitCode = Artisan::call(
            'queue:work',
            [
                'connection' => 'database',
                '--once' => true,
                '--tries' => 1,
            ]
        );

        $this->assertSame(0, $exitCode);

        $jobsAfterWorker = DB::table('jobs')->count();

        $this->assertSame(
            $jobsBefore,
            $jobsAfterWorker,
            'The real queue worker must consume the queued job.'
        );

        $this->assertSame(
            0,
            DB::table('failed_jobs')->count(),
            'No failed jobs are expected.'
        );
    }
}
