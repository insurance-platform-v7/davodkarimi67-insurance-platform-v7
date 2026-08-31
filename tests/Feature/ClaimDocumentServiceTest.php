<?php

namespace Tests\Feature;

use App\Services\Claim\ClaimDocumentService;
use Tests\TestCase;

class ClaimDocumentServiceTest extends TestCase
{
    public function test_service_exists(): void
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
