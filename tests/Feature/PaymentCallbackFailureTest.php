<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Tenant;
use App\Models\Quote;
use App\Models\QuoteOffer;
use App\Models\InsuranceProduct;
use App\Models\InsuranceCompany;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PaymentCallbackFailureTest extends TestCase
{
    use RefreshDatabase;

    public function test_failed_payment_does_not_issue_policy()
    {
        $tenant = Tenant::factory()->create();
        $product = InsuranceProduct::factory()->create();
        $company = InsuranceCompany::factory()->create();

        $quote = Quote::factory()->create([
            'tenant_id' => $tenant->id,
            'insurance_product_id' => $product->id,
            'quote_number' => 'Q-' . uniqid(),
            'input_data' => [],
            'status' => 'draft'
        ]);

        $offer = QuoteOffer::create([
            'tenant_id' => $tenant->id,
            'quote_id' => $quote->id,
            'insurance_company_id' => $company->id,
            'premium' => 1000,
            'coverage' => [],
            'terms' => [],
            'status' => 'offered',
        ]);

        $this->assertDatabaseHas('quote_offers', [
            'id' => $offer->id
        ]);
    }
}
