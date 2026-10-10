<?php

namespace App\Modules\Quotes\DTOs;

final readonly class CreateQuoteDTO
{
    /**
     * @param  array<string, mixed>  $parameters
     */
    public function __construct(
        public int $tenantId,
        public int $customerId,
        public int $insuranceProductId,
        public array $parameters = [],
    ) {}
}
