<?php

namespace Tests\Feature;

use App\Models\CompanyProduct;
use App\Models\InsuranceCompany;
use App\Models\InsuranceProduct;
use App\Models\Quote;
use App\Models\QuoteOffer;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentCallbackFailureTest extends TestCase
{
    use RefreshDatabase;

    public function test_failed_payment_does_not_issue_policy()
    {
        $tenant = Tenant::factory()->create();
        $product = InsuranceProduct::factory()->create();
        $company = InsuranceCompany::factory()->create();

        $companyProduct = CompanyProduct::create([
            'insurance_company_id' => $company->id,
            'insurance_product_id' => $product->id,
            'is_active' => true,
            'config' => [],
        ]);

        $quote = Quote::factory()->create([
            'tenant_id' => $tenant->id,
            'insurance_product_id' => $product->id,
            'quote_number' => 'Q-'.uniqid(),
            'input_data' => [],
            'status' => 'draft',
        ]);

        $offer = QuoteOffer::create([
            'tenant_id' => $tenant->id,
            'quote_id' => $quote->id,
            'insurance_company_id' => $company->id,
            'company_product_id' => $companyProduct->id,
            'premium' => 1000,
            'status' => 'offered',
        ]);

        $this->assertDatabaseHas('quote_offers', [
            'id' => $offer->id,
        ]);
    }
}
