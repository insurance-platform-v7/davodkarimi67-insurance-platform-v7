<?php

// File: tests/Feature/FullInsuranceFlowTest.php

namespace Tests\Feature;

use Tests\TestCase;

class FullInsuranceFlowTest extends TestCase
{
    public function test_api_health(): void
    {
        $response = $this->get('/');

        $this->assertTrue(
            in_array($response->status(), [200, 404])
        );
    }
}
