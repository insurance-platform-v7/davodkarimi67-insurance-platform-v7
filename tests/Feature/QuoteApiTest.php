<?php

namespace Tests\Feature;

use App\Models\CompanyProduct;
use App\Models\Customer;
use App\Models\InsuranceCompany;
use App\Models\InsuranceProduct;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuoteApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_quote()
    {
        $tenant = Tenant::create([
            'name' => 'Test Tenant',
            'code' => 'tenant_test',
        ]);

        $user = User::create([
            'tenant_id' => $tenant->id,
            'first_name' => 'Test',
            'last_name' => 'User',
            'mobile' => '09120000001',
            'email' => 'user@test.com',
            'password' => bcrypt('password'),
        ]);

        $customer = Customer::create([
            'tenant_id' => $tenant->id,
            'first_name' => 'Ali',
            'last_name' => 'Ahmadi',
            'mobile' => '09120000002',
            'national_code' => '00123456780',
            'email' => 'customer@test.com',
        ]);

        $product = InsuranceProduct::create([
            'name' => 'Car Insurance',
            'code' => 'car',
        ]);

        $company = InsuranceCompany::create([
            'tenant_id' => $tenant->id,
            'name' => 'Test Insurance',
            'code' => 'test-ins',
        ]);

        CompanyProduct::create([
            'insurance_company_id' => $company->id,
            'insurance_product_id' => $product->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->withHeaders([
                'X-Tenant-ID' => $tenant->id,
            ])
            ->postJson('/api/quotes', [
                'insurance_product_id' => $product->id,
                'customer_id' => $customer->id,
                'parameters' => [
                    'car_value' => 100000000,
                ],
            ]);



        $response->assertStatus(201);
    }
}
