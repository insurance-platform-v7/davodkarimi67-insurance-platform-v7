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
        /** @var object{id: int} $tenant */
        $tenant = app('tenant');

        /** @var array<string, mixed> $validated */
        $validated = $request->validated();

        /** @var int|string $customerId */
        $customerId = $validated['customer_id'];

        /** @var int|string $insuranceProductId */
        $insuranceProductId = $validated['insurance_product_id'];

        /** @var array<string, mixed> $parameters */
        $parameters = $validated['parameters'] ?? [];

        $result = $this->createQuoteAction->execute(
            new CreateQuoteDTO(
                tenantId: $tenant->id,
                customerId: (int) $customerId,
                insuranceProductId: (int) $insuranceProductId,
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
