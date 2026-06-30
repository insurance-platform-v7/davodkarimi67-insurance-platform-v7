<?php

namespace App\Contracts\Pricing;

class PriceableDTO
{
    public function __construct(
        public readonly float $finalPrice,
        public readonly float $basePrice,
        public readonly float $discount = 0,
        public readonly float $surcharge = 0,
        public readonly array $details = [],
        public readonly ?string $currency = 'IRR',
    ) {}

    public function toArray(): array
    {
        return [
            'final_price' => $this->finalPrice,
            'base_price' => $this->basePrice,
            'discount' => $this->discount,
            'surcharge' => $this->surcharge,
            'details' => $this->details,
            'currency' => $this->currency,
        ];
    }
}
