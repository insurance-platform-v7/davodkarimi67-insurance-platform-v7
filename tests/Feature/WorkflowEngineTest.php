<?php

namespace Tests\Feature;

use App\Enums\PolicyStatus;
use App\Models\Policy;
use App\Models\Tenant;
use App\Models\WorkflowState;
use App\Models\WorkflowTransition;
use App\Services\Workflow\WorkflowEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkflowEngineTest extends TestCase
{
    use RefreshDatabase;

    private function createStates(
        Tenant $tenant
    ): array {
        $pending = WorkflowState::create([
            'tenant_id' => $tenant->id,
            'entity_type' => 'policy',
            'name' => 'Pending Payment',
            'code' => PolicyStatus::PAYMENT_PENDING->value,
            'is_initial' => true,
            'is_final' => false,
            'is_active' => true,
        ]);

        $issued = WorkflowState::create([
            'tenant_id' => $tenant->id,
            'entity_type' => 'policy',
            'name' => 'Issued',
            'code' => PolicyStatus::ISSUED->value,
            'is_initial' => false,
            'is_final' => true,
            'is_active' => true,
        ]);

        WorkflowTransition::create([
            'tenant_id' => $tenant->id,
            'entity_type' => 'policy',
            'from_state_id' => $pending->id,
            'to_state_id' => $issued->id,
            'action' => 'issue',
            'is_active' => true,
        ]);

        return [$pending, $issued];
    }

    public function test_policy_can_transition_to_valid_state(): void
    {
        $tenant = Tenant::factory()->create();

        [$pending, $issued] = $this->createStates($tenant);

        $policy = Policy::factory()->create([
            'tenant_id' => $tenant->id,
            'status' => PolicyStatus::PAYMENT_PENDING,
        ]);

        app()->instance('tenant', $tenant);

        app(WorkflowEngine::class)->transition(
            $policy,
            PolicyStatus::ISSUED->value
        );

        $policy->refresh();

        $this->assertSame(
            PolicyStatus::ISSUED,
            $policy->status
        );

        $this->assertDatabaseHas('workflow_logs', [
            'tenant_id' => $tenant->id,
            'entity_type' => 'policy',
            'entity_id' => $policy->id,
            'from_state_id' => $pending->id,
            'to_state_id' => $issued->id,
        ]);
    }

    public function test_invalid_transition_is_rejected(): void
    {
        $tenant = Tenant::factory()->create();

        $pending = WorkflowState::create([
            'tenant_id' => $tenant->id,
            'entity_type' => 'policy',
            'name' => 'Pending Payment',
            'code' => PolicyStatus::PAYMENT_PENDING->value,
            'is_initial' => true,
            'is_final' => false,
            'is_active' => true,
        ]);

        $issued = WorkflowState::create([
            'tenant_id' => $tenant->id,
            'entity_type' => 'policy',
            'name' => 'Issued',
            'code' => PolicyStatus::ISSUED->value,
            'is_initial' => false,
            'is_final' => true,
            'is_active' => true,
        ]);

        $policy = Policy::factory()->create([
            'tenant_id' => $tenant->id,
            'status' => PolicyStatus::PAYMENT_PENDING,
        ]);

        app()->instance('tenant', $tenant);

        $this->expectException(\Exception::class);

        app(WorkflowEngine::class)->transition(
            $policy,
            PolicyStatus::ISSUED->value
        );
    }

    public function test_inactive_transition_is_rejected(): void
    {
        $tenant = Tenant::factory()->create();

        [$pending, $issued] = $this->createStates($tenant);

        WorkflowTransition::query()
            ->where('tenant_id', $tenant->id)
            ->update([
                'is_active' => false,
            ]);

        $policy = Policy::factory()->create([
            'tenant_id' => $tenant->id,
            'status' => PolicyStatus::PAYMENT_PENDING,
        ]);

        app()->instance('tenant', $tenant);

        $this->expectException(\Exception::class);

        app(WorkflowEngine::class)->transition(
            $policy,
            PolicyStatus::ISSUED->value
        );
    }

    public function test_workflow_transition_is_tenant_aware(): void
    {
        $tenantA = Tenant::factory()->create();
        $tenantB = Tenant::factory()->create();

        $this->createStates($tenantA);
        $this->createStates($tenantB);

        $policy = Policy::factory()->create([
            'tenant_id' => $tenantA->id,
            'status' => PolicyStatus::PAYMENT_PENDING,
        ]);

        app()->instance('tenant', $tenantA);

        app(WorkflowEngine::class)->transition(
            $policy,
            PolicyStatus::ISSUED->value
        );

        $this->assertDatabaseHas('workflow_logs', [
            'tenant_id' => $tenantA->id,
            'entity_id' => $policy->id,
        ]);

        $this->assertDatabaseMissing('workflow_logs', [
            'tenant_id' => $tenantB->id,
            'entity_id' => $policy->id,
        ]);
    }
}
