<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        Tenant::firstOrCreate(
            [
                'code' => 'SYSTEM',
            ],
            [
                'name' => 'System Tenant',
                'is_active' => true,
            ]
        );
    }
}
