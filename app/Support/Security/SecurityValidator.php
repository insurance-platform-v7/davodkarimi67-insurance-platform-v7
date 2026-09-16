<?php

namespace App\Support\Security;

class SecurityValidator
{
    public static function sanitize(mixed $value): mixed
    {
        return is_string($value) ? trim(strip_tags($value)) : $value;
    }

    public static function validateSafeInput(mixed $value): bool
    {
        if (! is_string($value)) {
            return true;
        }

        return ! preg_match('/(union|select|insert|delete|drop|--|;)/i', $value);
    }

    public static function assertTenantAccess(
        int|string $userTenantId,
        int|string $targetTenantId
    ): bool {
        return (int) $userTenantId === (int) $targetTenantId;
    }
}
