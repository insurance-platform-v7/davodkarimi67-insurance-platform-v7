<?php

namespace App\Infrastructure\Formula;

use App\Domain\Formula\FormulaEngine;
use RuntimeException;

class NewFormulaAdapter
{
    public function __construct(
        protected FormulaEngine $engine
    ) {}

    public function calculate(array $formula, array $input): float|int
    {
        $result = $this->engine->execute($formula, $input);

        if (
            ! is_array($result) ||
            ! array_key_exists('premium', $result) ||
            ! is_numeric($result['premium'])
        ) {
            throw new RuntimeException(
                'Formula engine returned an invalid premium.'
            );
        }

        return (float) $result['premium'];
    }
}
