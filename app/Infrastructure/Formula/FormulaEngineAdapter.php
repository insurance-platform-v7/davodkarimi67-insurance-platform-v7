<?php

namespace App\Infrastructure\Formula;

use App\Domain\Formula\FormulaEngine;

class FormulaEngineAdapter
{
    public function __construct(
        protected FormulaEngine $engine
    ) {}

    /**
     * @param  array<string, mixed>  $formulaJson
     * @param  array<string, mixed>  $input
     */
    public function calculate(
        array $formulaJson,
        array $input
    ): int {
        $result = $this->engine->execute(
            $formulaJson,
            $input
        );

        if (
            isset($result['premium'])
            && is_numeric($result['premium'])
        ) {
            return (int) round(
                (float) $result['premium']
            );
        }

        $values = array_values($result);
        $firstValue = $values[0] ?? 0;

        if (! is_numeric($firstValue)) {
            return 0;
        }

        return (int) round((float) $firstValue);
    }
}
