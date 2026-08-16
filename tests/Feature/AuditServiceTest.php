<?php

namespace Tests\Feature;

use App\Models\PolicyAuditLog;
use App\Models\Tenant;
use App\Services\Audit\AuditService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_audit_log_is_created_with_tracking_fields(): void
    {
        $tenant = Tenant::factory()->create();

        app()->instance('tenant', $tenant);

        $service = app(AuditService::class);

        $service->log(
            entityType: 'policy',
            entityId: 123,
            action: 'issued',
            payload: [
                'policy_number' => 'POL-1001',
            ],
            correlationId: 'corr-test-001',
            traceId: 'trace-test-001',
            source: 'issuance'
        );

        $this->assertDatabaseHas('policy_audit_logs', [
            'tenant_id' => $tenant->id,
            'entity_type' => 'policy',
            'entity_id' => 123,
            'action' => 'issued',
            'correlation_id' => 'corr-test-001',
            'trace_id' => 'trace-test-001',
            'source' => 'issuance',
        ]);
    }

    public function test_audit_service_generates_tracking_ids_when_not_provided(): void
    {
        $tenant = Tenant::factory()->create();

        app()->instance('tenant', $tenant);

        $service = app(AuditService::class);

        $service->log(
            entityType: 'payment',
            entityId: 456,
            action: 'paid',
            payload: [
                'amount' => 500000,
            ]
        );

        $audit = \DB::table('policy_audit_logs')
            ->where('tenant_id', $tenant->id)
            ->where('entity_type', 'payment')
            ->where('entity_id', 456)
            ->where('action', 'paid')
            ->first();

        $this->assertNotNull($audit);
        $this->assertNotEmpty($audit->correlation_id);
        $this->assertNotEmpty($audit->trace_id);
        $this->assertSame('system', $audit->source);
    }

    public function test_audit_service_does_not_throw_when_payload_cannot_be_encoded(): void
    {
        $tenant = Tenant::factory()->create();

        app()->instance('tenant', $tenant);

        $service = app(AuditService::class);

        $resource = fopen('php://memory', 'r');

        try {
            $service->log(
                entityType: 'policy',
                entityId: 999,
                action: 'test',
                payload: [
                    'invalid' => $resource,
                ]
            );

            $this->assertTrue(true);
        } finally {
            fclose($resource);
        }
    }

    public function test_audit_logs_are_tenant_isolated(): void
    {
        $tenantA = Tenant::factory()->create();
        $tenantB = Tenant::factory()->create();

        $service = app(AuditService::class);

        /*
        |--------------------------------------------------------------------------
        | Tenant A
        |--------------------------------------------------------------------------
        */

        app()->instance('tenant', $tenantA);

        $service->log(
            entityType: 'policy',
            entityId: 1001,
            action: 'issued',
            payload: [
                'tenant' => 'A',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Tenant B
        |--------------------------------------------------------------------------
        */

        app()->instance('tenant', $tenantB);

        $service->log(
            entityType: 'policy',
            entityId: 2001,
            action: 'issued',
            payload: [
                'tenant' => 'B',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Tenant A can see only A
        |--------------------------------------------------------------------------
        */

        app()->instance('tenant', $tenantA);

        $tenantALogs = PolicyAuditLog::query()->get();

        $this->assertCount(1, $tenantALogs);

        $this->assertSame(
            1001,
            $tenantALogs->first()->entity_id
        );

        $this->assertSame(
            $tenantA->id,
            $tenantALogs->first()->tenant_id
        );

        /*
        |--------------------------------------------------------------------------
        | Tenant B can see only B
        |--------------------------------------------------------------------------
        */

        app()->instance('tenant', $tenantB);

        $tenantBLogs = PolicyAuditLog::query()->get();

        $this->assertCount(1, $tenantBLogs);

        $this->assertSame(
            2001,
            $tenantBLogs->first()->entity_id
        );

        $this->assertSame(
            $tenantB->id,
            $tenantBLogs->first()->tenant_id
        );
    }
}
