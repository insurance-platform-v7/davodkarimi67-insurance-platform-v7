<?php

namespace App\Http\Controllers\Api;

use App\Application\Quote\QuoteApplicationService;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Quote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class QuoteController extends Controller
{
    public function __construct(
        protected QuoteApplicationService $quoteApplicationService,
    ) {}

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'insurance_product_id' => ['required', 'exists:insurance_products,id'],
            'customer_id' => ['required', 'exists:customers,id'],
            'parameters' => ['nullable', 'array'],
        ]);

        $customer = Customer::findOrFail($validated['customer_id']);

        do {
            $quoteNumber = 'QT-' . strtoupper(Str::random(8));
        } while (
            Quote::query()
                ->where('quote_number', $quoteNumber)
                ->exists()
        );

        $quote = Quote::create([
            'tenant_id' => $customer->tenant_id,
            'customer_id' => $customer->id,
            'insurance_product_id' => $validated['insurance_product_id'],
            'quote_number' => $quoteNumber,
            'input_data' => $validated['parameters'] ?? [],
            'status' => 'draft',
        ]);

        $offers = $this->quoteApplicationService->execute($quote);

        return response()->json([
            'quote_id' => $quote->id,
            'offers' => $offers,
        ], 201)->header('X-API-Version', 'v1');
    }
}
