<?php

namespace Tests\Feature;

use App\Models\CompanyProduct;
use App\Models\Customer;
use App\Models\InsuranceCompany;
use App\Models\InsuranceProduct;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class InsuranceFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_quote_issue_policy_and_initiate_payment(): void
    {
        $tenant = Tenant::forceCreate([
            'name' => 'Test Tenant',
            'code' => 'test-tenant',
        ]);

        $user = User::forceCreate([
            'tenant_id' => $tenant->id,
            'role_id' => null,
            'first_name' => 'Test',
            'last_name' => 'User',
            'mobile' => '09120000001',
            'email' => 'test@example.com',
            'national_code' => '00123456789',
            'password' => Hash::make('password'),
        ]);

        $customer = Customer::forceCreate([
            'tenant_id' => $tenant->id,
            'first_name' => 'Ali',
            'last_name' => 'Ahmadi',
            'mobile' => '09120000002',
            'national_code' => '00123456780',
            'email' => 'customer@example.com',
            'status' => 'active',
            'meta' => [],
        ]);

        $product = InsuranceProduct::forceCreate([
            'tenant_id' => $tenant->id,
            'name' => 'Car Insurance',
            'code' => 'car-insurance',
            'category' => 'car',
            'is_active' => true,
            'meta' => [],
        ]);

        $company = InsuranceCompany::forceCreate([
            'tenant_id' => $tenant->id,
            'name' => 'Test Insurance Company',
            'code' => 'test-insurance-company',
            'active' => true,
            'meta' => [],
        ]);

        CompanyProduct::forceCreate([
            'tenant_id' => $tenant->id,
            'insurance_company_id' => $company->id,
            'insurance_product_id' => $product->id,
            'is_active' => true,
            'config' => [],
        ]);

        Sanctum::actingAs($user);

        // ======================
        // CREATE QUOTE
        // ======================
        $quoteResponse = $this
            ->withHeader('X-Tenant-ID', $tenant->id)
            ->postJson('/api/quotes', [
                'insurance_product_id' => $product->id,
                'customer_id' => $customer->id,
                'parameters' => [
                    'car_value' => 1_000_000_000,
                    'car_model' => 'Peugeot 207',
                    'manufacture_year' => 1400,
                ],
            ]);

        $quoteResponse->assertCreated();

        $quoteId = $quoteResponse->json('quote_id');

        $this->assertDatabaseHas('quotes', [
            'id' => $quoteId,
            'tenant_id' => $tenant->id,
            'insurance_product_id' => $product->id,
            'status' => 'draft',
        ]);

        // ======================
        // QUOTE OFFERS CHECK
        // ======================
        $this->assertDatabaseHas('quote_offers', [
            'quote_id' => $quoteId,
            'tenant_id' => $tenant->id,
            'insurance_company_id' => $company->id,
            'status' => 'offered',
        ]);

        // دقیق بررسی premium (safe & stable)
        $premium = DB::table('quote_offers')
            ->where('quote_id', $quoteId)
            ->value('premium');

        $this->assertNotNull($premium);
        $this->assertIsNumeric($premium);

        // اگر logic شما همین است:
        $this->assertEquals(
            30_000_000,
            (int) $premium
        );

        // ======================
        // ISSUE POLICY
        // ======================
        $offerId = DB::table('quote_offers')
            ->where('quote_id', $quoteId)
            ->value('id');

        $policyResponse = $this
            ->withHeader('X-Tenant-ID', $tenant->id)
            ->postJson('/api/policies/issue', [
                'quote_id' => $quoteId,
                'offer_id' => $offerId,
            ]);

        $policyResponse->assertSuccessful();

        $policyId = $policyResponse->json('policy_id')
            ?? DB::table('policies')
                ->where('quote_id', $quoteId)
                ->value('id');

        $this->assertNotNull($policyId);

        $this->assertDatabaseHas('policies', [
            'id' => $policyId,
            'tenant_id' => $tenant->id,
            'quote_id' => $quoteId,
        ]);

        // ======================
        // PAYMENT
        // ======================
        $paymentResponse = $this
            ->withHeader('X-Tenant-ID', $tenant->id)
            ->postJson('/api/payments/create', [
                'policy_id' => $policyId,
            ]);

        $paymentResponse->assertSuccessful();

        $this->assertDatabaseHas('payments', [
            'policy_id' => $policyId,
        ]);
    }
}
