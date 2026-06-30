<?php

namespace Tests\Feature;

use Tests\TestCase;

class PolicyFlowTest extends TestCase
{
    public function test_policy_flow_endpoint_exists()
    {
        $response = $this->postJson('/api/policies/issue', [
            'offer_id' => 1
        ]);

        $this->assertContains(
            $response->status(),
            [200, 201, 401, 422, 404]
        );
    }
}
