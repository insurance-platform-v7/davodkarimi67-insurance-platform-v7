<?php

namespace App\Services\Formula;

use App\Models\FormulaVersion;

class FormulaConditionLoader
{
    public function load(
        FormulaVersion $version
    ): array {

        return $version->conditions()
            ->orderBy('priority')
            ->get()
            ->map(function ($condition) {

                return [
                    'field' => $condition->field,
                    'comparison' => $condition->comparison,
                    'value' => $condition->value,
                    'group_type' => $condition->group_type,
                ];
            })
            ->toArray();
    }
}
