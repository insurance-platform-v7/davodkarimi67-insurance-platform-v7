<?php

namespace App\Services\Quote;

use App\Models\Formula;
use App\Services\Formula\FormulaService;

class QuoteCalculationService
{
    public function __construct(
        protected FormulaService $formulaService
    ) {
    }

    public function calculate(array $data): array
    {
        $formula = Formula::query()
            ->where('code', 'BASE_CAR_FORMULA')
            ->first();

        if (! $formula) {
            throw new \RuntimeException(
                'Base car formula not found.'
            );
        }

        $premium = $this->formulaService->calculate(
            $formula->id,
            $data
        );

        return [
            'premium' => $premium,
            'status' => 'calculated',
        ];
    }
}
