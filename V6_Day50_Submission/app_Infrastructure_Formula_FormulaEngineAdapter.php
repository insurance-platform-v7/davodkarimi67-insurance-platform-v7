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

        // اگر خروجی structured بود
        if (is_array($result)) {
            if (isset($result['premium'])) {
                return (int) $result['premium'];
            }

            return (int) (array_values($result)[0] ?? 0);
        }

        return (int) $result;
    }
}
