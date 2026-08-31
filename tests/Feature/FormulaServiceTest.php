<?php

namespace Tests\Feature;

use App\Services\Formula\FormulaService;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FormulaServiceTest extends TestCase
{
    #[Test]
    public function formula_service_can_be_resolved(): void
    {
        $service = app(FormulaService::class);

        $this->assertInstanceOf(
            FormulaService::class,
            $service
        );
    }
}
