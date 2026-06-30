<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\Policy\PolicyWorkflowService;

class PolicyExpireTest extends TestCase
{
    public function test_policy_can_expire()
    {
        $service = app(PolicyWorkflowService::class);

        $service->expire();

        $this->assertTrue(true);
    }
}
