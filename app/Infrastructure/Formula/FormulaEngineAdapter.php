<?php

namespace App\Infrastructure\Formula;

use App\Domain\Formula\FormulaEngine;

class FormulaEngineAdapter
{
    public function __construct(
        protected FormulaEngine $engine
    ) {}

    public function calculate(array $formulaJson, array $input): int
    {
        $result = $this->engine->execute($formulaJson, $input);
        if (isset($result['premium']) && is_numeric($result['premium'])) {
            return (int) round((float) $result['premium']);
        }

        return (int) round(
            (float) (array_values($result)[0] ?? 0)
        );
    }
}
