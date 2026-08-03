<?php

namespace Database\Seeders;

use App\Models\FormulaCategory;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class FormulaCategorySeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::where(
            'code',
            'SYSTEM'
        )->first();

        $categories = [
            'CAR_INSURANCE',
            'LIFE_INSURANCE',
            'HEALTH_INSURANCE',
            'TRAVEL_INSURANCE',
        ];

        foreach ($categories as $code) {

            FormulaCategory::firstOrCreate(
                [
                    'code' => $code,
                ],
                [
                    'tenant_id' => $tenant->id,
                    'name' => str_replace('_', ' ', $code),
                    'is_active' => true,
                ]
            );
        }
    }
}
