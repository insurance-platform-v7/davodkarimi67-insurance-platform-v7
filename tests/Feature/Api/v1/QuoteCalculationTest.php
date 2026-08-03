<?php

namespace Tests\Feature\Api\v1;

use App\Models\CompanyProduct;
use App\Models\InsuranceCompany;
use App\Models\InsuranceProduct;
use App\Models\Quote;
use App\Services\Quote\QuoteEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuoteCalculationTest extends TestCase
{
    use RefreshDatabase;

    public function test_quote_engine_generates_offers()
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
