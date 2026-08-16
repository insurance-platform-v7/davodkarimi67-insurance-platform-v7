<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InsuranceSystemSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        /*
        |--------------------------------------------------------------------------
        | Workflow States
        |--------------------------------------------------------------------------
        */

        DB::table('workflow_states')->upsert(
            [
                [
                    'tenant_id' => null,
                    'entity_type' => 'quote',
                    'name' => 'Quote Created',
                    'code' => 'quote_created',
                    'is_initial' => true,
                    'is_final' => false,
                    'is_active' => true,
                    'meta' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'tenant_id' => null,
                    'entity_type' => 'quote',
                    'name' => 'Quote Calculated',
                    'code' => 'quote_calculated',
                    'is_initial' => false,
                    'is_final' => false,
                    'is_active' => true,
                    'meta' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'tenant_id' => null,
                    'entity_type' => 'policy',
                    'name' => 'Payment Pending',
                    'code' => 'payment_pending',
                    'is_initial' => true,
                    'is_final' => false,
                    'is_active' => true,
                    'meta' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'tenant_id' => null,
                    'entity_type' => 'policy',
                    'name' => 'Paid',
                    'code' => 'paid',
                    'is_initial' => false,
                    'is_final' => false,
                    'is_active' => true,
                    'meta' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'tenant_id' => null,
                    'entity_type' => 'policy',
                    'name' => 'Issued',
                    'code' => 'issued',
                    'is_initial' => false,
                    'is_final' => true,
                    'is_active' => true,
                    'meta' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
            [
                'tenant_id',
                'entity_type',
                'code',
            ],
            [
                'name',
                'is_initial',
                'is_final',
                'is_active',
                'meta',
                'updated_at',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Workflow Transitions
        |--------------------------------------------------------------------------
        */

        $states = DB::table('workflow_states')
            ->whereNull('tenant_id')
            ->get()
            ->keyBy(fn ($state) => "{$state->entity_type}:{$state->code}");

        $transitions = [
            [
                'entity_type' => 'quote',
                'from' => 'quote_created',
                'to' => 'quote_calculated',
                'action' => 'calculate',
            ],
            [
                'entity_type' => 'policy',
                'from' => 'payment_pending',
                'to' => 'paid',
                'action' => 'pay',
            ],
            [
                'entity_type' => 'policy',
                'from' => 'paid',
                'to' => 'issued',
                'action' => 'issue',
            ],
        ];

        foreach ($transitions as $transition) {
            $fromState = $states->get(
                "{$transition['entity_type']}:{$transition['from']}"
            );

            $toState = $states->get(
                "{$transition['entity_type']}:{$transition['to']}"
            );

            if (! $fromState || ! $toState) {
                continue;
            }

            DB::table('workflow_transitions')->updateOrInsert(
                [
                    'tenant_id' => null,
                    'entity_type' => $transition['entity_type'],
                    'from_state_id' => $fromState->id,
                    'to_state_id' => $toState->id,
                    'action' => $transition['action'],
                ],
                [
                    'conditions' => null,
                    'side_effects' => null,
                    'is_active' => true,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }
    }
}
