<?php

namespace Tests\Unit;

use App\Domain\Formula\Context;
use App\Domain\Formula\ExpressionResolver;
use App\Domain\Formula\RuleExecutor;
use App\Domain\Formula\VariableResolver;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class RuleExecutorTest extends TestCase
{
    public function test_it_rejects_unknown_rule_type(): void
    {
        $executor = new RuleExecutor(
            new VariableResolver(),
            new ExpressionResolver()
        );

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unknown rule type: unknown');

        $executor->execute(
            ['type' => 'unknown'],
            new Context([])
        );
    }

    public function test_it_rejects_empty_expression(): void
    {
        $executor = new RuleExecutor(
            new VariableResolver(),
            new ExpressionResolver()
        );

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Formula expression is empty.');

        $executor->execute(
            ['type' => 'expression'],
            new Context([])
        );
    }

    public function test_it_uses_default_premium_output_key(): void
    {
        $executor = new RuleExecutor(
            new VariableResolver(),
            new ExpressionResolver()
        );

        $context = new Context([]);

        $executor->execute(
            [
                'type' => 'expression',
                'expression' => '100 + 25',
            ],
            $context
        );

        $this->assertSame(
            125.0,
            $context->result()['premium']
        );
    }

    public function test_it_replaces_variables_before_evaluation(): void
    {
        $executor = new RuleExecutor(
            new VariableResolver(),
            new ExpressionResolver()
        );

        $context = new Context([
            'base' => 200,
        ]);

        $executor->execute(
            [
                'type' => 'expression',
                'expression' => '{{base}} + 50',
                'output' => 'total',
            ],
            $context
        );

        $this->assertSame(
            250.0,
            $context->result()['total']
        );
    }

    public function test_it_uses_custom_output_key(): void
    {
        $executor = new RuleExecutor(
            new VariableResolver(),
            new ExpressionResolver()
        );

        $context = new Context([]);

        $executor->execute(
            [
                'type' => 'expression',
                'expression' => '50 * 2',
                'output' => 'discount',
            ],
            $context
        );

        $this->assertSame(
            100.0,
            $context->result()['discount']
        );
    }
}
