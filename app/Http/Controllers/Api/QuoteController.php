<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\QuoteRequest;
use App\Modules\Quotes\Actions\CreateQuoteAction;
use App\Modules\Quotes\DTOs\CreateQuoteDTO;
use App\Modules\Quotes\Http\Resources\QuoteResource;
use Illuminate\Http\JsonResponse;

class QuoteController extends Controller
{
    public function __construct(
        private readonly CreateQuoteAction $createQuoteAction,
    ) {}

    public function store(QuoteRequest $request): JsonResponse
    {
        $tenant = app('tenant');

        $validated = $request->validated();

        /** @var array<string, mixed> $validated */
        $result = $this->createQuoteAction->execute(
            new CreateQuoteDTO(
                tenantId: $tenant->id,
                customerId: (int) $validated['customer_id'],
                insuranceProductId: (int) $validated['insurance_product_id'],
                parameters: $validated['parameters'] ?? [],
            ),
        );

        return response()->json([
            'data' => new QuoteResource($result['quote']),
            'offers' => $result['offers'],
            'recommendations' => $result['recommendations'],
        ], 201)->header('X-API-Version', 'v1');
    }
}
