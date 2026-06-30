<?php

namespace Tests\Feature;

use App\Services\Claim\ClaimDocumentService;
use Tests\TestCase;

class ClaimDocumentServiceTest extends TestCase
{
    public function test_service_exists()
    {
        $service = app(
            ClaimDocumentService::class
        );

        $this->assertInstanceOf(
            ClaimDocumentService::class,
            $service
        );
    }
}
