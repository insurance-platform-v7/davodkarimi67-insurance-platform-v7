<?php

namespace App\Support;

class FeatureFlag
{
    public static function enabled(string $key): bool
    {
        return match ($key) {
            'formula_engine_v2' => (bool) env('FORMULA_ENGINE_V2', false),
            default => false,
        };
    }
}
