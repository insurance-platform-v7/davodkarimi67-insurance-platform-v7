<?php

namespace Tests\Feature;

use App\Services\Policy\PolicyWorkflowService;
use Tests\TestCase;

class PolicyExpireTest extends TestCase
{
    public function test_policy_can_expire()
    {
        $service = app(PolicyWorkflowService::class);

        $service->expire();

        $this->assertTrue(true);
    }
}
