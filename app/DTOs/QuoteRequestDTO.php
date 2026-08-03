<?php

namespace App\DTOs;

class QuoteRequestDTO
{
    public function __construct(
        public int $insuranceTypeId,
        public string $vehicleType,
        public int $vehicleYear,
        public string $usageType,
        public int $noClaimYears = 0,
        public bool $hasPreviousClaim = false,
        public ?float $vehicleValue = null,
        public ?string $coverageLevel = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            insuranceTypeId: (int) $data['insurance_type_id'],
            vehicleType: $data['vehicle_type'],
            vehicleYear: (int) $data['vehicle_year'],
            usageType: $data['usage_type'],
            noClaimYears: (int) ($data['no_claim_years'] ?? 0),
            hasPreviousClaim: (bool) ($data['has_previous_claim'] ?? false),
            vehicleValue: isset($data['vehicle_value']) ? (float) $data['vehicle_value'] : null,
            coverageLevel: $data['coverage_level'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'insurance_type_id' => $this->insuranceTypeId,
            'vehicle_type' => $this->vehicleType,
            'vehicle_year' => $this->vehicleYear,
            'usage_type' => $this->usageType,
            'no_claim_years' => $this->noClaimYears,
            'has_previous_claim' => $this->hasPreviousClaim,
            'vehicle_value' => $this->vehicleValue,
            'coverage_level' => $this->coverageLevel,
        ];
    }
}
