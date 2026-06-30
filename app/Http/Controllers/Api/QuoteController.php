<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Quote;
use App\Models\Customer;
use App\Services\Quote\QuoteEngine;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class QuoteController extends Controller
{
    public function __construct(
        protected QuoteEngine $quoteEngine
    ) {}

    public function store(Request $request)
    {
        $data = $request->validate([
            'insurance_product_id' => ['required', 'exists:insurance_products,id'],
            'customer_id' => ['required', 'exists:customers,id'],
            'parameters' => ['nullable', 'array'],
        ]);

        $customer = Customer::findOrFail($data['customer_id']);

        $quote = Quote::create([
            'tenant_id' => $customer->tenant_id,
            'customer_id' => $customer->id,
            'insurance_product_id' => $data['insurance_product_id'],
            'quote_number' => 'QT-' . Str::random(8),
            'input_data' => $data['parameters'] ?? [],
            'status' => 'draft',
        ]);

        $this->quoteEngine->generateOffers($quote);

        return response()->json([
            'quote_id' => $quote->id,
        ], 201);
    }
}
