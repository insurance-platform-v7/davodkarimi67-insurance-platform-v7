<?php

namespace App\Modules\Quotes\Actions;

use App\Application\Quote\QuoteApplicationService;
use App\Models\Quote;
use App\Modules\Quotes\DTOs\CreateQuoteDTO;
use App\Repositories\Customer\CustomerRepository;
use App\Repositories\Quote\QuoteRepository;
use Illuminate\Support\Facades\DB;

final class CreateQuoteAction
{
    public function __construct(
        private readonly CustomerRepository $customerRepository,
        private readonly QuoteRepository $quoteRepository,
        private readonly QuoteApplicationService $quoteApplicationService,
    ) {}

    /**
     * @return array{
     *     quote: Quote,
     *     offers: array<int, array<string, mixed>>,
     *     recommendations: array<string, mixed>
     * }
     */
    public function execute(CreateQuoteDTO $dto): array
    {
        return DB::transaction(function () use ($dto): array {
            $customer = $this->customerRepository->findForTenantOrFail(
                $dto->customerId,
                $dto->tenantId,
            );

            $quote = $this->quoteRepository->create(
                $dto->tenantId,
                $customer->id,
                $dto->insuranceProductId,
                $dto->parameters,
            );

            $result = $this->quoteApplicationService->execute($quote);

            return [
                'quote' => $quote,
                'offers' => $result['offers'],
                'recommendations' => $result['recommendations'],
            ];
        });
    }
}
