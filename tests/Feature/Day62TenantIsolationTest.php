<?php

namespace Tests\Feature;

use Tests\TestCase;

class Day62TenantIsolationTest extends TestCase
{
    public function test_request_without_tenant_header_is_rejected(): void
    {
        $response = $this->postJson('/api/v1/quotes');

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'X-Tenant-ID header is required.',
            ]);
    }

    public function test_request_with_invalid_tenant_header_is_rejected(): void
    {
        $response = $this->withHeaders([
            'X-Tenant-ID' => 'abc',
        ])->postJson('/api/v1/quotes');

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'Invalid X-Tenant-ID header.',
            ]);
    }

    public function test_request_with_non_existing_tenant_is_rejected(): void
    {
        $response = $this->withHeaders([
            'X-Tenant-ID' => '999999',
        ])->postJson('/api/v1/quotes');

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Tenant not found.',
            ]);
    }
}
