<?php

namespace Tests\Feature\Api\v1;

use App\Models\CompanyProduct;
use App\Models\Formula;
use App\Models\FormulaVersion;
use App\Models\InsuranceCompany;
use App\Models\InsuranceProduct;
use App\Models\ProductFormula;
use App\Models\Quote;
use App\Services\Quote\QuoteEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuoteCalculationTest extends TestCase
{
    use RefreshDatabase;

    public function test_quote_engine_generates_offers(): void
    {
        $company = InsuranceCompany::create([
            'name' => 'Insurance',
            'code' => 'insurance-test',
            'active' => true,
        ]);

        $product = InsuranceProduct::create([
            'name' => 'Car Insurance',
            'code' => 'car-insurance',
            'category' => 'car',
            'is_active' => true,
        ]);

        CompanyProduct::create([
            'insurance_company_id' => $company->id,
            'insurance_product_id' => $product->id,
            'is_active' => true,
        ]);

        $formula = Formula::factory()->create();

        $version = FormulaVersion::factory()->create([
            'formula_id' => $formula->id,
            'version' => 1,
            'is_active' => true,
            'formula_json' => [
                'type' => 'expression',
                'expression' => '{{car_value}} * 0.03',
            ],
        ]);

        ProductFormula::create([
            'insurance_product_id' => $product->id,
            'insurance_company_id' => $company->id,
            'formula_id' => $formula->id,
            'formula_version_id' => $version->id,
            'name' => 'Test Car Premium Formula',
            'version' => '1.0.0',
            'formula_json' => $version->formula_json,
            'is_active' => true,
        ]);

        $quote = Quote::create([
            'tenant_id' => null,
            'insurance_product_id' => $product->id,
            'quote_number' => 'QT-001',
            'status' => 'draft',
            'input_data' => [
                'car_value' => 100000000,
            ],
        ]);

        $engine = app(QuoteEngine::class);
        $engine->generateOffers($quote);

        $this->assertDatabaseHas('quote_offers', [
            'quote_id' => $quote->id,
            'insurance_company_id' => $company->id,
            'status' => 'offered',
        ]);
    }
}