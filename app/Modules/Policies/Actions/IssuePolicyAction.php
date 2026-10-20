<?php

namespace App\Modules\Policies\Actions;

use App\Models\Policy;
use App\Modules\Policies\DTOs\IssuePolicyDTO;
use App\Services\Policy\PolicyService;

final class IssuePolicyAction
{
    public function __construct(
        private readonly PolicyService $policyService,
    ) {}

    public function execute(IssuePolicyDTO $dto): Policy
    {
        return $this->policyService->issueFromOffer($dto->offerId);
    }
}
