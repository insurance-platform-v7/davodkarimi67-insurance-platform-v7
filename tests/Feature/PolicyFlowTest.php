<?php

namespace Tests\Feature;

use App\Models\CompanyProduct;
use App\Models\Customer;
use App\Models\InsuranceCompany;
use App\Models\InsuranceProduct;
use App\Models\Quote;
use App\Models\QuoteOffer;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PolicyFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_policy_can_be_issued_through_controller_dto_action_resource_chain(): void
    {
        $tenant = Tenant::create([
            'name' => 'Policy Test Tenant',
            'code' => 'policy_test',
        ]);

        $user = User::create([
            'tenant_id' => $tenant->id,
            'first_name' => 'Policy',
            'last_name' => 'Tester',
            'mobile' => '09120000011',
            'email' => 'policy-test@example.com',
            'password' => bcrypt('password'),
        ]);

        $customer = Customer::create([
            'tenant_id' => $tenant->id,
            'first_name' => 'Policy',
            'last_name' => 'Customer',
            'mobile' => '09120000012',
            'national_code' => '00123456781',
            'email' => 'policy-customer@example.com',
        ]);

        $product = InsuranceProduct::create([
            'name' => 'Policy Test Product',
            'code' => 'policy-test',
        ]);

        $company = InsuranceCompany::create([
            'tenant_id' => $tenant->id,
            'name' => 'Policy Test Insurance',
            'code' => 'policy-test-ins',
        ]);

        $companyProduct = CompanyProduct::create([
            'tenant_id' => $tenant->id,
            'insurance_company_id' => $company->id,
            'insurance_product_id' => $product->id,
            'is_active' => true,
        ]);

        $quote = Quote::create([
            'tenant_id' => $tenant->id,
            'customer_id' => $customer->id,
            'insurance_product_id' => $product->id,
            'quote_number' => 'QT-POLICY-TEST',
            'input_data' => ['test' => true],
            'status' => 'draft',
        ]);

        $offer = QuoteOffer::create([
            'tenant_id' => $tenant->id,
            'quote_id' => $quote->id,
            'insurance_company_id' => $company->id,
            'company_product_id' => $companyProduct->id,
            'formula_version_id' => null,
            'premium' => 1500,
            'present_value' => 1500,
            'profit' => 150,
            'breakdown' => [],
            'meta' => [],
            'status' => 'available',
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->withHeaders([
                'X-Tenant-ID' => $tenant->id,
            ])
            ->postJson('/api/v1/policies/issue', [
                'offer_id' => $offer->id,
            ]);

        $response
            ->assertCreated()
            ->assertHeader('X-API-Version', 'v1')
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'policy_number',
                    'status',
                    'premium',
                    'quote_id',
                    'quote_offer_id',
                ],
            ]);

        $this->assertDatabaseHas('policies', [
            'tenant_id' => $tenant->id,
            'quote_id' => $quote->id,
            'quote_offer_id' => $offer->id,
            'customer_id' => $customer->id,
        ]);
    }
}
