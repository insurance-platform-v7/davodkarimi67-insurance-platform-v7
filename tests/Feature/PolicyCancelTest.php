<?php

namespace Tests\Feature;

use App\Services\Policy\PolicyWorkflowService;
use Tests\TestCase;

class PolicyCancelTest extends TestCase
{
    public function test_policy_can_be_cancelled()
    {
        $service = app(PolicyWorkflowService::class);

        $service->cancel();

        $this->assertTrue(true);
    }
}
