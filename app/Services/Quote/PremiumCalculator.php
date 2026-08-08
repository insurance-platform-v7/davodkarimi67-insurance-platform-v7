<?php

namespace App\Services\Quote;

use App\Domain\Formula\FormulaEngine;
use App\Models\CompanyProduct;
use App\Models\Quote;
use App\Services\Formula\FormulaService;
use RuntimeException;

class PremiumCalculator
{
    public function __construct(
        protected FormulaService $formulaService,
        protected FormulaEngine $formulaEngine,
    ) {}

    public function calculate(
        Quote $quote,
        CompanyProduct $companyProduct
    ): int {
        $input = $quote->input_data ?? [];

        // NEW ENGINE
        if (
            function_exists('feature_flag')
            && feature_flag('new_formula')
        ) {
            $result = $this->formulaEngine->execute(
                [
                    'rules' => [],
                ],
                $input
            );

            $premium = (float) ($result['premium'] ?? 0);

            if ($premium < 0) {
                throw new RuntimeException(
                    'Invalid premium calculated.'
                );
            }

            return (int) $premium;
        }

        // LEGACY ENGINE
        $premium = (float) $this->formulaService->calculateForProduct(
            $companyProduct,
            $input
        );

        if ($premium < 0) {
            throw new RuntimeException(
                'Invalid premium calculated.'
            );
        }

        return (int) $premium;
    }
}
