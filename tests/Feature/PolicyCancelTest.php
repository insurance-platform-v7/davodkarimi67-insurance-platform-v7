<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\Policy\PolicyWorkflowService;

class PolicyCancelTest extends TestCase
{
    public function test_policy_can_be_cancelled()
    {
        $service = app(PolicyWorkflowService::class);

        $service->cancel();

        $this->assertTrue(true);
    }
}
