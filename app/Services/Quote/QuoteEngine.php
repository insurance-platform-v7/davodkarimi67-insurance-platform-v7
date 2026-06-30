<?php

namespace App\Services\Quote;

use App\Models\CompanyProduct;
use App\Models\Quote;
use App\Models\QuoteOffer;

class QuoteEngine
{
    public function generateOffers(Quote $quote): void
    {
        $companyProducts = CompanyProduct::query()
            ->where('insurance_product_id', $quote->insurance_product_id)
            ->where('is_active', true)
            ->get();

        foreach ($companyProducts as $companyProduct) {
            $premium = $this->calculatePremium($quote, $companyProduct);

            QuoteOffer::create([
                'tenant_id' => $quote->tenant_id,
                'quote_id' => $quote->id,
                'insurance_company_id' => $companyProduct->insurance_company_id,
                'premium' => $premium,
                'coverage' => [],
                'deductible' => null,
                'terms' => [],
                'status' => 'offered',
                'meta' => [
                    'company_product_id' => $companyProduct->id,
                    'currency' => 'IRR',
                ],
            ]);
        }
    }

    protected function calculatePremium(Quote $quote, CompanyProduct $companyProduct): int
    {
        $inputData = $quote->input_data ?? [];

        $carValue = (int) ($inputData['car_value'] ?? 0);

        if ($carValue <= 0) {
            return 0;
        }

        return (int) round($carValue * 0.02);
    }
}
