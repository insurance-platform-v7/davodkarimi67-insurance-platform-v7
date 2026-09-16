<?php

namespace App\Services\Formula;

use App\Models\FormulaCondition;
use App\Models\FormulaVersion;

class FormulaConditionLoader
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function load(FormulaVersion $version): array
    {
        return $version
            ->conditions()
            ->orderBy('priority')
            ->get()
            ->map(
                static function (FormulaCondition $condition): array {
                    return [
                        'field' => $condition->field,
                        'comparison' => $condition->comparison,
                        'value' => $condition->value,
                        'group_type' => $condition->group_type,
                    ];
                }
            )
            ->values()
            ->all();
    }
}
