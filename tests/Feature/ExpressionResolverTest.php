<?php

namespace Tests\Feature;

use App\Domain\Formula\ExpressionResolver;
use InvalidArgumentException;
use Tests\TestCase;

class ExpressionResolverTest extends TestCase
{
    public function test_it_evaluates_operator_precedence(): void
    {
        $this->assertSame(
            14.0,
            (new ExpressionResolver())->evaluate('2 + 3 * 4')
        );
    }

    public function test_it_evaluates_parentheses(): void
    {
        $this->assertSame(
            20.0,
            (new ExpressionResolver())->evaluate('(2 + 3) * 4')
        );
    }

    public function test_it_evaluates_decimal_and_unary_operators(): void
    {
        $resolver = new ExpressionResolver();

        $this->assertSame(3.5, $resolver->evaluate('5.5 - 2'));
        $this->assertSame(-6.0, $resolver->evaluate('-(2 * 3)'));
        $this->assertSame(6.0, $resolver->evaluate('+(2 * 3)'));
    }

    public function test_empty_expression_returns_zero(): void
    {
        $this->assertSame(
            0,
            (new ExpressionResolver())->evaluate('   ')
        );
    }

    public function test_division_by_zero_is_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Division by zero.');

        (new ExpressionResolver())->evaluate('10 / 0');
    }

    public function test_invalid_expression_is_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid expression.');

        (new ExpressionResolver())->evaluate('10 + abc');
    }

    public function test_invalid_expression_syntax_is_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid expression syntax.');

        (new ExpressionResolver())->evaluate('10 20');
    }

    public function test_unclosed_parenthesis_is_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unclosed parenthesis.');

        (new ExpressionResolver())->evaluate('(10 + 5');
    }

    public function test_unexpected_end_of_expression_is_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unexpected end of expression.');

        (new ExpressionResolver())->evaluate('10 +');
    }
}