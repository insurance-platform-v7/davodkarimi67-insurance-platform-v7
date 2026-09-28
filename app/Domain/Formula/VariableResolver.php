<?php

namespace App\Domain\Formula;

use InvalidArgumentException;

class VariableResolver
{
    /**
     * @param  array<string, mixed>  $variables
     * @return array<string, mixed>
     */
    public function resolve(array $variables): array
    {
        return $variables;
    }

    /**
     * @param  array<string, mixed>  $variables
     */
    public function get(
        string $key,
        array $variables,
        mixed $default = null
    ): mixed {
        return data_get($variables, $key, $default);
    }

    /**
     * Replace {{variable}} placeholders with numeric values.
     *
     * Missing or non-numeric variables are replaced with zero.
     *
     * @param  array<string, mixed>  $variables
     */
    public function replace(
        string $expression,
        array $variables
    ): string {
        $result = preg_replace_callback(
            '/\{\{\s*([^{}]+?)\s*\}\}/',
            static function (array $matches) use ($variables): string {
                $key = trim($matches[1]);

                $value = data_get($variables, $key);

                if ($value === null) {
                    return '0';
                }

                if (is_bool($value)) {
                    return '0';
                }

                if (! is_numeric($value)) {
                    return '0';
                }

                return (string) $value;
            },
            $expression
        );

        if ($result === null) {
            throw new InvalidArgumentException(
                'Unable to resolve formula variables.'
            );
        }

        return $result;
    }
}
