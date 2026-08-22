<?php

namespace Tests\Feature;

use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_tenant_header_is_required(): void
    {
        $response = $this->getJson('/api/reinsurance/report');

        $response->assertStatus(400);

        $response->assertJson([
            'message' => 'X-Tenant-ID header is required.',
        ]);
    }

    public function test_tenant_header_must_be_valid_integer(): void
    {
        $response = $this->withHeader(
            'X-Tenant-ID',
            'invalid'
        )->getJson('/api/reinsurance/report');

        $response->assertStatus(400);

        $response->assertJson([
            'message' => 'Invalid X-Tenant-ID header.',
        ]);
    }

    public function test_tenant_must_exist(): void
    {
        $response = $this->withHeader(
            'X-Tenant-ID',
            '999999'
        )->getJson('/api/reinsurance/report');

        $response->assertStatus(404);

        $response->assertJson([
            'message' => 'Tenant not found.',
        ]);
    }

    public function test_valid_tenant_header_allows_request(): void
    {
        $tenant = Tenant::factory()->create();

        $response = $this
            ->withHeader('X-Tenant-ID', $tenant->id)
            ->getJson('/api/reinsurance/report');

        $this->assertNotSame(400, $response->status());
        $this->assertNotSame(404, $response->status());
    }
}
