<?php

namespace Tests\Feature;

use App\Domain\Formula\VariableResolver;
use Tests\TestCase;

class VariableResolverCoverageTest extends TestCase
{
    public function test_it_resolves_variables_as_is(): void
    {
        $resolver = new VariableResolver;

        $variables = [
            'car_value' => 1000,
            'nested' => ['rate' => 0.02],
        ];

        $this->assertSame($variables, $resolver->resolve($variables));
    }

    public function test_it_gets_existing_variable(): void
    {
        $resolver = new VariableResolver;

        $this->assertSame(
            1000,
            $resolver->get('car_value', ['car_value' => 1000])
        );
    }

    public function test_it_returns_default_for_missing_variable(): void
    {
        $resolver = new VariableResolver;

        $this->assertSame(
            50,
            $resolver->get('missing', [], 50)
        );
    }

    public function test_it_replaces_numeric_variables(): void
    {
        $resolver = new VariableResolver;

        $result = $resolver->replace(
            '{{car_value}} * {{rate}}',
            [
                'car_value' => 1000,
                'rate' => 0.02,
            ]
        );

        $this->assertSame('1000 * 0.02', $result);
    }

    public function test_it_replaces_non_numeric_variables_with_zero(): void
    {
        $resolver = new VariableResolver;

        $result = $resolver->replace(
            '{{name}} + {{missing}}',
            [
                'name' => 'Toyota',
            ]
        );

        $this->assertSame('0 + 0', $result);
    }
}
