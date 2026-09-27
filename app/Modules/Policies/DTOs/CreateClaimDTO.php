<?php

namespace App\Modules\Policies\DTOs;

final class CreateClaimDTO
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function __construct(
        public int $tenantId,
        public int $policyId,
        public array $data,
    ) {}
}
