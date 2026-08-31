<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoInsuranceSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        /*
        |--------------------------------------------------------------------------
        | Demo Tenant
        |--------------------------------------------------------------------------
        */

        $tenantId = DB::table('tenants')
            ->where('code', 'demo')
            ->value('id');

        if (! $tenantId) {
            $tenantId = DB::table('tenants')->insertGetId([
                'name' => 'Demo Insurance',
                'code' => 'demo',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Demo Insurance Product
        |--------------------------------------------------------------------------
        */

        $productId = DB::table('insurance_products')
            ->where('tenant_id', $tenantId)
            ->where('code', 'LIFE-DEMO')
            ->value('id');

        if (! $productId) {
            $productId = DB::table('insurance_products')->insertGetId([
                'tenant_id' => $tenantId,
                'name' => 'Demo Life Insurance',
                'code' => 'LIFE-DEMO',
                'category' => 'life',
                'is_active' => true,
                'schema' => json_encode([
                    'type' => 'object',
                    'properties' => [
                        'age' => [
                            'type' => 'integer',
                        ],
                        'capital' => [
                            'type' => 'number',
                        ],
                    ],
                ]),
                'meta' => json_encode([
                    'demo' => true,
                ]),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Demo Customer
        |--------------------------------------------------------------------------
        */

        $customerId = DB::table('customers')
            ->where('tenant_id', $tenantId)
            ->where('mobile', '09120000001')
            ->value('id');

        if (! $customerId) {
            $customerId = DB::table('customers')->insertGetId([
                'tenant_id' => $tenantId,
                'first_name' => 'Demo',
                'last_name' => 'Customer',
                'mobile' => '09120000001',
                'email' => 'demo@example.com',
                'national_code' => '0012345678',
                'birth_date' => '1370-01-01',
                'status' => 'active',
                'meta' => json_encode([
                    'demo' => true,
                ]),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $this->command?->info(
            "Demo tenant={$tenantId}, product={$productId}, customer={$customerId}"
        );
    }
}
