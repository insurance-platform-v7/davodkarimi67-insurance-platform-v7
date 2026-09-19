<?php

namespace Tests\Feature;

use App\Domain\Formula\FormulaEngine;
use App\Infrastructure\Formula\FormulaEngineAdapter;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FormulaEngineAdapterCoverageTest extends TestCase
{
    #[Test]
    public function it_returns_rounded_premium_from_engine_result(): void
    {
        $engine = Mockery::mock(FormulaEngine::class);
        $engine->shouldReceive('execute')
            ->once()
            ->with(['rules' => []], ['amount' => 100])
            ->andReturn(['premium' => 1234.6]);

        $adapter = new FormulaEngineAdapter($engine);

        $this->assertSame(
            1235,
            $adapter->calculate(['rules' => []], ['amount' => 100])
        );
    }

    #[Test]
    public function it_returns_first_result_when_premium_is_missing(): void
    {
        $engine = Mockery::mock(FormulaEngine::class);
        $engine->shouldReceive('execute')
            ->once()
            ->with(['type' => 'legacy'], ['amount' => 100])
            ->andReturn(['total' => 987.4]);

        $adapter = new FormulaEngineAdapter($engine);

        $this->assertSame(
            987,
            $adapter->calculate(['type' => 'legacy'], ['amount' => 100])
        );
    }
}
