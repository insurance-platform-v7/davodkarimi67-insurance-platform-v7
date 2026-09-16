<?php

namespace App\Repositories\Quote;

use App\Models\Quote;
use Illuminate\Support\Str;

class QuoteRepository
{
    /**
     * @param array<string, mixed> $inputData
     */
    public function create(
        int $tenantId,
        int $customerId,
        int $insuranceProductId,
        array $inputData
    ): Quote {
        do {
            $quoteNumber = 'QT-'.strtoupper(Str::random(8));
        } while (
            Quote::query()
                ->where('tenant_id', $tenantId)
                ->where('quote_number', $quoteNumber)
                ->exists()
        );

        return Quote::create([
            'tenant_id' => $tenantId,
            'customer_id' => $customerId,
            'insurance_product_id' => $insuranceProductId,
            'quote_number' => $quoteNumber,
            'input_data' => $inputData,
            'status' => 'draft',
        ]);
    }
}
