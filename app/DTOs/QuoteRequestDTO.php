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
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        self::validateRequiredNumeric($data, 'insurance_type_id');
        self::validateRequiredString($data, 'vehicle_type');
        self::validateRequiredNumeric($data, 'vehicle_year');
        self::validateRequiredString($data, 'usage_type');

        $noClaimYears = self::optionalNumeric(
            $data,
            'no_claim_years',
            0
        );

        $hasPreviousClaim = self::optionalBoolean(
            $data,
            'has_previous_claim',
            false
        );

        $vehicleValue = self::nullableNumeric(
            $data,
            'vehicle_value'
        );

        $coverageLevel = self::nullableString(
            $data,
            'coverage_level'
        );

        /** @var int|float $insuranceTypeId */
        $insuranceTypeId = $data['insurance_type_id'];

        /** @var int|float $vehicleYear */
        $vehicleYear = $data['vehicle_year'];

        /** @var string $vehicleType */
        $vehicleType = $data['vehicle_type'];

        /** @var string $usageType */
        $usageType = $data['usage_type'];

        return new self(
            insuranceTypeId: (int) $insuranceTypeId,
            vehicleType: $vehicleType,
            vehicleYear: (int) $vehicleYear,
            usageType: $usageType,
            noClaimYears: (int) $noClaimYears,
            hasPreviousClaim: $hasPreviousClaim,
            vehicleValue: $vehicleValue !== null
                ? (float) $vehicleValue
                : null,
            coverageLevel: $coverageLevel,
        );
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private static function validateRequiredNumeric(
        array $data,
        string $key
    ): void {
        if (! isset($data[$key]) || ! is_numeric($data[$key])) {
            throw new InvalidArgumentException(
                "{$key} must be numeric."
            );
        }
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private static function validateRequiredString(
        array $data,
        string $key
    ): void {
        if (! isset($data[$key]) || ! is_string($data[$key])) {
            throw new InvalidArgumentException(
                "{$key} must be a string."
            );
        }
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private static function optionalNumeric(
        array $data,
        string $key,
        int|float $default
    ): int|float {
        $value = $data[$key] ?? $default;

        if (! is_numeric($value)) {
            throw new InvalidArgumentException(
                "{$key} must be numeric."
            );
        }

        return is_int($value) ? $value : (float) $value;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private static function optionalBoolean(
        array $data,
        string $key,
        bool $default
    ): bool {
        $value = $data[$key] ?? $default;

        if (! is_bool($value)) {
            throw new InvalidArgumentException(
                "{$key} must be boolean."
            );
        }

        return $value;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private static function nullableNumeric(
        array $data,
        string $key
    ): int|float|null {
        $value = $data[$key] ?? null;

        if ($value !== null && ! is_numeric($value)) {
            throw new InvalidArgumentException(
                "{$key} must be numeric."
            );
        }

        if ($value === null) {
            return null;
        }

        return is_int($value) ? $value : (float) $value;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private static function nullableString(
        array $data,
        string $key
    ): ?string {
        $value = $data[$key] ?? null;

        if ($value !== null && ! is_string($value)) {
            throw new InvalidArgumentException(
                "{$key} must be a string or null."
            );
        }

        return $value;
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
