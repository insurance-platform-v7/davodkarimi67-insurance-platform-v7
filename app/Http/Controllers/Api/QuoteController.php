<?php

namespace App\Http\Controllers\Api;

use App\Application\Quote\QuoteApplicationService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\QuoteRequest;
use App\Models\Tenant;
use App\Repositories\Customer\CustomerRepository;
use App\Repositories\Quote\QuoteRepository;
use Illuminate\Http\JsonResponse;

class QuoteController extends Controller
{
    public function __construct(
        protected QuoteApplicationService $quoteApplicationService,
        protected CustomerRepository $customerRepository,
        protected QuoteRepository $quoteRepository,
    ) {}

    public function store(QuoteRequest $request): JsonResponse
    {
        /** @var array{
         *     customer_id: int,
         *     insurance_product_id: int,
         *     parameters?: array<string, mixed>
         * } $validated
         */
        $validated = $request->validated();

        $tenant = app('tenant');

        if (! $tenant instanceof Tenant) {
            abort(500, 'Tenant context is not available.');
        }

        $tenantId = $tenant->id;

        $customer = $this->customerRepository->findForTenantOrFail(
            $validated['customer_id'],
            $tenantId,
        );

        $quote = $this->quoteRepository->create(
            $tenantId,
            $customer->id,
            $validated['insurance_product_id'],
            $validated['parameters'] ?? [],
        );

        $result = $this->quoteApplicationService->execute($quote);

        return response()
            ->json([
                'quote_id' => $quote->id,
                'offers' => $result['offers'],
                'recommendations' => $result['recommendations'],
            ], 201)
            ->header('X-API-Version', 'v1');
    }
}
