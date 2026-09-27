<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\QuoteRequest;
use App\Models\Tenant;
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

        if (! $tenant instanceof Tenant) {
            abort(500, 'Tenant context is not available.');
        }

        $validated = $request->validated();

        if (! isset($validated['customer_id']) || ! is_numeric($validated['customer_id'])) {
            abort(422, 'Customer id must be numeric.');
        }

        if (! isset($validated['insurance_product_id']) || ! is_numeric($validated['insurance_product_id'])) {
            abort(422, 'Insurance product id must be numeric.');
        }

        $parameters = $validated['parameters'] ?? [];

        if (! is_array($parameters)) {
            abort(422, 'Parameters must be an array.');
        }

        /** @var array<string, mixed> $parameters */
        $result = $this->createQuoteAction->execute(
            new CreateQuoteDTO(
                tenantId: $tenant->id,
                customerId: (int) $validated['customer_id'],
                insuranceProductId: (int) $validated['insurance_product_id'],
                parameters: $parameters,
            ),
        );

        return response()->json([
            'data' => new QuoteResource($result['quote']),
            'offers' => $result['offers'],
            'recommendations' => $result['recommendations'],
        ], 201)->header('X-API-Version', 'v1');
    }
}
