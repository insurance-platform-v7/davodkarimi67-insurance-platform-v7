<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Policy;
use App\Models\Quote;
use App\Models\QuoteOffer;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_tenant_can_only_see_its_own_policies(): void
    {
        $tenantA = Tenant::factory()->create();
        $tenantB = Tenant::factory()->create();

        /*
        |--------------------------------------------------------------------------
        | Tenant A
        |--------------------------------------------------------------------------
        */

        app()->instance('tenant', $tenantA);

        $quoteA = Quote::factory()->create([
            'tenant_id' => $tenantA->id,
        ]);

        $offerA = QuoteOffer::factory()->create([
            'tenant_id' => $tenantA->id,
            'quote_id' => $quoteA->id,
        ]);

        $policyA = Policy::factory()->create([
            'tenant_id' => $tenantA->id,
            'quote_id' => $quoteA->id,
            'quote_offer_id' => $offerA->id,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Tenant B
        |--------------------------------------------------------------------------
        */

        app()->instance('tenant', $tenantB);

        $quoteB = Quote::factory()->create([
            'tenant_id' => $tenantB->id,
        ]);

        $offerB = QuoteOffer::factory()->create([
            'tenant_id' => $tenantB->id,
            'quote_id' => $quoteB->id,
        ]);

        $policyB = Policy::factory()->create([
            'tenant_id' => $tenantB->id,
            'quote_id' => $quoteB->id,
            'quote_offer_id' => $offerB->id,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Tenant B should only see its own policy
        |--------------------------------------------------------------------------
        */

        $policies = Policy::all();

        $this->assertCount(1, $policies);
        $this->assertTrue($policies->first()->is($policyB));

        /*
        |--------------------------------------------------------------------------
        | Switch back to Tenant A
        |--------------------------------------------------------------------------
        */

        app()->instance('tenant', $tenantA);

        $policies = Policy::all();

        $this->assertCount(1, $policies);
        $this->assertTrue($policies->first()->is($policyA));
    }

    public function test_tenant_can_only_see_its_own_customers(): void
    {
        $tenantA = Tenant::factory()->create();
        $tenantB = Tenant::factory()->create();

        /*
        |--------------------------------------------------------------------------
        | Tenant A
        |--------------------------------------------------------------------------
        */

        app()->instance('tenant', $tenantA);

        $customerA = Customer::factory()->create([
            'tenant_id' => $tenantA->id,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Tenant B
        |--------------------------------------------------------------------------
        */

        app()->instance('tenant', $tenantB);

        $customerB = Customer::factory()->create([
            'tenant_id' => $tenantB->id,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Tenant B should only see its own customer
        |--------------------------------------------------------------------------
        */

        $customers = Customer::all();

        $this->assertCount(1, $customers);
        $this->assertTrue($customers->first()->is($customerB));

        /*
        |--------------------------------------------------------------------------
        | Switch back to Tenant A
        |--------------------------------------------------------------------------
        */

        app()->instance('tenant', $tenantA);

        $customers = Customer::all();

        $this->assertCount(1, $customers);
        $this->assertTrue($customers->first()->is($customerA));
    }
}
