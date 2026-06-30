<?php

namespace App\Services\Formula;

use App\Models\Formula;
use App\Models\FormulaVersion;

class FormulaVersionResolver
{
    public function resolve(
        Formula|int $formula
    ): ?FormulaVersion {

        $formulaId = $formula instanceof Formula
            ? $formula->id
            : $formula;

        return FormulaVersion::query()
            ->where('formula_id', $formulaId)
            ->where('is_active', true)
            ->latest('version')
            ->first();
    }
}
