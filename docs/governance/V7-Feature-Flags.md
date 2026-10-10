# V7.0 Feature Flags

## formula_engine_v2

Feature key: `formula_engine_v2`

Configuration:

- Environment variable: `FORMULA_ENGINE_V2`
- Default: `false`
- Laravel configuration: `config/features.php`

Behavior:

- When disabled, the legacy formula path is used.
- When enabled, the V2 formula path is used.

Consumers:

- `App\Services\Formula\FormulaService`
- `App\Services\Quote\PremiumCalculator`

Verification:

- `FormulaServiceTest`: 7 tests passed.
- `PremiumCalculatorTest`: 7 tests passed.
- Total: 14 tests passed, 70 assertions.

No rollout mechanism, percentage rollout, or feature-flag management service is claimed by this document.
