<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Policy;
use App\Models\Quote;
use App\Models\QuoteOffer;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_entity_can_be_created(): void
    {
        $tenant = Tenant::factory()->create();

        app()->instance('tenant', $tenant);

        $customer = Customer::factory()->make([
            'tenant_id' => $tenant->id,
        ]);

        $this->assertInstanceOf(Customer::class, $customer);
        $this->assertSame($tenant->id, $customer->tenant_id);
    }

    public function test_customer_can_be_saved_to_database(): void
    {
        $tenant = Tenant::factory()->create();

        app()->instance('tenant', $tenant);

        $customer = Customer::factory()->create([
            'tenant_id' => $tenant->id,
        ]);

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'tenant_id' => $tenant->id,
            'first_name' => $customer->first_name,
            'last_name' => $customer->last_name,
            'status' => 'active',
        ]);
    }

    public function test_customer_can_be_retrieved_from_database(): void
    {
        $tenant = Tenant::factory()->create();

        app()->instance('tenant', $tenant);

        $customer = Customer::factory()->create([
            'tenant_id' => $tenant->id,
        ]);

        $retrieved = Customer::query()->find($customer->id);

        $this->assertNotNull($retrieved);
        $this->assertTrue($retrieved->is($customer));
        $this->assertSame($customer->first_name, $retrieved->first_name);
        $this->assertSame($customer->last_name, $retrieved->last_name);
    }

    public function test_customer_is_connected_to_quotes_and_policies(): void
    {
        $tenant = Tenant::factory()->create();

        app()->instance('tenant', $tenant);

        $customer = Customer::factory()->create([
            'tenant_id' => $tenant->id,
        ]);

        $quote = Quote::factory()->create([
            'tenant_id' => $tenant->id,
            'customer_id' => $customer->id,
        ]);

        $offer = QuoteOffer::factory()->create([
            'tenant_id' => $tenant->id,
            'quote_id' => $quote->id,
        ]);

        $policy = Policy::factory()->create([
            'tenant_id' => $tenant->id,
            'quote_id' => $quote->id,
            'quote_offer_id' => $offer->id,
            'customer_id' => $customer->id,
        ]);

        $this->assertTrue(
            $customer->quotes->contains($quote)
        );

        $this->assertTrue(
            $customer->policies->contains($policy)
        );

        $this->assertTrue(
            $quote->customer->is($customer)
        );

        $this->assertTrue(
            $policy->customer->is($customer)
        );
    }
}
