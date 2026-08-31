<?php

namespace Tests\Feature;

use App\Services\Formula\FormulaExecutor;
use Tests\TestCase;

class FormulaEngineTest extends TestCase
{
    public function test_it_executes_simple_formula(): void
    {
        $executor = app(FormulaExecutor::class);

        $formulaJson = [
            'type' => 'expression',
            'expression' => '15 + 25',
        ];

        $result = $executor->execute(
            $formulaJson,
            []
        );

        $this->assertSame(40, $result);
    }
}
