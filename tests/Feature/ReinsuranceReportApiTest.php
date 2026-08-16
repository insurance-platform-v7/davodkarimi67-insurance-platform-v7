<?php

namespace Tests\Feature;

use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReinsuranceReportApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_endpoint_exists()
    {
        $tenant = Tenant::create([
            'name' => 'Test Tenant',
            'code' => 'tenant_test',
        ]);

        $response = $this
            ->withHeaders([
                'X-Tenant-ID' => $tenant->id,
            ])
            ->get('/api/reinsurance/report');



        $this->assertContains(
            $response->status(),
            [200, 401, 403]
        );
    }
}
