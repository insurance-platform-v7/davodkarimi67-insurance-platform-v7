<?php

namespace App\Domain\Formula;

use InvalidArgumentException;

class ExpressionResolver
{
    /**
     * Evaluate a mathematical expression without eval().
     *
     * Supported:
     * - integers
     * - decimal numbers
     * - + - * /
     * - parentheses
     * - unary +/-
     *
     * @throws InvalidArgumentException
     */
    public function evaluate(string $expression): float|int
    {
        $expression = trim($expression);

        if ($expression === '') {
            return 0;
        }

        if (! preg_match('/^[0-9+\-*\/().\s]+$/', $expression)) {
            throw new InvalidArgumentException(
                'Invalid expression.'
            );
        }

        $tokens = $this->tokenize($expression);

        if ($tokens === []) {
            throw new InvalidArgumentException(
                'Invalid expression.'
            );
        }

        $position = 0;

        $result = $this->parseExpression(
            $tokens,
            $position
        );

        if ($position !== count($tokens)) {
            throw new InvalidArgumentException(
                'Invalid expression syntax.'
            );
        }

        if (! is_finite($result)) {
            throw new InvalidArgumentException(
                'Expression result is not finite.'
            );
        }

        return (float) $result;
    }

    /**
     * @return array<int, string>
     */
    private function tokenize(string $expression): array
    {
        preg_match_all(
            '/\d+(?:\.\d+)?|[()+\-*\/]/',
            $expression,
            $matches
        );

        $tokens = $matches[0];

        $withoutWhitespace = preg_replace(
            '/\s+/',
            '',
            $expression
        );

        if (
            $withoutWhitespace === null
            || $withoutWhitespace !== implode('', $tokens)
        ) {
            throw new InvalidArgumentException(
                'Invalid expression.'
            );
        }

        return $tokens;
    }

    /**
     * @param array<int, string> $tokens
     */
    private function parseExpression(
        array $tokens,
        int &$position
    ): float {
        $result = $this->parseTerm(
            $tokens,
            $position
        );

        while ($position < count($tokens)) {
            $operator = $tokens[$position];

            if ($operator !== '+' && $operator !== '-') {
                break;
            }

            $position++;

            $right = $this->parseTerm(
                $tokens,
                $position
            );

            $result = $operator === '+'
                ? $result + $right
                : $result - $right;
        }

        return $result;
    }

    /**
     * @param array<int, string> $tokens
     */
    private function parseTerm(
        array $tokens,
        int &$position
    ): float {
        $result = $this->parseFactor(
            $tokens,
            $position
        );

        while ($position < count($tokens)) {
            $operator = $tokens[$position];

            if ($operator !== '*' && $operator !== '/') {
                break;
            }

            $position++;

            $right = $this->parseFactor(
                $tokens,
                $position
            );

            if ($operator === '/' && $right == 0.0) {
                throw new InvalidArgumentException(
                    'Division by zero.'
                );
            }

            $result = $operator === '*'
                ? $result * $right
                : $result / $right;
        }

        return $result;
    }

    /**
     * @param array<int, string> $tokens
     */
    private function parseFactor(
        array $tokens,
        int &$position
    ): float {
        if (! isset($tokens[$position])) {
            throw new InvalidArgumentException(
                'Unexpected end of expression.'
            );
        }

        $token = $tokens[$position];

        if ($token === '+') {
            $position++;

            return $this->parseFactor(
                $tokens,
                $position
            );
        }

        if ($token === '-') {
            $position++;

            return -$this->parseFactor(
                $tokens,
                $position
            );
        }

        if ($token === '(') {
            $position++;

            $result = $this->parseExpression(
                $tokens,
                $position
            );

            if (($tokens[$position] ?? null) !== ')') {
                throw new InvalidArgumentException(
                    'Unclosed parenthesis.'
                );
            }

            $position++;

            return $result;
        }

        if (! preg_match(
            '/^\d+(?:\.\d+)?$/',
            $token
        )) {
            throw new InvalidArgumentException(
                'Invalid number.'
            );
        }

        $position++;

        return (float) $token;
    }
}
