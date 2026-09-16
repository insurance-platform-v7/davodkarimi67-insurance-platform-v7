<?php

namespace App\DTOs;

use InvalidArgumentException;

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

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        if (! isset($data['insurance_type_id']) || ! is_numeric($data['insurance_type_id'])) {
            throw new InvalidArgumentException('insurance_type_id must be numeric.');
        }

        if (! isset($data['vehicle_type']) || ! is_string($data['vehicle_type'])) {
            throw new InvalidArgumentException('vehicle_type must be a string.');
        }

        if (! isset($data['vehicle_year']) || ! is_numeric($data['vehicle_year'])) {
            throw new InvalidArgumentException('vehicle_year must be numeric.');
        }

        if (! isset($data['usage_type']) || ! is_string($data['usage_type'])) {
            throw new InvalidArgumentException('usage_type must be a string.');
        }

        $noClaimYears = $data['no_claim_years'] ?? 0;

        if (! is_numeric($noClaimYears)) {
            throw new InvalidArgumentException('no_claim_years must be numeric.');
        }

        $hasPreviousClaim = $data['has_previous_claim'] ?? false;

        if (! is_bool($hasPreviousClaim)) {
            throw new InvalidArgumentException('has_previous_claim must be boolean.');
        }

        $vehicleValue = $data['vehicle_value'] ?? null;

        if ($vehicleValue !== null && ! is_numeric($vehicleValue)) {
            throw new InvalidArgumentException('vehicle_value must be numeric.');
        }

        $coverageLevel = $data['coverage_level'] ?? null;

        if ($coverageLevel !== null && ! is_string($coverageLevel)) {
            throw new InvalidArgumentException('coverage_level must be a string or null.');
        }

        return new self(
            insuranceTypeId: (int) $data['insurance_type_id'],
            vehicleType: $data['vehicle_type'],
            vehicleYear: (int) $data['vehicle_year'],
            usageType: $data['usage_type'],
            noClaimYears: (int) $noClaimYears,
            hasPreviousClaim: $hasPreviousClaim,
            vehicleValue: $vehicleValue !== null ? (float) $vehicleValue : null,
            coverageLevel: $coverageLevel,
        );
    }

    /**
     * @return array<string, mixed>
     */
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
