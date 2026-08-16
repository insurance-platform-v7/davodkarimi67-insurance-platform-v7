<?php

namespace App\Http\Controllers\Api;

use App\Application\Quote\QuoteApplicationService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\QuoteRequest;
use App\Models\Customer;
use App\Models\Quote;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class QuoteController extends Controller
{
    public function __construct(
        protected QuoteApplicationService $quoteApplicationService,
    ) {
    }

    public function store(QuoteRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $tenantId = app('tenant')->id;

        $customer = Customer::query()
            ->whereKey($validated['customer_id'])
            ->where('tenant_id', $tenantId)
            ->firstOrFail();

        do {
            $quoteNumber = 'QT-' . strtoupper(Str::random(8));
        } while (
            Quote::query()
                ->where('quote_number', $quoteNumber)
                ->exists()
        );

        $quote = Quote::create([
            'tenant_id' => $tenantId,
            'customer_id' => $customer->id,
            'insurance_product_id' => $validated['insurance_product_id'],
            'quote_number' => $quoteNumber,
            'input_data' => $validated['parameters'] ?? [],
            'status' => 'draft',
        ]);

        $result = $this->quoteApplicationService->execute($quote);

        return response()
            ->json([
                'quote_id' => $quote->id,
                'recommendations' => $result['recommendations'],
            ], 201)
            ->header('X-API-Version', 'v1');
    }
}
