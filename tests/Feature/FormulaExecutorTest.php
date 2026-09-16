<?php

namespace Tests\Feature;

use App\Domain\Formula\ExpressionResolver;
use App\Domain\Formula\VariableResolver;
use App\Services\Formula\ConditionEvaluator;
use App\Services\Formula\FormulaExecutor;
use App\Services\Formula\FormulaVersionResolver;
use Tests\TestCase;

class FormulaExecutorTest extends TestCase
{
    public function test_it_executes_expression_formula(): void
    {
        $executor = app(FormulaExecutor::class);
        $this->assertSame(40, $executor->execute(['type' => 'expression', 'expression' => '15 + 25']));
    }

    public function test_it_executes_expression_with_variables(): void
    {
        $executor = app(FormulaExecutor::class);
        $this->assertSame(200, $executor->executeVariables('{{amount}} * 2', ['amount' => 100]));
    }

    public function test_it_executes_expression_directly(): void
    {
        $executor = app(FormulaExecutor::class);
        $this->assertSame(30, $executor->executeExpression('10 + 20'));
    }

    public function test_it_returns_true_when_conditions_are_empty(): void
    {
        $executor = app(FormulaExecutor::class);
        $this->assertTrue($executor->canExecute([], []));
    }

    public function test_it_rejects_unsupported_formula_type(): void
    {
        $executor = app(FormulaExecutor::class);
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unsupported formula type [unknown]');
        $executor->execute(['type' => 'unknown', 'expression' => '10 + 20']);
    }

    public function test_it_rejects_empty_expression(): void
    {
        $executor = app(FormulaExecutor::class);
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Formula expression cannot be empty.');
        $executor->execute(['type' => 'expression', 'expression' => '']);
    }

    public function test_it_rejects_missing_formula_version(): void
    {
        $versionResolver = \Mockery::mock(FormulaVersionResolver::class);
        $versionResolver->shouldReceive('resolve')->once()->with(999)->andReturn(null);
        $executor = new FormulaExecutor(
            new VariableResolver(),
            new ExpressionResolver(),
            \Mockery::mock(ConditionEvaluator::class),
            $versionResolver
        );
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Active formula version not found.');
        $executor->executeVersion(999);
    }
    public function test_it_evaluates_conditions(): void
    {
        $conditionEvaluator = \Mockery::mock(ConditionEvaluator::class);
        $conditionEvaluator
            ->shouldReceive('evaluateGroup')
            ->once()
            ->with(
                'AND',
                [['field' => 'amount', 'operator' => '>', 'value' => 100]],
                ['amount' => 200]
            )
            ->andReturn(true);

        $executor = new FormulaExecutor(
            new VariableResolver(),
            new ExpressionResolver(),
            $conditionEvaluator,
            \Mockery::mock(FormulaVersionResolver::class),
        );

        $this->assertTrue(
            $executor->canExecute(
                [['field' => 'amount', 'operator' => '>', 'value' => 100]],
                ['amount' => 200]
            )
        );
    }
}