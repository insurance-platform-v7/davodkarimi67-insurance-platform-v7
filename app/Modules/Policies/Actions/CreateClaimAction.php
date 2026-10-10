<?php

namespace App\Modules\Policies\Actions;

use App\Domain\Policy\PolicyRepository;
use App\Models\Claim;
use App\Modules\Policies\DTOs\CreateClaimDTO;
use App\Services\Claim\ClaimService;

final class CreateClaimAction
{
    public function __construct(
        private readonly PolicyRepository $policyRepository,
        private readonly ClaimService $claimService,
    ) {}

    public function execute(CreateClaimDTO $dto): Claim
    {
        $policy = $this->policyRepository->findForTenantOrFail(
            $dto->policyId,
            $dto->tenantId,
        );

        return $this->claimService->create(
            $policy,
            $dto->data,
        );
    }
}
