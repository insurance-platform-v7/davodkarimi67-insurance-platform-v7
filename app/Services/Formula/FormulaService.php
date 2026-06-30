<?php

namespace App\Services\Formula;

class FormulaService
{
    public function __construct(
        protected FormulaVersionResolver $versionResolver,
        protected FormulaConditionLoader $conditionLoader,
        protected FormulaExecutor $executor
    ) {
    }

    public function calculate(
        int $formulaId,
        array $variables = []
    ): float|int {

        $version = $this->versionResolver
            ->resolve($formulaId);

        if (! $version) {
            throw new \RuntimeException(
                'Active formula version not found.'
            );
        }

        $conditions = $this->conditionLoader
            ->load($version);

        if (! $this->executor->canExecute(
            $conditions,
            $variables
        )) {
            throw new \RuntimeException(
                'Formula conditions failed.'
            );
        }

        return $this->executor->execute(
            $version->formula_json,
            $variables
        );
    }
}
