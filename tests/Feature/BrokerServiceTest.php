<?php

namespace Tests\Feature;

use App\Models\Broker;
use App\Services\Broker\BrokerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BrokerServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_broker_can_be_created(): void
    {
        $service = app(BrokerService::class);

        $broker = $service->create([
            'name' => 'Test Broker',
            'code' => 'BR-001',
            'commission_rate' => 12.50,
            'is_active' => true,
        ]);

        $this->assertInstanceOf(
            Broker::class,
            $broker
        );

        $this->assertSame(
            'Test Broker',
            $broker->name
        );

        $this->assertSame(
            'BR-001',
            $broker->code
        );

        $this->assertSame(
            '12.50',
            $broker->commission_rate
        );

        $this->assertTrue(
            $broker->is_active
        );
    }

    public function test_active_returns_only_active_brokers(): void
    {
        Broker::query()->create([
            'name' => 'Active Broker',
            'code' => 'BR-ACTIVE',
            'commission_rate' => 10,
            'is_active' => true,
        ]);

        Broker::query()->create([
            'name' => 'Inactive Broker',
            'code' => 'BR-INACTIVE',
            'commission_rate' => 15,
            'is_active' => false,
        ]);

        $service = app(BrokerService::class);

        $brokers = $service->active();

        $this->assertCount(1, $brokers);

        $this->assertSame(
            'BR-ACTIVE',
            $brokers->first()->code
        );
    }
}
