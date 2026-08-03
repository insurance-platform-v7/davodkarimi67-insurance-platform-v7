<?php

namespace App\Contracts\Pricing;

use App\DTOs\QuoteRequestDTO;
use App\Models\PricingRule;

interface PricingStrategyInterface
{
    /**
     * تعیین اینکه این استراتژی برای چه نوع فرمولی مناسب است
     */
    public function supports(string $formulaType): bool;

    /**
     * محاسبه قیمت نهایی
     */
    public function calculate(QuoteRequestDTO $dto, PricingRule $rule): PriceableDTO;
}
