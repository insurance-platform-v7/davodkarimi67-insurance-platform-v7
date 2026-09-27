<?php

namespace App\Modules\Policies\DTOs;

final readonly class IssuePolicyDTO
{
    public function __construct(
        public int $offerId,
    ) {}
}
