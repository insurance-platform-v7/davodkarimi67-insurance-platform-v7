<?php

namespace App\Infrastructure\Formula;

class NewFormulaAdapter
{
    public function run(array $inputs): float
    {
        $base = (float) ($inputs['base'] ?? 0);
        $coeff = (float) ($inputs['coefficient'] ?? 1.0);

        return $base * $coeff;
    }
}
