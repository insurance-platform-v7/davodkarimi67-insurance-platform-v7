<?php

namespace App\Domain\Formula;

class VariableResolver
{
    /**
     * @param array<string, mixed> $variables
     * @return array<string, mixed>
     */
    public function resolve(array $variables): array
    {
        return $variables;
    }

    /**
     * @param array<string, mixed> $variables
     */
    public function get(
        string $key,
        array $variables,
        mixed $default = null
    ): mixed {
        return data_get($variables, $key, $default);
    }

    /**
     * @param array<string, mixed> $variables
     */
    public function replace(
        string $expression,
        array $variables
    ): string {
        $result = preg_replace_callback(
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

        return $result ?? $expression;
    }
}
