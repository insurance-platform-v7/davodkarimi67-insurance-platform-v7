<?php

namespace Database\Seeders;

use App\Models\Formula;
use App\Models\FormulaVersion;
use App\Models\Tenant;
use App\Models\FormulaCategory;
use Illuminate\Database\Seeder;

class FormulaSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::where(
            'code',
            'SYSTEM'
        )->first();

        $category = FormulaCategory::where(
            'code',
            'CAR_INSURANCE'
        )->first();

        $formula = Formula::firstOrCreate(
            [
                'code' => 'BASE_CAR_FORMULA',
            ],
            [
                'tenant_id' => $tenant->id,
                'formula_category_id' => $category->id,
                'name' => 'Base Car Formula',
                'description' => 'Default pricing formula',
                'is_active' => true,
            ]
        );

        FormulaVersion::firstOrCreate(
            [
                'formula_id' => $formula->id,
                'version' => 1,
            ],
            [
                'formula_json' => [
                    'type' => 'expression',
                    'expression' => '{{car_value}} * 0.02',
                ],
                'is_active' => true,
            ]
        );
    }
}
