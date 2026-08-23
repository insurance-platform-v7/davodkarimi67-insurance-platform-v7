<?php

namespace App\Services\Formula;

class VariableResolver
{
    /**
     * Resolve variables used inside formulas.
     *
     * Example:
     * ['car_value' => 1000]
     */
    public function resolve(array $variables): array
    {
        return $variables;
    }

    /**
     * Get one variable.
     */
    public function get(
        string $key,
        array $variables,
        mixed $default = null
    ): mixed {
        return data_get($variables, $key, $default);
    }

    /**
     * Replace placeholders inside expression.
     *
     * Example:
     * {{car_value}} * 0.02
     */
    public function replace(
        string $expression,
        array $variables
    ): string {
        return preg_replace_callback(
            '/\{\{\s*(.*?)\s*\}\}/',
            static function (array $matches) use ($variables): string {
                $key = trim($matches[1]);

                $value = data_get($variables, $key, 0);

                if (! is_numeric($value)) {
                    return '0';
                }

                return (string) $value;
            },
            $expression
        );
    }
}
