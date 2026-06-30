<?php

namespace Tests\Feature;

use Tests\TestCase;

class ReinsuranceReportApiTest extends TestCase
{
    public function test_endpoint_exists()
    {
        $response = $this->get(
            '/api/reinsurance/report'
        );

        $this->assertContains(
            $response->status(),
            [200, 401, 403]
        );
    }
}
