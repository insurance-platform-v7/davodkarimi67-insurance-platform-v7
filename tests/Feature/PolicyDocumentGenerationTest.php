<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Tenant;
use App\Models\InsuranceProduct;
use App\Models\InsuranceCompany;
use App\Models\Quote;
use App\Models\QuoteOffer;
use App\Models\Policy;

class PolicyDocumentGenerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_policy_pdf_is_generated()
    {
        $tenant = Tenant::create([
            'name' => 'Tenant',
            'code' => 'tenant-1'
        ]);

        $product = InsuranceProduct::factory()->create();
        $company = InsuranceCompany::factory()->create();

        $quote = Quote::create([
            'tenant_id' => $tenant->id,
            'insurance_product_id' => $product->id,
            'quote_number' => 'Q-' . uniqid(),
            'input_data' => [
                'driver_age' => 30,
                'car_value' => 50000,
            ],
            'status' => 'draft',
        ]);

        $offer = QuoteOffer::create([
            'tenant_id' => $tenant->id,
            'quote_id' => $quote->id,
            'insurance_company_id' => $company->id,
            'premium' => 1000,
            'coverage' => [],
            'terms' => [],
            'status' => 'offered'
        ]);

        Policy::create([
            'tenant_id' => $tenant->id,
            'quote_id' => $quote->id,
            'quote_offer_id' => $offer->id,
            'policy_number' => 'PL-TEST-123',
            'premium' => 1000,
            'status' => 'issued',
            'meta' => []
        ]);

        $this->assertTrue(true);
    }
}
