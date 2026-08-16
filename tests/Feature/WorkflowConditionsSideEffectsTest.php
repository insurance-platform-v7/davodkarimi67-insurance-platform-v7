<?php

namespace Tests\Feature;

use App\Models\Policy;
use App\Models\Tenant;
use App\Models\WorkflowState;
use App\Models\WorkflowTransition;
use App\Services\Workflow\WorkflowEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkflowConditionsSideEffectsTest extends TestCase
{
    use RefreshDatabase;

    private function createWorkflow(
        Tenant $tenant,
        array $conditions = [],
        array $sideEffects = []
    ): array {
        $pending = WorkflowState::create([
            'tenant_id' => $tenant->id,
            'entity_type' => 'policy',
            'name' => 'Pending Payment',
            'code' => 'payment_pending',
            'is_initial' => true,
            'is_final' => false,
            'is_active' => true,
        ]);

        $issued = WorkflowState::create([
            'tenant_id' => $tenant->id,
            'entity_type' => 'policy',
            'name' => 'Issued',
            'code' => 'issued',
            'is_initial' => false,
            'is_final' => true,
            'is_active' => true,
        ]);

        $transition = WorkflowTransition::create([
            'tenant_id' => $tenant->id,
            'entity_type' => 'policy',
            'name' => 'Issue Policy',
            'from_state_id' => $pending->id,
            'to_state_id' => $issued->id,
            'action' => 'issue',
            'conditions' => $conditions,
            'side_effects' => $sideEffects,
            'is_active' => true,
        ]);

        return [$pending, $issued, $transition];
    }

    public function test_transition_passes_when_condition_matches(): void
    {
        $tenant = Tenant::factory()->create();

        $this->createWorkflow(
            $tenant,
            [
                'premium' => [
                    'min' => 100,
                ],
            ]
        );

        $policy = Policy::factory()->create([
            'tenant_id' => $tenant->id,
            'status' => 'payment_pending',
            'premium' => 500,
        ]);

        app()->instance('tenant', $tenant);

        app(WorkflowEngine::class)->transition(
            $policy,
            'issued'
        );

        $policy->refresh();

        $this->assertSame(
            'issued',
            $policy->status->value
        );
    }

    public function test_transition_is_rejected_when_condition_fails(): void
    {
        $tenant = Tenant::factory()->create();

        $this->createWorkflow(
            $tenant,
            [
                'premium' => [
                    'min' => 1000,
                ],
            ]
        );

        $policy = Policy::factory()->create([
            'tenant_id' => $tenant->id,
            'status' => 'payment_pending',
            'premium' => 500,
        ]);

        app()->instance('tenant', $tenant);

        $this->expectException(\Exception::class);

        app(WorkflowEngine::class)->transition(
            $policy,
            'issued'
        );
    }

    public function test_set_side_effect_is_executed(): void
    {
        $tenant = Tenant::factory()->create();

        $this->createWorkflow(
            $tenant,
            [],
            [
                [
                    'type' => 'set',
                    'field' => 'policy_number',
                    'value' => 'POL-TEST-001',
                ],
            ]
        );

        $policy = Policy::factory()->create([
            'tenant_id' => $tenant->id,
            'status' => 'payment_pending',
            'policy_number' => 'POL-OLD-001',
        ]);

        app()->instance('tenant', $tenant);

        app(WorkflowEngine::class)->transition(
            $policy,
            'issued'
        );

        $policy->refresh();

        $this->assertSame(
            'POL-TEST-001',
            $policy->policy_number
        );
    }

    public function test_side_effect_failure_rolls_back_entire_transition(): void
    {
        $tenant = Tenant::factory()->create();

        $this->createWorkflow(
            $tenant,
            [],
            [
                [
                    'type' => 'set',
                    'field' => 'policy_number',
                    'value' => 'POL-NEW-001',
                ],
                [
                    'type' => 'unsupported_effect',
                    'value' => 'this must fail',
                ],
            ]
        );

        $policy = Policy::factory()->create([
            'tenant_id' => $tenant->id,
            'status' => 'payment_pending',
            'policy_number' => 'POL-OLD-001',
        ]);

        app()->instance('tenant', $tenant);

        $this->expectException(\Exception::class);

        try {
            app(WorkflowEngine::class)->transition(
                $policy,
                'issued'
            );
        } finally {
            $policy->refresh();

            $this->assertSame(
                'payment_pending',
                $policy->status->value
            );

            $this->assertSame(
                'POL-OLD-001',
                $policy->policy_number
            );

            $this->assertDatabaseMissing('workflow_logs', [
                'tenant_id' => $tenant->id,
                'entity_id' => $policy->id,
            ]);
        }
    }

    public function test_merge_meta_side_effect_is_executed(): void
    {
        $tenant = Tenant::factory()->create();

        $this->createWorkflow(
            $tenant,
            [],
            [
                [
                    'type' => 'merge_meta',
                    'key' => 'workflow_test',
                    'value' => true,
                ],
            ]
        );

        $policy = Policy::factory()->create([
            'tenant_id' => $tenant->id,
            'status' => 'payment_pending',
            'policy_number' => 'POL-OLD-001',
            'meta' => [
                'existing' => 'value',
            ],
        ]);

        app()->instance('tenant', $tenant);

        app(WorkflowEngine::class)->transition(
            $policy,
            'issued'
        );

        $policy->refresh();

        $this->assertSame(
            'value',
            $policy->meta['existing']
        );

        $this->assertTrue(
            $policy->meta['workflow_test']
        );
    }
}
