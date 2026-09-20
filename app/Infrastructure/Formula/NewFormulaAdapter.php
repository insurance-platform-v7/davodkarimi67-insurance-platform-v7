<?php

namespace App\Infrastructure\Formula;

class NewFormulaAdapter
{
    /**
     * @param  array<string, mixed>  $inputs
     */
    public function run(array $inputs): float
    {
        $base = $inputs['base'] ?? 0;
        $coefficient = $inputs['coefficient'] ?? 1.0;

        $baseValue = is_numeric($base)
            ? (float) $base
            : 0.0;

        $coefficientValue = is_numeric($coefficient)
            ? (float) $coefficient
            : 1.0;

        return $baseValue * $coefficientValue;
    }
}
