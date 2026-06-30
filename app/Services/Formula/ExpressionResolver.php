<?php

namespace App\Services\Formula;

use InvalidArgumentException;

class ExpressionResolver
{
    /**
     * Evaluate a mathematical expression.
     *
     * @throws InvalidArgumentException
     */
    public function evaluate(string $expression): float|int
    {
        $expression = trim($expression);

        if ($expression === '') {
            return 0;
        }

        // فقط کاراکترهای مجاز
        if (! preg_match('/^[0-9+\-*\/().\s]+$/', $expression)) {
            throw new InvalidArgumentException('Invalid expression.');
        }

        try {
            /** @noinspection PhpUsageOfSilenceOperatorInspection */
            $result = @eval("return {$expression};");
        } catch (\Throwable $e) {
            throw new InvalidArgumentException('Expression evaluation failed.', 0, $e);
        }

        if (! is_numeric($result)) {
            throw new InvalidArgumentException('Expression did not return numeric value.');
        }

        return $result;
    }
}
