<?php

namespace Tests\Feature;

use App\Models\CompanyProduct;
use App\Models\InsuranceCompany;
use App\Models\InsuranceProduct;
use App\Models\Quote;
use App\Models\QuoteOffer;
use App\Repositories\Quote\QuoteOfferRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuoteOfferRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_repository_can_find_or_create_offer(): void
    {
        $company = InsuranceCompany::create([
            'name' => 'Repository Insurance',
            'code' => 'repo-insurance',
            'active' => true,
        ]);

        $product = InsuranceProduct::create([
            'name' => 'Repository Car',
            'code' => 'repo-car',
            'category' => 'car',
            'is_active' => true,
        ]);

        $companyProduct = CompanyProduct::create([
            'insurance_company_id' => $company->id,
            'insurance_product_id' => $product->id,
            'is_active' => true,
        ]);

        $quote = Quote::create([
            'tenant_id' => null,
            'insurance_product_id' => $product->id,
            'quote_number' => 'QT-REPO-001',
            'status' => 'draft',
            'input_data' => [],
        ]);

        $repository = app(QuoteOfferRepository::class);

        $offer = $repository->findOrCreate(
            $quote,
            $companyProduct,
            500000
        );

        $this->assertInstanceOf(
            QuoteOffer::class,
            $offer
        );

        $this->assertDatabaseHas('quote_offers', [
            'quote_id' => $quote->id,
            'company_product_id' => $companyProduct->id,
            'premium' => 500000,
        ]);
    }
}
