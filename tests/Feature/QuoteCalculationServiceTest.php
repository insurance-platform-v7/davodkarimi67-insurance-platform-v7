<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Tenant;
use App\Models\Formula;
use App\Models\FormulaVersion;
use App\Models\FormulaCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\Quote\QuoteCalculationService;
use PHPUnit\Framework\Attributes\Test;

class QuoteCalculationServiceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_calculates_and_stores_car_insurance_quote(): void
    {
        $tenant = Tenant::create([
            'name' => 'System',
            'code' => 'SYSTEM',
            'is_active' => true,
        ]);

        $category = FormulaCategory::create([
            'tenant_id' => $tenant->id,
            'name' => 'Car Insurance',
            'code' => 'CAR_INSURANCE',
            'is_active' => true,
        ]);

        $formula = Formula::create([
            'tenant_id' => $tenant->id,
            'formula_category_id' => $category->id,
            'name' => 'Base Car Formula',
            'code' => 'BASE_CAR_FORMULA',
            'is_active' => true,
        ]);

        FormulaVersion::create([
            'formula_id' => $formula->id,
            'version' => 1,
            'formula_json' => [
                'type' => 'expression',
                'expression' => '{{car_value}} * 0.02',
            ],
            'is_active' => true,
        ]);

        $service = app(QuoteCalculationService::class);

        $result = $service->calculate([
            'driver_age' => 30,
            'car_value' => 50000,
        ]);

        $this->assertEquals(
            1000,
            $result['premium']
        );
    }
}
