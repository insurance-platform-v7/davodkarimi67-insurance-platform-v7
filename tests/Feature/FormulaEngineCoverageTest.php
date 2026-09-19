<?php

namespace Tests\Feature;

use App\Domain\Formula\FormulaEngine;
use InvalidArgumentException;
use Tests\TestCase;

class FormulaEngineCoverageTest extends TestCase
{
    public function test_it_rejects_empty_formula(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Formula cannot be empty.');

        app(FormulaEngine::class)->execute([], []);
    }

    public function test_it_rejects_non_array_rules(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid formula rules.');

        app(FormulaEngine::class)->execute(
            ['rules' => 'invalid'],
            []
        );
    }

    public function test_it_rejects_non_array_rule(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid formula rule.');

        app(FormulaEngine::class)->execute(
            ['rules' => ['invalid']],
            []
        );
    }

    public function test_it_executes_multiple_rules(): void
    {
        $result = app(FormulaEngine::class)->execute(
            [
                'rules' => [
                    [
                        'type' => 'expression',
                        'expression' => '100 + 50',
                        'output' => 'first',
                    ],
                    [
                        'type' => 'expression',
                        'expression' => '200 + 25',
                        'output' => 'second',
                    ],
                ],
            ],
            []
        );

        $this->assertSame(150.0, $result['first']);
        $this->assertSame(225.0, $result['second']);
    }

    public function test_it_uses_formula_as_single_rule_when_rules_key_is_missing(): void
    {
        $result = app(FormulaEngine::class)->execute(
            [
                'type' => 'expression',
                'expression' => '300 + 25',
            ],
            []
        );

        $this->assertSame(325.0, $result['premium']);
    }
}
