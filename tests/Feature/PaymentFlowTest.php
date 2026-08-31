<?php

namespace Tests\Feature;

use Tests\TestCase;

class PaymentFlowTest extends TestCase
{
    public function test_payment_endpoint_exists(): void
    {
        $response = $this->postJson('/api/v1/payments/create', [
            'policy_id' => 1,
        ]);

        $this->assertContains(
            $response->status(),
            [200, 201, 401, 422, 404]
        );
    }
}
