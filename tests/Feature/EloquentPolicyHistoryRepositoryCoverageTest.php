<?php

namespace Tests\Feature;

use App\Domain\Policy\EloquentPolicyHistoryRepository;
use App\Models\PolicyAuditLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EloquentPolicyHistoryRepositoryCoverageTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_creates_policy_history_with_tracking_fields(): void
    {
        $repository = new EloquentPolicyHistoryRepository();

        $log = $repository->create(
            10,
            'issued',
            ['status' => 'issued'],
            'corr-123',
            'trace-456',
            'test'
        );

        $this->assertInstanceOf(PolicyAuditLog::class, $log);
        $this->assertSame('policy', $log->entity_type);
        $this->assertSame(10, $log->entity_id);
        $this->assertSame('issued', $log->action);
        $this->assertSame(['status' => 'issued'], $log->payload);
        $this->assertSame('corr-123', $log->correlation_id);
        $this->assertSame('trace-456', $log->trace_id);
        $this->assertSame('test', $log->source);
    }

    public function test_create_uses_system_as_default_source(): void
    {
        $repository = new EloquentPolicyHistoryRepository();

        $log = $repository->create(
            20,
            'created',
            ['foo' => 'bar']
        );

        $this->assertSame('system', $log->source);
    }

    public function test_get_history_returns_only_policy_history_in_creation_order(): void
    {
        PolicyAuditLog::create([
            'entity_type' => 'policy',
            'entity_id' => 10,
            'action' => 'created',
            'payload' => ['step' => 1],
        ]);

        PolicyAuditLog::create([
            'entity_type' => 'claim',
            'entity_id' => 10,
            'action' => 'created',
            'payload' => ['step' => 99],
        ]);

        PolicyAuditLog::create([
            'entity_type' => 'policy',
            'entity_id' => 20,
            'action' => 'created',
            'payload' => ['step' => 88],
        ]);

        PolicyAuditLog::create([
            'entity_type' => 'policy',
            'entity_id' => 10,
            'action' => 'issued',
            'payload' => ['step' => 2],
        ]);

        $history = (new EloquentPolicyHistoryRepository())->getHistory(10);

        $this->assertCount(2, $history);
        $this->assertSame(
            ['created', 'issued'],
            $history->pluck('action')->all()
        );
    }
}