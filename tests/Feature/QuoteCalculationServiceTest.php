<?php

namespace Tests\Feature;

use App\Models\CompanyProduct;
use App\Models\Formula;
use App\Models\FormulaCategory;
use App\Models\FormulaVersion;
use App\Models\InsuranceCompany;
use App\Models\InsuranceProduct;
use App\Models\ProductFormula;
use App\Models\Tenant;
use App\Services\Quote\QuoteCalculationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

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

        $version = FormulaVersion::create([
            'formula_id' => $formula->id,
            'version' => 1,
            'formula_json' => [
                'type' => 'expression',
                'expression' => '{{car_value}} * 0.03',
            ],
            'is_active' => true,
        ]);

        $company = InsuranceCompany::create([
            'tenant_id' => $tenant->id,
            'name' => 'Test Company',
            'code' => 'TEST_COMPANY',
            'active' => true,
        ]);

        $product = InsuranceProduct::create([
            'tenant_id' => $tenant->id,
            'name' => 'Test Product',
            'code' => 'TEST_PRODUCT',
            'category' => 'car',
            'is_active' => true,
        ]);

        $companyProduct = CompanyProduct::create([
            'tenant_id' => $tenant->id,
            'insurance_company_id' => $company->id,
            'insurance_product_id' => $product->id,
            'is_active' => true,
            'config' => [],
        ]);

        ProductFormula::create([
            'tenant_id' => $tenant->id,
            'insurance_product_id' => $product->id,
            'insurance_company_id' => $company->id,
            'formula_id' => $formula->id,
            'formula_version_id' => $version->id,
            'name' => 'Test Car Premium Formula',
            'version' => '1.0.0',
            'formula_json' => $version->formula_json,
            'is_active' => true,
        ]);

        $service = app(QuoteCalculationService::class);

        $data = [
            'driver_age' => 30,
            'car_value' => 50000,
        ];

        $result = $service->calculate(
            $companyProduct,
            $data
        );

        $expected = (int) round(
            $data['car_value'] * 0.03
        );

        $this->assertEquals(
            $expected,
            $result
        );

        $this->assertIsInt($result);

        $this->assertGreaterThan(
            0,
            $result
        );
    }
}
