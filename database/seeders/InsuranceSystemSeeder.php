<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InsuranceSystemSeeder extends Seeder
{
    public function run(): void
    {

        DB::table('workflow_states')->insert([
            [
                'entity_type' => 'quote',
                'name' => 'Quote Created',
                'code' => 'QUOTE_CREATED',
                'is_initial' => true,
                'is_final' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'entity_type' => 'quote',
                'name' => 'Quote Calculated',
                'code' => 'QUOTE_CALCULATED',
                'is_initial' => false,
                'is_final' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'entity_type' => 'policy',
                'name' => 'Policy Pending Payment',
                'code' => 'POLICY_PENDING_PAYMENT',
                'is_initial' => true,
                'is_final' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'entity_type' => 'policy',
                'name' => 'Policy Issued',
                'code' => 'POLICY_ISSUED',
                'is_initial' => false,
                'is_final' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

    }
}
