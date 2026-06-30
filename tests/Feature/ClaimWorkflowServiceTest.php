<?php

namespace Tests\Feature;

use App\Services\Claim\ClaimWorkflowService;
use Tests\TestCase;

class ClaimWorkflowServiceTest extends TestCase
{
    public function test_workflow_service_exists()
    {
        $service = app(
            ClaimWorkflowService::class
        );

        $this->assertInstanceOf(
            ClaimWorkflowService::class,
            $service
        );
    }
}
